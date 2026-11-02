<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'lesson_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Student who completed the lesson.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    /**
     * Lesson that was completed.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(
            CourseLesson::class,
            'lesson_id'
        );
    }
}