<?php
// File: database/migrations/2025_09_27_164046_create_courses_table.php

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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->decimal('price', 8, 2)->default(0.00);
            $table->string('thumbnail_url')->nullable();
            $table->string('trailer_url')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('duration_minutes')->default(0);
            $table->json('requirements')->nullable();
            $table->json('what_you_learn')->nullable();
            $table->boolean('is_free')->default(true);
            $table->integer('max_students')->nullable();
            $table->decimal('rating', 2, 1)->default(0.0);
            $table->integer('total_reviews')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['instructor_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['status', 'published_at']);
            $table->index('rating');
            $table->index('is_free');
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};