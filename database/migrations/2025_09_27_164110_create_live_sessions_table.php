<?php
// File: database/migrations/2025_09_27_164110_create_live_session_attendees_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_session_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->integer('attendance_duration_minutes')->default(0);
            $table->timestamps();

            $table->unique(['live_session_id', 'user_id']);
            $table->index(['user_id', 'joined_at']);
            $table->index('attendance_duration_minutes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_session_attendees');
    }
};

// ================================