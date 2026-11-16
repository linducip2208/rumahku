<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AgentListingLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function registerAgent(string $email = 'agen-lifecycle@example.com'): User
    {
        $this->post('/register', [
            'name' => 'Agen Lifecycle',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        return User::where('email', $email)->firstOrFail();
    }

    private function listingPayload(array $over = []): array
    {
        return array_merge([
            'title' => 'Rumah Lifecycle Depok',
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
            'address' => 'Jl. Lifecycle No. 1',
            'description' => 'Rumah uji lifecycle.',
        ], $over);
    }

    public function test_created_listing_appears_on_landing_pages(): void
    {
        Storage::fake('public');
        $this->registerAgent();

        $this->post(route('agent.listings.store'), array_merge($this->listingPayload(), [
            'images' => [UploadedFile::fake()->image('a.jpg', 800, 600)],
        ]))->assertRedirect(route('agent.listings.index'));

        $property = Property::firstOrFail();

        // Landing: latest, katalog, detail.
        $this->get('/')->assertStatus(200)->assertSee('Rumah Lifecycle Depok');
        $this->get('/properti')->assertStatus(200)->assertSee('Rumah Lifecycle Depok');
        $this->get(route('properties.show', $property->slug))->assertStatus(200)->assertSee('Rumah Lifecycle Depok');
    }

    public function test_update_changes_title_and_manages_images(): void
    {
        Storage::fake('public');
        $this->registerAgent();

        $this->post(route('agent.listings.store'), array_merge($this->listingPayload(), [
            'images' => [UploadedFile::fake()->image('a.jpg', 800, 600)],
        ]));

        $property = Property::firstOrFail();
        $oldPath = $property->images->first()->path;
        Storage::disk('public')->assertExists($oldPath);

        $this->put(route('agent.listings.update', $property), array_merge(
            $this->listingPayload(['title' => 'Rumah Lifecycle Updated']),
            [
                'remove_images' => [$property->images->first()->id],
                'images' => [UploadedFile::fake()->image('b.jpg', 800, 600)],
            ]
        ))->assertRedirect(route('agent.listings.index'));

        $property->refresh();
        $this->assertSame('Rumah Lifecycle Updated', $property->title);
        $this->assertCount(1, $property->images);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($property->images->first()->path);

        // Slug ikut judul baru → URL lama 404, URL baru 200.
        $this->get('/properti/'.$property->slug)->assertStatus(200);
    }

    public function test_destroy_removes_db_rows_and_files(): void
    {
        Storage::fake('public');
        $this->registerAgent();

        $this->post(route('agent.listings.store'), array_merge($this->listingPayload(), [
            'images' => [UploadedFile::fake()->image('a.jpg', 800, 600)],
        ]));

        $property = Property::firstOrFail();
        $path = $property->images->first()->path;
        $slug = $property->slug;

        $this->delete(route('agent.listings.destroy', $property))->assertRedirect(route('agent.listings.index'));

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
        $this->assertDatabaseMissing('property_images', ['property_id' => $property->id]);
        Storage::disk('public')->assertMissing($path);
        $this->get('/properti/'.$slug)->assertStatus(404);
        $this->get('/properti')->assertStatus(200)->assertDontSee('Rumah Lifecycle Depok');
    }

    public function test_agent_cannot_modify_other_agents_listing(): void
    {
        Storage::fake('public');
        $this->registerAgent('agen-a@example.com');
        $this->post(route('agent.listings.store'), $this->listingPayload());
        $property = Property::firstOrFail();
        $this->post(route('logout'));

        $this->registerAgent('agen-b@example.com');

        $this->get(route('agent.listings.edit', $property))->assertStatus(403);
        $this->put(route('agent.listings.update', $property), $this->listingPayload(['title' => 'Bajak']))->assertStatus(403);
        $this->delete(route('agent.listings.destroy', $property))->assertStatus(403);
        $this->assertSame('Rumah Lifecycle Depok', $property->fresh()->title);
    }
}
