<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_returns_200(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_listing_filter_returns_200(): void
    {
        $path = $this->listingPath();
        if ($path === null) {
            $this->markTestSkipped('Route listing (properties.index / /properti) belum ada.');
        }

        $this->get($path.'?city=Depok&type=rumah&status=jual')->assertStatus(200);
    }

    public function test_property_show_returns_200(): void
    {
        if (! class_exists(Property::class) || ! Schema::hasTable('properties')) {
            $this->markTestSkipped('Model/tabel Property belum ada.');
        }
        if (! Route::has('properties.show')) {
            $this->markTestSkipped('Route properties.show belum ada.');
        }

        $propertyId = $this->createDemoProperty();
        $slug = DB::table('properties')->where('id', $propertyId)->value('slug');

        $this->get(route('properties.show', $slug))->assertStatus(200);
    }

    public function test_leads_store_redirect_and_persist(): void
    {
        $hasRoute = Route::has('leads.store') || $this->routePathExists('/leads');
        if (! class_exists(Lead::class) || ! Schema::hasTable('leads') || ! $hasRoute) {
            $this->markTestSkipped('Route leads.store / model Lead / tabel leads belum ada.');
        }

        $target = Route::has('leads.store') ? route('leads.store') : '/leads';
        $payload = [
            'property_id' => $this->createDemoProperty(),
            'name' => 'Budi',
            'phone' => '08123456789',
            'message' => 'Saya tertarik, hubungi saya.',
        ];

        $response = $this->post($target, $payload);
        $response->assertStatus(302);
        $this->assertDatabaseHas('leads', ['phone' => '08123456789']);
    }

    public function test_pseo_city_route_returns_200(): void
    {
        $path = $this->pseoPath('depok');
        if ($path === null) {
            $this->markTestSkipped('Route pSEO city belum ada.');
        }

        $this->get($path)->assertStatus(200);
    }

    private function listingPath(): ?string
    {
        if (Route::has('properties.index')) {
            return route('properties.index');
        }
        foreach (['/properti', '/properties', '/listing'] as $path) {
            if ($this->routePathExists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function propertyShowPath(int|string $id): ?string
    {
        if (Route::has('properties.show')) {
            return route('properties.show', $id);
        }
        foreach (["/properti/{$id}", "/properties/{$id}"] as $path) {
            if ($this->routePathExists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function pseoPath(string $city): ?string
    {
        if (Route::has('seo.landing')) {
            return route('seo.landing', ['listing' => 'jual', 'type' => 'rumah', 'city' => $city]);
        }
        foreach (["/jual-rumah-di-{$city}", "/properti/{$city}"] as $path) {
            if ($this->routePathExists($path)) {
                return $path;
            }
        }
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (str_contains($uri, '{city}') && in_array('GET', $route->methods(), true)) {
                return '/'.str_replace('{city}', $city, $uri);
            }
        }

        return null;
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

    private function createDemoProperty(): int|string
    {
        // Tanpa factory Property (belum ada): buat user + insert minimal
        // sesuai kolom aktual (difilter agar toleran perubahan skema).
        $userId = User::factory()->create()->id;
        $columns = Schema::getColumnListing('properties');
        $candidate = [
            'user_id' => $userId,
            'agent_id' => $userId,
            'title' => 'Dijual Rumah di Depok (Test)',
            'name' => 'Dijual Rumah di Depok (Test)',
            'slug' => 'dijual-rumah-di-depok-test-'.uniqid(),
            'description' => 'Property test MarketplaceTest.',
            'body' => 'Property test MarketplaceTest.',
            'type' => 'rumah',
            'property_type' => 'rumah',
            'listing_type' => 'jual',
            'listing_status' => 'jual',
            'status' => 'jual',
            'transaction' => 'jual',
            'province' => 'Jawa Barat',
            'city' => 'Depok',
            'address' => 'Jl. Test No. 1, Depok',
            'price' => 1500000000,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'published' => true,
            'is_published' => true,
            'is_active' => true,
            'published_at' => now(),
        ];
        $row = [];
        foreach ($candidate as $key => $value) {
            if (in_array($key, $columns, true)) {
                $row[$key] = $value;
            }
        }
        if (in_array('created_at', $columns, true)) {
            $row['created_at'] = now();
        }
        if (in_array('updated_at', $columns, true)) {
            $row['updated_at'] = now();
        }

        return DB::table('properties')->insertGetId($row);
    }
}
