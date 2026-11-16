<?php

namespace Database\Seeders;

use App\Models\AgentProfile;
use App\Models\Article;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * DemoSeeder — data demo marketplace Rumah123.
 *
 * Defensif: setiap model opsional (AgentProfile, Property, PropertyImage,
 * Project, Article, Lead, Favorite) dicek via class_exists + Schema::hasTable
 * sebelum dipakai. Jika model/migrasi belum ada, blok terkait di-skip dengan
 * pesan — TIDAK membuat model sendiri.
 *
 * Kredensial lokal: admin@rumah123.local / agen1..10@rumah123.local dengan
 * password default `password`, atau override via env DEMO_PASSWORD.
 * Jangan commit password real.
 */
class DemoSeeder extends Seeder
{
    /** @var array<string, array<int, string>> */
    private array $skipped = [];

    public function run(): void
    {
        $password = Hash::make((string) env('DEMO_PASSWORD', 'password'));
        $hasRoleColumn = Schema::hasTable('users') && Schema::hasColumn('users', 'role');

        // 1 admin.
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@rumah123.local'],
            ['name' => 'Admin Rumah123', 'email' => 'admin@rumah123.local', 'password' => $password]
        );
        $this->applyRole($admin, 'admin', $hasRoleColumn);

        // 10 agen (user role agent + AgentProfile bila tersedia).
        $agents = [];
        for ($i = 1; $i <= 10; $i++) {
            $agent = User::query()->firstOrCreate(
                ['email' => "agen{$i}@rumah123.local"],
                ['name' => "Agen Demo {$i}", 'email' => "agen{$i}@rumah123.local", 'password' => $password]
            );
            $this->applyRole($agent, 'agent', $hasRoleColumn);
            $agents[] = $agent;
        }

        $this->seedAgentProfiles($agents);
        $propertyIds = $this->seedProperties($agents);
        $this->seedPropertyImages($propertyIds);
        $this->seedProjects($agents);
        $this->seedArticles();

        foreach ($this->skipped as $model => $reasons) {
            foreach ($reasons as $reason) {
                $this->command->warn("DemoSeeder: skip {$model} — {$reason}");
            }
        }

