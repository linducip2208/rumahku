<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Semua halaman admin penting harus render 200.
 * Menangkap error runtime (mis. type-hint closure Filament yang salah)
 * yang hanya muncul saat form/tabel benar-benar di-render.
 */
class AdminPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    /** @var list<string> */
    private const PAGES = [
        '/admin',
        '/admin/users',
        '/admin/roles',
        '/admin/permissions',
        '/admin/properties',
        '/admin/properties/create',
        '/admin/leads',
        '/admin/property-orders',
        '/admin/property-orders/create',
        '/admin/pengaturan-pembayaran',
        '/admin/pengaturan-landing',
        '/admin/manage-storage',
    ];

    public function test_all_admin_pages_render(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        foreach (self::PAGES as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->assertTrue(true);
    }
}
