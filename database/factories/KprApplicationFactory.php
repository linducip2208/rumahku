<?php

namespace Database\Factories;

use App\Models\KprApplication;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KprApplication>
 */
class KprApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('id_ID');

        return [
            'user_id' => User::factory(),
            'property_id' => Property::factory(),
            'name' => $faker->name(),
            'phone' => '08'.$faker->numerify('##########'),
            'email' => $faker->optional()->safeEmail(),
            'dp_amount' => $faker->numberBetween(50, 500) * 1000000,
            'tenor_months' => $faker->randomElement(KprApplication::TENORS),
            'bank_preference' => $faker->optional()->randomElement(KprApplication::BANKS),
            'monthly_income' => $faker->optional()->numberBetween(5, 100) * 1000000,
            'status' => 'pending',
            'notes' => $faker->optional()->sentence(10),
        ];
    }
}
