<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->json('facilities')->nullable()->after('description');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->text('facilities')->nullable()->after('description');
            $table->json('unit_types')->nullable()->after('facilities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['facilities', 'unit_types']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('facilities');
        });
    }
};
