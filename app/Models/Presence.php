<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'date',
        'status',
        'arrival_time',
        'departure_time',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'arrival_time' => 'datetime',
        'departure_time' => 'datetime',
    ];

    /**
     * Get the student for this presence.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course for this presence.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if student is present.
     */
    public function isPresent(): bool
    {
        return $this->status === 'present';
    }

    /**
     * Check if student is absent.
     */
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    /**
     * Check if student is late.
     */
    public function isLate(): bool
    {
        return $this->status === 'late';
    }

    /**
     * Get presence duration in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        if (!$this->arrival_time || !$this->departure_time) {
            return 0;
        }

        return $this->arrival_time->diffInMinutes($this->departure_time);
    }

    /**
     * Get presence duration in hours.
     */
    public function getDurationInHoursAttribute(): float
    {
        return round($this->duration_in_minutes / 60, 2);
    }
}
