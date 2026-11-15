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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['rumah', 'apartemen', 'tanah', 'ruko', 'villa']);
            $table->enum('listing_status', ['jual', 'sewa']);
            $table->unsignedBigInteger('price');
            $table->unsignedSmallInteger('bedrooms')->nullable();
            $table->unsignedSmallInteger('bathrooms')->nullable();
            $table->float('land_area')->nullable()->comment('Luas tanah (m2)');
            $table->float('building_area')->nullable()->comment('Luas bangunan (m2)');
            $table->string('certificate')->nullable()->comment('SHM/HGB/dll');
            $table->string('province');
            $table->string('city');
            $table->string('district')->nullable();
            $table->text('address');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->longText('description');
            $table->boolean('featured')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['city', 'type', 'listing_status']);
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
