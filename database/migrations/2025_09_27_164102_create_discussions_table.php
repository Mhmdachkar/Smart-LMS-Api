<?php
// File: database/migrations/2025_09_27_164102_create_discussions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->foreignId('parent_id')->nullable()->constrained('discussions')->onDelete('cascade');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();

            $table->index(['course_id', 'created_at']);
            $table->index(['lesson_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['parent_id', 'created_at']);
            $table->index(['is_pinned', 'is_resolved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussions');
    }
};

// ================================
