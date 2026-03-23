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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Name Fields
            $table->string('firstName');
            $table->string('lastName');
            // Virtual column to help with search/display
            $table->string('name')->virtualAs('concat(firstName, " ", lastName)'); 
            
            // Authentication & Contact
            // UNIQUE() is critical here to prevent duplicate email entries
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('password');
            
            // Visitor Specific Fields
            $table->string('visitorType')->default('Individual'); 
            $table->string('citizenship')->default('Ethiopian');
            
            // Access Control
            $table->string('role')->default('visitor'); // visitor, guide, admin
            
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};