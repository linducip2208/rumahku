<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Property;
use Database\Seeders\FacilitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_fills_property_facilities_by_type(): void
    {
        Property::factory()->create(['type' => 'rumah', 'facilities' => null]);
        Property::factory()->create(['type' => 'apartemen', 'facilities' => null]);
        Property::factory()->create(['type' => 'tanah', 'facilities' => null]);
        Property::factory()->create(['type' => 'ruko', 'facilities' => null]);
        Property::factory()->create(['type' => 'villa', 'facilities' => null]);

        $this->seed(FacilitySeeder::class);

        foreach (FacilitySeeder::DEFAULTS as $type => $expected) {
            $property = Property::query()->where('type', $type)->firstOrFail();

            $this->assertIsArray($property->facilities);
            $this->assertNotEmpty($property->facilities);
            $this->assertSame($expected, $property->facilities);
        }
    }

    public function test_seeder_fills_project_facilities_and_unit_types(): void
    {
        Project::factory()->create(['facilities' => null, 'unit_types' => null]);

        $this->seed(FacilitySeeder::class);

        $project = Project::query()->firstOrFail();

        $this->assertNotEmpty($project->facilities);
        $this->assertIsArray($project->unit_types);
        $this->assertCount(2, $project->unit_types);

        foreach ($project->unit_types as $unit) {
            foreach (['nama', 'lt', 'lb', 'kt', 'km', 'harga'] as $key) {
                $this->assertArrayHasKey($key, $unit);
            }
        }
    }

    public function test_seeder_is_idempotent(): void
    {
        Property::factory()->create(['type' => 'rumah', 'facilities' => null]);
        Project::factory()->create(['facilities' => null, 'unit_types' => null]);

        $this->seed(FacilitySeeder::class);

        $propertyCount = Property::query()->count();
        $projectCount = Project::query()->count();

        $this->seed(FacilitySeeder::class);

        $this->assertSame($propertyCount, Property::query()->count());
        $this->assertSame($projectCount, Project::query()->count());
        $this->assertSame(
            FacilitySeeder::DEFAULTS['rumah'],
            Property::query()->firstOrFail()->facilities
        );
    }
}
