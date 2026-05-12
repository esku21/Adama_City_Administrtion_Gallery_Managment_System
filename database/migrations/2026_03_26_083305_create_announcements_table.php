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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            
            // Core Content (Used by AnnouncementController)
            $table->string('title');
            $table->text('content'); // Maps to 'message' in your Vue forms
            
            // Categorization
            $table->string('type')->default('info'); // e.g., 'danger', 'warning', 'info'
            
            // Scheduling & Dates for ACAGMS Gallery logic
            $table->date('target_date')->nullable();
            $table->date('reschedule_date')->nullable();
            
            // Status Control
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};