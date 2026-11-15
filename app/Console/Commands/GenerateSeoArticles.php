<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Property;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GenerateSeoArticles extends Command
{
    /**
     * @var string
     */
    protected $signature = 'seo:articles {--listing= : Batasi ke jual|sewa} {--fresh : Hapus artikel SEO terkait lalu buat ulang}';

    /**
     * @var string
     */
    protected $description = 'Generate/update artikel SEO idempoten per kombinasi listing × tipe × kota (data-driven dari DB).';

    /**
     * @var list<string>
     */
    private const TYPES = ['rumah', 'apartemen', 'tanah', 'ruko', 'villa'];

    /**
     * @var list<string>
     */
    private const LISTINGS = ['jual', 'sewa'];

    public function handle(): int
    {
        $listingFilter = $this->option('listing') ? strtolower((string) $this->option('listing')) : null;

        if ($listingFilter !== null && ! in_array($listingFilter, self::LISTINGS, true)) {
            $this->error('Opsi --listing harus jual|sewa.');

            return self::FAILURE;
        }

        $listings = $listingFilter !== null ? [$listingFilter] : self::LISTINGS;

        /** @var Collection<int, string> $cities */
        $cities = Property::query()
            ->whereNotNull('published_at')
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city')
            ->map(fn ($city) => trim((string) $city))
            ->reject(fn ($city) => $city === '')
            ->unique()
            ->values();

        if ($cities->isEmpty()) {
            $this->warn('Tidak ada kota dengan properti published. Tidak ada artikel dibuat.');

            return self::SUCCESS;
        }

        $created = 0;
        $updated = 0;

        foreach ($listings as $listing) {
            foreach (self::TYPES as $type) {
                foreach ($cities as $cityName) {
                    $citySlug = Str::slug($cityName);
                    $slug = "{$listing}-{$type}-{$citySlug}";

                    if ($this->option('fresh')) {
                        Article::query()->where('slug', $slug)->delete();
                    }

                    $stats = Property::query()
                        ->whereNotNull('published_at')
                        ->where('type', $type)
                        ->where('listing_status', $listing)
                        ->where('city', $cityName)
                        ->selectRaw('COUNT(*) as total, MIN(price) as min_price, MAX(price) as max_price, AVG(price) as avg_price')
                        ->first();

                    $total = (int) ($stats->total ?? 0);
                    $min = $stats->min_price !== null ? (int) $stats->min_price : null;
                    $max = $stats->max_price !== null ? (int) $stats->max_price : null;
                    $avg = $stats->avg_price !== null ? (int) round((float) $stats->avg_price) : null;

                    $listingLabel = $listing === 'jual' ? 'Jual' : 'Sewa';
                    $typeLabel = ucfirst($type);
                    $title = "{$listingLabel} {$typeLabel} di {$cityName} — Pilihan Terbaru";

                    $body = $this->buildBody($listing, $type, $listingLabel, $typeLabel, $cityName, $citySlug, $total, $min, $max, $avg);

                    $article = Article::query()->where('slug', $slug)->first();

                    Article::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'title' => $title,
                            'body' => $body,
                            'city' => $cityName,
                            'type' => $type,
                        ]
                    );

                    if ($article === null) {
                        $created++;
                    } else {
                        $updated++;
                    }
                }
            }
        }

        $this->info("SEO articles selesai: {$created} dibuat, {$updated} diperbarui ({$cities->count()} kota).");

        return self::SUCCESS;
    }

    private function buildBody(
        string $listing,
        string $type,
        string $listingLabel,
        string $typeLabel,
        string $cityName,
        string $citySlug,
        int $total,
        ?int $min,
        ?int $max,
        ?int $avg
    ): string {
        $verb = $listing === 'jual' ? 'dibeli' : 'disewa';
        $fmt = fn (?int $value): string => $value === null ? 'belum tersedia' : 'Rp '.number_format($value, 0, ',', '.');

        $priceLine = $total > 0
            ? "Saat ini ada {$total} {$typeLabel} {$listing} di {$cityName} yang tayang di Rumah123, "
                ."dengan harga terendah {$this->escape($fmt($min))}, tertinggi {$this->escape($fmt($max))}, "
                ."dan rata-rata {$this->escape($fmt($avg))}."
            : "Saat ini belum ada {$typeLabel} {$listing} di {$cityName} yang tayang. "
                .'Angka harga di bawah akan terisi otomatis begitu ada listing published baru.';

        $otherLinks = [];
        foreach (self::TYPES as $otherType) {
            if ($otherType === $type) {
                continue;
            }
            $otherLinks[] = '- '.route('seo.landing', ['listing' => $listing, 'type' => $otherType, 'city' => $citySlug])
                ." ({$listingLabel} ".ucfirst($otherType)." di {$cityName})";
            if (count($otherLinks) >= 3) {
                break;
            }
        }
        $otherLinks[] = '- '.route('properties.index').' (Semua properti)';
        $linksBlock = implode("\n", $otherLinks);

        return <<<BODY
{$listingLabel} {$typeLabel} di {$cityName} — Pilihan Terbaru

Sedang cari {$typeLabel} untuk {$listing} di {$cityName}? Halaman ini merangkum pilihan {$typeLabel} yang {$verb} di {$cityName} berdasarkan data listing published terbaru di Rumah123.

{$priceLine}

## Ringkasan harga {$typeLabel} {$listing} di {$cityName}

- Total listing tayang: {$total} unit
- Harga terendah: {$fmt($min)}
- Harga tertinggi: {$fmt($max)}
- Harga rata-rata: {$fmt($avg)}

Catatan: angka di atas dihitung langsung dari database listing published (tipe "{$type}", status "{$listing}", kota "{$cityName}"). Jika total masih 0, cek kembali nanti karena data diperbarui mingguan.

## Tips memilih {$typeLabel} di {$cityName}

1. Tentukan bujet mengacu harga rata-rata {$fmt($avg)} agar negosiasi realistis.
2. Bandingkan minimal 3 listing {$type} {$listing} di {$cityName} sebelum survei.
3. Cek legalitas (SHM/HGB/AJB) dan akses jalan sebelum tanda jadi.
4. Untuk {$listing} {$typeLabel}, prioritaskan listing dengan foto lengkap dan agen terverifikasi.

## FAQ

**Q: Berapa harga {$typeLabel} {$listing} di {$cityName}?**
A: Berdasarkan data saat ini: terendah {$fmt($min)}, tertinggi {$fmt($max)}, rata-rata {$fmt($avg)} dari {$total} listing tayang.

**Q: Di mana lihat daftar {$typeLabel} {$listing} di {$cityName}?**
A: Lihat halaman landing {$listingLabel} {$typeLabel} di {$cityName} dan gunakan filter harga serta foto.

**Q: Apakah data ini terbaru?**
A: Ya, artikel ini dibuat otomatis (perintah seo:articles) dari query database listing published dan dijadwalkan diperbarui mingguan.

## Tautan terkait

{$linksBlock}
BODY;
    }

    private function escape(string $value): string
    {
        return $value;
    }
}
