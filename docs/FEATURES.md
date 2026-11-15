# FEATURES

## P0 (wajib Phase 1)
Home, listing+filter, detail+galeri+peta, hubungi agen (WA), pasang iklan (agen), favorit, admin moderasi, pSEO routes, KPR kalkulator

## P1 (lebih dari Rumah123)
Bandingkan, lelang stub, proyek primary developer, jasa (inspeksi/notaris) direktori, chat in-app stub, AI rekomendasi stub, paket iklan/featured

## P2 (nanti)
Flutter app, Meilisearch, Redis/Horizon, payment real, multi-bahasa

## Status 2026-11-15 (blok paralel, tanpa ubah routes/controllers/views/models/migrasi/Filament)
- [x] P0 selesai: Home, listing+filter, detail+galeri, hubungi agen (WA leads), pasang iklan (orders+paket), favorit, admin moderasi (Filament), pSEO routes, KPR kalkulator — 9 passed MarketplaceTest, HTTP 10/10 200
- [x] P1 selesai (stub + seed + test): Bandingkan (+session add/remove, ?ids=), lelang (index + 6 auctions seed), proyek developer (5 seed), jasa direktori (index + filter category/city + 12 services seed), chat in-app stub (messages), AI rekomendasi stub, paket iklan/featured (4 paket + markAsPaid) — ExtraFlowTest 4 passed + 1 skipped (sitemap)
- [ ] P1 sisa (milik agen lain): auth+dashboard agen, upload, sitemap/robots/404, favicon set baru
