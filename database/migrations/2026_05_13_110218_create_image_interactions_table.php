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
        Schema::create('image_interactions', function (Blueprint $table) {
            $table->id();
            // Link to the image
            $table->foreignId('image_id')->constrained()->onDelete('cascade');
            
            // Store the user's IP address to identify them
            $table->string('ip_address');
            
            // Type of interaction: 'view', 'like', or 'dislike'
            $table->string('type'); 
            
            $table->timestamps();

            /**
             * CRITICAL FIX:
             * This unique constraint prevents the same IP from having 
             * more than one record of the same type for the same image.
             */
            $table->unique(['image_id', 'ip_address', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_interactions');
    }
};