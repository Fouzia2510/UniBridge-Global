<?php

namespace App\Services;

use App\Models\StudentProfile;
use App\Models\University;

class UniversityMatchingService
{
    /**
     * Calculate a consistent 0-100 preliminary match score.
     */
    public function evaluate(
        ?StudentProfile $profile,
        University $university
    ): array {
        if (!$profile) {
            return [
                'match_score' => 0,
                'eligibility_status' => 'Not Assessed',
                'reasons' => [],
                'warnings' => ['Complete your student profile to assess this university.'],
                'requirements' => [],
            ];
        }

        $score = 0;
        $availablePoints = 0;
        $reasons = [];
        $warnings = [];
        $requirements = [];
        $failedRequirements = 0;

        // Country: 20 points
        if (filled($profile->preferred_country)) {
            $availablePoints += 20;

            $matched = strcasecmp(
                trim($profile->preferred_country),
                trim((string) $university->country)
            ) === 0;

            if ($matched) {
                $score += 20;
                $reasons[] = 'Matches your preferred country.';
            } else {
                $warnings[] = 'University country differs from your preference.';
                $failedRequirements++;
            }

            $requirements[] = [
                'name' => 'Country',
                'student' => $profile->preferred_country,
                'required' => $university->country,
                'matched' => $matched,
            ];
        }

        // Degree level: 20 points
        if (filled($profile->preferred_degree_level)) {
            $availablePoints += 20;

            $matched = strcasecmp(
                trim($profile->preferred_degree_level),
                trim((string) $university->degree_level)
            ) === 0;

            if ($matched) {
                $score += 20;
                $reasons[] = 'Matches your preferred degree level.';
            } else {
                $warnings[] = 'Degree level differs from your preference.';
                $failedRequirements++;
            }

            $requirements[] = [
                'name' => 'Degree Level',
                'student' => $profile->preferred_degree_level,
                'required' => $university->degree_level,
                'matched' => $matched,
            ];
        }

        // Subject: 20 points
        if (filled($profile->preferred_subject)) {
            $availablePoints += 20;

            $studentSubject = mb_strtolower(trim($profile->preferred_subject));
            $universitySubject = mb_strtolower(trim((string) $university->subject));

            $matched = $universitySubject !== ''
                && (
                    str_contains($universitySubject, $studentSubject)
                    || str_contains($studentSubject, $universitySubject)
                );

            if ($matched) {
                $score += 20;
                $reasons[] = 'Matches your preferred subject.';
            } else {
                $warnings[] = 'Subject differs from your preference.';
                $failedRequirements++;
            }

            $requirements[] = [
                'name' => 'Subject',
                'student' => $profile->preferred_subject,
                'required' => $university->subject,
                'matched' => $matched,
            ];
        }

        // CGPA: 20 points
        if ($profile->cgpa !== null && $university->min_cgpa !== null) {
            $availablePoints += 20;

            $matched = (float) $profile->cgpa >= (float) $university->min_cgpa;

            if ($matched) {
                $score += 20;
                $reasons[] = 'Your CGPA meets the listed minimum.';
            } else {
                $warnings[] = 'Your CGPA is below the listed minimum.';
                $failedRequirements++;
            }

            $requirements[] = [
                'name' => 'CGPA',
                'student' => number_format((float) $profile->cgpa, 2),
                'required' => number_format((float) $university->min_cgpa, 2),
                'matched' => $matched,
            ];
        }

        // English test: 10 points, only compare compatible test types.
        if (
            $profile->english_score !== null
            && filled($profile->english_test)
            && strcasecmp(trim($profile->english_test), 'None') !== 0
            && $university->min_english_score !== null
            && filled($university->english_test_type)
        ) {
            $availablePoints += 10;

            $studentTest = mb_strtolower(trim($profile->english_test));
            $requiredTest = mb_strtolower(trim($university->english_test_type));

            $sameTest = $studentTest === $requiredTest;

            $matched = $sameTest
                && (float) $profile->english_score
                    >= (float) $university->min_english_score;

            if ($matched) {
                $score += 10;
                $reasons[] = 'Your English score meets the listed minimum for the same test type.';
            } elseif (!$sameTest) {
                $warnings[] = 'English test types differ; their scores cannot be compared directly.';
                $failedRequirements++;
            } else {
                $warnings[] = 'Your English score is below the listed minimum.';
                $failedRequirements++;
            }

            $requirements[] = [
                'name' => $university->english_test_type,
                'student' => $profile->english_test . ' ' . number_format((float) $profile->english_score, 2),
                'required' => $university->english_test_type . ' ' . number_format((float) $university->min_english_score, 2),
                'matched' => $matched,
            ];
        }

        // Budget: 10 points; include tuition plus estimated living cost.
        if ($profile->budget !== null) {
            $totalCost = (float) ($university->tuition_fee ?? 0)
                + (float) ($university->estimated_living_cost ?? 0);

            if ($totalCost > 0) {
                $availablePoints += 10;

                $matched = (float) $profile->budget >= $totalCost;

                if ($matched) {
                    $score += 10;
                    $reasons[] = 'Estimated tuition plus living cost fits your budget.';
                } else {
                    $warnings[] = 'Estimated tuition plus living cost is above your budget.';
                    $failedRequirements++;
                }

                $requirements[] = [
                    'name' => 'Estimated Annual Cost',
                    'student' => '$' . number_format((float) $profile->budget, 2),
                    'required' => '$' . number_format($totalCost, 2),
                    'matched' => $matched,
                ];
            }
        }

        $matchScore = $availablePoints > 0
            ? (int) round(($score / $availablePoints) * 100)
            : 0;

        if ($availablePoints === 0) {
            $status = 'Not Assessed';
        } elseif ($failedRequirements === 0 && $matchScore >= 80) {
            $status = 'Eligible';
        } elseif ($matchScore >= 50) {
            $status = 'Possible';
        } else {
            $status = 'Not Eligible';
        }

        return [
            'match_score' => $matchScore,
            'eligibility_status' => $status,
            'reasons' => $reasons,
            'warnings' => $warnings,
            'requirements' => $requirements,
        ];
    }
}
