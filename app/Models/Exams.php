<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exams extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'duration',
        'passing_score',
        'max_score',
        'exam_date',
        'status',
        'instructions',
        'materials_allowed',
        'location',
    ];

    protected $casts = [
        'duration' => 'integer',
        'passing_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'exam_date' => 'date',
        'materials_allowed' => 'array',
    ];

    /**
     * Get the results for this exam.
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class, 'exam_id');
    }

    /**
     * Check if exam is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if exam is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if exam is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->exam_date && $this->exam_date->isFuture();
    }

    /**
     * Get exam duration in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        return $this->duration ?? 0;
    }

    /**
     * Get exam duration in hours.
     */
    public function getDurationInHoursAttribute(): float
    {
        return round($this->duration_in_minutes / 60, 2);
    }

    /**
     * Get exam type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'theoretical' => 'Théorique',
            'practical' => 'Pratique',
            'final' => 'Final',
            'midterm' => 'Mi-parcours',
            default => 'Non défini'
        };
    }
}
