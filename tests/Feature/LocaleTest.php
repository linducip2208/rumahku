<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_defaults_to_indonesian(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Jual');
        $response->assertSee('Masuk');
    }

    public function test_home_english_via_query_param(): void
    {
        $response = $this->get('/?lang=en');

        $response->assertStatus(200);
        $response->assertSee('Sell');
        $response->assertSee('Sign In');
    }

    public function test_invalid_locale_falls_back_to_indonesian(): void
    {
        $response = $this->get('/?lang=xx');

        $response->assertStatus(200);
        $response->assertSee('Jual');
        $response->assertSee('Masuk');
    }

    public function test_locale_persists_in_session(): void
    {
        $this->get('/?lang=en');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sell');
    }

    public function test_register_still_works_with_english_locale(): void
    {
        $response = $this->withSession(['locale' => 'en'])->post(route('register'), [
            'name' => 'Agen EN',
            'email' => 'agen-en@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'agen-en@example.com']);
    }
}
