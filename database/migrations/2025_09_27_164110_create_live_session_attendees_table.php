<?php
// File: database/migrations/2025_09_27_164110_create_live_sessions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('meeting_url')->nullable();
            $table->string('meeting_id')->nullable();
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $table->json('recording_data')->nullable();
            $table->timestamps();

            $table->index(['lesson_id', 'status']);
            $table->index(['instructor_id', 'scheduled_at']);
            $table->index(['status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_sessions');
    }
};

// ================================