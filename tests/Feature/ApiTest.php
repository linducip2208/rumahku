<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Api User',
            'email' => 'api@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);

        $this->assertDatabaseHas('users', ['email' => 'api@example.com']);
    }

    public function test_property_listing_filter_returns_200_with_structure(): void
    {
        Property::factory()->count(3)->create([
            'type' => 'rumah',
            'listing_status' => 'jual',
            'city' => 'Depok',
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/properties?type=rumah&status=jual&city=Depok');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);

        $this->assertNotEmpty($response->json('data'));
    }

    public function test_property_show_returns_200(): void
    {
        $property = Property::factory()->create(['published_at' => now()]);

        $response = $this->getJson('/api/v1/properties/'.$property->slug);

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', $property->slug)
            ->assertJsonStructure(['data' => ['id', 'slug', 'title', 'images', 'agent', 'related_ids']]);
    }

    public function test_lead_store_returns_201(): void
    {
        $property = Property::factory()->create(['published_at' => now()]);

        $response = $this->postJson('/api/v1/leads', [
            'property_id' => $property->id,
            'name' => 'Budi',
            'phone' => '08123456789',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('leads', ['phone' => '08123456789']);
    }

    public function test_guest_favorites_returns_401(): void
    {
        $this->getJson('/api/v1/favorites')->assertStatus(401);
        $this->postJson('/api/v1/favorites/toggle', ['property_id' => 1])->assertStatus(401);
    }
}
