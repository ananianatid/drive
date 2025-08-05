<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'academic_class_id',
        'student_number',
        'enrollment_date',
        'status',
        'license_type',
        'progress_percentage',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'progress_percentage' => 'decimal:2',
    ];

    /**
     * Get the user that owns the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the academic class for this student.
     */
    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class);
    }

    /**
     * Get the presences for this student.
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Get the results for this student.
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get the identity card for this student.
     */
    public function identityCard(): HasMany
    {
        return $this->hasMany(IdentityCard::class);
    }

    /**
     * Check if student is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get student's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? 'N/A';
    }

    /**
     * Get student's email.
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email ?? 'N/A';
    }
}
