<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
        'action',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_roles');
    }

    /**
     * Get the users that have this permission directly.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_users');
    }

    /**
     * Check if permission is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get permission display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->display_name ?? $this->name;
    }

    /**
     * Get permission full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->module}.{$this->action}";
    }
}
