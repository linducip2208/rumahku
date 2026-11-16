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
        Schema::table('leads', function (Blueprint $table) {
            $table->enum('status', ['baru', 'dihubungi', 'survei', 'negosiasi', 'closing', 'gagal'])->default('baru')->after('source');
            $table->dateTime('follow_up_at')->nullable()->after('status');
            $table->text('notes')->nullable()->after('follow_up_at');
            $table->index(['agent_user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['agent_user_id', 'status']);
            $table->dropColumn(['status', 'follow_up_at', 'notes']);
        });
    }
};
