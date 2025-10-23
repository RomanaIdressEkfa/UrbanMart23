<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
     public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            
            // Basic user information
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // User type and status
            $table->enum('user_type', ['customer', 'wholesaler'])->default('customer');
            $table->enum('status', ['active', 'inactive', 'pending', 'rejected'])->default('active');
            
            // Contact information
            $table->string('phone')->nullable();
            
            // Wholesaler specific fields (nullable for regular users)
            $table->string('business_name')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('website_link')->nullable();
            $table->text('address')->nullable();
            $table->string('trade_license')->nullable();
            
            // Email verification
            $table->string('verification_token')->nullable();
            $table->boolean('is_verified')->default(false);
            
            // Profile and preferences
            $table->text('profile_image')->nullable();
            $table->json('preferences')->nullable();
            
            // Timestamps and tokens
            $table->rememberToken();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_type', 'status']);
            $table->index('email_verified_at');
            $table->index('verification_token');
        });
    }
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
