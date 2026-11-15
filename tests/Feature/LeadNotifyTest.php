<?php

namespace Tests\Feature;

use App\Mail\LeadReceived;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadNotifyTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_lead_store_persists_and_sends_notification(): void
    {
        Mail::fake();

        $property = Property::factory()->create(['published_at' => now()]);

        $response = $this->post(route('leads.store'), [
            'property_id' => $property->id,
            'name' => 'Budi',
            'phone' => '08123456789',
            'message' => 'Saya tertarik, hubungi saya.',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('leads', [
            'property_id' => $property->id,
            'phone' => '08123456789',
        ]);
        Mail::assertSent(LeadReceived::class);
    }

    public function test_api_lead_store_persists_and_sends_notification(): void
    {
        Mail::fake();

        $property = Property::factory()->create(['published_at' => now()]);

        $response = $this->postJson('/api/v1/leads', [
            'property_id' => $property->id,
            'name' => 'Siti',
            'phone' => '+62 812-3456-789',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('leads', [
            'property_id' => $property->id,
            'phone' => '+62 812-3456-789',
        ]);
        Mail::assertSent(LeadReceived::class);
    }
}
