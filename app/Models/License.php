<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'requirements',
        'validity_period',
        'minimum_age',
        'training_hours',
        'exam_requirements',
        'is_active',
    ];

    protected $casts = [
        'requirements' => 'array',
        'exam_requirements' => 'array',
        'validity_period' => 'integer',
        'minimum_age' => 'integer',
        'training_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Check if license is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get license display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->code})";
    }

    /**
     * Get validity period in years.
     */
    public function getValidityPeriodInYearsAttribute(): int
    {
        return $this->validity_period ?? 0;
    }

    /**
     * Get training hours in days.
     */
    public function getTrainingHoursInDaysAttribute(): int
    {
        return round($this->training_hours / 8);
    }
}
