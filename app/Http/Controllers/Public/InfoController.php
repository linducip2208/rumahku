<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class InfoController extends Controller
{
    /**
     * @var array<string, array{title: string, intro: string, sections: array<int, array{heading: string, body: string}>}>
     */
    private const PAGES = [
        'tentang' => [
            'title' => 'Tentang Kami',
            'intro' => 'Rumah123 adalah portal properti Indonesia yang membantu Anda menemukan rumah, apartemen, tanah, ruko, dan villa dijual maupun disewa, lengkap dengan proyek baru, agen terverifikasi, dan simulasi KPR.',
            'sections' => [
                [
                    'heading' => 'Apa yang kami lakukan',
                    'body' => 'Kami menghimpun listing properti dari agen dan pengembang di berbagai kota — termasuk Jakarta Selatan, Depok, Bekasi, Bandung, dan Surabaya — agar Anda bisa mencari, membandingkan, dan menghubungi pemasang iklan dari satu tempat.',
                ],
                [
                    'heading' => 'Layanan kami',
                    'body' => 'Selain pencarian properti, tersedia direktori agen terverifikasi, artikel panduan harga dan tips membeli, proyek hunian baru, lelang, jasa properti, serta simulasi KPR untuk memperkirakan cicilan sebelum mengajukan pembiayaan ke bank.',
                ],
                [
                    'heading' => 'Hubungi kami',
                    'body' => 'Ada pertanyaan, masukan, atau kendala? Sampaikan melalui halaman kontak dan tim kami akan menindaklanjutinya pada jam kerja.',
                ],
            ],
        ],
        'bantuan' => [
            'title' => 'Pusat Bantuan',
            'intro' => 'Jawaban atas pertanyaan yang paling sering diajukan seputar penggunaan Rumah123.',
            'sections' => [
                [
                    'heading' => 'Mencari properti',
                    'body' => 'Gunakan kolom pencarian di beranda atau halaman properti untuk memfilter berdasarkan kata kunci, tipe, status jual/sewa, kota, rentang harga, dan jumlah kamar. Setiap kartu listing menampilkan foto, harga, dan lokasi ringkas.',
                ],
                [
                    'heading' => 'Menghubungi agen',
                    'body' => 'Buka halaman detail properti, lalu isi formulir kontak dengan nama, nomor telepon, dan pesan Anda. Agen pemasang akan menerima dan menindaklanjuti pesan tersebut.',
                ],
                [
                    'heading' => 'Memasang iklan',
                    'body' => 'Daftar sebagai agen, masuk ke dasbor, lalu buat listing baru melalui menu listing. Cantumkan foto yang jelas, harga yang wajar, dan deskripsi lengkap agar listing cepat dilirik calon pembeli.',
                ],
                [
                    'heading' => 'Masih butuh bantuan?',
                    'body' => 'Jika panduan di atas belum menyelesaikan masalah Anda, kirimkan pesan melalui halaman kontak dengan menjelaskan kendala selengkap mungkin.',
                ],
            ],
        ],
        'privasi' => [
            'title' => 'Kebijakan Privasi',
            'intro' => 'Penjelasan umum mengenai data yang kami kumpulkan dan cara kami menggunakannya.',
            'sections' => [
                [
                    'heading' => 'Data yang dikumpulkan',
                    'body' => 'Data akun (nama, email, kata sandi terenkripsi), data listing properti yang Anda pasang, pesan kontak yang Anda kirimkan, serta data teknis dasar seperti alamat IP dan aktivitas penggunaan untuk keamanan dan peningkatan layanan.',
                ],
                [
                    'heading' => 'Penggunaan data',
                    'body' => 'Data digunakan untuk mengoperasikan layanan (menampilkan listing, meneruskan pesan ke agen), menjaga keamanan akun, serta menyusun statistik agregat. Kami tidak menjual data pribadi Anda kepada pihak ketiga.',
                ],
                [
                    'heading' => 'Penyimpanan dan keamanan',
                    'body' => 'Data disimpan pada infrastruktur yang kami kelola dengan kontrol akses terbatas. Meski demikian, tidak ada sistem yang sepenuhnya kebal risiko; gunakan kata sandi yang kuat dan jaga kerahasiaan akun Anda.',
                ],
                [
                    'heading' => 'Hak Anda',
                    'body' => 'Anda dapat meminta pembaruan atau penghapusan data pribadi Anda dengan menghubungi kami melalui halaman kontak.',
                ],
            ],
        ],
        'syarat' => [
            'title' => 'Syarat & Ketentuan',
            'intro' => 'Aturan umum penggunaan layanan Rumah123.',
            'sections' => [
                [
                    'heading' => 'Penggunaan layanan',
                    'body' => 'Layanan ini disediakan untuk membantu pencarian dan pemasaran properti. Anda setuju untuk menggunakannya secara patut, tidak melanggar hukum, dan tidak mengganggu ketersediaan layanan bagi pengguna lain.',
                ],
                [
                    'heading' => 'Akurasi listing',
                    'body' => 'Informasi properti (harga, ketersediaan, spesifikasi) diisi oleh pemasang iklan. Kami berupaya menjaga kualitas data, namun tidak menjamin seluruh informasi selalu akurat dan terkini. Verifikasi kembali detail penting langsung dengan pemasang sebelum bertransaksi.',
                ],
                [
                    'heading' => 'Akun agen',
                    'body' => 'Pemasang iklan bertanggung jawab atas kebenaran konten listingnya. Kami dapat menonaktifkan listing atau akun yang terbukti menyesatkan, duplikat, atau melanggar ketentuan.',
                ],
                [
                    'heading' => 'Perubahan ketentuan',
                    'body' => 'Ketentuan ini dapat diperbarui sewaktu-waktu mengikuti perkembangan layanan. Penggunaan berkelanjutan atas layanan ini dianggap sebagai persetujuan terhadap ketentuan terbaru.',
                ],
            ],
        ],
    ];

    public function show(string $page): View
    {
        abort_unless(array_key_exists($page, self::PAGES), 404);

        $info = self::PAGES[$page];

        return view('pages.info', [
            'page' => $page,
            'title' => $info['title'],
            'intro' => $info['intro'],
            'sections' => $info['sections'],
        ]);
    }
}
