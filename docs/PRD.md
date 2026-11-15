# Rumah123 Clone — PRD (Lebih dari Full Marketplace)

Status: Phase 1 — Scaffold
Stack: Laravel 13 + PHP 8.3 + MySQL 8.4 (`rumah123`) + Blade + Tailwind + Alpine (theme Metronic v9 Tailwind) + Filament (admin) + Sanctum (API siap Flutter)

## 1. Visi
Marketplace properti Indonesia jual/sewa: rumah, apartemen, tanah, ruko, villa — plus ekstensi: lelang, proyek developer (primary), jasa (KPR, notaris, inspeksi), leads WA, pSEO massal.

## 2. User
- Pencari (guest/member): cari, filter, favorit, bandingkan, hubungi agen, ajukan KPR
- Agen/Developer: daftar, pasang iklan, kelola listing, terima leads, paket iklan
- Admin: moderasi, paket, artikel/pSEO, setting adapter (storage, payment, WA)

## 3. Scope MVP (Phase 1-2)
1. Listing CRUD + galeri + fasilitas + peta (lat/lng)
2. Search/filter: keyword, tipe, status jual/sewa, kota, harga, KT/KM/LT/LB, sertifikat
3. Detail + agen + tombol WA + form leads
4. Favorit + bandingkan (member)
5. Auth agen + dashboard agen sederhana
6. Admin Filament: users, listings, leads, articles
7. pSEO: /jual/{tipe}/{kota}, /sewa/{tipe}/{kota}, detail /properti/{slug}
8. KPR kalkulator (JS) + form pengajuan (adapter bank via admin UI, no hardcode)

## 4. Non-goals Phase 1
- Mobile Flutter (web dulu, API disiapkan)
- Redis/Horizon, Meilisearch (pakai DB + cache file dulu)
- Payment gateway real (stub adapter dulu)

## 5. Aturan Global
- pSEO wajib, no hardcoded providers (storage S3-compatible, WA, payment via adapter + admin UI)
- Frontend nempel Laravel (Blade), bukan SPA terpisah
- Lisensi: Metronic di `D:\projects\metronics bajakan\` hanya untuk dev; produksi wajib lisensi resmi Keenthemes
