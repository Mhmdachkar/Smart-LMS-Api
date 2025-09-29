<?php
// File: database/migrations/2025_09_27_164057_create_quiz_questions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay'])->default('multiple_choice');
            $table->json('options')->nullable();
            $table->json('correct_answers');
            $table->integer('points')->default(1);
            $table->text('explanation')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['quiz_id', 'sort_order']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};

// ================================
