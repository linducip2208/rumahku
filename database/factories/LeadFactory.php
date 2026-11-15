<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
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
            'property_id' => Property::factory(),
            'agent_user_id' => User::factory(),
            'name' => $faker->name(),
            'phone' => '08'.$faker->numerify('##########'),
            'message' => $faker->optional()->sentence(12),
            'source' => $faker->randomElement(['web', 'web', 'web', 'whatsapp', 'telepon']),
        ];
    }
}
