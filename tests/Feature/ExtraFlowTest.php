<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Property;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * ExtraFlowTest — alur halaman ekstensi P1 (lelang, jasa, bandingkan, harga, sitemap).
 *
 * Setiap test SKIP graceful bila route/model/tabel belum ada
 * (cek Route::has / class_exists / Schema::hasTable, tanpa asumsi).
 */
class ExtraFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_lelang_page_returns_200(): void
    {
        if (! Route::has('auctions.index') && ! $this->routePathExists('/lelang')) {
            $this->markTestSkipped('Route lelang (auctions.index / /lelang) belum ada.');
        }

        $this->get('/lelang')->assertStatus(200);
    }

    public function test_jasa_page_and_category_filter_returns_200(): void
    {
        if (! Route::has('services.index') && ! $this->routePathExists('/jasa')) {
            $this->markTestSkipped('Route jasa (services.index / /jasa) belum ada.');
        }

        $this->get('/jasa')->assertStatus(200);
        $this->get('/jasa?category=notaris')->assertStatus(200);

        if (class_exists(Service::class) && Schema::hasTable('services')) {
            Service::factory()->create(['category' => 'notaris', 'name' => 'Jasa Notaris Test '.uniqid()]);
            $this->get('/jasa?category=notaris')->assertStatus(200);
        }
    }

    public function test_bandingkan_with_ids_returns_200(): void
    {
        if (! Route::has('compare.index') && ! $this->routePathExists('/bandingkan')) {
            $this->markTestSkipped('Route bandingkan (compare.index / /bandingkan) belum ada.');
        }

        if (class_exists(Property::class) && Schema::hasTable('properties')) {
            /** @var User $user */
            $user = User::factory()->create();
            $a = Property::factory()->for($user)->create();
            $b = Property::factory()->for($user)->create();

            $this->get("/bandingkan?ids={$a->id},{$b->id}")->assertStatus(200);

            return;
        }

        $this->get('/bandingkan')->assertStatus(200);
    }

    public function test_harga_page_shows_package_names(): void
    {
        if (! Route::has('pricing') && ! $this->routePathExists('/harga')) {
            $this->markTestSkipped('Route harga (pricing / /harga) belum ada.');
        }
        if (! class_exists(Package::class) || ! Schema::hasTable('packages')) {
            $this->markTestSkipped('Model/tabel Package belum ada.');
        }

        $this->seed(PackageSeeder::class);

        $response = $this->get('/harga')->assertStatus(200);
        foreach (['Gratis', 'Basic', 'Plus', 'Pro'] as $name) {
            $response->assertSee($name);
        }
    }

    public function test_sitemap_returns_200_or_skipped(): void
    {
        $path = null;
        if (Route::has('sitemap')) {
            $path = route('sitemap');
        } elseif ($this->routePathExists('/sitemap.xml')) {
            $path = '/sitemap.xml';
        } elseif ($this->routePathExists('/sitemap')) {
            $path = '/sitemap';
        }

        if ($path === null) {
            $this->markTestSkipped('Route sitemap belum ada.');
        }

        $this->get($path)->assertStatus(200);
    }

    private function routePathExists(string $path): bool
    {
        foreach (Route::getRoutes() as $route) {
            if ('/'.ltrim($route->uri(), '/') === '/'.ltrim($path, '/')
                && in_array('GET', $route->methods(), true)) {
                return true;
            }
        }

        return false;
    }
}
