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
        'academic_class_id',
        'lesson_id',
        'teacher_id',
        'vehicule_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'date',
        'duration',
        'status',
        'max_students',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'date' => 'date',
        'duration' => 'integer',
    ];

    /**
     * Get the academic class for this course.
     */
    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class);
    }

    /**
     * Get the lesson for this course.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the teacher for this course.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the vehicule for this course.
     */
    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
    }

    /**
     * Get the presences for this course.
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Check if course is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if course is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get course duration in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        return $this->duration ?? 0;
    }

    /**
     * Get course duration in hours.
     */
    public function getDurationInHoursAttribute(): float
    {
        return round($this->duration_in_minutes / 60, 2);
    }
}
