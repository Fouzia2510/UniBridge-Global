<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_module_id',
        'title',
        'slug',
        'lesson_type',
        'description',
        'content',
        'video_url',
        'resource_url',
        'duration_minutes',
        'sort_order',
        'is_preview',
        'is_active',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'sort_order' => 'integer',
        'is_preview' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Module this lesson belongs to.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(
            CourseModule::class,
            'course_module_id'
        );
    }

    /**
     * Students who completed this lesson.
     */
    public function completions(): HasMany
    {
        return $this->hasMany(
            LessonCompletion::class,
            'lesson_id'
        );
    }
}