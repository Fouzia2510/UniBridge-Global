<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisaApplication extends Model
{
    use HasFactory;

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Relationships
        |--------------------------------------------------------------------------
        */

        'application_id',
        'student_id',

        /*
        |--------------------------------------------------------------------------
        | Basic Visa Information
        |--------------------------------------------------------------------------
        */

        'country',
        'visa_type',
        'visa_purpose',

        /*
        |--------------------------------------------------------------------------
        | Applicant / Personal Information
        |--------------------------------------------------------------------------
        */

        'full_name',
        'father_name',
        'mother_name',
        'date_of_birth',
        'gender',
        'nationality',
        'country_of_birth',
        'city_of_birth',
        'national_id_number',
        'marital_status',

        /*
        |--------------------------------------------------------------------------
        | Contact / Address
        |--------------------------------------------------------------------------
        */

        'phone',
        'email',
        'current_address',
        'city',
        'state_province',
        'postal_code',
        'residence_country',

        /*
        |--------------------------------------------------------------------------
        | Passport
        |--------------------------------------------------------------------------
        */

        'passport_number',
        'passport_type',
        'passport_issuing_country',
        'passport_issuing_authority',
        'passport_issue_date',
        'passport_expiry_date',
        'has_previous_passport',
        'previous_passport_number',
        'has_other_nationality',
        'other_nationality',

        /*
        |--------------------------------------------------------------------------
        | Travel / Immigration
        |--------------------------------------------------------------------------
        */

        'intended_arrival_date',
        'intended_departure_date',
        'intended_duration',
        'current_immigration_status',
        'has_previous_visa',
        'previous_visa_details',
        'has_visa_refusal',
        'visa_refusal_details',
        'has_immigration_violation',
        'immigration_violation_details',

        /*
        |--------------------------------------------------------------------------
        | Education
        |--------------------------------------------------------------------------
        */

        'highest_qualification',
        'highest_qualification_institution',
        'highest_qualification_year',
        'previous_education_details',
        'english_test_type',
        'english_test_score',

        /*
        |--------------------------------------------------------------------------
        | Employment
        |--------------------------------------------------------------------------
        */

        'employment_status',
        'employer_name',
        'job_title',
        'employment_start_date',
        'employment_history',

        /*
        |--------------------------------------------------------------------------
        | Financial / Sponsor
        |--------------------------------------------------------------------------
        */

        'funding_source',
        'sponsor_name',
        'sponsor_relationship',
        'sponsor_occupation',
        'sponsor_annual_income',
        'sponsor_income_currency',
        'available_funds',
        'available_funds_currency',
        'has_scholarship',
        'scholarship_name',
        'scholarship_amount',
        'scholarship_currency',
        'financial_details',

        /*
        |--------------------------------------------------------------------------
        | Travel History
        |--------------------------------------------------------------------------
        */

        'has_international_travel',
        'international_travel_history',
        'countries_previously_visited',

        /*
        |--------------------------------------------------------------------------
        | Accommodation / Arrival
        |--------------------------------------------------------------------------
        */

        'accommodation_type',
        'accommodation_address',
        'arrival_city',
        'airport_or_port',

        /*
        |--------------------------------------------------------------------------
        | Emergency Contact
        |--------------------------------------------------------------------------
        */

        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'emergency_contact_email',
        'emergency_contact_address',

        /*
        |--------------------------------------------------------------------------
        | Health / Insurance
        |--------------------------------------------------------------------------
        */

        'medical_exam_required',
        'medical_exam_status',
        'has_health_insurance',
        'insurance_provider',
        'insurance_policy_number',
        'insurance_start_date',
        'insurance_end_date',

        /*
        |--------------------------------------------------------------------------
        | Additional Information / Declaration
        |--------------------------------------------------------------------------
        */

        'additional_information',
        'student_declaration',
        'declaration_confirmed',
        'declaration_confirmed_at',

        /*
        |--------------------------------------------------------------------------
        | Visa Service Payment
        |--------------------------------------------------------------------------
        */

        'visa_service_fee',
        'visa_service_fee_currency',
        'payment_status',
        'payment_verified_at',

        /*
        |--------------------------------------------------------------------------
        | Submission
        |--------------------------------------------------------------------------
        */

        'ready_for_submission_at',
        'final_submitted_at',
        'submission_notes',

        /*
        |--------------------------------------------------------------------------
        | Status / Admin Review
        |--------------------------------------------------------------------------
        */

        'status',
        'student_notes',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
        'decision_at',
    ];

    protected $casts = [

        /*
        |--------------------------------------------------------------------------
        | Dates
        |--------------------------------------------------------------------------
        */

        'date_of_birth' => 'date',
        'passport_issue_date' => 'date',
        'passport_expiry_date' => 'date',
        'employment_start_date' => 'date',
        'intended_arrival_date' => 'date',
        'intended_departure_date' => 'date',
        'insurance_start_date' => 'date',
        'insurance_end_date' => 'date',

        /*
        |--------------------------------------------------------------------------
        | Boolean Fields
        |--------------------------------------------------------------------------
        */

        'has_previous_passport' => 'boolean',
        'has_other_nationality' => 'boolean',
        'has_previous_visa' => 'boolean',
        'has_visa_refusal' => 'boolean',
        'has_immigration_violation' => 'boolean',
        'has_scholarship' => 'boolean',
        'has_international_travel' => 'boolean',
        'medical_exam_required' => 'boolean',
        'has_health_insurance' => 'boolean',
        'declaration_confirmed' => 'boolean',

        /*
        |--------------------------------------------------------------------------
        | Money
        |--------------------------------------------------------------------------
        */

        'sponsor_annual_income' => 'decimal:2',
        'available_funds' => 'decimal:2',
        'scholarship_amount' => 'decimal:2',
        'visa_service_fee' => 'decimal:2',

        /*
        |--------------------------------------------------------------------------
        | Timestamps
        |--------------------------------------------------------------------------
        */

        'declaration_confirmed_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'ready_for_submission_at' => 'datetime',
        'final_submitted_at' => 'datetime',
        'submitted_at' => 'datetime',
        'decision_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | University Application
    |--------------------------------------------------------------------------
    */

    public function application(): BelongsTo
    {
        return $this->belongsTo(
            Application::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Student
    |--------------------------------------------------------------------------
    */

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Reviewer
    |--------------------------------------------------------------------------
    */

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Visa Documents
    |--------------------------------------------------------------------------
    */

    public function documents(): HasMany
    {
        return $this->hasMany(
            UserDocument::class,
            'visa_application_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isSubmitted(): bool
    {
        return in_array(
            $this->status,
            [
                'submitted',
                'processing',
                'approved',
                'rejected',
            ],
            true
        );
    }

    public function isDraft(): bool
    {
        return $this->status === 'not_started';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function isReadyToApply(): bool
    {
        return $this->status === 'ready_to_apply';
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Helpers
    |--------------------------------------------------------------------------
    */

    public function isPaymentVerified(): bool
    {
        return $this->payment_status === 'verified';
    }

    /*
    |--------------------------------------------------------------------------
    | Declaration Helper
    |--------------------------------------------------------------------------
    */

    public function hasConfirmedDeclaration(): bool
    {
        return (bool) $this->declaration_confirmed;
    }
}