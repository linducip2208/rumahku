<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Gratis',
                'slug' => 'gratis',
                'price' => 0,
                'duration_days' => 14,
                'max_photos' => 3,
                'max_listings' => 5,
                'featured_included' => false,
                'badge' => null,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price' => 99000,
                'duration_days' => 30,
                'max_photos' => 10,
                'max_listings' => 20,
                'featured_included' => false,
                'badge' => null,
            ],
            [
                'name' => 'Plus',
                'slug' => 'plus',
                'price' => 249000,
                'duration_days' => 30,
                'max_photos' => 20,
                'max_listings' => 50,
                'featured_included' => true,
                'badge' => 'Populer',
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 499000,
                'duration_days' => 60,
                'max_photos' => 40,
                'max_listings' => null,
                'featured_included' => true,
                'badge' => 'Terbaik',
            ],
        ];

        foreach ($packages as $package) {
            Package::query()->updateOrCreate(
                ['slug' => $package['slug']],
                $package
            );
        }
    }
}
