<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->unsignedInteger('wa_clicks')->default(0)->after('views');
            $table->unsignedInteger('tel_clicks')->default(0)->after('wa_clicks');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->dropColumn(['wa_clicks', 'tel_clicks']);
        });
    }
};
