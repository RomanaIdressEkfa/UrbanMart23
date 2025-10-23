<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreorderFieldsToProductStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->integer('preorder_qty')->default(0)->after('qty');
            $table->date('expected_arrival_date')->nullable()->after('preorder_qty');
            $table->integer('preorder_limit')->nullable()->after('expected_arrival_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropColumn('preorder_qty');
            $table->dropColumn('expected_arrival_date');
            $table->dropColumn('preorder_limit');
        });
    }
}