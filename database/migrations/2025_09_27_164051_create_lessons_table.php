<?php
// File: database/migrations/2025_09_27_164051_create_lessons_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'text', 'quiz', 'assignment', 'live_session'])->default('video');
            $table->json('content')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_mandatory')->default(true);
            $table->json('resources')->nullable();
            $table->timestamps();

            $table->index(['section_id', 'sort_order']);
            $table->index('type');
            $table->index(['is_preview', 'is_mandatory']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};

// ================================
