<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\UserDocument;
use App\Models\VisaApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisaApplicationController extends Controller
{
    /**
     * Student: Show all visa procedures belonging to the logged-in student.
     */
    public function index(Request $request): View
    {
        $visaApplications = VisaApplication::query()
            ->where('student_id', $request->user()->id)
            ->with([
                'application.university',
                'documents',
            ])
            ->latest()
            ->paginate(10);

        return view(
            'student.visa.index',
            compact('visaApplications')
        );
    }

    /**
     * Admin: Show all visa procedures.
     */
    public function adminIndex(): View
    {
        $visaApplications = VisaApplication::query()
            ->with([
                'student',
                'application.university',
                'reviewer',
                'documents',
            ])
            ->latest()
            ->paginate(20);

        return view(
            'admin.visa.index',
            compact('visaApplications')
        );
    }

    /**
     * Student/Admin: Show visa procedure creation page.
     */
    public function create(
        Request $request,
        Application $application
    ): View {
        /*
        |--------------------------------------------------------------------------
        | ADMIN ACCESS
        |--------------------------------------------------------------------------
        */

        if ($request->user()->role === 'admin') {
            abort_if(
                !in_array(
                    $application->status,
                    [
                        'submitted',
                        'accepted',
                        'under_review',
                    ],
                    true
                ),
                403,
                'Visa procedure is not available for this application yet.'
            );

            $application->load('university');

            $existingVisaApplication = $application
                ->visaApplication()
                ->first();

            return view(
                'student.visa.create',
                compact(
                    'application',
                    'existingVisaApplication'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STUDENT ACCESS
        |--------------------------------------------------------------------------
        */

        abort_if(
            $application->student_id !== $request->user()->id,
            403
        );

        /*
         * Payment is intentionally NOT required at this stage.
         * Payment will be handled as a later workflow stage.
         */
        if (config('app.unibridge_visa_payment_gate', true)) {
            abort_if(
                !$application->hasVerifiedPayment(),
                403,
                'Visa procedure is locked until the application payment has been verified by admin.'
            );
        }

        abort_if(
            !in_array(
                $application->status,
                [
                    'submitted',
                    'accepted',
                    'under_review',
                ],
                true
            ),
            403,
            'Visa procedure is not available for this application yet.'
        );

        $application->load('university');

        $existingVisaApplication = $application
            ->visaApplication()
            ->first();

        return view(
            'student.visa.create',
            compact(
                'application',
                'existingVisaApplication'
            )
        );
    }

    /**
     * Student/Admin: Create a visa procedure.
     */
    public function store(
        Request $request,
        Application $application
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | ADMIN CREATE
        |--------------------------------------------------------------------------
        */

        if ($request->user()->role === 'admin') {
            abort_if(
                !in_array(
                    $application->status,
                    [
                        'submitted',
                        'accepted',
                        'under_review',
                    ],
                    true
                ),
                403,
                'Visa procedure is not available for this application yet.'
            );

            $validated = $request->validate([
                'country' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'visa_type' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'student_notes' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],
            ]);

            $existingVisaApplication = $application
                ->visaApplication()
                ->first();

            if ($existingVisaApplication) {
                return redirect()
                    ->route(
                        'admin.visa.show',
                        $existingVisaApplication
                    )
                    ->with(
                        'status',
                        'A visa procedure already exists for this application.'
                    );
            }

            $visaApplication = VisaApplication::create([
                'application_id' => $application->id,
                'student_id' => $application->student_id,
                'country' => $validated['country'],
                'visa_type' => $validated['visa_type'],
                'status' => 'documents_required',
                'student_notes' => $validated['student_notes'] ?? null,
            ]);

            return redirect()
                ->route(
                    'admin.visa.show',
                    $visaApplication
                )
                ->with(
                    'status',
                    'Visa procedure created successfully.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | STUDENT ACCESS
        |--------------------------------------------------------------------------
        */

        abort_if(
            $application->student_id !== $request->user()->id,
            403
        );

        /*
         * Payment is intentionally not required while
         * building/testing the Visa procedure.
         */
        if (
            config('app.unibridge_visa_payment_gate', true) &&
            !$application->hasVerifiedPayment()
        ) {
            return redirect()
                ->back()
                ->withErrors([
                    'visa' =>
                        'Visa procedure is locked until the application payment has been verified by admin.',
                ]);
        }

        if (!in_array(
            $application->status,
            [
                'submitted',
                'accepted',
                'under_review',
            ],
            true
        )) {
            return redirect()
                ->back()
                ->withErrors([
                    'visa' =>
                        'Visa procedure is not available for this application yet.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | DETAILED VISA FORM VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Basic Visa Information
            |--------------------------------------------------------------------------
            */

            'country' => [
                'required',
                'string',
                'max:100',
            ],

            'visa_type' => [
                'required',
                'string',
                'max:150',
            ],

            'visa_purpose' => [
                'required',
                'string',
                'max:255',
            ],

            'intended_arrival_date' => [
                'nullable',
                'date',
            ],

            'intended_departure_date' => [
                'nullable',
                'date',
                'after_or_equal:intended_arrival_date',
            ],

            'intended_duration' => [
                'nullable',
                'string',
                'max:100',
            ],

            'current_immigration_status' => [
                'nullable',
                'string',
                'max:150',
            ],

            /*
            |--------------------------------------------------------------------------
            | Personal Information
            |--------------------------------------------------------------------------
            */

            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'father_name' => [
                'required',
                'string',
                'max:255',
            ],

            'mother_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'date_of_birth' => [
                'required',
                'date',
            ],

            'gender' => [
                'required',
                'string',
                'max:50',
            ],

            'nationality' => [
                'required',
                'string',
                'max:100',
            ],

            'country_of_birth' => [
                'required',
                'string',
                'max:100',
            ],

            'city_of_birth' => [
                'required',
                'string',
                'max:150',
            ],

            'national_id_number' => [
                'required',
                'string',
                'max:100',
            ],

            'marital_status' => [
                'required',
                'string',
                'max:50',
            ],

            /*
            |--------------------------------------------------------------------------
            | Contact Information
            |--------------------------------------------------------------------------
            */

            'phone' => [
                'required',
                'string',
                'max:50',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'current_address' => [
                'required',
                'string',
                'max:1000',
            ],

            'city' => [
                'required',
                'string',
                'max:150',
            ],

            'state_province' => [
                'nullable',
                'string',
                'max:150',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:30',
            ],

            'residence_country' => [
                'required',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Passport
            |--------------------------------------------------------------------------
            */

            'passport_number' => [
                'required',
                'string',
                'max:100',
            ],

            'passport_type' => [
                'required',
                'string',
                'max:100',
            ],

            'passport_issuing_country' => [
                'required',
                'string',
                'max:100',
            ],

            'passport_issuing_authority' => [
                'nullable',
                'string',
                'max:255',
            ],

            'passport_issue_date' => [
                'required',
                'date',
            ],

            'passport_expiry_date' => [
                'required',
                'date',
                'after:passport_issue_date',
            ],

            'has_previous_passport' => [
                'nullable',
                'boolean',
            ],

            'previous_passport_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'has_other_nationality' => [
                'nullable',
                'boolean',
            ],

            'other_nationality' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Immigration / Visa History
            |--------------------------------------------------------------------------
            */

            'has_previous_visa' => [
                'nullable',
                'boolean',
            ],

            'previous_visa_details' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'has_visa_refusal' => [
                'nullable',
                'boolean',
            ],

            'visa_refusal_details' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'has_immigration_violation' => [
                'nullable',
                'boolean',
            ],

            'immigration_violation_details' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Education
            |--------------------------------------------------------------------------
            */

            'highest_qualification' => [
                'nullable',
                'string',
                'max:255',
            ],

            'highest_qualification_institution' => [
                'nullable',
                'string',
                'max:255',
            ],

            'highest_qualification_year' => [
                'nullable',
                'integer',
                'min:1950',
                'max:2100',
            ],

            'previous_education_details' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'english_test_type' => [
                'nullable',
                'string',
                'max:100',
            ],

            'english_test_score' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Employment
            |--------------------------------------------------------------------------
            */

            'employment_status' => [
                'nullable',
                'string',
                'max:100',
            ],

            'employer_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'employment_start_date' => [
                'nullable',
                'date',
            ],

            'employment_history' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Financial / Sponsor
            |--------------------------------------------------------------------------
            */

            'funding_source' => [
                'required',
                'string',
                'max:150',
            ],

            'sponsor_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sponsor_relationship' => [
                'nullable',
                'string',
                'max:100',
            ],

            'sponsor_occupation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sponsor_annual_income' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'sponsor_income_currency' => [
                'nullable',
                'string',
                'max:10',
            ],

            'available_funds' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'available_funds_currency' => [
                'nullable',
                'string',
                'max:10',
            ],

            'has_scholarship' => [
                'nullable',
                'boolean',
            ],

            'scholarship_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'scholarship_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'scholarship_currency' => [
                'nullable',
                'string',
                'max:10',
            ],

            'financial_details' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Travel History
            |--------------------------------------------------------------------------
            */

            'has_international_travel' => [
                'nullable',
                'boolean',
            ],

            'international_travel_history' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'countries_previously_visited' => [
                'nullable',
                'string',
                'max:2000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Accommodation / Arrival
            |--------------------------------------------------------------------------
            */

            'accommodation_type' => [
                'nullable',
                'string',
                'max:150',
            ],

            'accommodation_address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'arrival_city' => [
                'nullable',
                'string',
                'max:150',
            ],

            'airport_or_port' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Emergency Contact
            |--------------------------------------------------------------------------
            */

            'emergency_contact_name' => [
                'required',
                'string',
                'max:255',
            ],

            'emergency_contact_relationship' => [
                'required',
                'string',
                'max:100',
            ],

            'emergency_contact_phone' => [
                'required',
                'string',
                'max:50',
            ],

            'emergency_contact_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'emergency_contact_address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Health / Insurance
            |--------------------------------------------------------------------------
            */

            'medical_exam_required' => [
                'nullable',
                'boolean',
            ],

            'medical_exam_status' => [
                'nullable',
                'string',
                'max:100',
            ],

            'has_health_insurance' => [
                'nullable',
                'boolean',
            ],

            'insurance_provider' => [
                'nullable',
                'string',
                'max:255',
            ],

            'insurance_policy_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'insurance_start_date' => [
                'nullable',
                'date',
            ],

            'insurance_end_date' => [
                'nullable',
                'date',
                'after_or_equal:insurance_start_date',
            ],

            /*
            |--------------------------------------------------------------------------
            | Additional Information
            |--------------------------------------------------------------------------
            */

            'additional_information' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'student_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Document Uploads
            |--------------------------------------------------------------------------
            */

            'documents' => [
                'required',
                'array',
            ],

            'documents.passport_copy' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.passport_photo' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],

            'documents.national_id' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.offer_letter' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.academic_certificates' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.transcripts' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.english_test' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.financial_proof' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.sponsor_letter' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.scholarship_letter' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.medical_documents' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.insurance_documents' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.previous_visa' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'documents.additional_document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            /*
            |--------------------------------------------------------------------------
            | Declaration
            |--------------------------------------------------------------------------
            */

            'student_declaration' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'declaration_confirmed' => [
                'required',
                'accepted',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Visa Procedure
        |--------------------------------------------------------------------------
        */

        $existingVisaApplication = $application
            ->visaApplication()
            ->first();

        if ($existingVisaApplication) {
            return redirect()
                ->route(
                    'student.visa.show',
                    $existingVisaApplication
                )
                ->with(
                    'status',
                    'Your visa procedure already exists.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Visa Application
        |--------------------------------------------------------------------------
        */

        $visaData = $validated;

        /*
         * Uploaded files belong to the documents table,
         * not the visa_applications table.
         */
        unset($visaData['documents']);

        $visaData['application_id'] = $application->id;
        $visaData['student_id'] = $request->user()->id;
        $visaData['status'] = 'documents_required';

        /*
        |--------------------------------------------------------------------------
        | Boolean Defaults
        |--------------------------------------------------------------------------
        */

        $booleanFields = [
            'has_previous_passport',
            'has_other_nationality',
            'has_previous_visa',
            'has_visa_refusal',
            'has_immigration_violation',
            'has_scholarship',
            'has_international_travel',
            'medical_exam_required',
            'has_health_insurance',
        ];

        foreach ($booleanFields as $field) {
            $visaData[$field] = (bool) (
                $validated[$field] ?? false
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Declaration Timestamp
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['declaration_confirmed'])) {
            $visaData['declaration_confirmed'] = true;
            $visaData['declaration_confirmed_at'] = now();
        }

        /*
        |--------------------------------------------------------------------------
        | Create Visa Application Record
        |--------------------------------------------------------------------------
        */

        $visaApplication = VisaApplication::create($visaData);

        /*
        |--------------------------------------------------------------------------
        | Upload Documents
        |--------------------------------------------------------------------------
        */

        $documentDefinitions = [

            'passport_copy' => [
                'type' => 'visa_passport',
                'title' => 'Passport Copy',
            ],

            'passport_photo' => [
                'type' => 'visa_passport_photo',
                'title' => 'Passport-size Photograph',
            ],

            'national_id' => [
                'type' => 'visa_national_id',
                'title' => 'National ID / CNIC',
            ],

            'offer_letter' => [
                'type' => 'visa_offer_letter',
                'title' => 'University Offer / Admission Letter',
            ],

            'academic_certificates' => [
                'type' => 'visa_academic_certificate',
                'title' => 'Academic Certificates / Degrees',
            ],

            'transcripts' => [
                'type' => 'visa_transcript',
                'title' => 'Academic Transcripts / Marksheets',
            ],

            'english_test' => [
                'type' => 'visa_english_test',
                'title' => 'English Test Result',
            ],

            'financial_proof' => [
                'type' => 'visa_financial_proof',
                'title' => 'Financial / Bank Statement',
            ],

            'sponsor_letter' => [
                'type' => 'visa_sponsor_letter',
                'title' => 'Sponsor / Financial Sponsorship Evidence',
            ],

            'scholarship_letter' => [
                'type' => 'visa_scholarship',
                'title' => 'Scholarship Letter',
            ],

            'medical_documents' => [
                'type' => 'visa_medical',
                'title' => 'Medical / Health Documents',
            ],

            'insurance_documents' => [
                'type' => 'visa_insurance',
                'title' => 'Health Insurance Evidence',
            ],

            'previous_visa' => [
                'type' => 'visa_previous_visa',
                'title' => 'Previous Visa / Immigration Documents',
            ],

            'additional_document' => [
                'type' => 'visa_additional',
                'title' => 'Additional Supporting Document',
            ],
        ];

        foreach ($documentDefinitions as $field => $definition) {
            if (!$request->hasFile("documents.{$field}")) {
                continue;
            }

            $file = $request->file("documents.{$field}");

            if (!$file->isValid()) {
                continue;
            }

            $storagePath = $file->store(
                "visa-documents/{$visaApplication->id}",
                'public'
            );

            UserDocument::create([
                'user_id' => $request->user()->id,
                'visa_application_id' => $visaApplication->id,
                'document_type' => $definition['type'],
                'title' => $definition['title'],
                'original_name' => $file->getClientOriginalName(),
                'storage_path' => $storagePath,
                'verification_status' => 'pending',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Final Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'student.visa.show',
                $visaApplication
            )
            ->with(
                'status',
                'Visa application information and uploaded documents were saved successfully. Please complete the required document review before final submission.'
            );
    }

    /**
     * Student: Show one visa procedure.
     */
    public function show(
        Request $request,
        VisaApplication $visaApplication
    ): View {
        abort_if(
            $visaApplication->student_id !== $request->user()->id,
            403
        );

        $visaApplication->load([
            'application.university',
            'application.payments',
            'documents',
        ]);

        return view(
            'student.visa.show',
            compact('visaApplication')
        );
    }

    /**
     * Admin: Show one visa procedure.
     */
    public function adminShow(
        VisaApplication $visaApplication
    ): View {
        $visaApplication->load([
            'student',
            'reviewer',
            'application.university',
            'application.payments',
            'documents',
        ]);

        return view(
            'admin.visa.show',
            compact('visaApplication')
        );
    }

    /**
     * Student: Submit visa procedure for review.
     *
     * Payment is intentionally not checked here.
     * Payment will be handled as the second-last workflow stage.
     */
    public function submit(
        Request $request,
        VisaApplication $visaApplication
    ): RedirectResponse {
        abort_if(
            $visaApplication->student_id !== $request->user()->id,
            403
        );

        $visaApplication->load([
            'application',
            'documents',
        ]);

        if (!$visaApplication->hasConfirmedDeclaration()) {
            return redirect()
                ->back()
                ->withErrors([
                    'visa' =>
                        'Please confirm the visa declaration before final submission.',
                ]);
        }

        if (!in_array(
            $visaApplication->status,
            [
                'documents_required',
                'documents_submitted',
                'ready_to_apply',
            ],
            true
        )) {
            return redirect()
                ->back()
                ->withErrors([
                    'visa' =>
                        'This visa procedure cannot be submitted in its current status.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Required Document Check
        |--------------------------------------------------------------------------
        */

        $requiredDocumentTypes = [
            'visa_passport',
            'visa_passport_photo',
            'visa_national_id',
            'visa_offer_letter',
            'visa_academic_certificate',
            'visa_transcript',
            'visa_financial_proof',
        ];

        $uploadedDocumentTypes = $visaApplication
            ->documents
            ->pluck('document_type')
            ->unique()
            ->values()
            ->all();

        $missingDocuments = array_diff(
            $requiredDocumentTypes,
            $uploadedDocumentTypes
        );

        if (!empty($missingDocuments)) {
            return redirect()
                ->back()
                ->withErrors([
                    'visa' =>
                        'Please upload all required visa documents before final submission.',
                ]);
        }

        $visaApplication->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'final_submitted_at' => now(),
            'ready_for_submission_at' => now(),
        ]);

        return redirect()
            ->route(
                'student.visa.show',
                $visaApplication
            )
            ->with(
                'status',
                'Visa application and required documents submitted successfully and are now ready for review.'
            );
    }

    /**
     * Admin: Update visa procedure status and notes.
     */
    public function adminSubmit(
        Request $request,
        VisaApplication $visaApplication
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:not_started,documents_required,documents_submitted,under_review,ready_to_apply,submitted,processing,approved,rejected',
            ],

            'admin_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $data = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ];

        if (
            $validated['status'] === 'submitted' &&
            !$visaApplication->submitted_at
        ) {
            $data['submitted_at'] = now();
        }

        if (
            in_array(
                $validated['status'],
                [
                    'approved',
                    'rejected',
                ],
                true
            )
        ) {
            $data['decision_at'] = now();
        }

        $visaApplication->update($data);

        return redirect()
            ->route(
                'admin.visa.show',
                $visaApplication
            )
            ->with(
                'status',
                'Visa procedure updated successfully.'
            );
    }
}