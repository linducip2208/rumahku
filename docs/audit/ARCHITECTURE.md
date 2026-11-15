# ARCHITECTURE (audit)

Monolit Laravel 13: Blade (Metronic) + Filament /admin + REST /api/v1 (Sanctum) + Flutter terpisah. DB MySQL 8.4 (FK cascade, index komposit listings, unique slug/order). Search Scout→Meilisearch (fallback manual). Payment: PaymentManager + driver (midtrans/xendit/manual), kunci di tabel settings via admin. Mail: log driver + try/catch. Queue: sync (belum worker). Storage: public disk lokal + halaman instruksi S3. Scheduler: seo:articles weekly. Tanpa SPA terpisah, tanpa microservice — sesuai stack-lock.
