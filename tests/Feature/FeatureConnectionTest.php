<?php

namespace Tests\Feature;

use App\Mail\LeadReceived;
use App\Models\AgentProfile;
use App\Models\Auction;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Property;
use App\Models\PropertyOrder;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Verifikasi KONEKSI antar fitur (bukan fitur satuan):
 * properti↔agen↔lead↔notifikasi, listing↔order↔payment↔featured,
 * lelang↔bid, dan konsistensi web↔API.
 */
class FeatureConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_journey_links_property_agent_lead_and_notification(): void
    {
        Mail::fake();

        $agent = User::factory()->create(['role' => 'agent']);
        AgentProfile::factory()->create([
            'user_id' => $agent->id,
            'verified_at' => now(),
            'phone' => '081234567890',
        ]);
        $property = Property::factory()->create([
            'user_id' => $agent->id,
            'published_at' => now(),
            'sold_at' => null,
        ]);

        // 1. Properti agen tayang di katalog publik + API dengan data sama.
        $this->get('/properti')->assertOk()->assertSee($property->title);
        $this->getJson('/api/v1/properties/'.$property->slug)
            ->assertOk()
            ->assertJsonPath('data.title', $property->title);

        // 2. Guest kirim lead → terikat ke properti DAN agen pemiliknya.
        $this->post(route('leads.store'), [
            'property_id' => $property->id,
            'name' => 'Budi',
            'phone' => '08123456789',
            'message' => 'Tertarik.',
        ])->assertStatus(302);

        $lead = Lead::query()->where('phone', '08123456789')->firstOrFail();
        $this->assertSame($property->id, (int) $lead->property_id);
        $this->assertSame($agent->id, (int) $lead->agent_user_id);

        // 3. Notifikasi terkirim ke email agen (bukan nyasar).
        Mail::assertSent(LeadReceived::class, function (LeadReceived $mail) use ($agent): bool {
            return $mail->hasTo($agent->email);
        });
    }

    public function test_monetization_chain_order_payment_and_featured(): void
    {
        Setting::set('payments.default_driver', 'manual', 'payments');
        Setting::set('payments.bank_accounts', 'BCA 1 a.n. X', 'payments');

        $agent = User::factory()->create(['role' => 'agent']);
        $property = Property::factory()->create([
            'user_id' => $agent->id,
            'published_at' => now(),
            'sold_at' => null,
            'featured' => false,
        ]);
        $package = Package::query()->firstOrCreate(
            ['slug' => 'plus'],
            ['name' => 'Plus', 'price' => 250000, 'duration_days' => 30, 'max_photos' => 10, 'featured_included' => true]
        );

        // 1. Order nominal = harga paket (snapshot, bukan input user).
        $this->actingAs($agent)->post('/orders', [
            'property_id' => $property->id,
            'package_slug' => $package->slug,
        ])->assertStatus(302);

        $order = PropertyOrder::query()->where('property_id', $property->id)->firstOrFail();
        $this->assertSame($package->price, (int) $order->amount);
        $this->assertSame('pending', $order->status);

        // 2. Bayar manual → instruksi + reference tercatat.
        $this->actingAs($agent)->get('/pay/'.$order->id)->assertOk();
        $order->refresh();
        $this->assertNotNull($order->payment_reference);

        // 3. Callback lunas → order paid + properti featured (paket Plus).
        $this->postJson('/pay/callback/manual', [
            'reference_id' => $order->payment_reference,
            'status' => 'paid',
        ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertTrue($property->fresh()->featured);
    }

    public function test_auction_chain_bid_updates_current_price(): void
    {
        $owner = User::factory()->create();
        $bidder = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $owner->id]);
        $auction = Auction::factory()->open()->create(['property_id' => $property->id]);

        $minimum = $auction->minimumNextBid();

        // Bid di bawah minimum ditolak, harga tidak berubah.
        $this->actingAs($bidder)->post("/lelang/{$auction->id}/bid", ['amount' => $minimum - 1])
            ->assertSessionHasErrors('amount');
        $this->assertSame($auction->current_price, $auction->fresh()->current_price);

        // Bid sah → current_price ikut naik + riwayat tampil di detail.
        $this->actingAs($bidder)->post("/lelang/{$auction->id}/bid", ['amount' => $minimum])
            ->assertStatus(302);
        $this->assertSame($minimum, (int) $auction->fresh()->current_price);
        $this->get("/lelang/{$auction->id}")->assertOk()->assertSee(number_format($minimum, 0, ',', '.'));
    }

    public function test_sold_property_disappears_from_all_channels_consistently(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create([
            'user_id' => $user->id,
            'published_at' => now(),
            'sold_at' => null,
        ]);

        $this->get('/')->assertSee($property->title);
        $this->getJson('/api/v1/properties/'.$property->slug)->assertOk();

        $property->update(['sold_at' => now()]);

        // Semua kanal konsisten: hilang dari home, katalog, sitemap, API.
        $this->get('/')->assertDontSee($property->title);
        $this->get('/properti')->assertDontSee($property->title);
        $this->get('/sitemap.xml')->assertDontSee($property->slug);
        $this->getJson('/api/v1/properties/'.$property->slug)->assertNotFound();
        $this->get('/properti/'.$property->slug)->assertNotFound();
    }

    public function test_social_chain_favorite_compare_and_track(): void
    {
        $owner = User::factory()->create(['role' => 'agent']);
        AgentProfile::factory()->create([
            'user_id' => $owner->id,
            'verified_at' => now(),
            'phone' => '081234567890',
            'wa' => '081234567890',
        ]);
        $property = Property::factory()->create([
            'user_id' => $owner->id,
            'published_at' => now(),
            'sold_at' => null,
        ]);
        $buyer = User::factory()->create();

        // Favorit: toggle on → tercatat milik user yang benar.
        $this->actingAs($buyer)->post('/favorit/'.$property->id)->assertStatus(302);
        $this->assertDatabaseHas('favorites', ['user_id' => $buyer->id, 'property_id' => $property->id]);

        // Bandingkan: tambah → muncul di halaman compare.
        $this->actingAs($buyer)->post('/bandingkan/'.$property->id)->assertStatus(302);
        $this->get('/bandingkan?ids='.$property->id)->assertOk()->assertSee($property->title);

        // Track klik WA agen → redirect + counter naik, nomor milik agen.
        $response = $this->get("/t/{$property->id}/wa");
        $response->assertRedirect();
        $this->assertStringContainsString('6281234567890', (string) $response->headers->get('Location'));
        $this->assertSame(1, (int) $property->fresh()->wa_clicks);
    }

    public function test_financing_chain_kpr_linked_to_property(): void
    {
        $owner = User::factory()->create();
        $property = Property::factory()->create([
            'user_id' => $owner->id,
            'published_at' => now(),
            'price' => 500000000,
        ]);

        // DP melebihi harga → ditolak, tidak ada application tersimpan.
        $this->post('/kpr', [
            'property_id' => $property->id,
            'name' => 'Budi',
            'phone' => '08123456789',
            'dp_amount' => 600000000,
            'tenor_months' => 120,
        ])->assertSessionHasErrors('dp_amount');
        $this->assertDatabaseCount('kpr_applications', 0);

        // DP valid → tersimpan terikat ke properti + tenor/bank ikut.
        $this->post('/kpr', [
            'property_id' => $property->id,
            'name' => 'Budi',
            'phone' => '08123456789',
            'dp_amount' => 100000000,
            'tenor_months' => 120,
            'bank_preference' => 'bca',
        ])->assertStatus(302);
        $this->assertDatabaseHas('kpr_applications', [
            'property_id' => $property->id,
            'tenor_months' => 120,
            'bank_preference' => 'bca',
            'status' => 'pending',
        ]);
    }

    public function test_discussion_chain_message_appears_in_thread(): void
    {
        $owner = User::factory()->create();
        $property = Property::factory()->create([
            'user_id' => $owner->id,
            'published_at' => now(),
            'sold_at' => null,
        ]);
        $buyer = User::factory()->create();

        // Guest tidak bisa chat (auth), user bisa.
        $this->post('/messages', ['property_id' => $property->id, 'body' => 'Halo?'])
            ->assertRedirect('/login');

        $this->actingAs($buyer)->post('/messages', [
            'property_id' => $property->id,
            'body' => 'Apakah masih tersedia unit hook?',
        ])->assertStatus(302);
        $this->assertDatabaseHas('messages', [
            'property_id' => $property->id,
            'sender_user_id' => $buyer->id,
        ]);

        // Pesan muncul di thread halaman detail.
        $this->get('/properti/'.$property->slug)->assertOk()->assertSee('Apakah masih tersedia unit hook?');
    }

    public function test_guest_intent_chain_pricing_form_routes_correctly(): void
    {
        $owner = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $owner->id]);
        Package::query()->firstOrCreate(
            ['slug' => 'basic'],
            ['name' => 'Basic', 'price' => 100000, 'duration_days' => 30, 'max_photos' => 5]
        );

        // Guest: form pricing mengarah ke order-intent (data selamat via session).
        $this->get('/harga?property_id='.$property->id)->assertOk()
            ->assertSee(route('orders.intent'), false);

        // User login: form mengarah langsung ke orders.store.
        $this->actingAs($owner)->get('/harga?property_id='.$property->id)->assertOk()
            ->assertSee(route('orders.store'), false);
    }
}
