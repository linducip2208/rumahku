<?php

namespace App\Services\Payments;

use App\Models\PropertyOrder;
use App\Models\Setting;

/**
 * Kontrak payment gateway.
 *
 * Semua kredensial WAJIB dibaca dari admin settings (tabel `settings`),
 * bukan dari .env / konstanta hardcode. Lihat {@see Setting}.
 */
interface PaymentGateway
{
    public function name(): string;

    /**
     * Buat tagihan untuk sebuah order.
     *
     * @return array{reference_id: string, pay_url: ?string, token: ?string, raw: array<string, mixed>}
     *
     * @throws MissingCredentials bila kunci gateway belum diisi di admin.
     */
    public function createCharge(PropertyOrder $order): array;

    /**
     * Tangani callback/webhook dari gateway.
     *
     * Mengembalikan order yang berhasil dilunasi (sudah di-markAsPaid),
     * atau null bila payload tidak valid / bukan status lunas.
     */
    public function handleCallback(array $payload): ?PropertyOrder;
}
