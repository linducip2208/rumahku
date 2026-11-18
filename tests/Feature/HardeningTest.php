<?php

namespace Tests\Feature;

use App\Models\AgentProfile;
use App\Models\Article;
use App\Models\Auction;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyOrder;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_order_dedup_returns_409(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $user->id]);
        $package = Package::query()->firstOrCreate(
            ['slug' => 'basic'],
            ['name' => 'Basic', 'price' => 100000, 'duration_days' => 30, 'max_photos' => 5]
        );
        $token = $user->createToken('t', ['orders:write'])->plainTextToken;

        $payload = ['property_id' => $property->id, 'package_slug' => $package->slug];
        $this->postJson('/api/v1/orders', $payload, ['Authorization' => 'Bearer '.$token])->assertCreated();
        $this->postJson('/api/v1/orders', $payload, ['Authorization' => 'Bearer '.$token])
            ->assertStatus(409)
            ->assertJsonPath('status', 'pending');
    }

    public function test_sold_excluded_from_project_agent_and_sitemap(): void
    {
        $user = User::factory()->create();
        $profile = AgentProfile::factory()->create(['user_id' => $user->id, 'verified_at' => now()]);
        $project = Project::factory()->create(['developer_user_id' => $user->id]);
        $sold = Property::factory()->create([
            'user_id' => $user->id, 'published_at' => now(), 'sold_at' => now(), 'title' => 'Sold Hide Me XYZ',
        ]);

        $this->get('/proyek/'.$project->slug)->assertOk()->assertDontSee('Sold Hide Me XYZ');
        $this->get('/agen/'.$profile->id)->assertOk()->assertDontSee('Sold Hide Me XYZ');
        $this->get('/sitemap.xml')->assertOk()->assertDontSee($sold->slug);
    }

    public function test_sitemap_includes_auction_service_agent(): void
    {
        $property = Property::factory()->create(['published_at' => now(), 'sold_at' => null]);
        Auction::factory()->create(['property_id' => $property->id, 'status' => 'open']);
        $service = Service::factory()->create();
        $user = User::factory()->create();
        $profile = AgentProfile::factory()->create(['user_id' => $user->id, 'verified_at' => now()]);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('/lelang', $xml);
        $this->assertStringContainsString('/jasa/'.$service->id, $xml);
        $this->assertStringContainsString('/agen/'.$profile->id, $xml);
    }

    public function test_property_detail_has_json_ld(): void
    {
        $property = Property::factory()->create(['published_at' => now(), 'sold_at' => null]);
        $this->get('/properti/'.$property->slug)->assertOk()->assertSee('RealEstateListing', false);
    }

    public function test_admin_users_resource_registered(): void
    {
        $this->assertTrue(Route::has('filament.admin.resources.users.index'));
    }

    public function test_login_hint_view_exists(): void
    {
        $this->assertTrue(view()->exists('filament.auth.login-hint'));
        $html = view('filament.auth.login-hint')->render();
        $this->assertStringContainsString('admin@rumah123.local', $html);
    }

    public function test_unknown_type_returns_empty_not_all(): void
    {
        Property::factory()->create(['published_at' => now(), 'sold_at' => null, 'type' => 'rumah', 'title' => 'Rumah Asli XYZ']);
        $this->get('/properti?type=kost')->assertOk()->assertDontSee('Rumah Asli XYZ');
        $this->getJson('/api/v1/properties?type=kost')->assertStatus(422);
    }

    public function test_kontak_routes_before_catchall(): void
    {
        $this->get('/kontak')->assertOk();
        $this->get('/tentang')->assertOk();
    }

    public function test_compare_rejects_sold(): void
    {
        $sold = Property::factory()->create(['published_at' => now(), 'sold_at' => now()]);
        $this->post('/bandingkan/'.$sold->id)->assertNotFound();
        $this->get('/bandingkan?ids='.$sold->id)->assertOk()->assertDontSee($sold->title);
    }

    public function test_favorite_rejects_sold(): void
    {
        $user = User::factory()->create();
        $sold = Property::factory()->create(['user_id' => $user->id, 'published_at' => now(), 'sold_at' => now()]);
        $this->actingAs($user)->post('/favorit/'.$sold->id)->assertSessionHasErrors('property_id');

        $token = $user->createToken('t', ['favorites:write'])->plainTextToken;
        $this->postJson('/api/v1/favorites/toggle', ['property_id' => $sold->id], ['Authorization' => 'Bearer '.$token])
            ->assertStatus(422);
    }

    public function test_api_v1_has_global_throttle(): void
    {
        $route = Route::getRoutes()->getByName('api.v1.properties.index');
        $this->assertNotNull($route);
        $middleware = implode(',', $route->gatherMiddleware());
        $this->assertStringContainsString('throttle', $middleware);
    }

    public function test_unknown_property_slug_returns_404(): void
    {
        $this->get('/properti/slug-yang-tidak-ada-xyz')->assertNotFound();
        $this->getJson('/api/v1/properties/slug-yang-tidak-ada-xyz')->assertNotFound();
    }

    public function test_contact_passthrough_without_captcha_secret(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Siti',
            'phone' => '081234567890',
            'message' => 'Halo, info lebih lanjut.',
        ])->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', ['name' => 'Siti']);
    }

    public function test_soft_deletes_and_restore(): void
    {
        $lead = Lead::factory()->create();
        $lead->delete();
        $this->assertSoftDeleted('leads', ['id' => $lead->id]);
        $lead->restore();
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'deleted_at' => null]);

        $article = Article::factory()->create();
        $article->delete();
        $this->assertSoftDeleted('articles', ['id' => $article->id]);
        $this->assertCount(0, Article::query()->where('id', $article->id)->get());

        $order = PropertyOrder::factory()->create();
        $order->delete();
        $this->assertSoftDeleted('property_orders', ['id' => $order->id]);
        $order->forceDelete();
        $this->assertDatabaseMissing('property_orders', ['id' => $order->id]);
    }

    public function test_guest_order_intent_fulfilled_after_login(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $user->id]);
        $package = Package::query()->firstOrCreate(
            ['slug' => 'basic'],
            ['name' => 'Basic', 'price' => 100000, 'duration_days' => 30, 'max_photos' => 5]
        );

        $this->post(route('orders.intent'), [
            'property_id' => $property->id,
            'package_slug' => $package->slug,
        ])->assertRedirect(route('login'));

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('agent.orders.index'));
        $this->assertDatabaseHas('property_orders', [
            'user_id' => $user->id,
            'property_id' => $property->id,
            'status' => 'pending',
        ]);
    }

    public function test_order_intent_rejected_for_others_property(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $owner->id]);
        $package = Package::query()->firstOrCreate(
            ['slug' => 'basic'],
            ['name' => 'Basic', 'price' => 100000, 'duration_days' => 30, 'max_photos' => 5]
        );

        $this->post(route('orders.intent'), [
            'property_id' => $property->id,
            'package_slug' => $package->slug,
        ])->assertRedirect(route('login'));

        $this->post(route('login'), ['email' => $other->email, 'password' => 'password'])
            ->assertRedirect(route('pricing'));
        $this->assertDatabaseMissing('property_orders', ['property_id' => $property->id]);
    }
}
