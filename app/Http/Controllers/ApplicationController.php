<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Display the student's applications.
     */
    public function index(Request $request): View
    {
        $applications = Application::where(
            'student_id',
            $request->user()->id
        )
            ->with('university')
            ->latest()
            ->get();

        return view(
            'student.applications.index',
            compact('applications')
        );
    }

    /**
     * Start a new university application.
     *
     * The old/simple application form is no longer shown.
     *
     * When the student clicks "Apply Now" from a university,
     * an application record is created automatically and the
     * student is taken directly to the detailed Visa form.
     */
    public function create(
        Request $request,
        ?University $university = null
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Get University
        |--------------------------------------------------------------------------
        */

        if (!$university && $request->filled('university_id')) {
            $university = University::findOrFail(
                $request->university_id
            );
        }

        abort_if(
            !$university,
            404,
            'University not found.'
        );

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Active Application
        |--------------------------------------------------------------------------
        */

        $existingApplication = Application::where(
            'student_id',
            $request->user()->id
        )
            ->where(
                'university_id',
                $university->id
            )
            ->whereNotIn('status', [
                'rejected',
                'withdrawn',
            ])
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Existing Application
        |--------------------------------------------------------------------------
        */

        if ($existingApplication) {
            /*
             * If a VisaApplication already exists,
             * open its details page.
             */
            if ($existingApplication->visaApplication()->exists()) {
                $visaApplication = $existingApplication
                    ->visaApplication()
                    ->first();

                return redirect()
                    ->route(
                        'student.visa.show',
                        $visaApplication
                    );
            }

            /*
             * Otherwise continue directly to the detailed
             * Visa information form.
             */
            return redirect()
                ->route(
                    'student.visa.create',
                    $existingApplication
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Automatically Create Application
        |--------------------------------------------------------------------------
        |
        | The student no longer fills the old/simple application form.
        |
        | The university information supplies the initial application
        | values. Detailed personal, passport, financial, immigration
        | and other information is collected in the new Visa form.
        |
        */

        $program = $university->subject
            ?: $university->degree_level
            ?: 'University Program';

        $application = Application::create([
            'student_id' => $request->user()->id,
            'university_id' => $university->id,
            'program' => $program,
            'intake' => $university->intake,
            'personal_statement' => null,
            'notes' => null,
            'status' => 'submitted',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Open Detailed Visa Form
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'student.visa.create',
                $application
            );
    }

    /**
     * Legacy store endpoint.
     *
     * The old/simple application form is no longer part of the
     * student flow. This method is kept so the existing route
     * does not break if something still references it.
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()
            ->route('student.universities.index')
            ->withErrors([
                'application' =>
                    'Please start your application from the University Finder using the Apply Now button.',
            ]);
    }

    /**
     * Display one application.
     */
    public function show(
        Request $request,
        Application $application
    ): View {
        abort_if(
            $application->student_id !== $request->user()->id,
            403
        );

        $application->load('university');

        return view(
            'student.applications.show',
            compact('application')
        );
    }

    /**
     * Allow a student to withdraw an application.
     */
    public function update(
        Request $request,
        Application $application
    ): RedirectResponse {
        abort_if(
            $application->student_id !== $request->user()->id,
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:withdrawn',
            ],
        ]);

        if (in_array($application->status, [
            'accepted',
            'rejected',
            'withdrawn',
        ], true)) {
            return back()->withErrors([
                'status' =>
                    'This application can no longer be withdrawn.',
            ]);
        }

        $application->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('student.applications.index')
            ->with(
                'status',
                'Application withdrawn successfully.'
            );
    }
}
