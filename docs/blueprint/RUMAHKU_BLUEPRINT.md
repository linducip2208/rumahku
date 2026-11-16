# RUMAHKU BLUEPRINT — Property Business Platform (bukan sekadar katalog)

Acuan: analisis alur bisnis Rumah123.com (marketplace + CRM agen + revenue engine). Prinsip: setiap fitur = alur bisnis nyata, data real, tanpa mockup.

## 1. Tiga mesin (wajib hidup bersama)
- Traffic Engine: SEO/pSEO, artikel, KPR tools, share → datangkan pencari.
- Listing Engine: agen/owner/developer mudah pasang → listing berkualitas (quality score).
- Revenue Engine: membership + kredit + booster + layanan developer.

## 2. Aktor & peran
| Peran | Daftar via | Bisa |
|---|---|---|
| Buyer/guest | — | cari, bandingkan, favorit(login), hubungi, ajukan KPR, ikut lelang |
| Owner | register (role owner) | SEMUA kemampuan agen utk properti sendiri, TANPA badge agensi |
| Agen | register (role agent) | + profil agensi, badge Terverifikasi, terima leads |
| Developer | admin assign (role developer) | kelola Project + unit + listing proyek |
| Admin | manual | moderasi, paket, order, payments, settings, audit |

## 3. Halaman publik (route → status)
- / (hero+search+cats+featured+latest+kota+agen+artikel) ADA
- /properti (filter/sort/chips/grid-list/map) ADA — KURANG: sort harga (controller belum dukung, tambah)
- /properti/{slug} (galeri, spek, fasilitas, peta OSM, agen, leads WA, KPR+pengajuan, chat, serupa) ADA
- /proyek, /proyek/{slug} (unit, fasilitas) ADA — KURANG: cover sudah AI, inventory count
- /agen, /agen/{id} ADA — KURANG: filter kota (tambah ?city), statistik agen
- /artikel, /lelang(+bid), /jasa, /harga, /bandingkan, /favorit ADA
- /tentang /bantuan /privasi /syarat /kontak(+inbox) ADA
- sitemap/robots/404(403/419/500/503) ADA

## 4. Dashboard agen/owner (vs Rumah123 Pro)
| Modul | Status |
|---|---|
| Statistik (listing/views/leads/orders) | ADA |
| My Listings CRUD + foto | ADA |
| Pending/Active/Sold | SEBAGIAN → tambah status Terjual (sold_at) + filter |
| Leads Management | ADA (list) — KURANG: pipeline status + follow-up reminder |
| Booster (featured/premier) | ADA via paket (featured) — KURANG: premier/Top slot + sundul |
| Membership + Credit | ADA paket/order manual — KURANG: kuota listing per paket + kredit wallet |
| Instant Brochure | BELUM — generate brosur PDF/print view per listing |
| Performance | ADA parsial (views count) — KURANG: klik WA/telp tracking |

## 5. Alur listing (state machine)
draft (published_at null) → active (published) → [sold (sold_at) | expired] ; moderasi admin: unpublish kapan saja. Aturan: unpublished/sold tidak tampil di katalog; show → 404; API ikut.
Owner flow = agent flow minus badge agensi (role owner, tanpa AgentProfile wajib).

## 6. CRM leads (target)
Lead masuk (web/API/WA-klik) → status: baru → dihubungi → survei → negosiasi → closing/gagal. Butuh: kolom status+follow_up_at di leads, reminder di dashboard, WA deep-link template. (Saat ini: list + notifikasi email.)

## 7. Monetisasi (target)
- Membership: Free (3 listing) / Basic / Pro / Business (kuota + harga) — ganti paket sekali-beli jadi langganan + enforce kuota saat store.
- Kredit wallet: beli kredit (payment gateway ada) → belanjakan utk Featured/Premier/Sundul per listing per hari.
- Booster: featured flag (ADA) + premier (slot khusus home) + sundul (bump updated_at).
- Developer: paket proyek (halaman + daftar proyek).
- AI value-add: deskripsi SEO otomatis (butuh LLM key user via settings — dilarang hardcode).

## 8. Verifikasi, KPR, Marketing, Analytics
- Verifikasi: agen verified_at manual (ADA); tambah: KTP/NPWP upload + badge; properti verified flag sudah ada (tambah cek admin).
- KPR: kalkulator+pengajuan ADA; tambah: perbandingan bank + status pengajuan.
- Marketing: share WA ADA; BELUM: brosur PDF, og-image per listing (pakai cover), auto-caption.
- Analytics: views ADA; BELUM: klik WA/telp counter, conversion funnel admin.

## 9. Admin/BI (ADA, perluas)
Filament: semua resource + widgets + audit log + settings (payments, landing, storage). KURANG: revenue dashboard (paid orders sum), leads funnel, moderation queue (pending list).

## 10. Prioritas implementasi
P0: status Terjual + filter (sold list), role owner eksplisit, kuota listing per paket, leads pipeline status+reminder, klik WA/telp tracking.
P1: kredit wallet + booster premier/sundul, brosur PDF, verifikasi KTP, perbandingan bank KPR.
P2: AI deskripsi (BYOK), virtual tour embed, peta cluster, multi-bahasa penuh, aplikasi mobile store build.
DILARANG: data palsu, tombol mati, mockup gantikan fungsi, kredensial hardcode.
