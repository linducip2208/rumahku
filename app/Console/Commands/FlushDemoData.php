<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FlushDemoData extends Command
{
    protected $signature = 'demo:flush {--force : jalankan tanpa konfirmasi} {--keep-admin-email=admin@rumah123.local : email admin yang dipertahankan}';

    protected $description = 'Hapus SEMUA data demo marketplace (mode data produksi). Paket + admin dipertahankan.';

    public function handle(): int
    {
        if (app()->isProduction() && ! $this->option('force')) {
            $this->error('Ditolak di production tanpa --force.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('Hapus SEMUA data demo? Lanjut?')) {
            return self::FAILURE;
        }

        $keepEmail = (string) $this->option('keep-admin-email');
        $adminId = DB::table('users')->where('email', $keepEmail)->value('id');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach (['audit_logs', 'messages', 'auction_bids', 'auctions', 'services', 'kpr_applications', 'property_orders', 'leads', 'favorites', 'property_images', 'properties', 'projects', 'articles', 'agent_profiles'] as $table) {
            // DELETE (bukan TRUNCATE) agar transaksional & aman di test.
            DB::table($table)->delete();
            $this->info("kosongkan {$table}");
        }

        if ($adminId) {
            DB::table('users')->where('id', '!=', $adminId)->delete();
            $this->info("users: hanya {$keepEmail} dipertahankan");
        } else {
            $this->warn('Admin tidak ditemukan — users dikosongkan. Buat via Filament user atau seeder.');
            DB::table('users')->delete();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Selesai. Jangan lupa: php artisan scout:flush "App\\Models\\Property" && php artisan scout:import "App\\Models\\Property"');

        return self::SUCCESS;
    }
}
