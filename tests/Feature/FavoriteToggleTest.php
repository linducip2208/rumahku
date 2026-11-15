<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirected_to_login(): void
    {
        $property = Property::factory()->create(['published_at' => now()]);

        $this->post(route('favorites.toggle', $property))->assertRedirect(route('login'));
    }

    public function test_auth_can_toggle_favorite_on_and_off(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['published_at' => now()]);

        $this->actingAs($user)->post(route('favorites.toggle', $property))->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'property_id' => $property->id,
        ]);

        $this->actingAs($user)->post(route('favorites.toggle', $property))->assertRedirect();
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'property_id' => $property->id,
        ]);
        $this->assertSame(0, Favorite::query()->count());
    }
}
