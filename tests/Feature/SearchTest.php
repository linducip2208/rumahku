<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('scout.driver', 'database');
    }

    public function test_search_by_keyword_returns_200_and_matching_property(): void
    {
        Property::factory()->create([
            'title' => 'Rumah Hook Test Dijual di Beji',
            'description' => 'Rumah hook test yang nyaman dekat kampus.',
            'type' => 'rumah',
            'listing_status' => 'jual',
            'city' => 'Depok',
            'district' => 'Beji',
            'published_at' => now(),
        ]);

        Property::factory()->create([
            'title' => 'Rumah Draft Belum Terbit di Beji',
            'description' => 'Rumah draft yang belum dipublish.',
            'type' => 'rumah',
            'listing_status' => 'jual',
            'city' => 'Depok',
            'published_at' => null,
        ]);

        $response = $this->get('/properti?q=rumah');

        $response->assertStatus(200);
        $response->assertSee('Rumah Hook Test Dijual di Beji');
        $response->assertDontSee('Rumah Draft Belum Terbit di Beji');
    }

    public function test_combined_filters_return_200(): void
    {
        Property::factory()->create([
            'title' => 'Rumah Kombinasi Dijual di Beji',
            'description' => 'Rumah kombinasi untuk uji filter.',
            'type' => 'rumah',
            'listing_status' => 'jual',
            'city' => 'Depok',
            'price' => 1500000000,
            'bedrooms' => 3,
            'published_at' => now(),
        ]);

        $response = $this->get('/properti?q=rumah&type=rumah&status=jual&city=Depok&min_price=100000000&max_price=5000000000&bedrooms=2');

        $response->assertStatus(200);
        $response->assertSee('Rumah Kombinasi Dijual di Beji');
    }
}
