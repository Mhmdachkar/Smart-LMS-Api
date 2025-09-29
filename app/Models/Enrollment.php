<?php
// File: app/Models/Enrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'student_id',
        'amount_paid',
        'status',
        'progress_percentage',
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'progress_percentage' => 'integer',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }
}
