<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpr_applications', function (Blueprint $table) {
            $table->index(['user_id', 'property_id', 'status'], 'kpr_applications_user_property_status_index');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->index('agent_user_id', 'leads_agent_user_id_index');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->index(['published_at', 'featured'], 'properties_published_featured_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index('sender_user_id', 'messages_sender_user_id_index');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index('city', 'projects_city_index');
        });
    }

    public function down(): void
    {
        Schema::table('kpr_applications', function (Blueprint $table) {
            $table->dropIndex('kpr_applications_user_property_status_index');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_agent_user_id_index');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex('properties_published_featured_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_sender_user_id_index');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_city_index');
        });
    }
};
