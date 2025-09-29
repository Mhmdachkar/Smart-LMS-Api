<?php
// File: database/migrations/[TIMESTAMP]_create_enrollments_table_fixed.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount_paid', 8, 2)->default(0.00);
            $table->enum('status', ['active', 'completed', 'dropped', 'suspended'])->default('active');
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'student_id']);
            $table->index(['student_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index('progress_percentage');
            $table->index('enrolled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};