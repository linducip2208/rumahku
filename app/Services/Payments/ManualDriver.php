<?php

namespace App\Services\Payments;

use App\Models\PropertyOrder;
use App\Models\Setting;

/**
 * Driver manual (transfer bank).
 *
 * Tidak butuh kredensial apa pun — daftar rekening dibaca dari setting
 * `payments.bank_accounts` yang diisi admin. Cocok sebagai fallback
 * selama kunci Midtrans/Xendit belum diisi.
 */
class ManualDriver implements PaymentGateway
{
    public function name(): string
    {
        return 'manual';
    }

    /**
     * @return array{reference_id: string, pay_url: ?string, token: ?string, raw: array<string, mixed>}
     */
    public function createCharge(PropertyOrder $order): array
    {
        $referenceId = 'MANUAL-'.$order->id.'-'.now()->format('YmdHis');
        $bankAccounts = (string) Setting::get('payments.bank_accounts', '');

        $instructions = 'Lakukan transfer sebesar Rp '.number_format((int) $order->amount, 0, ',', '.')
            .' ke salah satu rekening berikut dengan berita/kode: '.$referenceId.'.';

        return [
            'reference_id' => $referenceId,
            'pay_url' => null,
            'token' => null,
            'raw' => [
                'instructions' => $instructions,
                'bank_accounts' => $bankAccounts,
                'amount' => (int) $order->amount,
                'reference_id' => $referenceId,
            ],
        ];
    }

    public function handleCallback(array $payload): ?PropertyOrder
    {
        $status = strtolower((string) ($payload['status'] ?? ''));
        $referenceId = (string) ($payload['reference_id'] ?? '');

        if ($referenceId === '' || ! in_array($status, ['paid', 'settled', 'lunas'], true)) {
            return null;
        }

        $order = PropertyOrder::query()->where('payment_reference', $referenceId)->first();

        if (! $order || $order->status === 'paid') {
            return $order;
        }

        $order->markAsPaid();

        return $order->fresh();
    }
}
