<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PublicGeminiController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $question = trim($validated['prompt']);

            $countries = University::query()
                ->whereNotNull('country')
                ->where('country', '!=', '')
                ->distinct()
                ->orderBy('country')
                ->pluck('country')
                ->values()
                ->all();

            $universities = University::query()
                ->orderBy('ranking')
                ->limit(10)
                ->get([
                    'name',
                    'country',
                    'city',
                    'degree_level',
                    'subject',
                    'tuition_fee',
                    'min_cgpa',
                    'min_english_score',
                    'english_test_type',
                    'intake',
                    'scholarship_available',
                    'website_url',
                    'application_url',
                ]);

            $universityContext = $universities->isEmpty()
                ? 'No universities are currently listed in the database.'
                : $universities->toJson(JSON_PRETTY_PRINT);

            $systemInstruction = "
You are UniBridge AI, a helpful public study-abroad assistant.

Answer visitors' questions about studying abroad, admissions,
courses, universities and scholarships in clear, simple language.

Use only the university information supplied below when discussing
specific universities, fees, requirements, scholarships or links.
Never invent university details or guarantee admission, a visa,
or a scholarship. Requirements can change, so advise visitors to
confirm details on official university websites.

Available countries in the UniBridge database:
" . implode(', ', $countries) . "

University information from the UniBridge database:
{$universityContext}

The visitor is not necessarily logged in. Do not claim to know
their personal profile or private account information.
Ask only for relevant details when needed.
Keep the answer concise and friendly.
Use plain text without Markdown tables.

Visitor question:
{$question}
";

            $gemini = app(GeminiService::class);

            $answer = $gemini->generate($systemInstruction);

            if (!is_string($answer) || trim($answer) === '') {
                throw new \RuntimeException('The AI returned an empty response.');
            }

            return response()->json([
                'text' => $answer,
            ]);
        
        } catch (Throwable $exception) {
            report($exception);

            $question = strtolower(trim($validated['prompt']));

            if (str_contains($question, 'visa')) {
                $answer = 'Student visa requirements depend on your destination country, course, and circumstances. Please check the official immigration website for current requirements. Which country are you planning to study in?';

            } elseif (
                str_contains($question, 'scholarship') ||
                str_contains($question, 'funding')
            ) {
                $answer = 'Scholarships depend on the university, course, academic record, and application deadlines. Please check the scholarship section on your chosen university website. Which country and degree level are you interested in?';

            } elseif (
                str_contains($question, 'ielts') ||
                str_contains($question, 'english test') ||
                str_contains($question, 'english requirement')
            ) {
                $answer = 'English-language requirements vary by university and course. Check the university website for accepted tests and minimum scores. Which course and country are you considering?';

            } elseif (
                str_contains($question, 'fee') ||
                str_contains($question, 'tuition') ||
                str_contains($question, 'cost')
            ) {
                $answer = 'Tuition fees and living costs vary by university, course, and country. Please check the official university fee page. Tell me your preferred country and degree level so I can help you narrow your options.';

            } elseif (
                str_contains($question, 'university') ||
                str_contains($question, 'universities')
            ) {
                $answer = 'I can help you explore universities by country, course, and degree level. Please tell me your preferred destination and subject. You can also browse the universities listed on UniBridge.';

            } elseif (
                str_contains($question, 'country') ||
                str_contains($question, 'countries') ||
                str_contains($question, 'study abroad')
            ) {
                $answer = 'UniBridge helps students explore study-abroad options, universities, courses, and scholarships. Popular destinations include the UK, Canada, and Australia. Your options depend on your academic background and budget. Which country interests you?';

            } else {
                $answer = 'Welcome to UniBridge! I can help you get started with university admissions, study destinations, courses, tuition fees, scholarships, and student visa information. What would you like to know?';
            }

            return response()->json([
                'text' => $answer,
                'fallback' => true,
            ]);
        }
    }
}
