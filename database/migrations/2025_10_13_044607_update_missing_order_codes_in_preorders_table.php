<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Preorder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update preorders that have null or empty order_code
        $preorders = Preorder::whereNull('order_code')
            ->orWhere('order_code', '')
            ->get();

        foreach ($preorders as $preorder) {
            $preorder->order_code = date('Ymd-His') . rand(10, 99);
            $preorder->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this operation
    }
};
