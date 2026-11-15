<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['notaris', 'inspeksi', 'kpr', 'renovasi']);
            $table->string('city');
            $table->string('phone');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['category', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
