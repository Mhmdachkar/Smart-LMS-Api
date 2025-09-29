<?php
// File: app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'avatar_url',
        'role',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'status' => $this->status,
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is an instructor.
     */
    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include users with a specific role.
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Relationships

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get courses taught by this instructor.
     */
    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get course enrollments for this student.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * Get assignment submissions by this student.
     */
    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }

    /**
     * Get quiz attempts by this student.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'student_id');
    }

    /**
     * Get discussions posted by this user.
     */
    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    /**
     * Get course reviews by this student.
     */
    public function courseReviews(): HasMany
    {
        return $this->hasMany(CourseReview::class, 'student_id');
    }

    /**
     * Get transactions made by this user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get activity logs for this user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(UserActivityLog::class);
    }

    /**
     * Get lesson completions by this user.
     */
    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    /**
     * Get live sessions hosted by this instructor.
     */
    public function hostedLiveSessions(): HasMany
    {
        return $this->hasMany(LiveSession::class, 'instructor_id');
    }

    /**
     * Get live session attendances by this user.
     */
    public function liveSessionAttendances(): HasMany
    {
        return $this->hasMany(LiveSessionAttendee::class);
    }

    /**
     * Get assignments graded by this instructor.
     */
    public function gradedAssignments(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'graded_by');
    }

    /**
     * Get discussion votes by this user.
     */
    public function discussionVotes(): HasMany
    {
        return $this->hasMany(DiscussionVote::class);
    }
}