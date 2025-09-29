<?php
// File: app/Models/LessonCompletion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'progress_percentage',
        'completion_data',
        'completed_at',
    ];

    protected $casts = [
        'completion_data' => 'array',
        'progress_percentage' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function scopeCompleted($query)
    {
        return $query->where('progress_percentage', 100);
    }

    public function scopeInProgress($query)
    {
        return $query->where('progress_percentage', '>', 0)
                    ->where('progress_percentage', '<', 100);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isCompleted(): bool
    {
        return $this->progress_percentage >= 100;
    }

    public function isInProgress(): bool
    {
        return $this->progress_percentage > 0 && $this->progress_percentage < 100;
    }

    // Relationships
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}