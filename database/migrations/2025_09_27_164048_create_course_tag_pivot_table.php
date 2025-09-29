<?php
// File: database/migrations/2025_09_27_164048_create_course_tag_pivot_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_tag_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['course_id', 'course_tag_id']);
            $table->index('course_id');
            $table->index('course_tag_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_tag_pivot');
    }
};

// ================================
