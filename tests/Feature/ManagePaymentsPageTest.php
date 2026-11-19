<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagePaymentsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_v5_schema_utilities_exist(): void
    {
        // Regresi: closure form pernah type-hint Filament\Forms\Get/Set
        // yang tidak ada di Filament v5 (TypeError saat render).
        $this->assertTrue(class_exists(Get::class));
        $this->assertTrue(class_exists(Set::class));
        $this->assertFalse(class_exists('Filament\Forms\Get'));
        $this->assertFalse(class_exists('Filament\Forms\Set'));
    }

    public function test_manage_payments_page_renders(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        // Merender form mengevaluasi closure required(fn (Get $get) ...).
        $this->actingAs($admin)->get('/admin/pengaturan-pembayaran')->assertOk();
    }
}
