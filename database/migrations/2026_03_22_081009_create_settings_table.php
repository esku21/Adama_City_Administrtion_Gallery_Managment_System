<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            // system_status can be: active, inactive, maintenance
            $table->string('system_status')->default('active');
            $table->string('feedback_status')->default('active');
            $table->timestamps();
        });

        // Insert the initial default row so the app doesn't crash on first load
        DB::table('settings')->insert([
            'system_status' => 'active',
            'feedback_status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};