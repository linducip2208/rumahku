<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feature_ideas', function (Blueprint $table): void {
            $table->string('submodule', 64)->nullable()->after('mechanism');
            $table->string('priority', 8)->default('P2')->after('status');
            $table->string('business_value', 32)->nullable()->after('priority');
            $table->string('complexity', 16)->nullable()->after('business_value');
            $table->string('dependencies', 128)->nullable()->after('complexity');
            $table->text('security_notes')->nullable()->after('dependencies');
            $table->string('db_impact', 64)->nullable()->after('security_notes');
            $table->string('api_impact', 64)->nullable()->after('db_impact');
            $table->string('ui_impact', 64)->nullable()->after('api_impact');
            $table->string('testing_notes', 128)->nullable()->after('ui_impact');
        });
    }

    public function down(): void
    {
        Schema::table('feature_ideas', function (Blueprint $table): void {
            $table->dropColumn([
                'submodule', 'priority', 'business_value', 'complexity',
                'dependencies', 'security_notes', 'db_impact', 'api_impact',
                'ui_impact', 'testing_notes',
            ]);
        });
    }
};
