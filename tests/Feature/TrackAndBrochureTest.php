<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackAndBrochureTest extends TestCase
{
    use RefreshDatabase;

    private function propertyWithAgent(): Property
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $agent->agentProfile()->create(['phone' => '08123456789', 'wa' => '08123456789']);

        return Property::factory()->create([
            'user_id' => $agent->id,
            'published_at' => now(),
        ]);
    }

    public function test_wa_click_increments_and_redirects(): void
    {
        $property = $this->propertyWithAgent();

        $response = $this->get(route('track.click', [$property, 'wa']));

        $response->assertRedirect();
        $this->assertStringStartsWith('https://wa.me/628123456789', $response->headers->get('Location'));
        $this->assertSame(1, $property->fresh()->wa_clicks);
    }

    public function test_tel_click_increments_and_redirects(): void
    {
        $property = $this->propertyWithAgent();

        $response = $this->get(route('track.click', [$property, 'tel']));

        $response->assertRedirect('tel:628123456789');
        $this->assertSame(1, $property->fresh()->tel_clicks);
    }

    public function test_invalid_channel_returns_404(): void
    {
        $property = $this->propertyWithAgent();

        $this->get('/t/'.$property->id.'/sms')->assertStatus(404);
        $this->assertSame(0, $property->fresh()->wa_clicks);
    }

    public function test_brochure_downloads_pdf(): void
    {
        $property = $this->propertyWithAgent();

        $response = $this->get(route('properties.brochure', $property->slug));

        $response->assertStatus(200);
        $this->assertStringContainsString('pdf', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());
    }

    public function test_brochure_unpublished_returns_404(): void
    {
        $property = Property::factory()->create(['published_at' => null]);

        $this->get(route('properties.brochure', $property->slug))->assertStatus(404);
    }
}
