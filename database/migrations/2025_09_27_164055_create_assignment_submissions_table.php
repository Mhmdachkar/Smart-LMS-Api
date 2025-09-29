<?php
// File: database/migrations/2025_09_27_164055_create_assignment_submissions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('submission_text')->nullable();
            $table->json('file_attachments')->nullable();
            $table->string('submission_url')->nullable();
            $table->enum('status', ['draft', 'submitted', 'graded', 'returned'])->default('draft');
            $table->integer('points_earned')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['assignment_id', 'student_id']);
            $table->index(['student_id', 'status']);
            $table->index(['graded_by', 'status']);
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};