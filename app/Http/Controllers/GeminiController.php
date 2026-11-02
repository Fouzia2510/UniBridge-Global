<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class GeminiController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:10000'],
        ]);

        try {
            $user = auth()->user();

            /*
             * -----------------------------------------
             * Student Profile
             * -----------------------------------------
             */
            $profile = $user?->studentProfile;

            $studentContext = '';

            if ($profile) {
                $studentContext = "
STUDENT PROFILE

Degree: " . ($profile->degree ?? 'Not provided') . "
Field of Study: " . ($profile->field_of_study ?? 'Not provided') . "
CGPA: " . ($profile->cgpa ?? 'Not provided') . "
Graduation Year: " . ($profile->graduation_year ?? 'Not provided') . "
English Test: " . ($profile->english_test ?? 'Not provided') . "
English Score: " . ($profile->english_score ?? 'Not provided') . "
Preferred Country: " . ($profile->preferred_country ?? 'Not provided') . "
Preferred Degree Level: " . ($profile->preferred_degree_level ?? 'Not provided') . "
Preferred Subject: " . ($profile->preferred_subject ?? 'Not provided') . "
Budget: " . ($profile->budget ?? 'Not provided') . "
Preferred Intake: " . ($profile->preferred_intake ?? 'Not provided') . "
Work Experience: " . ($profile->work_experience_years ?? 'Not provided') . " year(s)
Readiness Score: " . ($profile->readiness_score ?? 'Not provided') . "
";
            } else {
                $studentContext = "
STUDENT PROFILE

No student profile was found for the logged-in student.
Do not invent student information.
";
            }

            /*
             * -----------------------------------------
             * Find Relevant Universities
             * -----------------------------------------
             */
            $availableCountries = University::query()
    ->whereNotNull('country')
    ->where('country', '!=', '')
    ->distinct()
    ->orderBy('country')
    ->pluck('country')
    ->values()
    ->all();

$question = trim($validated['prompt']);

if (preg_match('/\b(which|what|available)\b.*\bcountr(?:y|ies)\b|\bcountr(?:y|ies)\b.*\b(study|available|offer|uni(?:versit(?:y|ies))?)\b/i', $question)) {
    return response()->json([
        'text' => empty($availableCountries)
            ? 'No study destinations are currently listed in the UniBridge database.'
            : 'UniBridge currently has these study destinations in its database: '
                . implode(', ', $availableCountries)
                . '. These are the countries currently listed in our database, not a guarantee of admission.',
    ]);
}
            $universityQuery = University::query();

            if ($profile?->preferred_country) {
                $universityQuery->where(
                    'country',
                    $profile->preferred_country
                );
            }

            if ($profile?->preferred_degree_level) {
                $universityQuery->where(function ($query) use ($profile) {
                    $query->where(
                        'degree_level',
                        $profile->preferred_degree_level
                    )->orWhereNull('degree_level');
                });
            }

            if ($profile?->preferred_subject) {
                $subject = $profile->preferred_subject;

                $universityQuery->where(function ($query) use ($subject) {
                    $query->where(
                        'subject',
                        'like',
                        '%' . $subject . '%'
                    )->orWhereNull('subject');
                });
            }

            $universities = $universityQuery
                ->orderBy('ranking')
                ->limit(10)
                ->get([
                    'id',
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
                    'website_url',
                    'application_url',
                ]);

            /*
             * -----------------------------------------
             * University Context
             * -----------------------------------------
             */
            if ($universities->isNotEmpty()) {

                $universityContext = "
RELEVANT UNIVERSITIES FROM UNIBRIDGE DATABASE

";

                foreach ($universities as $university) {

                    $universityContext .= "
University: {$university->name}
Country: " . ($university->country ?? 'Not provided') . "
City: " . ($university->city ?? 'Not provided') . "
Degree Level: " . ($university->degree_level ?? 'Not provided') . "
Subject: " . ($university->subject ?? 'Not provided') . "
Tuition Fee: " . ($university->tuition_fee ?? 'Not provided') . "
Estimated Living Cost: " . ($university->estimated_living_cost ?? 'Not provided') . "
Minimum CGPA: " . ($university->min_cgpa ?? 'Not provided') . "
Minimum English Score: " . ($university->min_english_score ?? 'Not provided') . "
English Test Type: " . ($university->english_test_type ?? 'Not provided') . "
Intake: " . ($university->intake ?? 'Not provided') . "
Ranking: " . ($university->ranking ?? 'Not provided') . "
Scholarship Available: " . ($university->scholarship_available ? 'Yes' : 'No') . "
Post-Study Work: " . ($university->post_study_work ? 'Yes' : 'No') . "
Description: " . ($university->description ?? 'Not provided') . "
Official Website: " . ($university->website_url ?? 'Not provided') . "
Application URL: " . ($university->application_url ?? 'Not provided') . "

-------------------------
";
                }

            } else {

                $universityContext = "
No matching universities were found in the current UniBridge database.

Do not invent university names or requirements.
";
            }

            /*
             * -----------------------------------------
             * Gemini Instructions
             * -----------------------------------------
             */
            
$systemInstruction = "
You are UniBridge AI Assistant, a helpful study-abroad guidance assistant.

CONVERSATION RULES:

- Reply naturally to greetings such as Hello, Hi, and Good morning.
- For greetings and general questions, answer directly. Do not ask for a student profile unless it is genuinely needed.
- Do not assume every user is a student. The logged-in user may be an administrator, consultant, or student.
- Understand the user's question and answer it directly.
- Ask for missing student details only when they are necessary for a personalized study-abroad recommendation.
- Do not repeatedly ask for information the user has already provided.
- Keep responses friendly, practical, concise, and easy to understand.
- Use plain text only. Do not use Markdown headings, asterisks, code fences, or tables.

UNIBRIDGE DATABASE RULES:

- Use only university information provided in the UniBridge database context.
- Never invent university names, tuition fees, CGPA requirements, English requirements, rankings, scholarships, intakes, or website and application links.
- If no matching university is available in the database, say so clearly.
- Do not guarantee admission, visa approval, or scholarship approval.
- When recommending universities, briefly explain why each university may match the student's profile.
- Compare the student's CGPA and English score with university requirements only when both values are available.
- Never invent missing student information.
- Treat the readiness score as guidance, not a guarantee of admission.
- University requirements may change. Recommend checking the university's official website for confirmation.
- For visa, legal, or official matters, recommend checking the relevant official authority or consulting a UniBridge consultant.
- Do not claim to have checked information that is not available in the supplied context.

STUDENT PROFILE:

{$studentContext}

UNIVERSITY DATABASE:

{$universityContext}

USER QUESTION:

{$validated['prompt']}
";

            /*
             * -----------------------------------------
             * Generate AI Response
             * -----------------------------------------
             */
            $gemini = app(GeminiService::class);

            return response()->json([
                'text' => $gemini->generate($systemInstruction),
            ]);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([
                'message' => 'Gemini is temporarily unavailable.',
            ], 503);
        }
    }
}