<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'duration',
        'description',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Check if period is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get period duration in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        return $this->duration ?? 0;
    }

    /**
     * Get period duration in hours.
     */
    public function getDurationInHoursAttribute(): float
    {
        return round($this->duration_in_minutes / 60, 2);
    }

    /**
     * Get period display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->start_time->format('H:i')} - {$this->end_time->format('H:i')})";
    }
}