        $this->command->info('DemoSeeder: admin 1, agen '.count($agents).', properties '.count($propertyIds).' seeded.');
    }

    private function applyRole(User $user, string $role, bool $hasRoleColumn): void
    {
        if ($hasRoleColumn && $user->getAttribute('role') !== $role) {
            $user->forceFill(['role' => $role])->save();
        }
    }

    private function skip(string $model, string $reason): void
    {
        $this->skipped[$model][] = $reason;
    }

    /** @param  array<int, User>  $agents */
    private function seedAgentProfiles(array $agents): void
    {
        if (! class_exists(AgentProfile::class)) {
            $this->skip('AgentProfile', 'class App\Models\AgentProfile belum ada');

            return;
        }
        if (! Schema::hasTable('agent_profiles')) {
            $this->skip('AgentProfile', 'tabel agent_profiles belum ada');

            return;
        }
        $columns = Schema::getColumnListing('agent_profiles');
        foreach ($agents as $i => $agent) {
            $phone = '0812'.str_pad((string) (1000000 + $i * 12345), 7, '0', STR_PAD_LEFT);
            $candidate = [
                'user_id' => $agent->id,
                'agency' => 'Rumah123 Agency '.chr(65 + ($i % 5)),
                'phone' => $phone,
                'wa' => $phone,
                'photo' => 'avatars/avatar-'.(($i % 10) + 1).'.png',
                'verified_at' => now(),
            ];
            DB::table('agent_profiles')->updateOrInsert(
                ['user_id' => $agent->id],
                $this->row($columns, $candidate)
            );
        }
    }

    /**
     * @param  array<int, User>  $agents
     * @return array<int, int|string> Inserted property IDs.
     */
    private function seedProperties(array $agents): array
    {
        if (! class_exists(Property::class)) {
            $this->skip('Property', 'class App\Models\Property belum ada');

            return [];
        }
        if (! Schema::hasTable('properties')) {
            $this->skip('Property', 'tabel properties belum ada');

            return [];
        }
        $columns = Schema::getColumnListing('properties');
        $cities = $this->cities();
        $types = ['rumah', 'apartemen', 'tanah', 'ruko', 'villa'];
        $ids = [];

        for ($i = 1; $i <= 50; $i++) {
            $type = $types[($i - 1) % count($types)];
            $city = $cities[($i - 1) % count($cities)];
            $isSale = $i % 3 !== 0; // mix jual/sewa ± 2:1
            $listingWord = $isSale ? 'Dijual' : 'Disewakan';
            $title = "{$listingWord} ".ucfirst($type)." di {$city} #{$i}";
            $candidate = [
                'user_id' => $agents[($i - 1) % count($agents)]->id,
                'title' => $title,
                'slug' => Str::slug($title)."-{$i}",
                'type' => $type,
                'listing_status' => $isSale ? 'jual' : 'sewa',
                'price' => $this->realisticPrice($type, $isSale, $i),
                'bedrooms' => $type === 'tanah' ? null : 2 + ($i % 4),
                'bathrooms' => $type === 'tanah' ? null : 1 + ($i % 3),
                'land_area' => 60 + ($i * 7) % 440,
                'building_area' => $type === 'tanah' ? null : 45 + ($i * 5) % 255,
                'certificate' => ['SHM', 'HGB', 'SHM', 'AJB'][$i % 4],
                'province' => $this->provinceOf($city),
                'city' => $city,
                'district' => 'Kecamatan Demo '.(($i % 6) + 1),
                'address' => "Jl. Demo No. {$i}, {$city}",
                'lat' => -6.2 - ($i * 0.001),
                'lng' => 106.8 + ($i * 0.001),
                'description' => "Listing demo {$type} {$listingWord} di {$city}. Lokasi strategis, cocok untuk hunian maupun investasi.",
                'featured' => $i % 10 === 0,
                'views' => $i * 3,
                'published_at' => now(), // published
            ];
            $ids[] = DB::table('properties')->insertGetId($this->row($columns, $candidate));
        }

        return $ids;
    }

    /** @param  array<int, int|string>  $propertyIds */
    private function seedPropertyImages(array $propertyIds): void
    {
        if (empty($propertyIds)) {
            $this->skip('PropertyImage', 'tidak ada property (Property belum di-seed)');

            return;
        }
        if (! class_exists(PropertyImage::class)) {
            $this->skip('PropertyImage', 'class App\Models\PropertyImage belum ada');

            return;
        }
        if (! Schema::hasTable('property_images')) {
            $this->skip('PropertyImage', 'tabel property_images belum ada');

            return;
        }
        $columns = Schema::getColumnListing('property_images');
        foreach ($propertyIds as $index => $propertyId) {
            $count = 2 + ($index % 2); // 2-3 images per property
            for ($j = 1; $j <= $count; $j++) {
                $candidate = [
                    'property_id' => $propertyId,
                    'path' => "properties/local-{$propertyId}-{$j}.jpg",
                    'sort' => $j,
                    'order' => $j,
                    'sort_order' => $j,
                    'position' => $j,
                ];
                DB::table('property_images')->insert($this->row($columns, $candidate));
            }
        }
    }

    /** @param  array<int, User>  $agents */
    private function seedProjects(array $agents): void
    {
        if (! class_exists(Project::class)) {
            $this->skip('Project', 'class App\Models\Project belum ada');

            return;
        }
        if (! Schema::hasTable('projects')) {
            $this->skip('Project', 'tabel projects belum ada');

            return;
        }
        $columns = Schema::getColumnListing('projects');
        $names = ['Griya Asri', 'Pesona Hijau', 'Citra Residence', 'Nusa Indah Park', 'Wastu Living'];
        foreach ($names as $i => $name) {
            $city = $this->cities()[$i % 5];
            $fullName = "{$name} {$city}";
            $candidate = [
                'developer_user_id' => $agents[$i % count($agents)]->id,
                'name' => $fullName,
                'slug' => Str::slug($fullName),
                'city' => $city,
                'price_from' => (500 + $i * 250) * 1_000_000,
                'description' => "Proyek perumahan demo {$fullName}.",
            ];
            DB::table('projects')->updateOrInsert(
                ['slug' => Str::slug($fullName)],
                $this->row($columns, $candidate)
            );
        }
    }

    private function seedArticles(): void
    {
        if (! class_exists(Article::class)) {
            $this->skip('Article', 'class App\Models\Article belum ada');

            return;
        }
        if (! Schema::hasTable('articles')) {
            $this->skip('Article', 'tabel articles belum ada');

            return;
        }
        $columns = Schema::getColumnListing('articles');
        $cities = $this->cities();
        $templates = ['Jual Rumah di {kota}', 'Sewa Apartemen di {kota}', 'Jual Tanah di {kota}', 'Panduan KPR Rumah di {kota}', 'Harga Pasaran Properti {kota} 2026'];
        $n = 0;
        foreach ($templates as $t) {
            foreach (array_slice($cities, 0, 2) as $city) {
                if ($n >= 10) {
                    break 2;
                }
                $title = str_replace('{kota}', $city, $t);
                $candidate = [
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'body' => "Artikel pSEO demo: {$title}. Panduan harga, tips survei, dan daftar listing terkait di {$city}.",
                    'content' => "Artikel pSEO demo: {$title}. Panduan harga, tips survei, dan daftar listing terkait di {$city}.",
                    'city' => $city,
                    'type' => str_contains($t, 'Apartemen') ? 'apartemen' : (str_contains($t, 'Tanah') ? 'tanah' : 'rumah'),
                ];
                DB::table('articles')->updateOrInsert(
                    ['slug' => Str::slug($title)],
                    $this->row($columns, $candidate)
                );
                $n++;
            }
        }
    }

    /** @return array<int, string> */
    private function cities(): array
    {
        return ['Jakarta Selatan', 'Depok', 'Bekasi', 'Surabaya', 'Bandung'];
    }

    private function provinceOf(string $city): string
    {
        return match ($city) {
            'Jakarta Selatan' => 'DKI Jakarta',
            'Surabaya' => 'Jawa Timur',
            default => 'Jawa Barat',
        };
    }

    private function realisticPrice(string $type, bool $isSale, int $seed): int
    {
        if (! $isSale) {
            // Sewa tahunan IDR 20jt–300jt.
            return (20 + (($seed * 13) % 280)) * 1_000_000;
        }

        return match ($type) {
            'apartemen' => (500 + (($seed * 37) % 2500)) * 1_000_000,
            'tanah' => (300 + (($seed * 41) % 1700)) * 1_000_000,
            'ruko' => (1000 + (($seed * 53) % 7000)) * 1_000_000,
            'villa' => (2000 + (($seed * 61) % 13000)) * 1_000_000,
            default => (800 + (($seed * 47) % 4200)) * 1_000_000, // rumah 800jt–5M
        };
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<string, mixed>  $candidate
     * @return array<string, mixed>
     */
    private function row(array $columns, array $candidate): array
    {
        $row = [];
        foreach ($candidate as $key => $value) {
            if (in_array($key, $columns, true)) {
                $row[$key] = $value;
            }
        }
        if (in_array('created_at', $columns, true) && ! array_key_exists('created_at', $row)) {
            $row['created_at'] = now();
        }
        if (in_array('updated_at', $columns, true) && ! array_key_exists('updated_at', $row)) {
            $row['updated_at'] = now();
        }

        return $row;
    }
}
