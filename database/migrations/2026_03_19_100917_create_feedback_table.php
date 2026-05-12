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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();

            // 1. Relationships
            // user_id: The visitor submitting the feedback
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // booking_id: Optional link to a specific booking
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('cascade');
            
            // hall_id: Link to a specific hall if type is 'hall'
            $table->foreignId('hall_id')->nullable()->constrained()->onDelete('cascade');
            
            // 2. Identification (For guest feedback if needed)
            $table->string('name')->nullable();
            $table->string('email')->nullable();

            // 3. Content & Metadata
            $table->string('type')->default('general'); // 'general' or 'hall'
            $table->string('subject')->nullable();
            $table->text('message');
            $table->integer('rating')->default(5); // 1 to 5 scale

            // 4. Sentiment Status
            // Replaced old 'GoodFeedback'/'BadFeedback' with your new options:
            // 'Satisfaction', 'Unsatisfactory', 'Neutral'
            $table->string('sentiment_status', 50)->nullable()->default('Neutral');
            
            // 5. Assets & Categorization
            $table->string('image_path')->nullable();
            $table->string('topic_tag')->nullable(); // 'Staff', 'Facilities', 'Pricing', etc.

            // 6. Admin Audit Trail
            // verified_by: The Admin/Staff who reviewed the feedback
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resolution_notes')->nullable();

            // 7. Timestamps
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