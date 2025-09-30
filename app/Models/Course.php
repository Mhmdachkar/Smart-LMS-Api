<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'instructor_id',
        'category_id',
        'level',
        'price',
        'thumbnail_url',
        'trailer_url',
        'status',
        'duration_minutes',
        'requirements',
        'what_you_learn',
        'is_free',
        'max_students',
        'rating',
        'total_reviews',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'requirements' => 'array',
        'what_you_learn' => 'array',
        'is_free' => 'boolean',
        'rating' => 'decimal:1',
        'total_reviews' => 'integer',
        'duration_minutes' => 'integer',
        'max_students' => 'integer',
        'published_at' => 'datetime',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
