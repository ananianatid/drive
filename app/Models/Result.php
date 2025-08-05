<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'score',
        'max_score',
        'percentage',
        'status',
        'exam_date',
        'notes',
        'feedback',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'exam_date' => 'date',
    ];

    /**
     * Get the student for this result.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the exam for this result.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exams::class);
    }

    /**
     * Check if result is passing.
     */
    public function isPassing(): bool
    {
        return $this->percentage >= 70;
    }

    /**
     * Check if result is excellent.
     */
    public function isExcellent(): bool
    {
        return $this->percentage >= 90;
    }

    /**
     * Check if result is good.
     */
    public function isGood(): bool
    {
        return $this->percentage >= 80 && $this->percentage < 90;
    }

    /**
     * Check if result is average.
     */
    public function isAverage(): bool
    {
        return $this->percentage >= 70 && $this->percentage < 80;
    }

    /**
     * Check if result is failing.
     */
    public function isFailing(): bool
    {
        return $this->percentage < 70;
    }

    /**
     * Get result grade.
     */
    public function getGradeAttribute(): string
    {
        return match(true) {
            $this->isExcellent() => 'A',
            $this->isGood() => 'B',
            $this->isAverage() => 'C',
            $this->isPassing() => 'D',
            default => 'F'
        };
    }

    /**
     * Get result status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'passed' => 'Réussi',
            'failed' => 'Échoué',
            'pending' => 'En attente',
            'incomplete' => 'Incomplet',
            default => 'Non défini'
        };
    }
}
