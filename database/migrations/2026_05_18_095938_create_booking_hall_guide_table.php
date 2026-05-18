<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_hall_guide', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            $table->foreignId('hall_id')
                ->constrained('halls')
                ->cascadeOnDelete();

            $table->foreignId('guide_id')
                ->constrained('users') // Assuming guides are stored in your users table
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_hall_guide');
    }
};