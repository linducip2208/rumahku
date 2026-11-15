<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');

        return [
            'name' => $faker->company().' '.$faker->randomElement(['Notaris', 'Inspeksi', 'KPR', 'Renovasi']),
            'category' => $faker->randomElement(['notaris', 'inspeksi', 'kpr', 'renovasi']),
            'city' => $faker->randomElement(['Jakarta Selatan', 'Jakarta Timur', 'Bandung', 'Surabaya', 'Bekasi', 'Depok']),
            'phone' => $faker->phoneNumber(),
            'description' => $faker->paragraph(),
        ];
    }
}
