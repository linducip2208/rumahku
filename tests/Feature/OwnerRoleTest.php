<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function listingPayload(string $title = 'Rumah Owner Dijual di Bekasi'): array
    {
        return [
            'title' => $title,
            'type' => 'rumah',
            'listing_status' => 'jual',
            'price' => 1250000000,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'land_area' => 100,
            'building_area' => 80,
            'certificate' => 'SHM',
            'province' => 'Jawa Barat',
            'city' => 'Bekasi',
            'district' => 'Bekasi Timur',
            'address' => 'Jl. Owner No. 1, Bekasi',
            'description' => 'Rumah milik sendiri dijual langsung.',
        ];
    }

    public function test_register_owner_sets_role_without_agent_profile(): void
    {
        $response = $this->post('/register', [
            'name' => 'Pemilik Test',
            'email' => 'owner@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'owner',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'owner@example.com', 'role' => 'owner']);

        $owner = User::where('email', 'owner@example.com')->firstOrFail();
        $this->assertDatabaseMissing('agent_profiles', ['user_id' => $owner->id]);
    }

    public function test_register_defaults_to_agent_with_profile(): void
    {
        $this->post('/register', [
            'name' => 'Agen Default',
            'email' => 'agen-default@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', ['email' => 'agen-default@example.com', 'role' => 'agent']);
        $agent = User::where('email', 'agen-default@example.com')->firstOrFail();
        $this->assertDatabaseHas('agent_profiles', ['user_id' => $agent->id]);
    }

    public function test_register_rejects_invalid_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Bad Role',
            'email' => 'badrole@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'badrole@example.com']);
    }

    public function test_owner_can_crud_listing_and_open_dashboard(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $this->actingAs($owner)->get('/dashboard')->assertStatus(200);
        $this->actingAs($owner)->get(route('agent.listings.index'))->assertStatus(200);
        $this->actingAs($owner)->get(route('agent.listings.create'))->assertStatus(200);

        $store = $this->actingAs($owner)->post(route('agent.listings.store'), $this->listingPayload());
        $store->assertRedirect(route('agent.listings.index'));

        $property = Property::where('title', 'Rumah Owner Dijual di Bekasi')->firstOrFail();
        $this->assertSame($owner->id, $property->user_id);

        $this->actingAs($owner)
            ->get(route('agent.listings.edit', $property))
            ->assertStatus(200);

        $this->actingAs($owner)
            ->put(route('agent.listings.update', $property), $this->listingPayload('Rumah Owner Diedit'))
            ->assertRedirect(route('agent.listings.index'));
        $this->assertDatabaseHas('properties', ['id' => $property->id, 'title' => 'Rumah Owner Diedit']);

        $this->actingAs($owner)->get(route('agent.orders.index'))->assertStatus(200);

        $this->actingAs($owner)
            ->delete(route('agent.listings.destroy', $property))
            ->assertRedirect(route('agent.listings.index'));
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }

    public function test_owner_cannot_open_admin_panel(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $this->actingAs($owner)->get('/admin')->assertStatus(403);
    }

    public function test_owner_dashboard_shows_pemilik_title(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $this->actingAs($owner)->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('Dasbor Pemilik');
    }

    public function test_agent_dashboard_shows_agen_title(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);

        $this->actingAs($agent)->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('Dasbor Agen');
    }
}
