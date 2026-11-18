# TESTING

`vendor/bin/phpunit` (serial, stabil) — 81 tests/253 assertions hijau di MySQL rumah123_testing (SCOUT collection). Jangan `php artisan test` paralel bila flaky. Coverage: panel, auth, listing+upload, leads+notif, KPR, bid, favorit, orders+payment callback, throttle, moderation, SEO, ideas, backup. Belum: browser test, captcha, token expiry.

## Browser (Dusk) — butuh Chrome

- Lokal: pasang Chrome, lalu `php artisan dusk` (APP_URL + DB dari `.env`; chromedriver via `php artisan dusk:chrome-driver --detect`).
- CI: job `dusk` (Chrome via `browser-actions/setup-chrome`, MySQL `rumah123_dusk`, serve :8000, `php artisan dusk`). Smoke: home, login, redirect dashboard guest, pricing, admin login.
- `tests/Browser/SmokeTest.php` pakai `DatabaseMigrations` (bukan RefreshDatabase — request browser jalan di proses HTTP terpisah).

## Lighthouse

- `npm run lighthouse` (butuh Chrome; baca `lighthouserc.json`): budget performance warn ≥0.7, a11y/best-practice/SEO error ≥0.9 untuk `/` dan `/properti`.
- Jalankan sambil `php artisan serve --port=8000` dengan DB ter-seed.
