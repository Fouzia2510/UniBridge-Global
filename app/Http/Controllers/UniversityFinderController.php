<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Services\UniversityMatchingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityFinderController extends Controller
{
    public function index(Request $request): View
    {
        $query = University::query();

        /*
         * Manual Filters
         */

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        if ($request->filled('degree_level')) {
            $query->where('degree_level', $request->degree_level);
        }

        if ($request->filled('max_tuition')) {
            $query->where('tuition_fee', '<=', $request->max_tuition);
        }

        if ($request->boolean('scholarship_only')) {
            $query->where('scholarship_available', true);
        }

        /*
         * Get universities
         */

        $universities = $query
            ->orderBy('ranking')
            ->get();

        /*
         * Student profile
         */

        $profile = $request->user()->studentProfile;

        /*
         * Calculate matching details
         */

        foreach ($universities as $university) {

            $score = 0;
            $total = 0;

            $requirements = [];

            /*
             * Country
             */

            if ($profile?->preferred_country) {

                $total += 20;

                $countryMatch =
                    strtolower(trim($profile->preferred_country)) ===
                    strtolower(trim($university->country));

                if ($countryMatch) {
                    $score += 20;
                }

                $requirements[] = [
                    'name' => 'Country',
                    'student' => $profile->preferred_country,
                    'required' => $university->country,
                    'matched' => $countryMatch,
                ];
            }

            /*
             * Degree
             */

            if ($profile?->preferred_degree_level) {

                $total += 20;

                $degreeMatch =
                    strtolower(trim($profile->preferred_degree_level)) ===
                    strtolower(trim($university->degree_level));

                if ($degreeMatch) {
                    $score += 20;
                }

                $requirements[] = [
                    'name' => 'Degree Level',
                    'student' => $profile->preferred_degree_level,
                    'required' => $university->degree_level,
                    'matched' => $degreeMatch,
                ];
            }

            /*
             * Subject
             */

            if ($profile?->preferred_subject) {

                $total += 20;

                $studentSubject = strtolower(trim($profile->preferred_subject));
                $universitySubject = strtolower(trim($university->subject));

                $subjectMatch =
                    str_contains($universitySubject, $studentSubject) ||
                    str_contains($studentSubject, $universitySubject);

                if ($subjectMatch) {
                    $score += 20;
                }

                $requirements[] = [
                    'name' => 'Subject',
                    'student' => $profile->preferred_subject,
                    'required' => $university->subject,
                    'matched' => $subjectMatch,
                ];
            }

            /*
             * CGPA
             */

            if ($profile?->cgpa !== null) {

                $total += 20;

                $cgpaMatch =
                    (float) $profile->cgpa >=
                    (float) $university->min_cgpa;

                if ($cgpaMatch) {
                    $score += 20;
                }

                $requirements[] = [
                    'name' => 'CGPA',
                    'student' => number_format((float) $profile->cgpa, 2),
                    'required' => number_format((float) $university->min_cgpa, 2),
                    'matched' => $cgpaMatch,
                ];
            }

            /*
             * English Score
             */

            if ($profile?->english_score !== null) {

                $total += 10;

                $englishMatch =
                    (float) $profile->english_score >=
                    (float) $university->min_english_score;

                if ($englishMatch) {
                    $score += 10;
                }

                $requirements[] = [
                    'name' => $university->english_test_type ?: 'English Score',
                    'student' => number_format((float) $profile->english_score, 2),
                    'required' => number_format((float) $university->min_english_score, 2),
                    'matched' => $englishMatch,
                ];
            }

            /*
             * Budget
             */

            if ($profile?->budget !== null) {

                $total += 10;

                $budgetMatch =
                    (float) $profile->budget >=
                    (float) $university->tuition_fee;

                if ($budgetMatch) {
                    $score += 10;
                }

                $requirements[] = [
                    'name' => 'Annual Tuition',
                    'student' => '$' . number_format((float) $profile->budget),
                    'required' => '$' . number_format((float) $university->tuition_fee),
                    'matched' => $budgetMatch,
                ];
            }

            /*
             * Match percentage
             */

            $university->match_score = $total > 0
                ? (int) round(($score / $total) * 100)
                : 0;

            /*
             * Count failed requirements
             */

            $failedRequirements = collect($requirements)
                ->where('matched', false)
                ->count();

            /*
             * Eligibility status
             */

            if ($failedRequirements === 0 && $university->match_score >= 80) {

                $university->eligibility_status = 'Eligible';

            } elseif ($university->match_score >= 50) {

                $university->eligibility_status = 'Possible';

            } else {

                $university->eligibility_status = 'Not Eligible';
            }

            /*
             * Save comparison details for Blade
             */

            $university->requirements = $requirements;
        }

        /*
         * Sort by match score
         */

        $universities = $universities
            ->sortByDesc('match_score')
            ->values();

        /*
         * Filter dropdowns
         */

        $countries = University::query()
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        $degreeLevels = University::query()
            ->distinct()
            ->orderBy('degree_level')
            ->pluck('degree_level');

        return view(
            'student.university-finder',
            compact(
                'universities',
                'countries',
                'degreeLevels',
                'profile'
            )
        );
    }

    /*
     * Show university details
     */

    public function show(University $university): View
    {
        return view(
            'student.university-details',
            compact('university')
        );
    }
}