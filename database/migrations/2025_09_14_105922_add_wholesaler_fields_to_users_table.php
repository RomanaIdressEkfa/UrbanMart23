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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['customer', 'seller', 'wholesaler'])->default('customer')->after('email');
            $table->string('facebook_link')->nullable()->after('phone');
            $table->string('website_link')->nullable()->after('facebook_link');
            $table->text('address')->nullable()->after('website_link');
            $table->string('trade_license_number')->nullable()->after('address');
            $table->timestamp('approved_at')->nullable()->after('email_verified_at');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_type', 
                'facebook_link', 
                'website_link', 
                'address', 
                'trade_license_number', 
                'approved_at', 
                'approval_status'
            ]);
        });
    }
};
