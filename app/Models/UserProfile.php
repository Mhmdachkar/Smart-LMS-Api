<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'timezone',
        'language',
        'preferences',
        'social_links',
    ];

    protected $casts = [
        'preferences' => 'array',
        'social_links' => 'array',
    ];
}
