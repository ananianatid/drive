<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Administrator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'department',
        'position',
        'hire_date',
        'status',
        'access_level',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'access_level' => 'integer',
    ];

    /**
     * Get the user that owns the administrator.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if administrator is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if administrator has super admin access.
     */
    public function isSuperAdmin(): bool
    {
        return $this->access_level >= 100;
    }

    /**
     * Check if administrator has admin access.
     */
    public function isAdmin(): bool
    {
        return $this->access_level >= 50;
    }

    /**
     * Check if administrator has manager access.
     */
    public function isManager(): bool
    {
        return $this->access_level >= 25;
    }

    /**
     * Get administrator's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? 'N/A';
    }

    /**
     * Get administrator's email.
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email ?? 'N/A';
    }

    /**
     * Get access level label.
     */
    public function getAccessLevelLabelAttribute(): string
    {
        return match(true) {
            $this->isSuperAdmin() => 'Super Administrateur',
            $this->isAdmin() => 'Administrateur',
            $this->isManager() => 'Gestionnaire',
            default => 'Utilisateur'
        };
    }
}
