<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Models\User;
use Illuminate\Console\Command;

class ExportProperties extends Command
{
    /**
     * @var string
     */
    protected $signature = 'properties:export {--file= : Path file CSV tujuan (default: storage/app/exports/properti-Ymd-His.csv)} {--user= : Hanya ekspor properti milik email pengguna ini}';

    /**
     * @var string
     */
    protected $description = 'Ekspor data properti ke file CSV di storage/app/exports.';

    /**
     * @var list<string>
     */
    public const COLUMNS = [
        'title',
        'slug',
        'type',
        'listing_status',
        'price',
        'bedrooms',
        'bathrooms',
        'land_area',
        'building_area',
        'certificate',
        'province',
        'city',
        'district',
        'address',
        'lat',
        'lng',
        'description',
        'featured',
        'published_at',
    ];

    public function handle(): int
    {
        $query = Property::query()->orderBy('id');

        if ($email = $this->option('user')) {
            $user = User::where('email', $email)->first();
            if ($user === null) {
                $this->error("Pengguna dengan email [{$email}] tidak ditemukan.");

                return self::FAILURE;
            }

            $query->where('user_id', $user->id);
        }

        /** @var string $path */
        $path = $this->option('file') ?: storage_path('app/exports/properti-'.date('Ymd-His').'.csv');

        $directory = dirname($path);
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            $this->error("Gagal membuat direktori: {$directory}");

            return self::FAILURE;
        }

        $handle = fopen($path, 'w');
        if ($handle === false) {
            $this->error("Gagal menulis file: {$path}");

            return self::FAILURE;
        }

        fputcsv($handle, self::COLUMNS);

        $rows = 0;
        foreach ($query->get() as $property) {
            fputcsv($handle, [
                $property->title,
                $property->slug,
                $property->type,
                $property->listing_status,
                $property->price,
                $property->bedrooms,
                $property->bathrooms,
                $property->land_area,
                $property->building_area,
                $property->certificate,
                $property->province,
                $property->city,
                $property->district,
                $property->address,
                $property->lat,
                $property->lng,
                $property->description,
                $property->featured ? '1' : '0',
                $property->published_at?->format('Y-m-d H:i:s'),
            ]);
            $rows++;
        }

        fclose($handle);

        $this->info("Ekspor selesai: {$rows} baris properti ditulis ke {$path}");

        return self::SUCCESS;
    }
}
