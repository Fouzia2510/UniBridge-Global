<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'progress_percent',
        'certificate_issued',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'certificate_issued' => 'boolean',
    ];

    /**
     * Student who enrolled in the course.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    /**
     * Course associated with this enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(
            Course::class
        );
    }

    /**
     * Lesson completions belonging to this enrollment's student/course.
     */
    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(
            LessonCompletion::class,
            'student_id',
            'student_id'
        );
    }

    /**
     * Check whether the course is completed.
     */
    public function isCompleted(): bool
    {
        return $this->progress_percent >= 100;
    }

    /**
     * Check whether the student is eligible for a certificate.
     */
    public function isCertificateEligible(): bool
    {
        return $this->progress_percent >= 90;
    }

    /**
     * Get a readable progress status.
     */
    public function getProgressStatusAttribute(): string
    {
        if ($this->progress_percent >= 100) {
            return 'Completed';
        }

        if ($this->progress_percent >= 90) {
            return 'Certificate Eligible';
        }

        if ($this->progress_percent > 0) {
            return 'In Progress';
        }

        return 'Not Started';
    }
}