<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_token_has_expires_at(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()->assertJsonStructure(['user', 'token']);

        $token = $user->tokens()->latest('id')->firstOrFail();
        $this->assertNotNull($token->expires_at);
    }

    public function test_order_store_without_ability_is_forbidden(): void
    {
        $this->seed(PackageSeeder::class);

        $user = User::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->for($user)->create();

        $plainTextToken = $user->createToken('x', ['properties:read'])->plainTextToken;

        $response = $this->withToken($plainTextToken)->postJson('/api/v1/orders', [
            'property_id' => $property->id,
            'package_slug' => 'basic',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('property_orders', [
            'user_id' => $user->id,
            'property_id' => $property->id,
        ]);
    }
}
