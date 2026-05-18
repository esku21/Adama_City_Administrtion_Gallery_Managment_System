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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // ✅ RELATIONSHIPS (hall_id and guide_id are moved to the pivot table)
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // ✅ VISITOR INFO
            $table->string('visitor_name');
            $table->string('visitor_category')->default('Normal');
            $table->string('visitor_type')->nullable();
            $table->string('organization_name')->nullable();
            $table->unsignedInteger('number_of_visitors')->default(1);

            // ✅ BOOKING DETAILS (Changed to dateTime to support full timestamps safely)
            $table->dateTime('booking_date')->index();
            $table->string('slot_id')->nullable();

            // ✅ FILE UPLOAD
            $table->string('attachment')->nullable(); 

            // ✅ STATUS
            $table->string('status')->default('pending')->index();

            // ✅ ANALYTICS
            $table->timestamp('attended_at')->nullable();

            // ✅ SECURITY
            $table->string('qr_token')->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};