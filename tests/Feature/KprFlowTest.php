<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KprFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_valid_returns_redirect_and_saves(): void
    {
        $property = Property::factory()->create(['price' => 1000000000]);

        $response = $this->post(route('kpr.store'), [
            'property_id' => $property->id,
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'dp_amount' => 200000000,
            'tenor_months' => 120,
            'bank_preference' => 'mandiri',
            'monthly_income' => 15000000,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('kpr_applications', [
            'property_id' => $property->id,
            'name' => 'Budi Santoso',
            'tenor_months' => 120,
            'status' => 'pending',
        ]);
    }

    public function test_dp_greater_than_price_fails_with_session_error(): void
    {
        $property = Property::factory()->create(['price' => 500000000]);

        $response = $this->from(route('properties.show', $property->slug))->post(route('kpr.store'), [
            'property_id' => $property->id,
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'dp_amount' => 600000000,
            'tenor_months' => 60,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('dp_amount');
        $this->assertDatabaseMissing('kpr_applications', ['property_id' => $property->id]);
    }

    public function test_guest_can_apply_with_null_user_id(): void
    {
        $this->assertGuest();

        $property = Property::factory()->create(['price' => 800000000]);

        $response = $this->post(route('kpr.store'), [
            'property_id' => $property->id,
            'name' => 'Tamu Anonim',
            'phone' => '089876543210',
            'dp_amount' => 160000000,
            'tenor_months' => 240,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('kpr_applications', [
            'property_id' => $property->id,
            'name' => 'Tamu Anonim',
            'user_id' => null,
        ]);
    }

    public function test_authenticated_apply_links_user_id(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['price' => 800000000]);

        $response = $this->actingAs($user)->post(route('kpr.store'), [
            'property_id' => $property->id,
            'name' => $user->name,
            'phone' => '081111111111',
            'dp_amount' => 160000000,
            'tenor_months' => 36,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('kpr_applications', [
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_invalid_tenor_fails(): void
    {
        $property = Property::factory()->create(['price' => 800000000]);

        $response = $this->from(route('properties.show', $property->slug))->post(route('kpr.store'), [
            'property_id' => $property->id,
            'name' => 'Budi',
            'phone' => '081234567890',
            'dp_amount' => 100000000,
            'tenor_months' => 999,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('tenor_months');
    }
}
