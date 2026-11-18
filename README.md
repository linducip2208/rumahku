# Rumah123 — Marketplace Properti (Laravel + Filament + Flutter-ready API)

Marketplace properti: katalog publik pSEO, dashboard agen, admin Filament `/admin`, API v1 Sanctum, payment Midtrans/Xendit/Manual.

## Kredensial demo (lokal saja)
- Admin: `admin@rumah123.local` / `password` → `/admin/login` (hint otomatis muncul saat `APP_DEBUG=true`)
- Agen: `agen1@rumah123.local` … `agen10@rumah123.local` / `password` → `/login`
- Password default via `DEMO_PASSWORD`, jangan commit password real.

## Setup
```bash
composer setup        # install + key + migrate + build
php artisan migrate --seed   # DemoSeeder (1 admin, 10 agen, 50 properti) + PackageSeeder + ExtraSeeder
php artisan ideas:seed       # 20.000 bank ide (opsional)
npm run build
php artisan serve
```

## Pintu
- Publik: `/`, `/properti`, `/proyek`, `/agen`, `/artikel`, `/lelang`, `/jasa`, `/bandingkan`, `/harga`, `/sitemap.xml`
- Agen: `/login`, `/register`, `/dashboard`, `/dashboard/listings`, `/dashboard/orders`, `/dashboard/leads`
- Admin: `/admin` (hanya `role=admin`, `User::canAccessPanel`)
- API: `/api/v1/*` + docs `/docs/api` (Scramble)

## API v1 ringkas
- `POST /v1/auth/register|login`, `GET /v1/me`, `POST /v1/auth/logout` (Sanctum, 30 hari, abilities)
- `GET /v1/properties`, `/v1/projects`, `/v1/articles`, `/v1/ideas`
- `POST /v1/leads` (throttle 30/mnt), `GET/POST /v1/favorites`, `GET/POST /v1/orders`

## Payment
- `GET /pay/{order}` (auth, ownership), `POST /pay/callback/{driver}` (public, CSRF-except, idempoten `markAsPaid`)
- Midtrans verifikasi `sha512`, Xendit verifikasi `callback_token` (wajib di production), Manual instruksi transfer.
- Setting via admin: Pengaturan → Pembayaran. Storage: Pengaturan → Storage (test-write, tanpa tulis `.env` dari UI).

## Test
```bash
php artisan test --compact      # MySQL rumah123_testing, Scout collection
vendor/bin/pint --dirty --format agent
```
Cakupan: web publik, agen flow, lelang/bid race+lock, payment idempotency, API, admin panel, audit, CSV, i18n.

## Hardening yang sudah diterapkan
- Throttle: login/register 10/mnt, leads/messages/KPR/bid/brosur/kontak dibatasi
- `role` tidak fillable (pakai `forceFill`), ownership 403, anti-duplikat pending order (409 API), favorite `firstOrCreate` anti-race
- Sitemap/proyek/agen hanya `published + not sold`, city filter `LIKE`, `Project.facilities` cast array
- `payment_reference/driver` fillable via forceFill→fillable resmi, Xendit token wajib di prod, callback log warning

## Struktur
- `routes/web.php`, `routes/api.php`, `app/Http/Controllers/{Public,Auth,Api}`, `app/Filament/{Resources,Pages,Widgets}`, `app/Services/Payments`, `database/{migrations,seeders}`, `docs/` (INSTALLATION/ADMIN/API/TESTING/dll)
