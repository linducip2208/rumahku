<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create([
            'email' => 'admin-test@rumah123.local',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        return $admin->fresh();
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertStatus(200);
    }

    public function test_admin_can_open_dashboard(): void
    {
        $response = $this->actingAs($this->admin())->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_can_open_property_resource(): void
    {
        $response = $this->actingAs($this->admin())->get('/admin/properties');

        $response->assertStatus(200);
    }

    public function test_agent_cannot_open_admin_panel(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);

        $this->actingAs($agent)->get('/admin')->assertStatus(403);
    }

    public function test_admin_can_login_with_password(): void
    {
        User::factory()->create([
            'email' => 'admin-login@rumah123.local',
            'password' => 'secret12345',
            'role' => 'admin',
        ]);

        // Login Filament via Livewire (form-nya bukan POST biasa).
        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'admin-login@rumah123.local',
                'password' => 'secret12345',
            ])
            ->call('authenticate')
            ->assertRedirect('/admin');

        $this->assertAuthenticated();
    }
}
