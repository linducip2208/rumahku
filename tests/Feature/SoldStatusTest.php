<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoldStatusTest extends TestCase
{
    use RefreshDatabase;

    private function registerAgent(string $email): User
    {
        $this->post('/register', [
            'name' => 'Agen Sold',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        return User::where('email', $email)->firstOrFail();
    }

    private function listingPayload(array $over = []): array
    {
        return array_merge([
            'title' => 'Rumah Sold Status Depok',
            'type' => 'rumah',
            'listing_status' => 'jual',
            'price' => 1750000000,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'land_area' => 120,
            'building_area' => 90,
            'certificate' => 'SHM',
            'province' => 'Jawa Barat',
            'city' => 'Depok',
            'district' => 'Beji',
            'address' => 'Jl. Sold No. 1',
            'description' => 'Rumah uji status terjual.',
        ], $over);
    }

    public function test_sold_listing_hidden_from_public_and_api(): void
    {
        $this->registerAgent('agen-sold@example.com');
        $this->post(route('agent.listings.store'), $this->listingPayload())
            ->assertRedirect(route('agent.listings.index'));

        $property = Property::firstOrFail();
        $slug = $property->slug;

        // Terlihat saat aktif.
        $this->get(route('properties.show', $slug))->assertStatus(200);
        $this->get('/properti')->assertStatus(200)->assertSee('Rumah Sold Status Depok');
        $this->get('/')->assertStatus(200)->assertSee('Rumah Sold Status Depok');
        $this->getJson(route('api.v1.properties.index'))->assertStatus(200)->assertSee('Rumah Sold Status Depok');
        $this->getJson(route('api.v1.properties.show', $slug))->assertStatus(200);

        // Tandai terjual (idempotent: dua kali tetap sukses).
        $this->post(route('agent.listings.sold', $property))->assertRedirect(route('agent.listings.index'));
        $this->post(route('agent.listings.sold', $property))->assertRedirect(route('agent.listings.index'));
        $this->assertNotNull($property->fresh()->sold_at);

        // Hilang dari publik + API.
        $this->get(route('properties.show', $slug))->assertStatus(404);
        $this->get('/properti')->assertStatus(200)->assertDontSee('Rumah Sold Status Depok');
        $this->get('/')->assertStatus(200)->assertDontSee('Rumah Sold Status Depok');
        $this->getJson(route('api.v1.properties.index'))->assertStatus(200)->assertDontSee('Rumah Sold Status Depok');
        $this->getJson(route('api.v1.properties.show', $slug))->assertStatus(404);

        // Tab filter agen: muncul di Terjual, tidak di Aktif.
        $this->get(route('agent.listings.index', ['status' => 'terjual']))->assertStatus(200)->assertSee('Rumah Sold Status Depok');
        $this->get(route('agent.listings.index', ['status' => 'aktif']))->assertStatus(200)->assertDontSee('Rumah Sold Status Depok');

        // Aktifkan lagi → muncul kembali di mana-mana.
        $this->post(route('agent.listings.relist', $property))->assertRedirect(route('agent.listings.index'));
        $this->assertNull($property->fresh()->sold_at);
        $this->get(route('properties.show', $slug))->assertStatus(200);
        $this->get('/properti')->assertStatus(200)->assertSee('Rumah Sold Status Depok');
        $this->get('/')->assertStatus(200)->assertSee('Rumah Sold Status Depok');
        $this->getJson(route('api.v1.properties.show', $slug))->assertStatus(200);
    }

    public function test_other_agent_cannot_change_sold_status(): void
    {
        $this->registerAgent('agen-sold-a@example.com');
        $this->post(route('agent.listings.store'), $this->listingPayload());
        $property = Property::firstOrFail();
        $this->post(route('logout'));

        $this->registerAgent('agen-sold-b@example.com');

        $this->post(route('agent.listings.sold', $property))->assertStatus(403);
        $this->post(route('agent.listings.relist', $property))->assertStatus(403);
        $this->assertNull($property->fresh()->sold_at);
    }
}
