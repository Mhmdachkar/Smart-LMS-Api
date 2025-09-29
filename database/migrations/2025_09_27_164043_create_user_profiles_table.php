<?php
// File: database/migrations/2025_09_27_164043_create_user_profiles_table.php

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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('language', 5)->default('en');
            $table->json('preferences')->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique('user_id');
            $table->index('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};