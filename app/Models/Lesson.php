<?php
// File: app/Models/Lesson.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'title',
        'description',
        'type',
        'content',
        'duration_minutes',
        'sort_order',
        'is_preview',
        'is_mandatory',
        'resources',
    ];

    protected $casts = [
        'content' => 'array',
        'is_preview' => 'boolean',
        'is_mandatory' => 'boolean',
        'resources' => 'array',
        'duration_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopePreview($query)
    {
        return $query->where('is_preview', true);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getDurationFormattedAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        
        return "{$minutes}m";
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
    }

    public function isAssignment(): bool
    {
        return $this->type === 'assignment';
    }

    public function isLiveSession(): bool
    {
        return $this->type === 'live_session';
    }

    // Relationships
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function liveSession(): HasOne
    {
        return $this->hasOne(LiveSession::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(UserActivityLog::class);
    }
}

// ================================