<?php

namespace App\Services\Payments;

use App\Models\PropertyOrder;
use App\Models\Setting;
use RuntimeException;

/**
 * Driver Xendit (Invoice API).
 *
 * Kredensial dibaca dari settings:
 * - payments.xendit_secret_key (wajib)
 * - payments.xendit_callback_token (opsional tapi disarankan, untuk verifikasi webhook)
 *
 * Tidak ada key hardcode: bila secret key kosong, lempar MissingCredentials.
 */
class XenditDriver implements PaymentGateway
{
    /**
     * @var callable|null Fake HTTP untuk testing: fn(string, array, array): array
     */
    public function __construct(private mixed $http = null) {}

    public function name(): string
    {
        return 'xendit';
    }

    public function secretKey(): string
    {
        return (string) Setting::get('payments.xendit_secret_key', '');
    }

    /**
     * @return array{reference_id: string, pay_url: ?string, token: ?string, raw: array<string, mixed>}
     */
    public function createCharge(PropertyOrder $order): array
    {
        $secretKey = $this->secretKey();

        if ($secretKey === '') {
            throw new MissingCredentials(
                'Xendit secret key belum diisi. Isi di admin: Pengaturan → Pembayaran (payments.xendit_secret_key).'
            );
        }

        $referenceId = 'XND-'.$order->id.'-'.now()->format('YmdHis');

        $body = [
            'external_id' => $referenceId,
            'amount' => (int) $order->amount,
            'description' => 'Paket '.$order->package_slug.' #'.$order->id,
            'payer_email' => $order->user?->email,
            'currency' => 'IDR',
        ];

        $response = $this->postJson('https://api.xendit.co/v2/invoices', [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode($secretKey.':'),
        ], $body);

        return [
            'reference_id' => $referenceId,
            'pay_url' => $response['invoice_url'] ?? null,
            'token' => isset($response['id']) ? (string) $response['id'] : null,
            'raw' => $response,
        ];
    }

    public function handleCallback(array $payload): ?PropertyOrder
    {
        $secretKey = $this->secretKey();

        if ($secretKey === '') {
            throw new MissingCredentials(
                'Xendit secret key belum diisi. Isi di admin: Pengaturan → Pembayaran (payments.xendit_secret_key).'
            );
        }

        $expectedToken = (string) Setting::get('payments.xendit_callback_token', '');
        $givenToken = (string) ($payload['callback_token'] ?? '');

        if ($expectedToken !== '' && ! hash_equals($expectedToken, $givenToken)) {
            return null;
        }

        $status = strtoupper((string) ($payload['status'] ?? ''));
        $externalId = (string) ($payload['external_id'] ?? '');

        if ($externalId === '' || ! in_array($status, ['PAID', 'SETTLED'], true)) {
            return null;
        }

        $order = PropertyOrder::query()->where('payment_reference', $externalId)->first();

        if (! $order || $order->status === 'paid') {
            return $order;
        }

        $order->markAsPaid();

        return $order->fresh();
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    protected function postJson(string $url, array $headers, array $body): array
    {
        if (is_callable($this->http)) {
            return (array) call_user_func($this->http, $url, $headers, $body);
        }

        $handle = curl_init($url);

        if ($handle === false) {
            throw new RuntimeException('Xendit: gagal inisialisasi HTTP client.');
        }

        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_TIMEOUT => 30,
        ]);

        $raw = curl_exec($handle);
        $httpCode = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        if ($raw === false) {
            throw new RuntimeException('Xendit: koneksi gagal. '.$error);
        }

        $decoded = json_decode((string) $raw, true);

        if (! is_array($decoded) || $httpCode < 200 || $httpCode >= 300) {
            throw new RuntimeException('Xendit: respons tidak valid (HTTP '.$httpCode.').');
        }

        return $decoded;
    }
}
