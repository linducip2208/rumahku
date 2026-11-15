<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * ExtraSeeder — data ekstensi P1: lelang + jasa.
 *
 * Idempotent: aman di-seed ulang via updateOrCreate dengan key unik
 * (auctions: property_id, services: name). Defensif: skip blok bila
 * model/tabel belum ada — TIDAK membuat model/migrasi sendiri.
 */
class ExtraSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAuctions();
        $this->seedServices();
        $this->seedLeads();
    }

    private function seedAuctions(): void
    {
        if (! class_exists(Auction::class) || ! class_exists(Property::class)) {
            $this->command->warn('ExtraSeeder: skip auctions — class Auction/Property belum ada');

            return;
        }
        if (! Schema::hasTable('auctions') || ! Schema::hasTable('properties')) {
            $this->command->warn('ExtraSeeder: skip auctions — tabel auctions/properties belum ada');

            return;
        }

        $propertyIds = Property::query()->orderBy('id')->take(6)->pluck('id')->all();
        if (count($propertyIds) === 0) {
            $this->command->warn('ExtraSeeder: skip auctions — tidak ada property untuk dilink');

            return;
        }

        // Campur open/closed, harga realistis (IDR).
        $statuses = ['open', 'open', 'open', 'closed', 'closed', 'closed'];
        $count = 0;
        foreach ($propertyIds as $i => $propertyId) {
            $status = $statuses[$i % count($statuses)];
            $startPrice = (500 + $i * 250) * 1_000_000;

            Auction::query()->updateOrCreate(
                ['property_id' => $propertyId],
                [
                    'property_id' => $propertyId,
                    'starts_at' => now()->subDays($i + 1),
                    'ends_at' => $status === 'open' ? now()->addDays(7 - ($i % 5)) : now()->subDay(),
                    'start_price' => $startPrice,
                    'current_price' => $startPrice + ($i * 15) * 1_000_000,
                    'status' => $status,
                ]
            );
            $count++;
        }

        $this->command->info("ExtraSeeder: auctions {$count} seeded (open/closed campur).");
    }

    private function seedServices(): void
    {
        if (! class_exists(Service::class)) {
            $this->command->warn('ExtraSeeder: skip services — class App\Models\Service belum ada');

            return;
        }
        if (! Schema::hasTable('services')) {
            $this->command->warn('ExtraSeeder: skip services — tabel services belum ada');

            return;
        }

        $categories = ['notaris', 'inspeksi', 'kpr', 'renovasi'];
        $cities = ['Jakarta Selatan', 'Depok', 'Bekasi', 'Surabaya', 'Bandung', 'Jakarta Timur'];
        $labels = ['notaris' => 'Notaris', 'inspeksi' => 'Inspeksi', 'kpr' => 'KPR', 'renovasi' => 'Renovasi'];

        $count = 0;
        for ($i = 1; $i <= 12; $i++) {
            $category = $categories[($i - 1) % count($categories)];
            $city = $cities[($i - 1) % count($cities)];
            $name = "Jasa {$labels[$category]} Demo {$city} {$i}";

            Service::query()->updateOrCreate(
                ['name' => $name],
                [
                    'name' => $name,
                    'category' => $category,
                    'city' => $city,
                    'phone' => '0812'.str_pad((string) (2000000 + $i * 54321), 7, '0', STR_PAD_LEFT),
                    'description' => "Layanan {$labels[$category]} demo di {$city} — profesional dan terpercaya.",
                ]
            );
            $count++;
        }

        $this->command->info("ExtraSeeder: services {$count} seeded (notaris/inspeksi/kpr/renovasi).");
    }

    private function seedLeads(): void
    {
        if (! class_exists(Lead::class) || ! Schema::hasTable('leads')) {
            $this->command->warn('ExtraSeeder: skip leads — model/tabel Lead belum ada');

            return;
        }

        $demos = [
            ['Budi Santoso', '081234567801', 'Saya tertarik, bisa survei Sabtu pagi?'],
            ['Siti Rahma', '081234567802', 'Apakah harga masih bisa nego?'],
            ['Andi Wijaya', '081234567803', 'Minta info KPR untuk properti ini.'],
            ['Dewi Lestari', '081234567804', 'Kapan bisa lihat unitnya langsung?'],
            ['Rudi Hartono', '081234567805', 'Tolong hubungi saya secepatnya.'],
        ];

        $count = 0;
        $properties = Property::query()->orderBy('id')->take(count($demos))->get();
        foreach ($properties as $i => $property) {
            [$name, $phone, $message] = $demos[$i];
            $exists = Lead::query()
                ->where('property_id', $property->id)
                ->where('phone', $phone)
                ->exists();
            if ($exists) {
                continue;
            }
            Lead::query()->create([
                'property_id' => $property->id,
                'agent_user_id' => $property->user_id,
                'name' => $name,
                'phone' => $phone,
                'message' => $message,
                'source' => 'web',
            ]);
            $count++;
        }

        $this->command->info("ExtraSeeder: leads {$count} seeded (demo, idempotent).");
    }
}
