# PROGRESS — Rumah123 Clone

## Done
- [x] `composer create-project laravel/laravel rumah123` (Laravel 13.42, PHP 8.3)
- [x] `php artisan boost:install --no-interaction`
- [x] DB `rumah123` dibuat (MySQL 8.4, root/nopass)
- [x] `.env`: APP_NAME=Rumah123, LOCALE=id, FAKER=id_ID, TIMEZONE=Asia/Jakarta, DB=mysql rumah123
- [x] Composer: filament/filament ^5.11, sanctum ^4.4, spatie/permission ^8.5
- [x] Theme tersedia: `D:\projects\metronics bajakan\metronic-tailwind-html-starter-kit\` (v9.5 Tailwind) + `dist/html` 17 layout
- [x] docs/PRD.md dibuat

## In Progress
- [ ] Integrasi Metronic starter ke Blade (`resources/views/layouts`, `public/metronic`)
- [ ] Migrasi inti: agents, properties, property_images, favorites, leads, articles, projects
- [ ] Routes pSEO + home/listing/detail + Filament admin

## Next
- [x] Seeder 50 listing dummy id_ID + 10 agen + artikel pSEO
- [x] KPR kalkulator + form leads WA
- [x] Test: `php artisan test --compact` — 6 passed, 1 skipped (pSEO city belum ada)
- [x] `npm run build` + `php artisan serve` verifikasi visual — `/` 200, `/properti` 200

## 2026-11-15 — Integrasi penuh (4 agen paralel)
- [x] Metronic assets: `dist/assets` → `public/metronic/{css,js,media,vendors}`
- [x] Blade: `layouts/metronic.blade.php` + `pages/home,index,show` + `Public/{Home,Property,Lead}Controller` + routes `/, /properti, /properti/{slug}, POST /leads`
- [x] DB: users.role + agent_profiles, properties (index slug/city-type-status/price), images, favorites unique pair, leads, projects, articles + factories id_ID — `migrate:fresh` hijau
- [x] Filament /admin: Property, AgentProfile, Lead, Project, Article resources (19 routes)
- [x] Seed real: properties 50, images 125, agents 10, projects 5, articles 10
- [x] Build Vite OK, serve 200/200, pint fixed

Live: `php artisan serve --port=8001` → `/` `/properti` `/admin`. Preview Metronic mentah: `http://127.0.0.1:8901/html/layout-1/index.html`.

## 2026-11-15 — Sisa tertinggal (3 agen paralel + wiring)
- [x] pSEO: `SeoController@landing` `/{listing}/{type}/{city}` paling bawah + `showArticle`; views seo-landing/projects/agents/articles index+show
- [x] Publik: /proyek, /agen, /artikel (+detail), /harga, /bandingkan (+add/remove session), /favorit toggle auth, /lelang, /jasa
- [x] Monetisasi: packages (Gratis/Basic/Plus/Pro) + property_orders (pending→paid via Filament action `markAsPaid`, featured auto) + `OrderController@store` + pricing view
- [x] Ekstensi P1 stub: auctions, services (notaris/inspeksi/kpr/renovasi), messages (chat-box component), RecommendationService (city+type+±30% harga) + compare/recommendations components
- [x] Test fix: pseoPath pakai route seo.landing → 9 passed, 22 assertions
- [x] HTTP 10/10 200: /, /properti, /jual/rumah/depok, /proyek, /agen, /artikel, /harga, /bandingkan, /lelang, /jasa; view:cache+clear OK, pint passed

## 2026-11-15 — Favicon + P2 (3 agen paralel + wiring)
- [x] Favicon baru: GD house icon (blue gradient + white house + amber door) → public/favicon.ico (real ICO via Imagick, dulu 0-byte), favicon-16/32, apple-touch-icon, favicon-512; link di layouts/metronic head + theme-color
- [x] Auth: /login /register /logout (role agent + AgentProfile auto, intended /dashboard); guest /dashboard → 302 login
- [x] Dashboard agen: statistik (listing/views/leads/orders), CRUD listing saya (ownership 403, slug unik, upload multi-foto 5MB → storage/public/properties + hapus file), orders saya + info transfer stub
- [x] Nav header: Masuk/Daftar Agen (guest), Dasbor + Pasang Iklan → agent.listings.create (auth); fix link Agen/Artikel/Proyek Baru; hapus redirect /dashboard→/admin yang membayangi
- [x] SEO-tech: /sitemap.xml (statis + property/project/article + pSEO exists-only, cache 1 jam, application/xml), robots.txt (Allow / + Disallow /admin /dashboard + Sitemap), errors/404 custom, components/seo-meta
- [x] ExtraSeeder: auctions 6 (3 open/3 closed), services 12 (4 kategori × 6 kota), idempotent
- [x] Test: 16 passed, 55 assertions (AgentFlowTest register→login→listing+upload fake→orders; ExtraFlowTest; PackageTest; MarketplaceTest)
- [x] storage:link OK, pint passed
- [x] HTTP: /login 200, /register 200, /sitemap.xml 200 xml, /harga 200, 404 custom OK, /dashboard 302 guest

