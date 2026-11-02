<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'degree',
        'field_of_study',
        'cgpa',
        'graduation_year',
        'english_test',
        'english_score',
        'preferred_country',
        'preferred_degree_level',
        'preferred_subject',
        'budget',
        'preferred_intake',
        'work_experience_years',
        'readiness_score',
    ];

    protected $casts = [
        'cgpa' => 'decimal:2',
        'english_score' => 'decimal:2',
        'budget' => 'decimal:2',
        'work_experience_years' => 'decimal:1',
        'graduation_year' => 'integer',
        'readiness_score' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculateReadinessScore(): int
    {
        $score = 0;

        if ($this->cgpa !== null) {
            $score += min(
                ((float) $this->cgpa / 4) * 40,
                40
            );
        }

        if (
            $this->english_score !== null &&
            $this->english_test &&
            $this->english_test !== 'None'
        ) {
            $score += min(
                ((float) $this->english_score / 9) * 30,
                30
            );
        }

        $preferenceFields = [
            $this->preferred_country,
            $this->preferred_degree_level,
            $this->preferred_subject,
            $this->budget,
            $this->preferred_intake,
        ];

        $filled = collect($preferenceFields)
            ->filter(function ($value) {
                return !is_null($value) && $value !== '';
            })
            ->count();

        $score += ($filled / count($preferenceFields)) * 30;

        return (int) round($score);
    }

    public function eligibilitySummary(): array
    {
        $readinessScore = $this->calculateReadinessScore();

        $summary = [
            'admission_likelihood' => 'Low',
            'readiness_score' => $readinessScore,
            'strengths' => [],
            'gaps' => [],
            'recommendations' => [],
        ];

        if ($this->cgpa !== null) {
            if ((float) $this->cgpa >= 3.0) {
                $summary['strengths'][] =
                    'Strong academic record (CGPA ' .
                    number_format((float) $this->cgpa, 2) .
                    ')';
            } elseif ((float) $this->cgpa >= 2.5) {
                $summary['gaps'][] =
                    'CGPA may limit some competitive university options.';

                $summary['recommendations'][] =
                    'Focus on universities and programs that accept your academic profile.';
            } else {
                $summary['gaps'][] =
                    'Your CGPA is below the typical 2.5–3.0 range used by many programs.';

                $summary['recommendations'][] =
                    'Consider pathway or foundation options where appropriate.';
            }
        } else {
            $summary['gaps'][] =
                'Academic CGPA is not provided.';

            $summary['recommendations'][] =
                'Add your CGPA to improve eligibility assessment.';
        }

        if (
            $this->english_test &&
            $this->english_test !== 'None'
        ) {
            if ($this->english_score !== null) {
                if ((float) $this->english_score >= 7.0) {
                    $summary['strengths'][] =
                        $this->english_test .
                        ' score of ' .
                        number_format((float) $this->english_score, 2) .
                        ' is a strong result.';
                } elseif ((float) $this->english_score >= 6.5) {
                    $summary['strengths'][] =
                        $this->english_test .
                        ' score of ' .
                        number_format((float) $this->english_score, 2) .
                        ' meets a common minimum requirement for many programs.';
                } else {
                    $summary['gaps'][] =
                        $this->english_test .
                        ' score of ' .
                        number_format((float) $this->english_score, 2) .
                        ' may be below some university requirements.';

                    $summary['recommendations'][] =
                        'Consider improving your English test score to access more programs.';
                }
            } else {
                $summary['gaps'][] =
                    $this->english_test .
                    ' is selected but no score has been entered.';

                $summary['recommendations'][] =
                    'Add your latest English test score.';
            }
        } else {
            $summary['gaps'][] =
                'No English proficiency test score on record.';

            $summary['recommendations'][] =
                'Consider taking IELTS or PTE before applying to universities.';
        }

        if (!$this->preferred_country) {
            $summary['gaps'][] =
                'Preferred study country has not been selected.';

            $summary['recommendations'][] =
                'Select your preferred country to improve university matching.';
        }

        if (!$this->preferred_degree_level) {
            $summary['gaps'][] =
                'Preferred degree level has not been selected.';

            $summary['recommendations'][] =
                'Select your intended degree level.';
        }

        if (!$this->preferred_subject) {
            $summary['gaps'][] =
                'Preferred subject has not been selected.';

            $summary['recommendations'][] =
                'Select your preferred subject or field of study.';
        }

        if (!$this->budget) {
            $summary['gaps'][] =
                'Budget not specified.';

            $summary['recommendations'][] =
                'Add your estimated budget to improve cost-based matching.';
        }

        if (!$this->preferred_intake) {
            $summary['gaps'][] =
                'Preferred intake has not been selected.';

            $summary['recommendations'][] =
                'Select your preferred admission intake.';
        }

        if ($this->work_experience_years !== null) {
            if ((float) $this->work_experience_years > 0) {
                $summary['strengths'][] =
                    'Work experience: ' .
                    $this->work_experience_years .
                    ' year(s).';
            }
        }

        if ($readinessScore >= 75) {
            $summary['admission_likelihood'] = 'High';
        } elseif ($readinessScore >= 50) {
            $summary['admission_likelihood'] = 'Moderate';
        } else {
            $summary['admission_likelihood'] = 'Low';
        }

        if (empty($summary['gaps'])) {
            $summary['recommendations'][] =
                'Your profile is well prepared. You can now explore universities and suitable programs.';
        }

        return $summary;
    }
}