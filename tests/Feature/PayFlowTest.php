<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Property;
use App\Models\PropertyOrder;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PayFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Setting::set('payments.default_driver', 'manual', 'payments');
        Setting::set('payments.bank_accounts', 'BCA 1234567890 a.n. PT Rumah123', 'payments');
    }

    public function test_guest_pay_redirects_to_login(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->for($user)->create();
        $package = Package::factory()->create(['slug' => 'basic', 'price' => 99000]);

        $order = PropertyOrder::query()->create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'package_slug' => $package->slug,
            'amount' => $package->price,
            'status' => 'pending',
        ]);

        $this->get('/pay/'.$order->id)->assertRedirect('/login');
    }

    public function test_user_creates_order_then_sees_manual_instructions(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->for($user)->create();
        $package = Package::factory()->create(['slug' => 'basic', 'price' => 99000]);

        $this->actingAs($user)
            ->post('/orders', [
                'property_id' => $property->id,
                'package_slug' => $package->slug,
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('property_orders', [
            'user_id' => $user->id,
            'property_id' => $property->id,
            'package_slug' => $package->slug,
            'amount' => $package->price,
            'status' => 'pending',
        ]);

        $orderId = (int) PropertyOrder::query()->where('user_id', $user->id)->latest('id')->firstOrFail()->id;

        $this->actingAs($user)
            ->get('/pay/'.$orderId)
            ->assertStatus(200)
            ->assertSee('Instruksi Pembayaran', false);
    }

    public function test_manual_callback_with_empty_payload_returns_ok_true(): void
    {
        $this->postJson('/pay/callback/manual', [])
            ->assertStatus(200)
            ->assertJsonPath('ok', true);
    }

    public function test_unknown_driver_callback_returns_ok_false_without_500(): void
    {
        $this->postJson('/pay/callback/unknown-driver', [])
            ->assertStatus(200)
            ->assertJsonPath('ok', false);
    }
}
