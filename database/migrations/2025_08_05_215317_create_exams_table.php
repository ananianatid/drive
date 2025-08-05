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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['theoretical', 'practical', 'final', 'midterm'])->default('theoretical');
            $table->integer('duration')->default(60); // en minutes
            $table->decimal('passing_score', 5, 2)->default(70.00);
            $table->decimal('max_score', 5, 2)->default(100.00);
            $table->date('exam_date');
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->text('instructions')->nullable();
            $table->json('materials_allowed')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
