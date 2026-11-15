<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_orders', function (Blueprint $table) {
            $table->unique('payment_reference', 'property_orders_payment_reference_unique');
        });
    }

    public function down(): void
    {
        Schema::table('property_orders', function (Blueprint $table) {
            $table->dropUnique('property_orders_payment_reference_unique');
        });
    }
};
