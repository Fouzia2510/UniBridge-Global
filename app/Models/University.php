<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'city',
        'degree_level',
        'subject',
        'tuition_fee',
        'estimated_living_cost',
        'min_cgpa',
        'min_english_score',
        'english_test_type',
        'intake',
        'ranking',
        'scholarship_available',
        'post_study_work',
        'description',

        // Official university links
        'website_url',
        'application_url',

        // University contact details
        'contact_email',
        'contact_phone',
    ];

    protected $casts = [
        'scholarship_available' => 'boolean',
        'post_study_work' => 'boolean',
    ];

    /**
     * Applications submitted for this university.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
