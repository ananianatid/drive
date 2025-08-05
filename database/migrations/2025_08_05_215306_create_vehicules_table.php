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
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->integer('year');
            $table->string('license_plate')->unique();
            $table->string('color');
            $table->enum('type', ['car', 'motorcycle', 'truck', 'bus'])->default('car');
            $table->enum('status', ['available', 'maintenance', 'out_of_service'])->default('available');
            $table->enum('fuel_type', ['gasoline', 'diesel', 'electric', 'hybrid'])->default('gasoline');
            $table->enum('transmission', ['manual', 'automatic'])->default('manual');
            $table->integer('mileage')->default(0);
            $table->date('last_maintenance')->nullable();
            $table->date('next_maintenance')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('registration_expiry')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};
