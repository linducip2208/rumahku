# DEPLOYMENT (checklist produksi)

- APP_ENV=production, APP_DEBUG=false, APP_URL domain asli; `php artisan config:cache route:cache view:cache`
- DB MySQL user khusus (bukan root), backup via `backup:database` + cron harian
- `npm run build`, `storage:link`, permission storage/bootstrap/cache
- Meilisearch produksi (master key kuat, persistence), SCOUT_DRIVER=meilisearch
- Scheduler: cron `schedule:run` tiap menit (seo:articles weekly); queue worker bila notifikasi pindah ke queue
- Isi payment keys + mail di admin (bukan .env); Matikan IDE local: APP_DEBUG=false, Telescope/Pail non-prod
- Smoke: /up, /, /sitemap.xml, /api/v1/properties, /admin/login
