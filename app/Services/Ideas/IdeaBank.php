<?php

namespace App\Services\Ideas;

/**
 * Bank ide pengembangan: matriks morfologis 10 audiens x 10 kebutuhan x 10 mekanisme
 * = tepat 1.000 ide unik per fitur, deterministik (hash-based, tanpa AI/eksternal).
 */
class IdeaBank
{
    public const PER_FEATURE = 1000;

    /** @var array<string, string> key => label Indonesia */
    public const FEATURES = [
        'home' => 'Beranda & Pencarian Utama',
        'listing' => 'Daftar Listing Properti',
        'search' => 'Mesin Pencari (Scout/Meilisearch)',
        'detail' => 'Halaman Detail Properti',
        'leads' => 'Kontak & Leads Agen',
        'auth' => 'Registrasi & Login',
        'agent-dashboard' => 'Dasbor Agen',
        'favorites' => 'Favorit Tersimpan',
        'compare' => 'Bandingkan Properti',
        'auctions' => 'Lelang Properti',
        'projects' => 'Proyek Developer Baru',
        'services' => 'Direktori Jasa Properti',
        'articles' => 'Artikel & pSEO',
        'kpr' => 'Kalkulator & Pengajuan KPR',
        'packages' => 'Paket Iklan & Featured',
        'payments' => 'Pembayaran & Gateway',
        'notifications' => 'Notifikasi Email & WA',
        'sitemap' => 'Sitemap, Robots & SEO Teknis',
        'admin' => 'Panel Admin Filament',
        'api' => 'REST API & Aplikasi Flutter',
    ];

    public const AUDIENCES = [
        'pencari rumah pertama',
        'keluarga muda',
        'investor kos-kontrakan',
        'agen properti',
        'developer perumahan',
        'admin moderator',
        'pemilik properti',
        'ekspatriat pendatang',
        'pasangan baru menikah',
        'pembeli dengan KPR',
    ];

    public const NEEDS = [
        'kepastian harga yang wajar',
        'kecepatan membandingkan opsi',
        'kepercayaan pada agen penjual',
        'pengajuan KPR yang mudah',
        'pantauan lelang real-time',
        'akses jasa tepercaya',
        'informasi yang valid dan segar',
        'pengelolaan listing yang efisien',
        'konversi leads yang tinggi',
        'pengalaman mobile yang mulus',
    ];

    public const MECHANISMS = [
        'AI rekomendasi',
        'peta interaktif GIS',
        'video dan tur virtual 360',
        'chat real-time',
        'skoring transparansi',
        'gamifikasi poin',
        'notifikasi pintar',
        'kalkulator interaktif',
        'verifikasi digital KYC',
        'integrasi WhatsApp',
    ];

    public const EXAMPLES = [
        'mulai dari daftar terpopuler, lalu personalisasi per perilaku browsing pengguna.',
        'tampilkan sebagai badge dan filter agar mudah ditemukan dari halaman utama.',
        'kirim ringkasan mingguan otomatis agar pengguna kembali tanpa usaha.',
        'jadikan widget yang bisa disematkan di halaman detail dan dasbor agen.',
        'ukur dengan metrik konversi sebelum dan sesudah rilis selama 30 hari.',
    ];

    /**
     * @return array<int, array<string, mixed>> tepat 1.000 baris untuk $featureKey
     */
    public static function generate(string $featureKey): array
    {
        $label = self::FEATURES[$featureKey] ?? $featureKey;
        $rows = [];
        $slot = 0;

        foreach (self::AUDIENCES as $audience) {
            foreach (self::NEEDS as $need) {
                foreach (self::MECHANISMS as $mechanism) {
                    $h = crc32($featureKey.'|'.$audience.'|'.$need.'|'.$mechanism);
                    $rows[] = [
                        'feature_key' => $featureKey,
                        'slot' => $slot++,
                        'title' => $mechanism.' untuk '.$audience.' — '.$need,
                        'description' => 'Ide pengembangan '.$label.': manfaatkan '.mb_strtolower($mechanism)
                            .' agar '.$audience.' memperoleh '.$need.'. '
                            .self::EXAMPLES[$h % count(self::EXAMPLES)],
                        'audience' => $audience,
                        'need' => $need,
                        'mechanism' => $mechanism,
                        'impact' => ['low', 'medium', 'high'][($h >> 3) % 3],
                        'effort' => ['low', 'medium', 'high'][($h >> 5) % 3],
                        'status' => 'backlog',
                    ];
                }
            }
        }

        return $rows;
    }

    /** @return list<string> */
    public static function keys(): array
    {
        return array_keys(self::FEATURES);
    }
}
