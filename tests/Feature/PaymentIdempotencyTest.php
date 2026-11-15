<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Property;
use App\Models\PropertyOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_mark_as_paid_twice_keeps_paid_at_and_expires_at(): void
    {
        /** @var Package $package */
        $package = Package::factory()->create([
            'slug' => 'pro-idem',
            'price' => 499000,
            'duration_days' => 30,
            'featured_included' => false,
        ]);

        /** @var User $user */
        $user = User::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->for($user)->create();

        /** @var PropertyOrder $order */
        $order = PropertyOrder::factory()->create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'package_slug' => $package->slug,
            'amount' => $package->price,
            'status' => 'pending',
        ]);

        $this->travelTo(now());

        $order->markAsPaid();

        $firstPaidAt = $order->fresh()->paid_at;
        $firstExpiresAt = $order->fresh()->expires_at;

        $this->assertNotNull($firstPaidAt);
        $this->assertNotNull($firstExpiresAt);

        $this->travelTo(now()->addMinutes(5));

        $order->fresh()->markAsPaid();

        $fresh = $order->fresh();

        $this->assertSame('paid', $fresh->status);
        $this->assertTrue($firstPaidAt->equalTo($fresh->paid_at));
        $this->assertTrue($firstExpiresAt->equalTo($fresh->expires_at));
    }
}
