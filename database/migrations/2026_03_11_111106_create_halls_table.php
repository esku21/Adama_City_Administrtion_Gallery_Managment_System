<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: Capacity has been removed as per requirements.
     */
    public function up(): void
    {
        Schema::create('halls', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // creates created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We disable foreign key checks to prevent #1701 error when 
        // dropping 'halls' while 'booking_hall' still exists.
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('halls');
        Schema::enableForeignKeyConstraints();
    }
};