<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportProperties extends Command
{
    /**
     * @var string
     */
    protected $signature = 'properties:import {file : Path file CSV sumber} {--user= : Email pemilik properti (wajib)} {--dry-run : Validasi saja tanpa menyimpan}';

    /**
     * @var string
     */
    protected $description = 'Impor data properti dari file CSV (idempoten berdasarkan slug).';

    /**
     * @var list<string>
     */
    private const TYPES = ['rumah', 'apartemen', 'tanah', 'ruko', 'villa'];

    /**
     * @var list<string>
     */
    private const LISTING_STATUSES = ['jual', 'sewa'];

    public function handle(): int
    {
        $email = $this->option('user');
        if (! is_string($email) || trim($email) === '') {
            $this->error('Opsi --user=email wajib diisi sebagai pemilik properti.');

            return self::FAILURE;
        }

        $owner = User::where('email', trim($email))->first();
        if ($owner === null) {
            $this->error("Pengguna dengan email [{$email}] tidak ditemukan.");

            return self::FAILURE;
        }

        /** @var string $file */
        $file = $this->argument('file');
        if (! is_file($file) || ! is_readable($file)) {
            $this->error("File tidak ditemukan atau tidak bisa dibaca: {$file}");

            return self::FAILURE;
        }

        $handle = fopen($file, 'r');
        if ($handle === false) {
            $this->error("Gagal membuka file: {$file}");

            return self::FAILURE;
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            $this->error("File CSV kosong: {$file}");

            return self::FAILURE;
        }

        $header = array_map(fn ($column): string => trim((string) $column), $header);
        foreach (['title', 'type', 'listing_status', 'price'] as $required) {
            if (! in_array($required, $header, true)) {
                fclose($handle);
                $this->error("Header CSV wajib memuat kolom [{$required}].");

                return self::FAILURE;
            }
        }

        $dryRun = (bool) $this->option('dry-run');
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $total = 0;

        /** @var list<string> $errors */
        $errors = [];

        /** @var array<string, true> $slugsSeenInFile */
        $slugsSeenInFile = [];

        $line = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            if ($row === [null] || $row === []) {
                continue;
            }

            $total++;
            /** @var array<string, string> $data */
            $data = [];
            foreach ($header as $index => $column) {
                $data[$column] = trim((string) ($row[$index] ?? ''));
            }

            $validationError = $this->validateRow($data);
            if ($validationError !== null) {
                $skipped++;
                $errors[] = "Baris {$line}: {$validationError}";

                continue;
            }

            /** @var string $slug */
            $slug = $data['slug'] !== '' ? Str::slug($data['slug']) : Str::slug($data['title']);
            if ($slug === '') {
                $slug = 'properti';
            }

            // Duplikat slug di dalam file yang sama diberi akhiran deterministik
            // agar impor ulang file yang sama tetap idempoten.
            $base = $slug;
            $suffix = 2;
            while (isset($slugsSeenInFile[$slug])) {
                $slug = "{$base}-{$suffix}";
                $suffix++;
            }
            $slugsSeenInFile[$slug] = true;

            $attributes = [
                'user_id' => $owner->id,
                'title' => $data['title'],
                'slug' => $slug,
                'type' => strtolower($data['type']),
                'listing_status' => strtolower($data['listing_status']),
                'price' => (int) $data['price'],
                'bedrooms' => $data['bedrooms'] !== '' ? (int) $data['bedrooms'] : null,
                'bathrooms' => $data['bathrooms'] !== '' ? (int) $data['bathrooms'] : null,
                'land_area' => $data['land_area'] !== '' ? (float) $data['land_area'] : null,
                'building_area' => $data['building_area'] !== '' ? (float) $data['building_area'] : null,
                'certificate' => $data['certificate'] !== '' ? $data['certificate'] : null,
                'province' => $data['province'] !== '' ? $data['province'] : null,
                'city' => $data['city'] !== '' ? $data['city'] : null,
                'district' => $data['district'] !== '' ? $data['district'] : null,
                'address' => $data['address'] !== '' ? $data['address'] : null,
                'lat' => $data['lat'] !== '' ? $data['lat'] : null,
                'lng' => $data['lng'] !== '' ? $data['lng'] : null,
                'description' => $data['description'] !== '' ? $data['description'] : null,
                'featured' => in_array(strtolower($data['featured']), ['1', 'true', 'yes', 'y', 'ya'], true),
                'published_at' => $data['published_at'] !== '' ? $data['published_at'] : null,
            ];

            if ($dryRun) {
                continue;
            }

            $property = Property::updateOrCreate(['slug' => $slug], $attributes);
            if ($property->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        fclose($handle);

        if ($dryRun) {
            if ($errors !== []) {
                $this->error("Dry-run: {$total} baris diperiksa, {$skipped} baris invalid:");
                foreach ($errors as $error) {
                    $this->error("  - {$error}");
                }

                return self::FAILURE;
            }

            $this->info("Dry-run valid: {$total} baris siap diimpor, tidak ada data yang disimpan.");

            return self::SUCCESS;
        }

        $this->info("Impor selesai: {$created} baru, {$updated} diperbarui, {$skipped} dilewati (dari {$total} baris).");
        foreach ($errors as $error) {
            $this->warn("  - {$error}");
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, string>  $data
     */
    private function validateRow(array $data): ?string
    {
        if (($data['title'] ?? '') === '') {
            return 'title wajib diisi.';
        }

        if (! in_array(strtolower($data['type'] ?? ''), self::TYPES, true)) {
            return "type [{$data['type']}] tidak valid (rumah,apartemen,tanah,ruko,villa).";
        }

        if (! in_array(strtolower($data['listing_status'] ?? ''), self::LISTING_STATUSES, true)) {
            return "listing_status [{$data['listing_status']}] tidak valid (jual,sewa).";
        }

        if (($data['price'] ?? '') === '' || ! is_numeric($data['price']) || (float) $data['price'] <= 0) {
            return "price [{$data['price']}] harus angka lebih dari 0.";
        }

        foreach (['bedrooms', 'bathrooms'] as $field) {
            if (($data[$field] ?? '') !== '' && ! ctype_digit($data[$field])) {
                return "{$field} [{$data[$field]}] harus bilangan bulat >= 0.";
            }
        }

        foreach (['land_area', 'building_area', 'lat', 'lng'] as $field) {
            if (($data[$field] ?? '') !== '' && ! is_numeric($data[$field])) {
                return "{$field} [{$data[$field]}] harus angka.";
            }
        }

        if (($data['featured'] ?? '') !== '' && ! in_array(strtolower($data['featured']), ['0', '1', 'true', 'false', 'yes', 'no', 'y', 'n', 'ya', 'tidak'], true)) {
            return "featured [{$data['featured']}] tidak valid (0/1/true/false).";
        }

        if (($data['published_at'] ?? '') !== '') {
            try {
                Carbon::parse($data['published_at']);
            } catch (\Throwable) {
                return "published_at [{$data['published_at']}] bukan tanggal valid.";
            }
        }

        return null;
    }
}
