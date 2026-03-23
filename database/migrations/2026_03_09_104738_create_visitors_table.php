<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id(); // Using standard 'id' works best with Laravel relationships
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->unique();
            $table->string('phone_no');
            $table->string('visitorType'); // Student, Researcher, etc.
            $table->string('citizenship')->default('Ethiopian');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('visitors');
    }
};