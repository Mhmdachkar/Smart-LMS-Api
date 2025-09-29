<?php
// File: database/migrations/2025_09_27_164106_create_course_reviews_table.php

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
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating')->unsigned();
            $table->text('review')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('reviewed_at')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->unique(['course_id', 'student_id']);
            $table->index(['course_id', 'rating']);
            $table->index(['student_id', 'reviewed_at']);
            $table->index('is_featured');
            
            // Note: Check constraint removed - validation will be handled at application level
            // Rating should be between 1-5, enforced by API validation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_reviews');
    }
};