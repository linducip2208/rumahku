<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AgentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_register_login_create_listing_with_image(): void
    {
        Storage::fake('public');

        // Register → otomatis role agent + AgentProfile + login.
        $register = $this->post('/register', [
            'name' => 'Agen Test',
            'email' => 'agen@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $register->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'agen@example.com', 'role' => 'agent']);
        $this->assertDatabaseHas('agent_profiles', ['user_id' => User::where('email', 'agen@example.com')->first()->id]);

        // Dashboard memuat statistik.
        $this->get('/dashboard')->assertStatus(200);

        // Buat listing + upload foto palsu.
        $store = $this->post(route('agent.listings.store'), [
            'title' => 'Rumah Dijual di Depok Test',
            'type' => 'rumah',
            'listing_status' => 'jual',
            'price' => 1500000000,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'land_area' => 120,
            'building_area' => 90,
            'certificate' => 'SHM',
            'province' => 'Jawa Barat',
            'city' => 'Depok',
            'district' => 'Beji',
            'address' => 'Jl. Test No. 1, Depok',
            'description' => 'Rumah test yang nyaman.',
            'images' => [UploadedFile::fake()->image('rumah.jpg', 800, 600)],
        ]);

        $store->assertRedirect(route('agent.listings.index'));
        $this->assertDatabaseHas('properties', ['title' => 'Rumah Dijual di Depok Test']);

        $property = Property::where('title', 'Rumah Dijual di Depok Test')->firstOrFail();
        $this->assertNotNull($property->slug);
        $this->assertCount(1, $property->images);
        Storage::disk('public')->assertExists($property->images->first()->path);

        // Halaman listing saya & orders memuat.
        $this->get(route('agent.listings.index'))->assertStatus(200);
        $this->get(route('agent.orders.index'))->assertStatus(200);

        // Logout lalu login ulang via form.
        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();

        $login = $this->post('/login', [
            'email' => 'agen@example.com',
            'password' => 'password123',
        ]);

        $login->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
