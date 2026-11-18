<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Property;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Fasilitas default per tipe properti.
     *
     * @var array<string, list<string>>
     */
    public const DEFAULTS = [
        'rumah' => ['Carport', 'Taman', 'Keamanan 24 Jam', 'PAM', 'Listrik 2200W'],
        'apartemen' => ['Kolam Renang', 'Gym', 'Lift', 'Keamanan 24 Jam', 'Parkir Basement'],
        'tanah' => ['Akses Jalan', 'Sertifikat Pecah', 'Air Bersih'],
        'ruko' => ['Parkir Luas', 'Rolling Door', 'Toilet', 'Kantor Lantai 2'],
        'villa' => ['Private Pool', 'Taman Tropis', 'Gazebo', 'Keamanan'],
    ];

    public const PROJECT_FACILITIES = ['One Gate System', 'Taman Bermain', 'Masjid', 'Minimarket'];

    /**
     * @var list<array{nama: string, lt: int, lb: int, kt: int, km: int, harga: int}>
     */
    public const PROJECT_UNIT_TYPES = [
        ['nama' => 'Tipe 36', 'lt' => 72, 'lb' => 36, 'kt' => 2, 'km' => 1, 'harga' => 550000000],
        ['nama' => 'Tipe 45', 'lt' => 90, 'lb' => 45, 'kt' => 3, 'km' => 2, 'harga' => 750000000],
    ];

    /**
     * Idempotent: hanya mengisi baris yang masih kosong,
     * sehingga aman dijalankan berulang tanpa menimpa data kustom.
     */
    public function run(): void
    {
        foreach (self::DEFAULTS as $type => $facilities) {
            Property::query()
                ->where('type', $type)
                ->whereNull('facilities')
                ->update(['facilities' => json_encode($facilities)]);
        }

        Project::query()
            ->whereNull('facilities')
            ->update(['facilities' => json_encode(self::PROJECT_FACILITIES)]);

        Project::query()
            ->whereNull('unit_types')
            ->update(['unit_types' => json_encode(self::PROJECT_UNIT_TYPES)]);
    }
}
