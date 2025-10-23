<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mohammad Hassan - Add delivery_date column for pre-order delivery scheduling
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Mohammad Hassan - Add delivery_date column for pre-order delivery scheduling
            $table->date('delivery_date')->nullable()->after('delivery_status');
            $table->text('delivery_notes')->nullable()->after('delivery_date');
            $table->string('delivery_location')->nullable()->after('delivery_notes');
        });
    }

    /**
     * Reverse the migrations.
     * Mohammad Hassan
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Mohammad Hassan - Remove delivery related columns
            $table->dropColumn([
                'delivery_date',
                'delivery_notes',
                'delivery_location'
            ]);
        });
    }
};