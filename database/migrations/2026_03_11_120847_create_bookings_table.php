<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            // The Visitor/User who made the booking
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // NEW: The specific Hall assigned to this booking (1 of 9)
            // This is CRITICAL for the Guide's sidebar and dashboard filtering
            $table->foreignId('hall_id')->nullable()->constrained()->onDelete('set null');

            $table->string('visitor_name');      
            $table->string('visitor_category');  // VIP or Normal
            $table->string('visitor_type');      
            
            $table->string('organization_name')->nullable(); 

            $table->integer('number_of_visitors')->default(1); 
            $table->date('booking_date');
            $table->string('slot_id');           // m1, m2, a1, etc.
            
            $table->string('attachment_path')->nullable(); 
            $table->string('status')->default('pending');  // pending, approved, verified
            
            $table->string('qr_token')->unique()->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};