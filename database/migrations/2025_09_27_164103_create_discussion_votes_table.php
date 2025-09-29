<?php
// File: database/migrations/2025_09_27_164103_create_discussion_votes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussion_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('vote_type', ['upvote', 'downvote']);
            $table->timestamps();

            $table->unique(['discussion_id', 'user_id']);
            $table->index(['discussion_id', 'vote_type']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_votes');
    }
};

// ================================