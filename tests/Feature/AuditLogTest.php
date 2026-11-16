<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_property_records_created_log(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $property = Property::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'auditable_type' => Property::class,
            'auditable_id' => $property->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_update_property_records_updated_log_with_changes(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create();

        $this->actingAs($user);

        $property->update(['title' => 'Judul Baru Audit']);

        $log = AuditLog::query()
            ->where('action', 'updated')
            ->where('auditable_type', Property::class)
            ->where('auditable_id', $property->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame($user->id, $log->user_id);
        $this->assertArrayHasKey('title', $log->new_values ?? []);
        $this->assertSame('Judul Baru Audit', $log->new_values['title']);
    }

    public function test_delete_property_records_deleted_log(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create();

        $this->actingAs($user);

        $propertyId = $property->id;
        $property->delete();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'deleted',
            'auditable_type' => Property::class,
            'auditable_id' => $propertyId,
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_public_lead_has_null_user(): void
    {
        $property = Property::factory()->create();

        Lead::create([
            'property_id' => $property->id,
            'name' => 'Tamu Publik',
            'phone' => '08123456789',
            'message' => 'Tanya info.',
            'source' => 'web',
        ]);

        $lead = Lead::query()->latest('id')->first();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'auditable_type' => Lead::class,
            'auditable_id' => $lead->id,
            'user_id' => null,
        ]);
    }
}
