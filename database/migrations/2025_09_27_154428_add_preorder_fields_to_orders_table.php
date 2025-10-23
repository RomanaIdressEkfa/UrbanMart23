<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mohammad Hassan
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Mohammad Hassan - Add preorder related fields
            $table->boolean('is_preorder')->default(false)->after('payment_status');
            $table->enum('preorder_status', ['pending', 'confirmed', 'product_arrived', 'completed', 'cancelled'])
                  ->nullable()->after('is_preorder');
            $table->decimal('paid_amount', 20, 2)->default(0)->after('preorder_status');
            $table->text('preorder_notes')->nullable()->after('paid_amount');
            $table->timestamp('confirmed_at')->nullable()->after('preorder_notes');
            $table->timestamp('product_arrived_at')->nullable()->after('confirmed_at');
            $table->timestamp('completed_at')->nullable()->after('product_arrived_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     * Mohammad Hassan
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Mohammad Hassan - Remove preorder related fields
            $table->dropColumn([
                'is_preorder',
                'preorder_status',
                'paid_amount',
                'preorder_notes',
                'confirmed_at',
                'product_arrived_at',
                'completed_at',
                'cancelled_at'
            ]);
        });
    }
};
