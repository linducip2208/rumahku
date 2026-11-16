# LANDING_PAGE_IMPLEMENTATION (2026-11-16)

Arsitektur: `HomeController@index` (featured fill-up 8, latest 8, categories 8 + counts real, collections 5 count>0, cities by count, agents verified+count, articles 6, $landing settings) → `pages/home.blade.php` (10 section, toggle-aware) + header/footer di `layouts/metronic`.

Keputusan jujur:
- ComfyUI offline + 18 job AI image GAGAL total (API key invalid) → foto editorial pakai Picsum seed deterministik (foto real, beda per section, onerror→gradient). Listing properti TETAP gambar lokal (tidak campur).
- Kategori Kost/Gedung/Gudang count 0 → tile "Segera hadir" + link katalog (bukan filter mati).
- $landing via Setting::get loop (getMany repo tanpa group/defaults — API tetap valid).
- Tidak ada: testimoni palsu, count palsu, tombol mati (Privacy/Terms tanpa route → span non-link; Hubungi Kami → #cta-lead + /kontak).
