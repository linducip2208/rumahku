<?php

namespace Tests\Feature;

use App\Models\FeatureIdea;
use App\Services\Ideas\IdeaBank;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdeaBankTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_returns_exactly_1000_unique_rows(): void
    {
        $rows = IdeaBank::generate('detail');

        $this->assertCount(1000, $rows);
        $titles = array_column($rows, 'title');
        $this->assertCount(1000, array_unique($titles));
    }

    public function test_seed_command_is_idempotent_per_feature(): void
    {
        $this->artisan('ideas:seed', ['--feature' => 'kpr'])->assertSuccessful();
        $this->assertSame(1000, FeatureIdea::query()->where('feature_key', 'kpr')->count());

        $this->artisan('ideas:seed', ['--feature' => 'kpr'])->assertSuccessful();
        $this->assertSame(1000, FeatureIdea::query()->where('feature_key', 'kpr')->count());
    }

    public function test_api_lists_ideas_filtered_by_feature(): void
    {
        $this->artisan('ideas:seed', ['--feature' => 'search'])->assertSuccessful();

        $response = $this->getJson('/api/v1/ideas?feature=search&per_page=5');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json('data'));
        $this->assertSame(1000, $response->json('total'));
    }

    public function test_api_rejects_unknown_feature(): void
    {
        $this->getJson('/api/v1/ideas?feature=tidak-ada')->assertStatus(422);
    }
}
