<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CaptchaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::forget('captcha.turnstile_secret_key');
        Setting::forget('captcha.turnstile_site_key');
    }

    protected function tearDown(): void
    {
        Setting::forget('captcha.turnstile_secret_key');
        Setting::forget('captcha.turnstile_site_key');

        parent::tearDown();
    }

    public function test_lead_store_passes_when_secret_empty(): void
    {
        $property = Property::factory()->create(['published_at' => now()]);

        $response = $this->post(route('leads.store'), [
            'property_id' => $property->id,
            'name' => 'Budi',
            'phone' => '08123456789',
            'message' => 'Saya tertarik.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('leads', [
            'property_id' => $property->id,
            'phone' => '08123456789',
        ]);
    }

    public function test_lead_store_fails_captcha_when_verification_fails(): void
    {
        Setting::set('captcha.turnstile_secret_key', 'dummy-secret', 'captcha');

        Http::fake([
            'challenges.cloudflare.com/*' => Http::response(['success' => false], 200),
        ]);

        $property = Property::factory()->create(['published_at' => now()]);

        $response = $this->from(route('properties.show', $property->slug))->post(route('leads.store'), [
            'property_id' => $property->id,
            'name' => 'Budi',
            'phone' => '08123456789',
            'message' => 'Saya tertarik.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('captcha');
        $this->assertDatabaseMissing('leads', ['property_id' => $property->id]);
    }

    public function test_register_fails_captcha_when_verification_fails(): void
    {
        Setting::set('captcha.turnstile_secret_key', 'dummy-secret', 'captcha');

        Http::fake([
            'challenges.cloudflare.com/*' => Http::response(['success' => false], 200),
        ]);

        $response = $this->from(route('register'))->post(route('register'), [
            'name' => 'Agen Baru',
            'email' => 'agen@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('captcha');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'agen@example.com']);
    }

    public function test_register_passes_when_secret_empty(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Agen Baru',
            'email' => 'agen@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'agen@example.com']);
    }
}
