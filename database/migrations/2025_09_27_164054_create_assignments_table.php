<?php
// File: database/migrations/2025_09_27_164054_create_assignments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('instructions');
            $table->integer('max_points')->default(100);
            $table->timestamp('due_date')->nullable();
            $table->json('rubric')->nullable();
            $table->boolean('allow_late_submission')->default(true);
            $table->integer('late_penalty_percentage')->default(10);
            $table->enum('submission_type', ['file', 'text', 'url', 'both'])->default('text');
            $table->timestamps();

            $table->index(['lesson_id', 'due_date']);
            $table->index('submission_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};

// ================================
