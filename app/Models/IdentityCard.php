<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdentityCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'card_number',
        'issue_date',
        'expiry_date',
        'status',
        'card_type',
        'photo_path',
        'signature_path',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Get the student for this identity card.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Check if card is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if card is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if card is expiring soon (within 30 days).
     */
    public function isExpiringSoon(): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= 30;
    }

    /**
     * Get card status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Actif',
            'expired' => 'Expiré',
            'suspended' => 'Suspendu',
            'lost' => 'Perdu',
            'replaced' => 'Remplacé',
            default => 'Non défini'
        };
    }

    /**
     * Get card type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->card_type) {
            'student' => 'Étudiant',
            'temporary' => 'Temporaire',
            'permanent' => 'Permanent',
            default => 'Non défini'
        };
    }
}
