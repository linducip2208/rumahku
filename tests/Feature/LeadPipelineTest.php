<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadPipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_lead_defaults_to_baru(): void
    {
        $lead = Lead::factory()->create();

        $this->assertSame('baru', $lead->fresh()->status);
    }

    public function test_agent_can_update_own_lead(): void
    {
        $agent = User::factory()->create();
        $lead = Lead::factory()->create(['agent_user_id' => $agent->id]);

        $response = $this->actingAs($agent)->put(route('dashboard.leads.update', $lead), [
            'status' => 'dihubungi',
            'follow_up_at' => '2026-12-01 10:00:00',
            'notes' => 'Sudah dihubungi via telepon.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'status' => 'dihubungi',
            'notes' => 'Sudah dihubungi via telepon.',
        ]);
    }

    public function test_agent_cannot_update_other_agent_lead(): void
    {
        $agent = User::factory()->create();
        $other = User::factory()->create();
        $lead = Lead::factory()->create(['agent_user_id' => $other->id]);

        $this->actingAs($agent)->put(route('dashboard.leads.update', $lead), [
            'status' => 'closing',
        ])->assertForbidden();
    }

    public function test_status_filter_only_shows_matching_leads(): void
    {
        $agent = User::factory()->create();
        $baru = Lead::factory()->create(['agent_user_id' => $agent->id, 'status' => 'baru', 'name' => 'Lead Baru Unik']);
        $closing = Lead::factory()->create(['agent_user_id' => $agent->id, 'status' => 'closing', 'name' => 'Lead Closing Unik']);

        $response = $this->actingAs($agent)->get(route('dashboard.leads.index', ['status' => 'baru']));

        $response->assertOk();
        $response->assertSee($baru->name);
        $response->assertDontSee($closing->name);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard.leads.index'))->assertRedirect(route('login'));
    }
}
