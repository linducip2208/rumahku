# CHANGELOG

## 2026-11-14
- Scaffold Laravel 13 + Boost + DB rumah123 + Filament/Sanctum/Permission
- docs/: PRD, PROGRESS, ARCHITECTURE, ERD, FEATURES, ROADMAP, CHANGELOG, DECISIONS dibuat
- DemoSeeder defensif (admin + 10 agen + 50 properties + images + 5 projects + 10 articles; skip blok bila model/tabel belum ada) + MarketplaceTest (home/listing/show/leads/pSEO, skip gracefully)
- Hasil 2026-11-14: `db:seed` OK (users 12, agent_profiles 10, properties 50 jual34/sewa16, images 125, projects 5, articles 10, semua published); `php artisan test` hijau (7 tests: 6 passed, 1 skipped pSEO — route city belum ada)

## 2026-11-15
- ExtraSeeder (idempotent, updateOrCreate): 6 auctions (3 open + 3 closed, link property) + 12 services (notaris/inspeksi/kpr/renovasi × kota sample); didaftarkan di DatabaseSeeder; seed ulang stabil di auctions 6 / services 12
- ExtraFlowTest (skip graceful bila route/model belum ada): /lelang 200, /jasa 200 + filter category, /bandingkan?ids= 200, /harga 200 (memuat Gratis/Basic/Plus/Pro), sitemap SKIP (route belum ada)
- Hasil 2026-11-15: `php artisan test --compact` hijau (14 tests: 13 passed, 1 skipped sitemap); test lama tidak diubah
- PayFlowTest (4 tests hijau: guest /pay → login, POST /orders → pending → GET /pay 200 instruksi manual, callback manual {} ok:true, callback unknown-driver ok:false 200) + docs/API.md (kontrak API v1 Flutter)
