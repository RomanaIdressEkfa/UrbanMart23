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
            // Store selected variant info for regular products flagged as preorder
            if (!Schema::hasColumn('preorders', 'variant_name')) {
                $table->string('variant_name')->nullable()->after('unit_price');
            }
            if (!Schema::hasColumn('preorders', 'stock_id')) {
                $table->unsignedBigInteger('stock_id')->nullable()->after('variant_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            if (Schema::hasColumn('preorders', 'variant_name')) {
                $table->dropColumn('variant_name');
            }
            if (Schema::hasColumn('preorders', 'stock_id')) {
                $table->dropColumn('stock_id');
            }
        });
    }
};