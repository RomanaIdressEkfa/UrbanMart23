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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_preorder')->default(0)->after('auction_product');
            $table->decimal('preorder_price', 20, 2)->nullable()->after('is_preorder');
            $table->integer('preorder_payment_percentage')->default(50)->after('preorder_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_preorder', 'preorder_price', 'preorder_payment_percentage']);
        });
    }
};
