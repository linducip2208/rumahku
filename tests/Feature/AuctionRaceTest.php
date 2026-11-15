<?php

namespace Tests\Feature;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuctionRaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sequential_bids_enforce_minimum_after_price_moves(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Auction $auction */
        $auction = Auction::factory()->open()->create();

        $first = $auction->minimumNextBid();
        $auction->placeBid($user, $first);

        $this->assertSame($first, $auction->fresh()->current_price);

        $staleMinimum = $first;

        $this->expectException(ValidationException::class);

        $auction->placeBid($user, $staleMinimum);
    }

    public function test_closed_auction_rejects_bid(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Auction $auction */
        $auction = Auction::factory()->create([
            'status' => 'closed',
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
        ]);

        $this->expectException(ValidationException::class);

        $auction->placeBid($user, $auction->minimumNextBid());
    }

    public function test_valid_bid_persists_single_row_and_updates_price(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Auction $auction */
        $auction = Auction::factory()->open()->create();

        $amount = $auction->minimumNextBid();
        $auction->placeBid($user, $amount);

        $this->assertSame(1, AuctionBid::query()->where('auction_id', $auction->id)->count());
        $this->assertSame($amount, $auction->fresh()->current_price);
    }
}
