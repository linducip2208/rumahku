<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnpublishedApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unpublished_property_via_api_returns_404(): void
    {
        $property = Property::factory()->create(['published_at' => null]);

        $this->getJson('/api/v1/properties/'.$property->slug)->assertNotFound();
    }

    public function test_published_property_via_api_returns_200(): void
    {
        $property = Property::factory()->create(['published_at' => now()]);

        $this->getJson('/api/v1/properties/'.$property->slug)
            ->assertOk()
            ->assertJsonPath('data.slug', $property->slug);
    }
}
