<?php

namespace App\Services\Security;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Validasi token Cloudflare Turnstile via endpoint siteverify.
 *
 * Bila secret key belum diisi di settings (`captcha.turnstile_secret_key`),
 * validasi dilewati (return true) agar tidak memblokir alur dev lokal.
 */
class TurnstileValidator
{
    public const SECRET_KEY = 'captcha.turnstile_secret_key';

    public const SITE_KEY = 'captcha.turnstile_site_key';

    public function passes(?string $token, ?string $remoteIp = null): bool
    {
        $secret = trim((string) Setting::get(self::SECRET_KEY, ''));

        if ($secret === '') {
            Log::debug('Turnstile dilewati: secret key belum diisi (dev convenience).');

            return true;
        }

        if (! is_string($token) || trim($token) === '') {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                array_filter([
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ])
            );

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable $exception) {
            report($exception);

            return false;
        }
    }
}
