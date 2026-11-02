<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Services\UniversityMatchingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityMatchController extends Controller
{
    public function index(
        Request $request,
        UniversityMatchingService $matchingService
    ): View {
        $user = $request->user();
        $profile = $user->studentProfile;

        if (!$profile) {
            return view('student.university-match', [
                'profile' => null,
                'matches' => collect(),
            ]);
        }

        $universities = University::query()
            ->when(
                $profile->preferred_country,
                function ($query) use ($profile) {
                    $query->where(
                        'country',
                        $profile->preferred_country
                    );
                }
            )
            ->orderBy('ranking')
            ->get();

        $matches = $universities
            ->map(function (University $university) use (
                $profile,
                $matchingService
            ) {
                $result = $matchingService->evaluate(
                    $profile,
                    $university
                );

                return [
                    'university' => $university,
                    'match_score' => $result['match_score'],
                    'eligibility_status' => $result['eligibility_status'],
                    'reasons' => $result['reasons'],
                    'warnings' => $result['warnings'],
                    'requirements' => $result['requirements'],
                ];
            })
            ->sortByDesc('match_score')
            ->values();

        return view('student.university-match', [
            'profile' => $profile,
            'matches' => $matches,
        ]);
    }
}
