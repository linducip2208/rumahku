<?php

namespace App\Services\Payments;

use App\Models\Setting;
use InvalidArgumentException;

/**
 * Resolve driver gateway dari setting `payments.default_driver`.
 *
 * Nilai yang didukung: manual | midtrans | xendit.
 */
class PaymentManager
{
    /**
     * @var callable|null Fake HTTP untuk testing, diteruskan ke driver online.
     */
    public function __construct(private mixed $http = null) {}

    public function defaultDriver(): string
    {
        return (string) Setting::get('payments.default_driver', 'manual');
    }

    public function driver(?string $name = null): PaymentGateway
    {
        $name = strtolower((string) ($name ?? $this->defaultDriver()));

        return match ($name) {
            'manual' => new ManualDriver,
            'midtrans' => new MidtransDriver($this->http),
            'xendit' => new XenditDriver($this->http),
            default => throw new InvalidArgumentException("Driver pembayaran [{$name}] tidak didukung. Gunakan: manual, midtrans, xendit."),
        };
    }
}
