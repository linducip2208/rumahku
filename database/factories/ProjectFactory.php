<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('id_ID');
        $city = $faker->randomElement(['Jakarta Selatan', 'Depok', 'Bekasi', 'Surabaya', 'Bandung', 'Tangerang Selatan']);
        $name = sprintf(
            '%s %s %s',
            $faker->randomElement(['Citra', 'Griya', 'Pesona', 'Taman', 'Green', 'Grand']),
            $faker->randomElement(['Asri', 'Indah', 'Lestari', 'Residence', 'Village', 'Park']),
            $city
        );

        return [
            'developer_user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.$faker->unique()->numerify('####'),
            'city' => $city,
            'price_from' => $faker->numberBetween(400, 4000) * 1000000,
            'description' => implode("\n\n", $faker->paragraphs(3)),
        ];
    }
}
