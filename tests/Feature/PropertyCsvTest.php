<?php

namespace Tests\Feature;

use App\Console\Commands\ExportProperties;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyCsvTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var list<string>
     */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_export_creates_file_with_correct_header(): void
    {
        $user = User::factory()->create();
        Property::factory()->for($user)->count(2)->create();

        $path = storage_path('app/exports/test-properti-export.csv');
        $this->tempFiles[] = $path;
        @unlink($path);

        $this->artisan('properties:export', ['--file' => $path])
            ->assertSuccessful();

        $this->assertFileExists($path);

        $handle = fopen($path, 'r');
        $this->assertNotFalse($handle);

        /** @var list<string>|false $header */
        $header = fgetcsv($handle);
        $rows = 0;
        while (fgetcsv($handle) !== false) {
            $rows++;
        }
        fclose($handle);

        $this->assertSame(ExportProperties::COLUMNS, $header);
        $this->assertSame(2, $rows);
    }

    public function test_import_saves_valid_rows_and_reports_errors(): void
    {
        $user = User::factory()->create(['email' => 'agen@example.com']);
        $file = $this->makeCsvFile($this->sampleRows());

        $this->artisan('properties:import', ['file' => $file, '--user' => 'agen@example.com'])
            ->assertSuccessful();

        $this->assertSame(2, Property::count());
        $this->assertTrue(Property::where('slug', 'rumah-dijual-di-beji-depok')->exists());
        $this->assertTrue(Property::where('slug', 'villa-tes-unik')->exists());

        // Baris invalid (type tidak dikenal) dilewati, baris draft tanpa published_at tersimpan.
        $draft = Property::where('slug', 'rumah-dijual-di-beji-depok')->firstOrFail();
        $this->assertNull($draft->published_at);
        $this->assertSame($user->id, $draft->user_id);
    }

    public function test_import_dry_run_does_not_persist(): void
    {
        User::factory()->create(['email' => 'agen@example.com']);
        $file = $this->makeCsvFile($this->sampleRows());

        $this->artisan('properties:import', ['file' => $file, '--user' => 'agen@example.com', '--dry-run' => true])
            ->assertFailed();

        $this->assertSame(0, Property::count());
    }

    public function test_import_is_idempotent(): void
    {
        User::factory()->create(['email' => 'agen@example.com']);
        $file = $this->makeCsvFile($this->sampleRows());

        $this->artisan('properties:import', ['file' => $file, '--user' => 'agen@example.com'])
            ->assertSuccessful();
        $this->artisan('properties:import', ['file' => $file, '--user' => 'agen@example.com'])
            ->assertSuccessful();

        $this->assertSame(2, Property::count());
    }

    /**
     * @return list<array<string, string>>
     */
    private function sampleRows(): array
    {
        return [
            [
                'title' => 'Rumah Dijual di Beji Depok',
                'slug' => '',
                'type' => 'rumah',
                'listing_status' => 'jual',
                'price' => '1500000000',
                'bedrooms' => '3',
                'bathrooms' => '2',
                'land_area' => '120',
                'building_area' => '90',
                'certificate' => 'SHM',
                'province' => 'Jawa Barat',
                'city' => 'Depok',
                'district' => 'Beji',
                'address' => 'Jl. Kenanga No. 10, Beji, Depok',
                'lat' => '-6.3742',
                'lng' => '106.8294',
                'description' => 'Rumah nyaman dekat kampus.',
                'featured' => '0',
                'published_at' => '',
            ],
            [
                'title' => 'Villa Disewakan di Bandung',
                'slug' => 'villa-tes-unik',
                'type' => 'villa',
                'listing_status' => 'sewa',
                'price' => '150000000',
                'bedrooms' => '4',
                'bathrooms' => '3',
                'land_area' => '300',
                'building_area' => '200',
                'certificate' => 'SHM',
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Coblong',
                'address' => 'Jl. Mawar No. 5, Coblong, Bandung',
                'lat' => '-6.8853',
                'lng' => '107.6131',
                'description' => 'Villa sejuk untuk liburan.',
                'featured' => '1',
                'published_at' => '2026-01-10 10:00:00',
            ],
            [
                'title' => 'Properti Invalid',
                'slug' => 'properti-invalid',
                'type' => 'istana',
                'listing_status' => 'jual',
                'price' => '-100',
                'bedrooms' => '',
                'bathrooms' => '',
                'land_area' => '',
                'building_area' => '',
                'certificate' => '',
                'province' => '',
                'city' => '',
                'district' => '',
                'address' => '',
                'lat' => '',
                'lng' => '',
                'description' => '',
                'featured' => '0',
                'published_at' => '',
            ],
        ];
    }

    /**
     * @param  list<array<string, string>>  $rows
     */
    private function makeCsvFile(array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'properti-').'.csv';
        $this->assertNotFalse($path);
        $this->tempFiles[] = $path;

        $handle = fopen($path, 'w');
        $this->assertNotFalse($handle);

        fputcsv($handle, ExportProperties::COLUMNS);
        foreach ($rows as $row) {
            $line = [];
            foreach (ExportProperties::COLUMNS as $column) {
                $line[] = $row[$column] ?? '';
            }
            fputcsv($handle, $line);
        }
        fclose($handle);

        return $path;
    }
}
