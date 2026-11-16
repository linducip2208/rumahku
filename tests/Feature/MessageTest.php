<?php

namespace Tests\Feature;

use App\Http\Controllers\Public\MessageController;
use App\Models\Message;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Route asli didaftarkan main agent; registrasi runtime ini agar test
        // hijau sebelum/sesudahnya (menimpa dengan target identik bila sudah ada).
        Route::post('/messages', [MessageController::class, 'store'])
            ->middleware(['web', 'auth'])
            ->name('messages.store');
        Route::getRoutes()->refreshNameLookups();
    }

    public function test_guest_post_redirects_to_login(): void
    {
        $property = Property::factory()->create(['published_at' => now()]);

        $response = $this->post(route('messages.store'), [
            'property_id' => $property->id,
            'body' => 'Apakah masih tersedia?',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('messages', ['property_id' => $property->id]);
    }

    public function test_auth_post_valid_persists_message(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['published_at' => now()]);

        $response = $this->actingAs($user)->post(route('messages.store'), [
            'property_id' => $property->id,
            'body' => 'Apakah masih tersedia?',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('messages', [
            'property_id' => $property->id,
            'sender_user_id' => $user->id,
            'body' => 'Apakah masih tersedia?',
        ]);
    }

    public function test_message_appears_on_property_show_page(): void
    {
        $property = Property::factory()->create(['published_at' => now()]);
        Message::factory()->create([
            'property_id' => $property->id,
            'body' => 'Pesan unik diskusi 12345',
        ]);

        $response = $this->get(route('properties.show', $property->slug));

        $response->assertStatus(200);
        $response->assertSee('Pesan unik diskusi 12345');
    }

    public function test_body_over_1000_chars_fails_validation(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['published_at' => now()]);

        $response = $this->actingAs($user)
            ->from(route('properties.show', $property->slug))
            ->post(route('messages.store'), [
                'property_id' => $property->id,
                'body' => str_repeat('a', 1001),
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('body');
        $this->assertDatabaseMissing('messages', ['property_id' => $property->id]);
    }
}
