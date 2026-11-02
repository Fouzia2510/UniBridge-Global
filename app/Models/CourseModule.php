<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'sort_order',
        'estimated_minutes',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'estimated_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Course this module belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Lessons inside this module.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class)
            ->orderBy('sort_order');
    }

    /**
     * Only active lessons.
     */
    public function activeLessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}