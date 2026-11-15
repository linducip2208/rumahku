<?php

namespace Tests\Feature;

use App\Http\Controllers\Public\AuctionShowController;
use App\Http\Controllers\Public\BidController;
use App\Http\Controllers\Public\ServiceShowController;
use App\Models\Auction;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuctionBidTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Routes didaftarkan di sini agar test mandiri —
        // routes/web.php tetap milik main agent (tidak disentuh).
        // Grup 'web' wajib agar session/$errors/flash tersedia seperti route asli.
        Route::middleware('web')->group(function (): void {
            Route::get('/lelang/{auction}', [AuctionShowController::class, 'show']);
            Route::post('/lelang/{auction}/bid', [BidController::class, 'store'])->middleware('auth');
            Route::get('/jasa/{service}', [ServiceShowController::class, 'show']);
        });
    }

    public function test_valid_bid_menaikkan_current_price(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Auction $auction */
        $auction = Auction::factory()->open()->create();

        $amount = $auction->minimumNextBid();

        $response = $this->actingAs($user)->post("/lelang/{$auction->id}/bid", [
            'amount' => $amount,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('auction_bids', [
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'amount' => $amount,
        ]);

        $this->assertSame($amount, $auction->fresh()->current_price);
    }

    public function test_bid_di_bawah_minimum_ditolak(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Auction $auction */
        $auction = Auction::factory()->open()->create();

        $response = $this->actingAs($user)->post("/lelang/{$auction->id}/bid", [
            'amount' => $auction->current_price,
        ]);

        $response->assertSessionHasErrors('amount');

        $this->assertDatabaseMissing('auction_bids', [
            'auction_id' => $auction->id,
        ]);
    }

    public function test_guest_diarahkan_ke_login(): void
    {
        /** @var Auction $auction */
        $auction = Auction::factory()->open()->create();

        $this->post("/lelang/{$auction->id}/bid", [
            'amount' => $auction->minimumNextBid(),
        ])->assertRedirect(route('login'));
    }

    public function test_lelang_closed_menolak_bid(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Auction $auction */
        $auction = Auction::factory()->create([
            'status' => 'closed',
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->post("/lelang/{$auction->id}/bid", [
            'amount' => $auction->minimumNextBid(),
        ]);

        $response->assertSessionHasErrors('amount');

        $this->assertDatabaseMissing('auction_bids', [
            'auction_id' => $auction->id,
        ]);
    }

    public function test_service_show_page_returns_200(): void
    {
        $service = Service::factory()->create();

        $this->get("/jasa/{$service->id}")->assertStatus(200);
    }

    public function test_auction_show_page_returns_200(): void
    {
        /** @var Auction $auction */
        $auction = Auction::factory()->open()->create();

        $this->get("/lelang/{$auction->id}")->assertStatus(200);
    }
}
