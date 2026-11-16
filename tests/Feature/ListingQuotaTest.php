<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Property;
use App\Models\PropertyOrder;
use App\Models\User;
use App\Services\ListingQuota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingQuotaTest extends TestCase
{
    use RefreshDatabase;

    private function listingPayload(array $over = []): array
    {
        return array_merge([
            'title' => 'Rumah Kuota Depok',
            'type' => 'rumah',
            'listing_status' => 'jual',
            'price' => 1500000000,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'land_area' => 120,
            'building_area' => 90,
            'certificate' => 'SHM',
            'province' => 'Jawa Barat',
            'city' => 'Depok',
            'district' => 'Beji',
            'address' => 'Jl. Kuota No. 1',
            'description' => 'Rumah uji kuota.',
        ], $over);
    }

    private function seedQuotaPackages(): void
    {
        foreach ([
            ['name' => 'Gratis', 'slug' => 'gratis', 'price' => 0, 'duration_days' => 14, 'max_photos' => 3, 'max_listings' => 5, 'featured_included' => false, 'badge' => null],
            ['name' => 'Basic', 'slug' => 'basic', 'price' => 99000, 'duration_days' => 30, 'max_photos' => 10, 'max_listings' => 20, 'featured_included' => false, 'badge' => null],
            ['name' => 'Plus', 'slug' => 'plus', 'price' => 249000, 'duration_days' => 30, 'max_photos' => 20, 'max_listings' => 50, 'featured_included' => true, 'badge' => 'Populer'],
            ['name' => 'Pro', 'slug' => 'pro', 'price' => 499000, 'duration_days' => 60, 'max_photos' => 40, 'max_listings' => null, 'featured_included' => true, 'badge' => 'Terbaik'],
        ] as $package) {
            Package::query()->updateOrCreate(['slug' => $package['slug']], $package);
        }
    }

    public function test_gratis_user_bisa_buat_5_listing_keenam_ditolak(): void
    {
        $this->seedQuotaPackages();

        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('agent.listings.store'), $this->listingPayload(['title' => "Rumah Kuota {$i}"]))
                ->assertRedirect(route('agent.listings.index'));
        }

        $this->assertSame(5, ListingQuota::usageFor($user));
        $this->assertSame(5, ListingQuota::limitFor($user));
        $this->assertFalse(ListingQuota::canCreate($user));

        $response = $this->post(route('agent.listings.store'), $this->listingPayload(['title' => 'Rumah Kuota 6']));

        $response->assertSessionHasErrors('quota');
        $this->assertSame(5, Property::where('user_id', $user->id)->count());
    }

    public function test_sold_dan_draft_tidak_dihitung_dalam_kuota(): void
    {
        $this->seedQuotaPackages();

        /** @var User $user */
        $user = User::factory()->create();

        Property::factory()->for($user)->count(3)->create([
            'published_at' => now(),
            'sold_at' => null,
        ]);
        Property::factory()->for($user)->count(2)->create([
            'published_at' => now(),
            'sold_at' => now(),
        ]);
        Property::factory()->for($user)->create([
            'published_at' => null,
            'sold_at' => null,
        ]);

        $this->assertSame(3, ListingQuota::usageFor($user));
        $this->assertTrue(ListingQuota::canCreate($user));
    }

    public function test_order_paid_paket_plus_menaikkan_kuota(): void
    {
        $this->seedQuotaPackages();

        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertSame(5, ListingQuota::limitFor($user));

        /** @var Property $property */
        $property = Property::factory()->for($user)->create([
            'published_at' => now(),
            'sold_at' => null,
        ]);

        /** @var PropertyOrder $order */
        $order = PropertyOrder::query()->updateOrCreate(
            ['user_id' => $user->id, 'property_id' => $property->id, 'package_slug' => 'plus'],
            ['amount' => 249000, 'status' => 'pending', 'paid_at' => null, 'expires_at' => null]
        );
        $order->markAsPaid();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame(50, ListingQuota::limitFor($user));
        $this->assertTrue(ListingQuota::canCreate($user));

        // 5 listing aktif tetap bisa nambah karena kuota plus = 50.
        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('agent.listings.store'), $this->listingPayload(['title' => "Rumah Plus {$i}"]))
                ->assertRedirect(route('agent.listings.index'));
        }

        $this->assertSame(6, ListingQuota::usageFor($user));
        $this->assertTrue(ListingQuota::canCreate($user));
    }

    public function test_paket_pro_unlimited_selalu_bisa_buat(): void
    {
        $this->seedQuotaPackages();

        /** @var User $user */
        $user = User::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->for($user)->create([
            'published_at' => now(),
            'sold_at' => null,
        ]);

        /** @var PropertyOrder $order */
        $order = PropertyOrder::query()->updateOrCreate(
            ['user_id' => $user->id, 'property_id' => $property->id, 'package_slug' => 'pro'],
            ['amount' => 499000, 'status' => 'pending', 'paid_at' => null, 'expires_at' => null]
        );
        $order->markAsPaid();

        $this->assertNull(ListingQuota::limitFor($user));
        $this->assertTrue(ListingQuota::canCreate($user));
    }

    public function test_pricing_menampilkan_batas_listing(): void
    {
        $this->seedQuotaPackages();

        $this->get(route('pricing'))
            ->assertStatus(200)
            ->assertSee('Maks. 5 listing aktif')
            ->assertSee('Tanpa batas listing aktif');
    }
}
