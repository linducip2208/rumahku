# INSTALLATION (dev Windows)

1. PHP 8.3 + Composer + MySQL 8 + Node 24.
2. `composer install && npm install && npm run build`
3. Copy `.env.example` → `.env`, isi DB `rumah123`, `php artisan key:generate`
4. `mysql -u root -e "CREATE DATABASE rumah123 CHARACTER SET utf8mb4 ..."` (+ `rumah123_testing`)
5. `php artisan migrate --force && php artisan db:seed --force && php artisan seo:articles && php artisan storage:link`
6. Meilisearch: jalankan `D:\tools\meilisearch\meilisearch.exe --master-key <kunci> --http-addr 127.0.0.1:7700`, samakan `.env`, `php artisan scout:import "App\Models\Property"`
7. `php artisan serve --port=8000` → `/`, `/admin` (admin@rumah123.local / password), `/docs/api`
8. Jangan pernah `migrate:fresh` tanpa cek DB aktif (gunakan `.env.testing` untuk testing).
