<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('scout.driver', 'database');
    }

    public function test_unpublished_property_hidden_from_index_and_show_404(): void
    {
        $property = Property::factory()->create([
            'title' => 'Rumah Moderasi Draft Belum Terbit',
            'published_at' => null,
        ]);

        $this->assertNull($property->fresh()->published_at);
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'published_at' => null,
        ]);

        $this->get('/properti')->assertStatus(200)->assertDontSee('Rumah Moderasi Draft Belum Terbit');

        // PropertyController@show memakai firstOrFail() sehingga unpublished → 404.
        $this->get(route('properties.show', $property->slug))->assertStatus(404);
    }

    public function test_publish_unpublish_round_trip_via_model(): void
    {
        $property = Property::factory()->create(['published_at' => null]);

        // Simulasi bulk action "Publish": set published_at=now untuk yang null.
        Property::query()->whereKey($property->id)->whereNull('published_at')->update(['published_at' => now()]);

        $this->assertNotNull($property->fresh()->published_at);
        $this->get(route('properties.show', $property->slug))->assertStatus(200);

        // Simulasi bulk action "Unpublish": set null.
        Property::query()->whereKey($property->id)->update(['published_at' => null]);

        $this->assertNull($property->fresh()->published_at);
        $this->get(route('properties.show', $property->slug))->assertStatus(404);
    }

    public function test_leads_route_has_throttle_header(): void
    {
        Mail::fake();

        $property = Property::factory()->create(['published_at' => now()]);

        $response = $this->post(route('leads.store'), [
            'property_id' => $property->id,
            'name' => 'Moderasi Tester',
            'phone' => '08123456789',
        ]);

        $response->assertStatus(302);
        $response->assertHeader('X-RateLimit-Limit', '30');
    }
}
