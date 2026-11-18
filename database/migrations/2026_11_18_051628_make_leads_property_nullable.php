<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Lead umum (tanpa properti spesifik) tidak lagi ditempel acak ke
     * properti published terbaru — kolom dijadikan nullable agar
     * inquiry umum tersimpan jujur dengan property_id NULL.
     */
    public function up(): void
    {
        Schema::table('leads', function ($table): void {
            $table->dropForeign(['property_id']);
        });
        DB::statement('ALTER TABLE `leads` MODIFY `property_id` BIGINT UNSIGNED NULL');
        Schema::table('leads', function ($table): void {
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('leads')->whereNull('property_id')->delete();
        Schema::table('leads', function ($table): void {
            $table->dropForeign(['property_id']);
        });
        DB::statement('ALTER TABLE `leads` MODIFY `property_id` BIGINT UNSIGNED NOT NULL');
        Schema::table('leads', function ($table): void {
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });
    }
};
