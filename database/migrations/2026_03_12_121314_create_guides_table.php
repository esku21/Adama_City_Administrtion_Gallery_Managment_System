<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('gender');
            $table->string('phone');
            $table->string('password');
            
            // FIX: This creates the missing 'hall_id' column 
            // and links it to the 'id' on the 'halls' table
            $table->foreignId('hall_id')->constrained('halls')->onDelete('cascade');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};