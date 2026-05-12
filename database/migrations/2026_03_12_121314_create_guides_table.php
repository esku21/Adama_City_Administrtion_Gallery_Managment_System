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
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('gender');
            $table->string('phone');
            $table->string('password');
            
            // Added: Profile image path for the Staff Identity feature
            $table->string('profile_image')->nullable();
            
            // Link to the 'halls' table. 
            // Nullable allows creating guides before assigning them to a station.
            $table->foreignId('hall_id')
                  ->nullable() 
                  ->constrained('halls')
                  ->onDelete('set null'); // Keeps the guide even if a hall is deleted
            
            $table->boolean('is_active')->default(true)->index();
            $table->rememberToken(); // Important for "Remember Me" login functionality
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};