## 2026-11-15 — P2 akhir (4 agen paralel + wiring)
- [x] REST API v1 (Sanctum): install:api + routes/api.php (15 routes): auth register/login/me/logout+token, properties list+detail (filter/sort/paginate), projects, articles, leads, favorites, orders; Resources Api/* (thumbnail via Storage::url, related_ids); ApiTest 5 test
- [x] Search Scout DATABASE engine: SCOUT_DRIVER=database (.env+example), Property Searchable + shouldBeSearchable published-only, index() Scout branch + try/catch fallback manual; scout:import 50 records; SearchTest 2 test
- [x] Payment adapter (no hardcode): PaymentGateway interface + Midtrans/Xendit/Manual drivers (curl injectable, MissingCredentials bila kunci kosong), PaymentManager, settings table + Setting model + Filament ManagePayments (kunci via admin UI), orders tambah payment_reference/driver, PayController pay+callback, pay/instructions view, PaymentTest 10 test
- [x] Wiring main: GET /pay/{order} (auth), POST /pay/callback/{driver} (public + CSRF except pay/callback/* di bootstrap/app.php + callback try/catch → 200 ok:false bila driver unknown/kunci kosong, anti retry-storm)
- [x] Admin widgets: StatsOverview (listing/views/leads/revenue + mini chart), PropertiesChart, OrdersChart, LatestLeads + ManageStorage (disk aktif + test-write, tanpa tulis .env dari UI)
- [x] Test: 33 passed, 114 assertions; pint passed
- [x] HTTP: /api/v1/properties|projects|articles 200, /pay/callback/manual POST 200, /sitemap.xml 200, /jual/rumah/depok 200
- [ ] Flutter app: SDK tidak ada di mesin ini — API sudah Flutter-ready (kontrak di atas); scaffold Flutter setelah SDK terpasang

## 2026-11-15 — Putaran final (5 agen paralel + SDK + Meilisearch)
- [x] Flutter SDK: git clone stable → D:\flutter-sdk (Flutter 3.50.0 / Dart 3.14.0)
- [x] App Flutter D:\project flutter\rumah123 (com.rumah123): config, models, ApiClient, 7 screens + widgets, http+shared_preferences; analyze 0 issue, test 3/3, build web OK
- [x] Notifikasi leads: LeadReceived mailable + wa.me link + helper anti-500 + ADMIN_EMAIL via config app.admin_email; LeadNotifyTest 2 test (web 302 + API 201)
- [x] API docs Scramble v0.13.53: /docs/api 200 + /docs/api.json OpenAPI 3.1.2 (gate fix Laravel 13); ApiDocsTest 3 test
- [x] Generator pSEO: seo:articles (idempotent, data-driven min/max/avg, --fresh/--listing) + weekly schedule; 50 artikel dibuat (total 60); SeoArticlesTest 9 assertions
- [x] Detail UX: favorit/bandingkan/share, galeri Alpine+fallback, sticky bar mobile (tel/WA/Tanya), seo-meta tanpa title ganda (title milik layout)
- [x] PayFlowTest 4 test + docs/API.md (kontrak Flutter) + CHANGELOG/PROGRESS append
- [x] Meilisearch v1.56.2 lokal: D:\tools\meilisearch\meilisearch.exe (port 7700, analytics off) + meilisearch-php ^1.17; SCOUT_DRIVER=meilisearch, import 50 records; SearchTest hijau vs server real
- [x] Test: 43 passed, 146 assertions; pint passed
- [x] HTTP: /docs/api 200, /docs/api.json 200, /properti?q=rumah 200, /artikel/jual-rumah-depok 200, /api/v1/properties?q=villa 200

## 2026-11-15 — Audit MySQL + gas sisa
- [x] Audit: migrate:status semua Ran (19 migrasi, 4 batch), .env murni MySQL rumah123, TABLE_ROWS ternyata estimasi basi — COUNT(*) real: users 12, properties 50, images 125, agents 10, articles 60, projects 5, packages 4, auctions 6, services 12
- [x] ExtraSeeder + seedLeads (5 demo leads idempotent, agent = pemilik properti, source web) → leads 5
- [x] Orphan check: images/leads/favs/auctions 0 yatim
- [x] Meilisearch stats: 50 docs terindeks, isIndexing false; scout:import ulang OK
- [x] Test 43 passed / 146 assertions + pint passed; HTTP: /, /properti, /properti?q=jakarta, /agen 200

## 2026-11-16 — Full MySQL
- [x] phpunit.xml: sqlite memory → mysql rumah123_testing (+ SCOUT_DRIVER=collection agar test hermetis, tidak mengotori index Meilisearch dev)
- [x] Test: 43 passed / 146 assertions di MySQL
- [x] DB dev utuh: properties 50, users 12, articles 60, leads 5; index Meilisearch flush+import → 50 docs bersih

## 2026-11-16 — Polish (3 agen paralel + wiring)
- [x] backup:database (mysqldump auto-detect + gzip + prune --keep) + BackupTest; backup 25KB terverifikasi gunzip header
- [x] og-cover.png 1200×630 (GD, verif visual) + default di seo-meta; fix: home include seo-meta, show fallback og-cover (single title milik layout)
- [x] Filament bulk Publish/Unpublish + throttle leads 30/mnt + ModerationTest (unpublished → 404 + hidden index)
- [x] Test: 47 passed / 165 assertions; HTTP home og-cover OK, listing OK; pint passed

## 2026-11-16 — KPR + Bidding + insiden DB (2 agen + recovery)
- [x] KPR end-to-end: kpr_applications (dp≤harga, tenor/bank enum, guest ok) + form di detail + Filament + KprFlowTest 5 test; POST /kpr (throttle)
- [x] Lelang fungsional: auction_bids + placeBid (min increment max(1jt,1%), window, open-only) + /lelang/{auction} countdown + riwayat + Filament read-only + AuctionBidTest 6 test; /jasa/{service} (WA langsung, leads.property_id NOT NULL)
- [x] INSIDEN: `migrate:fresh --env=testing` tanpa .env.testing → fallback .env → DB dev ter-wipe. Recovery: .env.testing BARU (aman permanen) + db:seed + seo:articles (50) + scout flush/import → data pulih 100% (users 12, properties 50, images 125, agents 10, articles 60, projects 5, leads 5, packages 4, auctions 6, services 12)
- [x] Test serial: 58 passed / 193 assertions; pint passed
- [x] HTTP: /lelang 200, /lelang/1 200, /jasa 200, /jasa/1 200, /properti 200

## 2026-11-16 — Admin auth + hardening
- [x] TEMUAN: /admin 403 untuk semua user (Filament tolak default). FIX: User implements FilamentUser, canAccessPanel hanya role admin (agen pakai /dashboard)
- [x] AdminPanelTest 4 test (guest redirect, admin 200 dashboard+properties, agen 403)
- [x] config:cache + config:clear kompatibel; Meilisearch up; /admin/login 200
- [x] Test: 62 passed / 199 assertions; pint passed

## 2026-11-16 — Bank ide 20.000 (20 fitur × 1.000)
- [x] feature_ideas (feature_key+slot unik) + IdeaBank (matriks 10 audiens × 10 kebutuhan × 10 mekanisme, deterministik, tanpa AI/eksternal) + ideas:seed (upsert idempotent, --feature)
- [x] Filament FeatureIdeaResource (filter fitur/mekanisme/status + view) + API /api/v1/ideas (+features) + IdeaBankTest 4 test
- [x] Seed: 20.000 baris, 20 fitur, tiap fitur tepat 1.000 (cek GROUP BY), judul UTF-8 valid (em-dash E28094)
- [x] Fix test login Filament: via Livewire::test (POST biasa 405 by design — form Livewire)
- [x] Test: 69 passed / 221 assertions; pint passed; HTTP: features 20, ideas kpr total 1000

## 2026-11-16 — Eksekusi otonom (audit + fix + docs)
- [x] Audit paralel 3 agen (backend, frontend, security): 30 temuan; repo bukan kosong
- [x] Fix: web order ownership 403 + anti-duplikat; login/register throttle:10,1; role keluar Fillable; API show published-only; bid transaction+lock; markAsPaid idempotent; payment_reference unique; index hot tables; filter web in:; KPR validasi rapi
- [x] Fix FE: nav/footer links, named routes, hapus label Stub, @error per-field, tombol hapus compare, error pages 403/419/500/503, SEO meta 6 halaman
- [x] Docs: audit/ (7), execution/ (6), roadmap/ (3), INSTALLATION/DEPLOYMENT/BACKUP/TESTING/USER/ADMIN/SECURITY
- [x] Ideas metadata: priority P0-P4 + 10 kolom dampak (P0 8000, P1 8000, P2 4000)
- [x] Test: 81 passed / 253 assertions; pint passed

Update file ini setiap selesai satu blok.

## 2026-11-14 — Seeders + Tests (blok paralel)
- [x] `database/seeders/DemoSeeder.php` defensif (class_exists + hasTable; kolom users.role opsional)
- [x] Didaftarkan di `DatabaseSeeder` tanpa hapus seeder bawaan
- [x] `tests/Feature/MarketplaceTest.php` (skip gracefully bila route/model belum ada)
- Kredensial demo lokal: admin@rumah123.local / agen1..10@rumah123.local, password default `password` atau via env DEMO_PASSWORD (jangan commit password real)

## 2026-11-14 — Filament admin (blok paralel)
- [x] `php artisan filament:install --panels --no-interaction` → `app/Providers/Filament/AdminPanelProvider.php` (id `admin`, path `/admin`, login aktif, terdaftar di `bootstrap/providers.php`)
- [x] Resources (Filament v5: Resource + Schemas/Form + Tables/Table + Pages, field diselaraskan dgn model/migrasi aktual):
  - `PropertyResource` — form: user, title, slug, type (rumah/apartemen/tanah/ruko/villa), listing_status (jual/sewa), price Rp, KT/KM, LT/LB, sertifikat, provinsi/kota/kecamatan, alamat, lat/lng, deskripsi rich, featured, published_at; table: title, type badge, status badge, price money IDR, kota, featured, published; filter type/status/kota
  - `AgentProfileResource` — form: user, agency, phone, wa, photo, verified_at; table + TernaryFilter verifikasi
  - `LeadResource` — form: property, agen, nama, telepon, pesan, source; table + view (infolist) + bulk delete; filter source
  - `ProjectResource` — form: developer, name, slug, kota, price_from, deskripsi; table + filter kota
  - `ArticleResource` — form: title, slug, type pSEO, kota, body rich; table + filter type/kota
- [x] `php artisan route:list --path=admin` → 19 routes (dashboard, login/logout, 5 resources incl. leads view); tidak bentrok dgn routes web (`/` → HomeController, admin di bawah `/admin`)
- [x] `php artisan filament:optimize` OK (panel boot); `php -l` bersih untuk 33 file `app/Filament`
- Catatan: user admin TIDAK dibuat di blok ini (tanpa hardcode password di repo); DemoSeeder blok seeders sudah siapkan admin@rumah123.local untuk login `/admin`

## 2026-11-15 — P2: ExtraSeeder + ExtraFlowTest (blok paralel)
- [x] `database/seeders/ExtraSeeder.php` BARU: 6 auctions (3 open + 3 closed, link property_id berurutan, updateOrCreate key `property_id`) + 12 services (notaris/inspeksi/kpr/renovasi × kota sample, updateOrCreate key `name`); defensif class_exists + hasTable; idempotent (seed ulang tetap 6/12)
- [x] Didaftarkan di `DatabaseSeeder` (append `$this->call(ExtraSeeder::class)`, tanpa hapus seeder bawaan)
- [x] `tests/Feature/ExtraFlowTest.php` BARU (5 tests, skip graceful via Route::has/class_exists/Schema): /lelang 200, /jasa 200 + ?category=notaris, /bandingkan?ids= 200, /harga 200 memuat Gratis/Basic/Plus/Pro, sitemap SKIP (route belum ada)
- [x] Verifikasi: `db:seed --class=ExtraSeeder` hijau (auctions 6, services 12); `php artisan test --compact` hijau (14 tests: 13 passed, 1 skipped sitemap)
- [x] PayFlowTest BARU (4 tests hijau): guest /pay → login; POST /orders → pending → GET /pay 200 instruksi manual; callback manual {} ok:true; callback unknown-driver ok:false 200
- [x] docs/API.md BARU: kontrak API v1 Flutter-ready (ditulis dari routes/api.php + Resources aktual)
- Catatan: auth+dashboard agen, upload, sitemap/robots/404, favicon set baru tetap milik agen lain — blok ini hanya seeders/tests/docs
