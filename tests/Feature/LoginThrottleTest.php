<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_login_has_rate_limit_headers(): void
    {
        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'secret12345',
        ]);

        $response->assertHeader('X-RateLimit-Limit', 10);
    }

    public function test_web_register_has_rate_limit_headers(): void
    {
        $response = $this->post('/register', [
            'name' => 'Throttle Check',
            'email' => 'throttle-check@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertHeader('X-RateLimit-Limit', 10);
    }

    public function test_api_login_has_rate_limit_headers(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nobody@example.com',
            'password' => 'secret12345',
        ]);

        $response->assertHeader('X-RateLimit-Limit', 10);
    }

    public function test_api_register_has_rate_limit_headers(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Throttle Api',
            'email' => 'throttle-api@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated();
        $response->assertHeader('X-RateLimit-Limit', 10);
    }
}
