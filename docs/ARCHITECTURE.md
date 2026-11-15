# ARCHITECTURE — Rumah123 Clone

- Monolit Laravel 13, Blade + Tailwind (Metronic v9) + Alpine untuk interaktivitas ringan
- Admin Filament (CRUD + moderasi), auth Sanctum (siap Flutter, belum dibangun)
- MySQL 8.4 primary; queue/cache/database driver dulu (Redis/Horizon opsional Phase 2)
- Search Phase 1: MySQL fulltext/index; Phase 2: Meilisearch bila >50rb listing
- Storage: disk lokal Phase 1, adapter S3-compatible via admin UI Phase 2
- Modul: Listing, Agen, Leads, Favorit, Projects (primary), KPR (kalkulator + pengajuan stub), Artikel/pSEO, Paket iklan (stub)
