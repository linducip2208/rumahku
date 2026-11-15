<?php

namespace Database\Factories;

use App\Models\Auction;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Auction>
 */
class AuctionFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->numberBetween(300, 5000) * 1000000;

        return [
            'property_id' => Property::factory(),
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(fake()->numberBetween(3, 30)),
            'start_price' => $start,
            'current_price' => $start + (fake()->numberBetween(0, 20) * 1000000),
            'status' => fake()->randomElement(['open', 'open', 'open', 'draft', 'closed']),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'open',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(7),
        ]);
    }
}
