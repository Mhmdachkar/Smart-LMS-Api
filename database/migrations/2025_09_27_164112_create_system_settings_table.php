<?php
// File: database/migrations/2025_09_27_164112_create_system_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('string');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('key');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};