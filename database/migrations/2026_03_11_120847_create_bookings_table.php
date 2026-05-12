<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // ✅ RELATIONSHIPS
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('hall_id')
                ->constrained('halls')
                ->cascadeOnDelete();

            // ✅ VISITOR INFO
            $table->string('visitor_name');
            $table->string('visitor_category')->default('Normal');
            $table->string('visitor_type')->nullable();
            $table->string('organization_name')->nullable();
            $table->unsignedInteger('number_of_visitors')->default(1);

            // ✅ BOOKING DETAILS
            $table->date('booking_date')->index();
            $table->string('slot_id')->nullable();

            // ✅ FILE UPLOAD (FIXED)
            $table->string('attachment')->nullable(); // 🔥 IMPORTANT: matches model & frontend

            // ✅ STATUS
            $table->string('status')->default('pending')->index();

            // ✅ ANALYTICS
            $table->timestamp('attended_at')->nullable();

            // ✅ SECURITY
            $table->string('qr_token')->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};