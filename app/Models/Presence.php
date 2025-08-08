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
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
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
        return 0;
    }

    protected static function booted(): void
    {
        static::saving(function (Presence $presence) {
            if (empty($presence->date) && $presence->course_id) {
                $course = \App\Models\Course::find($presence->course_id);
                if ($course) {
                    $presence->date = $course->date;
                }
            }
        });
    }

    /**
     * Get presence duration in hours.
     */
    public function getDurationInHoursAttribute(): float
    {
        return round($this->duration_in_minutes / 60, 2);
    }
}
