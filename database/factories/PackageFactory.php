<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('id_ID')->randomElement(['Gratis', 'Basic', 'Plus', 'Pro']),
            'slug' => fake()->unique()->slug(2),
            'price' => 99000,
            'duration_days' => 30,
            'max_photos' => 5,
            'featured_included' => false,
            'badge' => null,
        ];
    }
}
