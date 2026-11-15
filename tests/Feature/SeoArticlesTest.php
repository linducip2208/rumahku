<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_seo_articles_command_generates_data_driven_articles(): void
    {
        Property::factory()->create([
            'type' => 'rumah',
            'listing_status' => 'jual',
            'city' => 'Depok',
            'price' => 1000000000,
            'published_at' => now(),
        ]);
        Property::factory()->create([
            'type' => 'rumah',
            'listing_status' => 'jual',
            'city' => 'Depok',
            'price' => 2000000000,
            'published_at' => now(),
        ]);

        $before = Article::count();

        $this->artisan('seo:articles')->assertSuccessful();

        $this->assertGreaterThan($before, Article::count());

        $article = Article::where('slug', 'jual-rumah-depok')->first();
        $this->assertNotNull($article);
        $this->assertStringContainsString('Rp 1.000.000.000', $article->body);
        $this->assertStringContainsString('Rp 2.000.000.000', $article->body);

        // Idempoten: jalankan lagi, jumlah tidak bertambah.
        $countAfterFirst = Article::count();
        $this->artisan('seo:articles')->assertSuccessful();
        $this->assertEquals($countAfterFirst, Article::count());

        $this->get('/artikel/jual-rumah-depok')->assertStatus(200);
        $this->get('/jual/rumah/depok')->assertStatus(200);
    }
}
