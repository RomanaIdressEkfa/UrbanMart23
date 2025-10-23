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
        Schema::table('addresses', function (Blueprint $table) {
            // Mohammad Hassan - Remove conflicting city column that conflicts with city relationship
            if (Schema::hasColumn('addresses', 'city')) {
                $table->dropColumn('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Mohammad Hassan - Add city column back if migration is rolled back
            $table->string('city')->nullable()->after('city_id');
        });
    }
};
