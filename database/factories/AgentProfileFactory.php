<?php

namespace Database\Factories;

use App\Models\AgentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgentProfile>
 */
class AgentProfileFactory extends Factory
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
            'agency' => $faker->randomElement([
                'Ray White Kebayoran',
                'Century 21 BSD',
                'LJ Hooker Depok',
                'Harveys Bekasi',
                'ERA Surabaya',
                'Promex Bandung',
                null,
            ]),
            'phone' => '08'.$faker->numerify('##########'),
            'wa' => '628'.$faker->numerify('#########'),
            'photo' => null,
            'verified_at' => $faker->optional(0.6)->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
