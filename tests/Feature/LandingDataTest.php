<?php

namespace Tests\Feature;

use App\Models\AgentProfile;
use App\Models\Article;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_exposes_landing_data(): void
    {
        Property::factory()->create([
            'type' => 'rumah',
            'price' => 2500000000,
            'featured' => true,
            'published_at' => now(),
        ]);
        Property::factory()->create([
            'type' => 'villa',
            'price' => 3000000000,
            'published_at' => now(),
        ]);
        Property::factory()->create([
            'type' => 'apartemen',
            'price' => 1500000000,
            'published_at' => now(),
        ]);
        Property::factory()->create([
            'type' => 'tanah',
            'price' => 800000000,
            'published_at' => now(),
        ]);

        $agent = User::factory()->create();
        AgentProfile::factory()->create([
            'user_id' => $agent->id,
            'verified_at' => now(),
        ]);
        Property::factory()->create([
            'user_id' => $agent->id,
            'published_at' => now(),
        ]);

        Article::factory()->count(2)->create();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewHas('featured');
        $response->assertViewHas('cities');
        $response->assertViewHas('latest');
        $response->assertViewHas('categories', function (array $categories): bool {
            return count($categories) === 8;
        });
        $response->assertViewHas('collections', function (array $collections): bool {
            return count($collections) > 0;
        });
        $response->assertViewHas('agents');
        $response->assertViewHas('articles');
        $response->assertViewHas('landing', function (array $landing): bool {
            return ($landing['hero_image'] ?? null) === 'landing/hero.jpg';
        });

        $this->assertNotEmpty($response->viewData('featured'));
        $this->assertNotEmpty($response->viewData('latest'));
        $this->assertNotEmpty($response->viewData('agents'));
        $this->assertNotEmpty($response->viewData('articles'));
    }
}
