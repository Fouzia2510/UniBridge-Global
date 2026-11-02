<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visa_applications', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Applicant / Personal Information
            |--------------------------------------------------------------------------
            */

            $table->string('full_name')->nullable()->after('student_id');

            $table->string('father_name')->nullable()->after('full_name');

            $table->string('mother_name')->nullable()->after('father_name');

            $table->date('date_of_birth')->nullable()->after('mother_name');

            $table->string('gender', 50)->nullable()->after('date_of_birth');

            $table->string('nationality', 100)->nullable()->after('gender');

            $table->string('country_of_birth', 100)->nullable()->after('nationality');

            $table->string('city_of_birth', 150)->nullable()->after('country_of_birth');

            $table->string('national_id_number', 100)->nullable()->after('city_of_birth');

            $table->string('marital_status', 50)->nullable()->after('national_id_number');


            /*
            |--------------------------------------------------------------------------
            | Contact / Current Address
            |--------------------------------------------------------------------------
            */

            $table->string('phone', 50)->nullable()->after('marital_status');

            $table->string('email')->nullable()->after('phone');

            $table->text('current_address')->nullable()->after('email');

            $table->string('city', 150)->nullable()->after('current_address');

            $table->string('state_province', 150)->nullable()->after('city');

            $table->string('postal_code', 50)->nullable()->after('state_province');

            $table->string('residence_country', 100)->nullable()->after('postal_code');


            /*
            |--------------------------------------------------------------------------
            | Passport Information
            |--------------------------------------------------------------------------
            */

            $table->string('passport_number', 100)->nullable()->after('residence_country');

            $table->string('passport_type', 50)->nullable()->after('passport_number');

            $table->string('passport_issuing_country', 100)
                ->nullable()
                ->after('passport_type');

            $table->string('passport_issuing_authority', 255)
                ->nullable()
                ->after('passport_issuing_country');

            $table->date('passport_issue_date')
                ->nullable()
                ->after('passport_issuing_authority');

            $table->date('passport_expiry_date')
                ->nullable()
                ->after('passport_issue_date');

            $table->boolean('has_previous_passport')
                ->default(false)
                ->after('passport_expiry_date');

            $table->string('previous_passport_number', 100)
                ->nullable()
                ->after('has_previous_passport');

            $table->boolean('has_other_nationality')
                ->default(false)
                ->after('previous_passport_number');

            $table->string('other_nationality', 100)
                ->nullable()
                ->after('has_other_nationality');


            /*
            |--------------------------------------------------------------------------
            | Visa / Immigration Information
            |--------------------------------------------------------------------------
            */

            $table->string('visa_purpose', 255)
                ->nullable()
                ->after('visa_type');

            $table->date('intended_arrival_date')
                ->nullable()
                ->after('visa_purpose');

            $table->date('intended_departure_date')
                ->nullable()
                ->after('intended_arrival_date');

            $table->string('intended_duration', 100)
                ->nullable()
                ->after('intended_departure_date');

            $table->string('current_immigration_status', 150)
                ->nullable()
                ->after('intended_duration');

            $table->boolean('has_previous_visa')
                ->default(false)
                ->after('current_immigration_status');

            $table->text('previous_visa_details')
                ->nullable()
                ->after('has_previous_visa');

            $table->boolean('has_visa_refusal')
                ->default(false)
                ->after('previous_visa_details');

            $table->text('visa_refusal_details')
                ->nullable()
                ->after('has_visa_refusal');

            $table->boolean('has_immigration_violation')
                ->default(false)
                ->after('visa_refusal_details');

            $table->text('immigration_violation_details')
                ->nullable()
                ->after('has_immigration_violation');


            /*
            |--------------------------------------------------------------------------
            | Education Information
            |--------------------------------------------------------------------------
            */

            $table->string('highest_qualification', 255)
                ->nullable()
                ->after('immigration_violation_details');

            $table->string('highest_qualification_institution', 255)
                ->nullable()
                ->after('highest_qualification');

            $table->string('highest_qualification_year', 20)
                ->nullable()
                ->after('highest_qualification_institution');

            $table->text('previous_education_details')
                ->nullable()
                ->after('highest_qualification_year');

            $table->string('english_test_type', 100)
                ->nullable()
                ->after('previous_education_details');

            $table->string('english_test_score', 100)
                ->nullable()
                ->after('english_test_type');


            /*
            |--------------------------------------------------------------------------
            | Employment Information
            |--------------------------------------------------------------------------
            */

            $table->string('employment_status', 100)
                ->nullable()
                ->after('english_test_score');

            $table->string('employer_name', 255)
                ->nullable()
                ->after('employment_status');

            $table->string('job_title', 255)
                ->nullable()
                ->after('employer_name');

            $table->date('employment_start_date')
                ->nullable()
                ->after('job_title');

            $table->text('employment_history')
                ->nullable()
                ->after('employment_start_date');


            /*
            |--------------------------------------------------------------------------
            | Financial / Sponsor Information
            |--------------------------------------------------------------------------
            */

            $table->string('funding_source', 100)
                ->nullable()
                ->after('employment_history');

            $table->string('sponsor_name', 255)
                ->nullable()
                ->after('funding_source');

            $table->string('sponsor_relationship', 150)
                ->nullable()
                ->after('sponsor_name');

            $table->string('sponsor_occupation', 255)
                ->nullable()
                ->after('sponsor_relationship');

            $table->decimal('sponsor_annual_income', 15, 2)
                ->nullable()
                ->after('sponsor_occupation');

            $table->string('sponsor_income_currency', 10)
                ->nullable()
                ->after('sponsor_annual_income');

            $table->decimal('available_funds', 15, 2)
                ->nullable()
                ->after('sponsor_income_currency');

            $table->string('available_funds_currency', 10)
                ->nullable()
                ->after('available_funds');

            $table->boolean('has_scholarship')
                ->default(false)
                ->after('available_funds_currency');

            $table->string('scholarship_name', 255)
                ->nullable()
                ->after('has_scholarship');

            $table->decimal('scholarship_amount', 15, 2)
                ->nullable()
                ->after('scholarship_name');

            $table->string('scholarship_currency', 10)
                ->nullable()
                ->after('scholarship_amount');

            $table->text('financial_details')
                ->nullable()
                ->after('scholarship_currency');


            /*
            |--------------------------------------------------------------------------
            | Travel History
            |--------------------------------------------------------------------------
            */

            $table->boolean('has_international_travel')
                ->default(false)
                ->after('financial_details');

            $table->text('international_travel_history')
                ->nullable()
                ->after('has_international_travel');

            $table->text('countries_previously_visited')
                ->nullable()
                ->after('international_travel_history');


            /*
            |--------------------------------------------------------------------------
            | Accommodation / Arrival Information
            |--------------------------------------------------------------------------
            */

            $table->string('accommodation_type', 150)
                ->nullable()
                ->after('countries_previously_visited');

            $table->text('accommodation_address')
                ->nullable()
                ->after('accommodation_type');

            $table->string('arrival_city', 150)
                ->nullable()
                ->after('accommodation_address');

            $table->string('airport_or_port', 255)
                ->nullable()
                ->after('arrival_city');


            /*
            |--------------------------------------------------------------------------
            | Emergency Contact
            |--------------------------------------------------------------------------
            */

            $table->string('emergency_contact_name', 255)
                ->nullable()
                ->after('airport_or_port');

            $table->string('emergency_contact_relationship', 150)
                ->nullable()
                ->after('emergency_contact_name');

            $table->string('emergency_contact_phone', 50)
                ->nullable()
                ->after('emergency_contact_relationship');

            $table->string('emergency_contact_email')
                ->nullable()
                ->after('emergency_contact_phone');

            $table->text('emergency_contact_address')
                ->nullable()
                ->after('emergency_contact_email');


            /*
            |--------------------------------------------------------------------------
            | Health / Insurance
            |--------------------------------------------------------------------------
            */

            $table->boolean('medical_exam_required')
                ->default(false)
                ->after('emergency_contact_address');

            $table->string('medical_exam_status', 100)
                ->nullable()
                ->after('medical_exam_required');

            $table->boolean('has_health_insurance')
                ->default(false)
                ->after('medical_exam_status');

            $table->string('insurance_provider', 255)
                ->nullable()
                ->after('has_health_insurance');

            $table->string('insurance_policy_number', 150)
                ->nullable()
                ->after('insurance_provider');

            $table->date('insurance_start_date')
                ->nullable()
                ->after('insurance_policy_number');

            $table->date('insurance_end_date')
                ->nullable()
                ->after('insurance_start_date');


            /*
            |--------------------------------------------------------------------------
            | Additional Information
            |--------------------------------------------------------------------------
            */

            $table->text('additional_information')
                ->nullable()
                ->after('insurance_end_date');

            $table->text('student_declaration')
                ->nullable()
                ->after('additional_information');

            $table->boolean('declaration_confirmed')
                ->default(false)
                ->after('student_declaration');

            $table->timestamp('declaration_confirmed_at')
                ->nullable()
                ->after('declaration_confirmed');


            /*
            |--------------------------------------------------------------------------
            | Visa Service / Payment Verification
            |--------------------------------------------------------------------------
            */

            $table->decimal('visa_service_fee', 15, 2)
                ->nullable()
                ->after('declaration_confirmed_at');

            $table->string('visa_service_fee_currency', 10)
                ->default('PKR')
                ->after('visa_service_fee');

            $table->string('payment_status', 50)
                ->default('not_required')
                ->after('visa_service_fee_currency');

            $table->timestamp('payment_verified_at')
                ->nullable()
                ->after('payment_status');


            /*
            |--------------------------------------------------------------------------
            | Final Review / Submission
            |--------------------------------------------------------------------------
            */

            $table->timestamp('ready_for_submission_at')
                ->nullable()
                ->after('payment_verified_at');

            $table->timestamp('final_submitted_at')
                ->nullable()
                ->after('ready_for_submission_at');

            $table->text('submission_notes')
                ->nullable()
                ->after('final_submitted_at');


            /*
            |--------------------------------------------------------------------------
            | Admin Review
            |--------------------------------------------------------------------------
            */

            $table->foreignId('reviewed_by')
                ->nullable()
                ->after('admin_notes')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('visa_applications', function (Blueprint $table) {

            $table->dropForeign(['reviewed_by']);

            $table->dropColumn([
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

                'phone',
                'email',
                'current_address',
                'city',
                'state_province',
                'postal_code',
                'residence_country',

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

                'visa_purpose',
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

                'highest_qualification',
                'highest_qualification_institution',
                'highest_qualification_year',
                'previous_education_details',
                'english_test_type',
                'english_test_score',

                'employment_status',
                'employer_name',
                'job_title',
                'employment_start_date',
                'employment_history',

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

                'has_international_travel',
                'international_travel_history',
                'countries_previously_visited',

                'accommodation_type',
                'accommodation_address',
                'arrival_city',
                'airport_or_port',

                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'emergency_contact_email',
                'emergency_contact_address',

                'medical_exam_required',
                'medical_exam_status',
                'has_health_insurance',
                'insurance_provider',
                'insurance_policy_number',
                'insurance_start_date',
                'insurance_end_date',

                'additional_information',
                'student_declaration',
                'declaration_confirmed',
                'declaration_confirmed_at',

                'visa_service_fee',
                'visa_service_fee_currency',
                'payment_status',
                'payment_verified_at',

                'ready_for_submission_at',
                'final_submitted_at',
                'submission_notes',

                'reviewed_by',
                'reviewed_at',
            ]);
        });
    }
};