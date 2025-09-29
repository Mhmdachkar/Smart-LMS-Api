<?php
// File: database/migrations/2025_09_27_164108_create_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('type', ['course_purchase', 'refund', 'subscription'])->default('course_purchase');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->json('payment_data')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('transaction_id');
            $table->index('processed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

// ================================