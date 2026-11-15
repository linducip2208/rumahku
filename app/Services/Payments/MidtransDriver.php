<?php

namespace App\Services\Payments;

use App\Models\PropertyOrder;
use App\Models\Setting;
use RuntimeException;

/**
 * Driver Midtrans (Snap API).
 *
 * Kredensial dibaca dari settings:
 * - payments.midtrans_server_key (wajib)
 * - payments.midtrans_client_key (opsional, untuk Snap.js di frontend)
 * - payments.midtrans_is_production ('1' = production, selain itu sandbox)
 *
 * Tidak ada key hardcode: bila server key kosong, lempar MissingCredentials.
 */
class MidtransDriver implements PaymentGateway
{
    /**
     * @var callable|null Fake HTTP untuk testing: fn(string, array, array): array
     */
    public function __construct(private mixed $http = null) {}

    public function name(): string
    {
        return 'midtrans';
    }

    public function isProduction(): bool
    {
        return Setting::get('payments.midtrans_is_production') === '1';
    }

    public function serverKey(): string
    {
        return (string) Setting::get('payments.midtrans_server_key', '');
    }

    /**
     * @return array{reference_id: string, pay_url: ?string, token: ?string, raw: array<string, mixed>}
     */
    public function createCharge(PropertyOrder $order): array
    {
        $serverKey = $this->serverKey();

        if ($serverKey === '') {
            throw new MissingCredentials(
                'Midtrans server key belum diisi. Isi di admin: Pengaturan → Pembayaran (payments.midtrans_server_key).'
            );
        }

        $referenceId = 'MID-'.$order->id.'-'.now()->format('YmdHis');

        $body = [
            'transaction_details' => [
                'order_id' => $referenceId,
                'gross_amount' => (int) $order->amount,
            ],
            'item_details' => [
                [
                    'id' => $order->package_slug,
                    'price' => (int) $order->amount,
                    'quantity' => 1,
                    'name' => 'Paket '.$order->package_slug,
                ],
            ],
            'customer_details' => [
                'email' => $order->user?->email,
                'first_name' => $order->user?->name,
            ],
        ];

        $url = $this->isProduction()
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $response = $this->postJson($url, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode($serverKey.':'),
        ], $body);

        return [
            'reference_id' => $referenceId,
            'pay_url' => $response['redirect_url'] ?? null,
            'token' => $response['token'] ?? null,
            'raw' => $response,
        ];
    }

    public function handleCallback(array $payload): ?PropertyOrder
    {
        $serverKey = $this->serverKey();

        if ($serverKey === '') {
            throw new MissingCredentials(
                'Midtrans server key belum diisi. Isi di admin: Pengaturan → Pembayaran (payments.midtrans_server_key).'
            );
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signature = (string) ($payload['signature_key'] ?? '');

        if ($orderId === '' || $signature === '') {
            return null;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        if (! hash_equals($expected, $signature)) {
            return null;
        }

        $status = (string) ($payload['transaction_status'] ?? '');
        $fraud = (string) ($payload['fraud_status'] ?? 'accept');

        $settled = $status === 'settlement'
            || ($status === 'capture' && $fraud === 'accept');

        if (! $settled) {
            return null;
        }

        $order = PropertyOrder::query()->where('payment_reference', $orderId)->first();

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
            throw new RuntimeException('Midtrans: gagal inisialisasi HTTP client.');
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
            throw new RuntimeException('Midtrans: koneksi gagal. '.$error);
        }

        $decoded = json_decode((string) $raw, true);

        if (! is_array($decoded) || $httpCode < 200 || $httpCode >= 300) {
            throw new RuntimeException('Midtrans: respons tidak valid (HTTP '.$httpCode.').');
        }

        return $decoded;
    }
}
