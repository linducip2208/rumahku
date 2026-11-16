<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlushDemoDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_flush_requires_force_flag(): void
    {
        Property::factory()->create(['published_at' => now()]);

        // Tanpa --force dan tanpa jawaban konfirmasi: perintah menolak, data utuh.
        $this->artisan('demo:flush')
            ->expectsQuestion('Hapus SEMUA data demo? Lanjut?', false)
            ->assertFailed();

        $this->assertSame(1, Property::query()->count());
    }

    public function test_flush_with_force_wipes_demo_but_keeps_packages(): void
    {
        Property::factory()->create(['published_at' => now()]);
        Package::query()->updateOrCreate(
            ['slug' => 'gratis'],
            ['name' => 'Gratis', 'price' => 0, 'duration_days' => 14, 'max_photos' => 3, 'featured_included' => false]
        );

        $this->artisan('demo:flush', ['--force' => true])->assertSuccessful();

        $this->assertSame(0, Property::query()->count());
        $this->assertSame(1, Package::query()->where('slug', 'gratis')->count());
    }
}
