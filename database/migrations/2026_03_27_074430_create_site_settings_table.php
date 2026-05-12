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
        // 1. Create the table
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); 
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 2. Insert default settings immediately
        DB::table('site_settings')->insert([
            [
                'key' => 'system_status',
                'value' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'feedback_status',
                'value' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};