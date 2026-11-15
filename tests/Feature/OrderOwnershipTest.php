<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyOrder;
use App\Models\User;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_order_other_users_property(): void
    {
        $this->seed(PackageSeeder::class);

        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->for($owner)->create();

        $response = $this->actingAs($attacker)->post(route('orders.store'), [
            'property_id' => $property->id,
            'package_slug' => 'basic',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('property_orders', [
            'user_id' => $attacker->id,
            'property_id' => $property->id,
        ]);
        $this->assertSame(0, PropertyOrder::query()->count());
    }

    public function test_duplicate_pending_order_is_rejected(): void
    {
        $this->seed(PackageSeeder::class);

        $owner = User::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->for($owner)->create();

        $first = $this->actingAs($owner)->post(route('orders.store'), [
            'property_id' => $property->id,
            'package_slug' => 'basic',
        ]);

        $first->assertRedirect();
        $first->assertSessionHasNoErrors();
        $this->assertSame(1, PropertyOrder::query()->count());

        $second = $this->actingAs($owner)->post(route('orders.store'), [
            'property_id' => $property->id,
            'package_slug' => 'basic',
        ]);

        $second->assertRedirect();
        $second->assertSessionHasErrors('property_id');
        $this->assertSame(1, PropertyOrder::query()->count());
    }
}
