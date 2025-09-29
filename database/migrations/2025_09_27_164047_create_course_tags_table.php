<?php
// File: database/migrations/2025_09_27_164047_create_course_tags_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->default('#007bff');
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_tags');
    }
};

// ================================