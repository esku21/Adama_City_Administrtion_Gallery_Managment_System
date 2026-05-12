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
        Schema::create('announcement_user', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys with Cascade Delete
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
                  
            $table->foreignId('announcement_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Tracking columns for the Visitor Notification UI
            $table->boolean('is_read')->default(false);
            
            $table->timestamps(); // Tracks when the notification was delivered
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_user');
    }
};