<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'specialization',
        'hire_date',
        'status',
        'license_types',
        'experience_years',
        'bio',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'license_types' => 'array',
        'experience_years' => 'integer',
    ];

    /**
     * Get the user that owns the teacher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the academic classes for this teacher.
     */
    public function academicClasses(): HasMany
    {
        return $this->hasMany(AcademicClass::class);
    }

    /**
     * Get the courses for this teacher.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Check if teacher is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get teacher's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? 'N/A';
    }

    /**
     * Get teacher's email.
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email ?? 'N/A';
    }

    /**
     * Get teacher's phone.
     */
    public function getPhoneAttribute(): string
    {
        return $this->user->phone ?? 'N/A';
    }
}
