<?php

namespace Tests\Feature;

use App\Filament\Resources\Permissions\PermissionResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_seeder_creates_roles_and_permissions_idempotently(): void
    {
        $this->seed(RolePermissionSeeder::class);

        foreach (['admin', 'agent', 'owner', 'user'] as $role) {
            $this->assertDatabaseHas('roles', ['name' => $role, 'guard_name' => 'web']);
        }

        $this->assertTrue(Role::findByName('admin')->hasPermissionTo('manage users'));
        $this->assertFalse(Role::findByName('agent')->hasPermissionTo('manage users'));
        $this->assertTrue(Role::findByName('agent')->hasPermissionTo('manage properties'));
        $this->assertSame(1, Permission::query()->where('name', 'manage users')->count());
    }

    public function test_admin_can_manage_users_but_agent_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $agent = User::factory()->create(['role' => 'agent']);
        $agent->assignRole('agent');

        $this->assertTrue($admin->can('manage users'));
        $this->assertFalse($agent->can('manage users'));

        $this->actingAs($admin);
        $this->assertTrue(UserResource::canViewAny());

        $this->actingAs($agent);
        $this->assertFalse(UserResource::canViewAny());
    }

    public function test_register_assigns_spatie_role(): void
    {
        $this->post('/register', [
            'name' => 'Agen Baru',
            'email' => 'agenbaru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'agent',
        ])->assertRedirect('/dashboard');

        $user = User::query()->where('email', 'agenbaru@example.com')->firstOrFail();
        $this->assertSame('agent', $user->role);
        $this->assertTrue($user->hasRole('agent'));
    }

    public function test_changing_role_column_syncs_spatie_role(): void
    {
        $user = User::factory()->create(['role' => 'agent']);
        $user->assignRole('agent');
        $this->assertFalse($user->can('manage users'));

        $user->forceFill(['role' => 'admin'])->save();

        $this->assertTrue($user->fresh()->hasRole('admin'));
        $this->assertTrue($user->fresh()->can('manage users'));
    }

    public function test_role_resource_visible_only_with_manage_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $agent = User::factory()->create(['role' => 'agent']);
        $agent->assignRole('agent');

        $this->actingAs($admin);
        $this->assertTrue(RoleResource::canViewAny());

        $this->actingAs($agent);
        $this->assertFalse(RoleResource::canViewAny());
    }

    public function test_panel_access_without_permission_is_denied_gated_pages(): void
    {
        // Agen lolos panel bila kolom role admin (in-memory, tanpa save agar
        // hook sinkronisasi tidak ikut jalan), tapi tanpa permission Spatie
        // yang sesuai → resource yang di-gate menolak (403).
        $user = User::factory()->create(['role' => 'agent']);
        $user->assignRole('agent');
        $user->forceFill(['role' => 'admin']);

        $this->actingAs($user)->get('/admin')->assertOk();
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
        $this->actingAs($user)->get('/admin/properties')->assertOk(); // agent punya manage properties
    }

    public function test_permission_resource_registered_and_gated(): void
    {
        $this->assertTrue(Route::has('filament.admin.resources.permissions.index'));

        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $this->actingAs($admin);
        $this->assertTrue(PermissionResource::canViewAny());
        $this->get('/admin/permissions')->assertOk();
    }
}
