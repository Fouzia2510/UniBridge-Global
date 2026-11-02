<?php

namespace App\Http\Controllers;

use App\Models\StudentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentProfileController extends Controller
{
    /**
     * Show the student's profile.
     */
    public function show(Request $request): View
    {
        $profile = $request->user()->studentProfile;

        return view('student.profile', compact('profile'));
    }

    /**
     * Show the form for creating/editing the student's profile.
     */
    public function edit(Request $request): View
    {
        $profile = $request->user()->studentProfile;

        return view('student.profile', compact('profile'));
    }

    /**
     * Show the student's eligibility information.
     */
    public function eligibility(Request $request): View
    {
        $profile = $request->user()->studentProfile;

        /*
         * If the student does not have a profile yet,
         * send a safe empty summary to the Blade view.
         */
        if (!$profile) {
            return view('student.eligibility', [
                'profile' => null,

                'summary' => [
                    'admission_likelihood' => 'Low',
                    'strengths' => [],
                    'weaknesses' => [],
                    'recommendations' => [],
                ],
            ]);
        }

        /*
         * Get the eligibility summary from StudentProfile model.
         */
        if (method_exists($profile, 'eligibilitySummary')) {
            $summary = $profile->eligibilitySummary();
        } else {
            $summary = [
                'admission_likelihood' => 'Low',
                'strengths' => [],
                'weaknesses' => [],
                'recommendations' => [],
            ];
        }

        /*
         * Send the variable as $summary because
         * eligibility.blade.php uses $summary.
         */
        return view('student.eligibility', [
            'profile' => $profile,
            'summary' => $summary,
        ]);
    }

    /**
     * Store or update the student's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'degree' => ['nullable', 'string', 'max:255'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'cgpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'graduation_year' => ['nullable', 'digits:4', 'integer'],
            'english_test' => ['nullable', 'in:IELTS,PTE,Other,None'],
            'english_score' => ['nullable', 'numeric', 'min:0', 'max:9'],
            'preferred_country' => ['nullable', 'string', 'max:255'],
            'preferred_degree_level' => ['nullable', 'string', 'max:255'],
            'preferred_subject' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'preferred_intake' => ['nullable', 'string', 'max:255'],
            'work_experience_years' => ['nullable', 'integer', 'min:0'],
        ]);

        $profile = StudentProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        /*
         * Recalculate readiness score after profile update.
         */
        $profile->update([
            'readiness_score' => $profile->calculateReadinessScore(),
        ]);

        return redirect()
            ->route('student.profile.edit')
            ->with('status', 'Profile updated successfully!');
    }
}