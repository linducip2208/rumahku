# SECURITY_AUDIT (diperbaiki 2026-11-16)

Auth: session web + Sanctum bearer; hash bcrypt12; Filament canAccessPanel admin-only (403 teruji). Register role hardcode (agent web / user API), role DIBUANG dari Fillable.
Fixed: web order ownership 403 + anti-duplikat pending; login/register throttle:10,1 web+API; API show published-only; bid dalam transaction+lockForUpdate; markAsPaid idempotent; payment_reference unique.
Terverifikasi: CSRF aktif kecuali pay/callback (signature Midtrans SHA512 / Xendit token, unknown→200 ok:false); XSS: 1x {!! !!} hanya deklarasi XML sitemap; upload image-only 5MB; leads/KPR throttle:30,1.
Sisa by-design: token tanpa expiry, abilities tak di-enforce, Spatie tak dipakai, Xendit tanpa token = tanpa verifikasi, tanpa captcha leads.
