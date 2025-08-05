<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'duration',
        'difficulty_level',
        'category',
        'status',
        'order',
        'prerequisites',
        'objectives',
        'materials_needed',
    ];

    protected $casts = [
        'duration' => 'integer',
        'order' => 'integer',
        'prerequisites' => 'array',
        'objectives' => 'array',
        'materials_needed' => 'array',
    ];

    /**
     * Get the courses for this lesson.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Check if lesson is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get lesson duration in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        return $this->duration ?? 0;
    }

    /**
     * Get lesson duration in hours.
     */
    public function getDurationInHoursAttribute(): float
    {
        return round($this->duration_in_minutes / 60, 2);
    }

    /**
     * Get difficulty level label.
     */
    public function getDifficultyLabelAttribute(): string
    {
        return match($this->difficulty_level) {
            'beginner' => 'Débutant',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé',
            default => 'Non défini'
        };
    }
}
