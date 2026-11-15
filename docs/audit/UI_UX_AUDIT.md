# UI_UX_AUDIT (diperbaiki 2026-11-16)

Layout tunggal Metronic: nav/footer link dibetulkan, auth-aware CTA. Semua list punya empty state; validasi global + @error per-field (login/register/listing). Mobile: grid responsif + sticky bar di detail. Meta/OG: home+show+6 halaman index/pricing + default layout. Error pages 404/403/419/500/503. Link mati: nihil; hardcoded url() → named routes; label "(Stub)" dihapus dari user-facing. Duplikasi kartu properti 6 file → NEXT: ekstrak x-property-card. Tanpa loading skeleton global; tanpa i18n (100% id).
