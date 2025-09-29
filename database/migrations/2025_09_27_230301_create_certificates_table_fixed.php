<?php
// File: database/migrations/2025_09_27_164100_create_certificates_table.php

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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->string('certificate_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->index('enrollment_id');
            $table->index('certificate_number');
            $table->index('issued_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};