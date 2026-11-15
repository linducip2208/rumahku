<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Property;
use App\Models\PropertyOrder;
use App\Models\User;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_seeder_seeds_four_packages(): void
    {
        $this->seed(PackageSeeder::class);

        $this->assertSame(4, Package::query()->count());

        foreach (['gratis', 'basic', 'plus', 'pro'] as $slug) {
            $this->assertDatabaseHas('packages', ['slug' => $slug]);
        }

        $this->assertDatabaseHas('packages', ['slug' => 'gratis', 'price' => 0]);
        $this->assertDatabaseHas('packages', ['slug' => 'basic', 'price' => 99000]);
        $this->assertDatabaseHas('packages', ['slug' => 'plus', 'price' => 249000]);
        $this->assertDatabaseHas('packages', ['slug' => 'pro', 'price' => 499000]);
    }

    public function test_paid_order_marks_property_as_featured(): void
    {
        $this->seed(PackageSeeder::class);

        /** @var User $user */
        $user = User::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->for($user)->create(['featured' => false]);

        $plus = Package::query()->where('slug', 'plus')->firstOrFail();

        /** @var PropertyOrder $order */
        $order = PropertyOrder::factory()->create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'package_slug' => $plus->slug,
            'amount' => $plus->price,
            'status' => 'pending',
        ]);

        $order->markAsPaid();

        $this->assertSame('paid', $order->fresh()?->status);
        $this->assertNotNull($order->fresh()?->paid_at);
        $this->assertNotNull($order->fresh()?->expires_at);
        $this->assertTrue($property->fresh()?->featured);
        $this->assertSame(1, PropertyOrder::query()->active()->count());
    }
}
