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
        // Renamed to 'feedbacks' (plural) to match Laravel's naming convention
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();

            // 1. The Visitor Link (Optional if you allow guest feedback)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // 2. Identification (Necessary for the Feedback.vue display)
            $table->string('name')->nullable();
            $table->string('email')->nullable();

            // 3. Related IDs (Optional for specific reviews)
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('hall_id')->nullable()->constrained()->onDelete('cascade');

            // 4. Metadata & Content
            $table->string('type')->default('general'); // 'general' or 'hall'
            $table->string('subject')->nullable();
            $table->text('message');
            $table->integer('rating')->default(5); // Star rating (1-5)
            
            // 5. Assets
            $table->string('image_path')->nullable();

            // 6. Timestamps
            // Using standard timestamps() is safer for Laravel's Eloquent
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};