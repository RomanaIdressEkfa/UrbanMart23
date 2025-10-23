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
        Schema::create('shipping_charge_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->string('title')->default('শিপিং চার্জ');
            $table->text('description')->nullable();
            $table->decimal('inside_dhaka_charge', 8, 2)->default(60.00);
            $table->decimal('outside_dhaka_charge', 8, 2)->default(120.00);
            $table->decimal('free_shipping_threshold', 8, 2)->nullable();
            $table->string('delivery_time_inside')->default('১-২ দিন');
            $table->string('delivery_time_outside')->default('৩-৫ দিন');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_charge_settings');
    }
};
