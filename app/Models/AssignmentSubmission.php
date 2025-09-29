<?php
// File: app/Models/AssignmentSubmission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'submission_text',
        'file_attachments',
        'submission_url',
        'status',
        'points_earned',
        'feedback',
        'graded_by',
        'submitted_at',
        'graded_at',
    ];

    protected $casts = [
        'file_attachments' => 'array',
        'points_earned' => 'integer',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    public function scopePendingGrading($query)
    {
        return $query->where('status', 'submitted');
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'graded', 'returned']);
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function getGradePercentageAttribute()
    {
        if (!$this->points_earned || !$this->assignment) {
            return null;
        }
        
        return round(($this->points_earned / $this->assignment->max_points) * 100, 1);
    }

    // Relationships
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
