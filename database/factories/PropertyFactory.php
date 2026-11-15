<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('id_ID');

        $city = $faker->randomElement([
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Kebayoran Baru', 'lat' => -6.2426, 'lng' => 106.7993],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Timur', 'district' => 'Cipayung', 'lat' => -6.3292, 'lng' => 106.9002],
            ['province' => 'Jawa Barat', 'city' => 'Depok', 'district' => 'Beji', 'lat' => -6.3742, 'lng' => 106.8294],
            ['province' => 'Jawa Barat', 'city' => 'Bekasi', 'district' => 'Bekasi Selatan', 'lat' => -6.2615, 'lng' => 106.9876],
            ['province' => 'Jawa Timur', 'city' => 'Surabaya', 'district' => 'Wonokromo', 'lat' => -7.3031, 'lng' => 112.7341],
            ['province' => 'Jawa Barat', 'city' => 'Bandung', 'district' => 'Coblong', 'lat' => -6.8853, 'lng' => 107.6131],
        ]);

        $type = $faker->randomElement(['rumah', 'apartemen', 'tanah', 'ruko', 'villa']);
        $listingStatus = $faker->randomElement(['jual', 'jual', 'jual', 'sewa']);
        $typeLabel = [
            'rumah' => 'Rumah',
            'apartemen' => 'Apartemen',
            'tanah' => 'Tanah Kavling',
            'ruko' => 'Ruko',
            'villa' => 'Villa',
        ][$type];
        $verb = $listingStatus === 'jual' ? 'Dijual' : 'Disewakan';

        $price = match ($type) {
            'apartemen' => $listingStatus === 'jual' ? $faker->numberBetween(350, 2200) * 1000000 : $faker->numberBetween(25, 80) * 1000000,
            'tanah' => $faker->numberBetween(200, 3000) * 1000000,
            'ruko' => $listingStatus === 'jual' ? $faker->numberBetween(1200, 6000) * 1000000 : $faker->numberBetween(60, 200) * 1000000,
            'villa' => $listingStatus === 'jual' ? $faker->numberBetween(2500, 9000) * 1000000 : $faker->numberBetween(100, 350) * 1000000,
            default => $listingStatus === 'jual' ? $faker->numberBetween(550, 5000) * 1000000 : $faker->numberBetween(30, 120) * 1000000,
        };

        $title = sprintf('%s %s di %s, %s', $typeLabel, $verb, $city['district'], $city['city']);
        $bedrooms = $type === 'tanah' ? null : $faker->numberBetween(1, 5);
        $bathrooms = $type === 'tanah' ? null : $faker->numberBetween(1, 4);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.$faker->unique()->numerify('######'),
            'type' => $type,
            'listing_status' => $listingStatus,
            'price' => $price,
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'land_area' => $type === 'apartemen' ? null : $faker->randomFloat(0, 60, 500),
            'building_area' => in_array($type, ['tanah'], true) ? null : $faker->randomFloat(0, 30, 350),
            'certificate' => $type === 'apartemen'
                ? $faker->randomElement(['HGB', 'Strata Title'])
                : $faker->randomElement(['SHM', 'HGB', 'SHM', 'AJB']),
            'province' => $city['province'],
            'city' => $city['city'],
            'district' => $city['district'],
            'address' => sprintf(
                'Jl. %s No. %d, %s, %s',
                $faker->randomElement(['Kenanga', 'Melati', 'Anggrek', 'Mawar', 'Flamboyan', 'Cendana', 'Mahoni']),
                $faker->numberBetween(1, 120),
                $city['district'],
                $city['city']
            ),
            'lat' => $city['lat'] + ($faker->randomFloat(7, -9000, 9000) / 10000000),
            'lng' => $city['lng'] + ($faker->randomFloat(7, -9000, 9000) / 10000000),
            'description' => implode("\n\n", $faker->paragraphs(4)),
            'featured' => $faker->boolean(15),
            'verified_at' => $faker->optional(0.7)->dateTimeBetween('-6 months', 'now'),
            'views' => $faker->numberBetween(0, 5000),
            'published_at' => $faker->optional(0.9)->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
