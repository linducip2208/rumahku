<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_ideas', function (Blueprint $table): void {
            $table->id();
            $table->string('feature_key', 64)->index();
            $table->unsignedSmallInteger('slot');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('audience', 64);
            $table->string('need', 128);
            $table->string('mechanism', 64);
            $table->string('impact', 16)->default('medium');
            $table->string('effort', 16)->default('medium');
            $table->string('status', 16)->default('backlog');
            $table->timestamps();

            $table->unique(['feature_key', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_ideas');
    }
};
