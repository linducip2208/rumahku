<?php

namespace Database\Factories;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuctionBid>
 */
class AuctionBidFactory extends Factory
{
    public function definition(): array
    {
        return [
            'auction_id' => Auction::factory(),
            'user_id' => User::factory(),
            'amount' => fake()->numberBetween(350, 5500) * 1000000,
        ];
    }
}
