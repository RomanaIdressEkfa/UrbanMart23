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
        Schema::table('preorders', function (Blueprint $table) {
            if (!Schema::hasColumn('preorders', 'shipping_name')) {
                $table->string('shipping_name')->nullable();
            }
            if (!Schema::hasColumn('preorders', 'shipping_email')) {
                $table->string('shipping_email')->nullable();
            }
            if (!Schema::hasColumn('preorders', 'shipping_phone')) {
                $table->string('shipping_phone')->nullable();
            }
            if (!Schema::hasColumn('preorders', 'shipping_city')) {
                $table->string('shipping_city')->nullable();
            }
            if (!Schema::hasColumn('preorders', 'shipping_pickup_point')) {
                $table->text('shipping_pickup_point')->nullable();
            }
            if (!Schema::hasColumn('preorders', 'delivery_type')) {
                $table->string('delivery_type')->default('delivery');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_name',
                'shipping_email',
                'shipping_phone',
                'shipping_city',
                'shipping_pickup_point',
                'delivery_type'
            ]);
        });
    }
};