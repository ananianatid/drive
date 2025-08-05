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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_class_id')->nullable()->constrained()->onDelete('set null');
            $table->string('student_number')->unique();
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'inactive', 'suspended', 'graduated'])->default('active');
            $table->enum('license_type', ['A', 'B', 'C', 'D'])->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
