<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table follows the official Laravel 11.x / 12.x requirements.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // Laravel uses a UUID string as the primary key for notifications
            $table->uuid('id')->primary();
            
            // The class name of the notification (e.g., App\Notifications\BookingConfirmed)
            $table->string('type');
            
            // This creates 'notifiable_id' (string) and 'notifiable_type' (string)
            // This allows the notification to belong to a User, Admin, etc.
            $table->morphs('notifiable');
            
            // The actual content of the notification stored as a JSON object
            $table->text('data');
            
            // Becomes a timestamp when read, stays NULL when unread
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};