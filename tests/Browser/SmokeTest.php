<?php

namespace Tests\Browser;

use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Smoke test alur kritis via browser asli (butuh Chrome + chromedriver).
 *
 * Lokal: `php artisan dusk` (Chrome harus terinstal).
 * CI: job `dusk` di .github/workflows/ci.yml.
 */
class SmokeTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_homepage_loads(): void
    {
        $this->browse(fn (Browser $browser) => $browser->visit('/')
            ->waitForText('Temukan Rumah Impianmu'));
    }

    public function test_login_page_shows_form(): void
    {
        $this->browse(fn (Browser $browser) => $browser->visit('/login')
            ->waitFor('input[name="email"]')
            ->assertPresent('input[name="password"]'));
    }

    public function test_guest_dashboard_redirects_to_login(): void
    {
        $this->browse(fn (Browser $browser) => $browser->visit('/dashboard')
            ->waitForLocation('/login'));
    }

    public function test_pricing_lists_packages(): void
    {
        $this->seed(PackageSeeder::class);

        $this->browse(fn (Browser $browser) => $browser->visit('/harga')
            ->waitForText('Gratis'));
    }

    public function test_admin_login_page_shows_form(): void
    {
        $this->browse(fn (Browser $browser) => $browser->visit('/admin/login')
            ->waitFor('input[type="email"]'));
    }
}
