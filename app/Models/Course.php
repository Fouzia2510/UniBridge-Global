<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'description',
        'mode',
        'duration_weeks',
        'price',
        'instructor_name',
    ];

    protected $casts = [
        'duration_weeks' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * All enrollments for this course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * All modules belonging to this course.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)
            ->orderBy('sort_order');
    }

    /**
     * Only active modules.
     */
    public function activeModules(): HasMany
    {
        return $this->hasMany(CourseModule::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}