<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model',
        'year',
        'license_plate',
        'color',
        'type',
        'status',
        'fuel_type',
        'transmission',
        'mileage',
        'last_maintenance',
        'next_maintenance',
        'insurance_expiry',
        'registration_expiry',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'mileage' => 'integer',
        'last_maintenance' => 'date',
        'next_maintenance' => 'date',
        'insurance_expiry' => 'date',
        'registration_expiry' => 'date',
    ];

    /**
     * Get the courses for this vehicule.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Check if vehicule is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if vehicule needs maintenance.
     */
    public function needsMaintenance(): bool
    {
        return $this->next_maintenance && $this->next_maintenance->isPast();
    }

    /**
     * Check if vehicule insurance is expired.
     */
    public function isInsuranceExpired(): bool
    {
        return $this->insurance_expiry && $this->insurance_expiry->isPast();
    }

    /**
     * Check if vehicule registration is expired.
     */
    public function isRegistrationExpired(): bool
    {
        return $this->registration_expiry && $this->registration_expiry->isPast();
    }

    /**
     * Get vehicule full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->name} {$this->model}";
    }
}
