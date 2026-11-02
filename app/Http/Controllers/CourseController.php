<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\services\GeminiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends Controller
{
    /**
     * List all available courses.
     */
    public function index(Request $request): View
    {
        $courses = Course::orderBy('title')->get();

        $enrolledCourseIds = Enrollment::where(
            'student_id',
            $request->user()->id
        )
            ->pluck('course_id')
            ->toArray();

        return view(
            'student.courses',
            compact(
                'courses',
                'enrolledCourseIds'
            )
        );
    }


    /**
     * Enroll the student in a course.
     */
    public function enroll(
        Request $request,
        Course $course
    ): RedirectResponse {
        Enrollment::firstOrCreate([
            'student_id' => $request->user()->id,
            'course_id' => $course->id,
        ]);

        return redirect()
            ->route('student.courses.index')
            ->with(
                'status',
                'Enrolled successfully!'
            );
    }


    /**
     * List the student's enrolled courses with progress.
     */
    public function myCourses(Request $request): View
    {
        $enrollments = Enrollment::where(
            'student_id',
            $request->user()->id
        )
            ->with('course')
            ->orderByDesc('updated_at')
            ->get();

        return view(
            'student.my-courses',
            compact('enrollments')
        );
    }


    /**
     * Show a specific enrolled course.
     */
    public function show(
        Request $request,
        Course $course
    ): View {
        $studentId = $request->user()->id;

        /*
         * Find student's enrollment.
         */
        $enrollment = Enrollment::where(
            'student_id',
            $studentId
        )
            ->where(
                'course_id',
                $course->id
            )
            ->first();

        /*
         * Student must be enrolled.
         */
        abort_unless(
            $enrollment,
            403,
            'You are not enrolled in this course.'
        );

        /*
         * Load active modules and active lessons.
         */
        $course->load([
            'modules' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->with([
                        'lessons' => function ($lessonQuery) {
                            $lessonQuery
                                ->where('is_active', true)
                                ->orderBy('sort_order');
                        },
                    ])
                    ->orderBy('sort_order');
            },
        ]);

        /*
         * Find completed lessons for this course.
         */
        $completedLessonIds = LessonCompletion::where(
            'student_id',
            $studentId
        )
            ->whereHas(
                'lesson.module',
                function ($query) use ($course) {
                    $query->where(
                        'course_id',
                        $course->id
                    );
                }
            )
            ->pluck('lesson_id')
            ->toArray();

        /*
         * Calculate progress.
         */
        $totalLessons = $course->modules->sum(
            fn ($module) => $module->lessons->count()
        );

        $completedLessons = count($completedLessonIds);

        $progress = $totalLessons > 0
            ? (int) round(
                ($completedLessons / $totalLessons) * 100
            )
            : 0;

        $remainingPercent = max(
            0,
            100 - $progress
        );

        /*
         * Keep enrollment progress synchronized.
         */
        if (
            $enrollment->progress_percent !== $progress ||
            $enrollment->certificate_issued !== ($progress >= 90)
        ) {
            $enrollment->update([
                'progress_percent' => $progress,
                'certificate_issued' => $progress >= 90,
            ]);
        }

        return view(
            'student.course-show',
            compact(
                'course',
                'enrollment',
                'completedLessonIds',
                'totalLessons',
                'completedLessons',
                'progress',
                'remainingPercent'
            )
        );
    }


    /**
     * Show a specific module with its lessons.
     */
    public function module(
        Request $request,
        Course $course,
        $module
    ): View {
        $studentId = $request->user()->id;

        /*
         * Verify enrollment.
         */
        $enrollment = Enrollment::where(
            'student_id',
            $studentId
        )
            ->where(
                'course_id',
                $course->id
            )
            ->first();

        abort_unless(
            $enrollment,
            403,
            'You are not enrolled in this course.'
        );

        /*
         * Find module belonging to this course.
         */
        $courseModule = $course->modules()
            ->where(
                'id',
                $module
            )
            ->where(
                'is_active',
                true
            )
            ->with([
                'lessons' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order');
                },
            ])
            ->firstOrFail();

        /*
         * Get completed lessons for this module.
         */
        $completedLessonIds = LessonCompletion::where(
            'student_id',
            $studentId
        )
            ->whereHas(
                'lesson',
                function ($query) use ($courseModule) {
                    $query->where(
                        'course_module_id',
                        $courseModule->id
                    );
                }
            )
            ->pluck('lesson_id')
            ->toArray();

        /*
         * Module lesson counts.
         */
        $totalLessons = $courseModule->lessons->count();

        $completedLessons = $courseModule->lessons
            ->whereIn(
                'id',
                $completedLessonIds
            )
            ->count();

        $progress = $totalLessons > 0
            ? (int) round(
                ($completedLessons / $totalLessons) * 100
            )
            : 0;

        return view(
            'student.course-module',
            compact(
                'course',
                'courseModule',
                'enrollment',
                'completedLessonIds',
                'totalLessons',
                'completedLessons',
                'progress'
            )
        );
    }


    /**
     * Show a specific lesson.
     */
    public function lesson(
        Request $request,
        Course $course,
        $module,
        $lesson
    ): View {
        $studentId = $request->user()->id;

        /*
         * Verify enrollment.
         */
        $enrollment = Enrollment::where(
            'student_id',
            $studentId
        )
            ->where(
                'course_id',
                $course->id
            )
            ->first();

        abort_unless(
            $enrollment,
            403,
            'You are not enrolled in this course.'
        );

        /*
         * Find lesson inside requested module.
         */
        $courseLesson = CourseLesson::where(
            'id',
            $lesson
        )
            ->where(
                'course_module_id',
                $module
            )
            ->where(
                'is_active',
                true
            )
            ->with([
                'module.course',
            ])
            ->firstOrFail();

        /*
         * Make sure module belongs to this course.
         */
        abort_unless(
            $courseLesson->module &&
            $courseLesson->module->course_id === $course->id,
            404
        );

        /*
         * Check completion.
         */
        $isCompleted = LessonCompletion::where(
            'student_id',
            $studentId
        )
            ->where(
                'lesson_id',
                $courseLesson->id
            )
            ->exists();

        return view(
            'student.course-lesson',
            [
                'course' => $course,
                'enrollment' => $enrollment,
                'module' => $courseLesson->module,
                'lesson' => $courseLesson,
                'isCompleted' => $isCompleted,
            ]
        );
    }


    /**
     * Mark a lesson as completed.
     */
    public function completeLesson(
        Request $request,
        Course $course,
        $module,
        $lesson
    ): RedirectResponse {
        $studentId = $request->user()->id;

        /*
         * Verify enrollment.
         */
        $enrollment = Enrollment::where(
            'student_id',
            $studentId
        )
            ->where(
                'course_id',
                $course->id
            )
            ->first();

        abort_unless(
            $enrollment,
            403,
            'You are not enrolled in this course.'
        );

        /*
         * Verify lesson belongs to module.
         */
        $courseLesson = CourseLesson::where(
            'id',
            $lesson
        )
            ->where(
                'course_module_id',
                $module
            )
            ->where(
                'is_active',
                true
            )
            ->with('module')
            ->firstOrFail();

        /*
         * Verify module belongs to course.
         */
        abort_unless(
            $courseLesson->module &&
            $courseLesson->module->course_id === $course->id,
            404
        );

        /*
         * Create completion only once.
         */
        LessonCompletion::firstOrCreate(
            [
                'student_id' => $studentId,
                'lesson_id' => $courseLesson->id,
            ],
            [
                'completed_at' => now(),
            ]
        );

        /*
         * Count all active lessons in this course.
         */
        $totalLessons = CourseLesson::whereHas(
            'module',
            function ($query) use ($course) {
                $query
                    ->where(
                        'course_id',
                        $course->id
                    )
                    ->where(
                        'is_active',
                        true
                    );
            }
        )
            ->where(
                'is_active',
                true
            )
            ->count();

        /*
         * Count completed lessons.
         */
        $completedLessons = LessonCompletion::where(
            'student_id',
            $studentId
        )
            ->whereHas(
                'lesson.module',
                function ($query) use ($course) {
                    $query
                        ->where(
                            'course_id',
                            $course->id
                        )
                        ->where(
                            'is_active',
                            true
                        );
                }
            )
            ->count();

        /*
         * Calculate progress.
         */
        $progress = $totalLessons > 0
            ? (int) round(
                ($completedLessons / $totalLessons) * 100
            )
            : 0;

        /*
         * Update enrollment.
         */
        $enrollment->update([
            'progress_percent' => $progress,
            'certificate_issued' => $progress >= 90,
        ]);

        return redirect()
            ->route(
                'student.my-courses.lesson',
                [
                    'course' => $course,
                    'module' => $module,
                    'lesson' => $lesson,
                ]
            )
            ->with(
                'status',
                'Lesson marked as completed!'
            );
    }


    /**
     * Update progress manually.
     *
     * Kept for compatibility with existing route.
     */
    public function updateProgress(
        Request $request,
        Enrollment $enrollment
    ): RedirectResponse {
        abort_if(
            $enrollment->student_id !== $request->user()->id,
            403
        );

        $request->validate([
            'progress_percent' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
        ]);

        $progress = (int) $request->progress_percent;

        $enrollment->update([
            'progress_percent' => $progress,
            'certificate_issued' => $progress >= 90,
        ]);

        return redirect()
            ->route(
                'student.my-courses.index'
            )
            ->with(
                'status',
                'Progress updated successfully!'
            );
    }


    /**
     * Download course certificate.
     */
    public function downloadCertificate(
        Request $request,
        Enrollment $enrollment
    ): Response {
        abort_if(
            $enrollment->student_id !== $request->user()->id,
            403
        );

        abort_unless(
            $enrollment->progress_percent >= 90,
            404,
            'Certificate not available yet.'
        );

        $enrollment->load([
            'course',
            'student',
        ]);

        $pdf = Pdf::loadView(
            'certificates.course-certificate',
            [
                'studentName' => $enrollment->student->name,
                'courseTitle' => $enrollment->course->title,
                'completionDate' => $enrollment->updated_at
                    ->format('F j, Y'),
            ]
        )->setPaper(
            'a4',
            'landscape'
        );

        return $pdf->download(
            'certificate-' .
            str($enrollment->course->title)->slug() .
            '.pdf'
        );
    }


    /* ============================================================
       AI COURSE ASSISTANT
       ============================================================ */


    /**
     * AI assistant for the student's enrolled course.
     *
     * Supports:
     *
     * 1. ask
     * 2. build_course
     * 3. quiz
     * 4. test
     * 5. roadmap
     * 6. syllabus
     * 7. study_plan
     * 8. flashcards
     */
    public function courseAi(
        Request $request,
        Course $course,
        $module = null
    ): JsonResponse {
        $studentId = $request->user()->id;

        /*
         * ---------------------------------------------------------
         * Verify enrollment
         * ---------------------------------------------------------
         */

        $enrollment = Enrollment::where(
            'student_id',
            $studentId
        )
            ->where(
                'course_id',
                $course->id
            )
            ->first();

        abort_unless(
            $enrollment,
            403,
            'You are not enrolled in this course.'
        );


        /*
         * ---------------------------------------------------------
         * Validate request
         * ---------------------------------------------------------
         */

        $validated = $request->validate([
            'mode' => [
                'required',
                'string',
                'in:ask,build_course,quiz,test,roadmap,syllabus,study_plan,flashcards',
            ],

            'prompt' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'topic' => [
                'nullable',
                'string',
                'max:500',
            ],

            'difficulty' => [
                'nullable',
                'string',
                'in:beginner,intermediate,advanced',
            ],

            'question_count' => [
                'nullable',
                'integer',
                'min:1',
                'max:30',
            ],
        ]);


        /*
         * ---------------------------------------------------------
         * Load course structure
         * ---------------------------------------------------------
         */

        $course->load([
            'modules' => function ($query) {

                $query
                    ->where('is_active', true)
                    ->with([
                        'lessons' => function ($lessonQuery) {

                            $lessonQuery
                                ->where('is_active', true)
                                ->orderBy('sort_order');

                        },
                    ])
                    ->orderBy('sort_order');

            },
        ]);


        /*
         * ---------------------------------------------------------
         * Current module
         * ---------------------------------------------------------
         */

        $currentModule = null;

        if ($module !== null) {

            $currentModule = $course->modules
                ->firstWhere(
                    'id',
                    (int) $module
                );

            abort_unless(
                $currentModule,
                404,
                'Module not found in this course.'
            );
        }


        /*
         * ---------------------------------------------------------
         * Completed lessons
         * ---------------------------------------------------------
         */

        $completedLessonIds = LessonCompletion::where(
            'student_id',
            $studentId
        )
            ->whereHas(
                'lesson.module',
                function ($query) use ($course) {
                    $query->where(
                        'course_id',
                        $course->id
                    );
                }
            )
            ->pluck('lesson_id')
            ->toArray();


        /*
         * ---------------------------------------------------------
         * Course context
         * ---------------------------------------------------------
         */

        $courseContext = $this->buildCourseAiContext(
            $course,
            $currentModule,
            $completedLessonIds
        );


        /*
         * ---------------------------------------------------------
         * Student learning context
         * ---------------------------------------------------------
         */

        $completedCount = count(
            $completedLessonIds
        );

        $totalLessons = $course->modules->sum(
            fn ($courseModule) =>
                $courseModule->lessons->count()
        );

        $progress = $totalLessons > 0
            ? (int) round(
                ($completedCount / $totalLessons) * 100
            )
            : 0;


        /*
         * ---------------------------------------------------------
         * Prompt
         * ---------------------------------------------------------
         */

        $mode = $validated['mode'];

        $prompt = trim(
            $validated['prompt'] ?? ''
        );

        $topic = trim(
            $validated['topic'] ?? ''
        );

        $difficulty =
            $validated['difficulty']
            ?? 'intermediate';

        $questionCount =
            $validated['question_count']
            ?? 10;


        /*
         * ---------------------------------------------------------
         * Build mode instructions
         * ---------------------------------------------------------
         */

        $modeInstruction = $this->buildCourseAiModeInstruction(
            $mode,
            $topic,
            $difficulty,
            $questionCount,
            $prompt
        );


        /*
         * ---------------------------------------------------------
         * Final AI instruction
         * ---------------------------------------------------------
         */

        $systemInstruction = <<<PROMPT

You are UniBridge AI Study Assistant.

You are helping a student who is enrolled in a real UniBridge short course.

Your job is to provide educational assistance based on the course context supplied below.

IMPORTANT RULES:

1. Stay focused on education and the selected course.
2. Use the supplied course, module and lesson information when relevant.
3. Do not invent existing course lessons and pretend they already exist.
4. If the student asks you to CREATE new learning material, you may generate it.
5. Clearly distinguish generated material from existing UniBridge course content.
6. Do not claim that AI-generated modules or lessons were officially added to the student's course.
7. Keep explanations simple and useful.
8. Use headings and clearly separated sections.
9. For quizzes and tests, provide questions and answers separately where appropriate.
10. Never provide unsafe, illegal or harmful instructions.
11. If information is missing, say that it is not available rather than pretending.
12. Do not provide guaranteed academic outcomes.
13. Encourage the student to verify important academic requirements with their instructor when appropriate.

CURRENT COURSE:

{$courseContext}

STUDENT LEARNING STATUS:

Completed lessons: {$completedCount}
Total lessons: {$totalLessons}
Current course progress: {$progress}%

CURRENT REQUEST TYPE:

{$mode}

REQUEST:

{$modeInstruction}

STUDENT MESSAGE:

{$prompt}

PROMPT;


        /*
         * ---------------------------------------------------------
         * Generate response
         * ---------------------------------------------------------
         */

        try {

            $gemini = app(
                GeminiService::class
            );

            $response = $gemini->generate(
                $systemInstruction
            );

            return response()->json([
                'success' => true,
                'mode' => $mode,
                'text' => $response,
                'course_id' => $course->id,
                'module_id' => $currentModule?->id,
            ]);

        } catch (\Throwable $exception) {

            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'AI Study Assistant is temporarily unavailable.',
            ], 503);
        }
    }


    /**
     * Build structured course context for AI.
     */
    private function buildCourseAiContext(
        Course $course,
        $currentModule = null,
        array $completedLessonIds = []
    ): string {
        $context = '';

        $context .= "COURSE TITLE: "
            . ($course->title ?? 'Not provided')
            . "\n";

        $context .= "COURSE DESCRIPTION: "
            . ($course->description ?? 'Not provided')
            . "\n";

        $context .= "COURSE MODE: "
            . ($course->mode ?? 'Not provided')
            . "\n";

        $context .= "COURSE DURATION: "
            . ($course->duration ?? 'Not provided')
            . "\n";

        $context .= "\nCOURSE MODULES:\n";


        foreach ($course->modules as $index => $courseModule) {

            $context .= "\n";

            $context .= "MODULE "
                . ($index + 1)
                . ": "
                . ($courseModule->title ?? 'Untitled')
                . "\n";

            if ($courseModule->description) {

                $context .= "DESCRIPTION: "
                    . $courseModule->description
                    . "\n";
            }


            $context .= "LESSONS:\n";


            foreach (
                $courseModule->lessons
                as $lessonIndex => $lesson
            ) {

                $completed = in_array(
                    $lesson->id,
                    $completedLessonIds
                );

                $context .= " - Lesson "
                    . ($lessonIndex + 1)
                    . ": "
                    . ($lesson->title ?? 'Untitled')
                    . " | Status: "
                    . ($completed ? 'Completed' : 'Not completed')
                    . "\n";

                if ($lesson->description) {

                    $context .= "   Description: "
                        . $lesson->description
                        . "\n";
                }

                if ($lesson->content) {

                    $lessonContent = strip_tags(
                        $lesson->content
                    );

                    $lessonContent = mb_substr(
                        $lessonContent,
                        0,
                        1500
                    );

                    $context .= "   Content: "
                        . $lessonContent
                        . "\n";
                }
            }
        }


        if ($currentModule) {

            $context .= "\nCURRENT MODULE:\n";

            $context .= "Title: "
                . ($currentModule->title ?? 'Not provided')
                . "\n";

            $context .= "Description: "
                . ($currentModule->description ?? 'Not provided')
                . "\n";

            $context .= "Lessons: "
                . $currentModule->lessons->count()
                . "\n";
        }


        return $context;
    }


    /**
     * Convert selected AI mode into clear instructions.
     */
    private function buildCourseAiModeInstruction(
        string $mode,
        string $topic,
        string $difficulty,
        int $questionCount,
        string $prompt
    ): string {
        switch ($mode) {

            case 'build_course':

                return <<<TEXT
Create a complete personalized mini-course for the student.

The course should contain these separate sections:

1. Course Title
2. Course Goal
3. Target Level
4. Learning Objectives
5. Full Syllabus
6. Learning Roadmap
7. Modules
8. Topics and Subtopics
9. Practice Activities
10. Quizzes
11. Tests
12. Assignments
13. Flashcards
14. Revision Sessions
15. Weekly Study Schedule
16. Final Assessment

Difficulty level:
{$difficulty}

Student topic:
{$topic}

Make the structure practical and easy to follow.

This is AI-generated learning material. Do not claim that it has automatically been added to UniBridge's official course database.
TEXT;


            case 'syllabus':

                return <<<TEXT
Create a structured syllabus for the student's requested subject.

Include:

1. Course overview
2. Learning objectives
3. Modules
4. Topics
5. Subtopics
6. Recommended sequence
7. Assessment points
8. Final assessment

Requested topic:
{$topic}
TEXT;


            case 'roadmap':

                return <<<TEXT
Create a step-by-step learning roadmap.

Include:

1. Starting point
2. Week-by-week learning path
3. Module sequence
4. Practice milestones
5. Quiz milestones
6. Test milestones
7. Revision stage
8. Final assessment

Requested topic:
{$topic}

Difficulty:
{$difficulty}
TEXT;


            case 'quiz':

                return <<<TEXT
Create a quiz for the student.

Number of questions:
{$questionCount}

Difficulty:
{$difficulty}

Topic:
{$topic}

Use a mixture of suitable question types.

Structure the response as:

QUIZ
Question 1
...
Question {$questionCount}

Then provide:

ANSWER KEY
1.
2.
3.
etc.

Add a short explanation for each answer where useful.
TEXT;


            case 'test':

                return <<<TEXT
Create a proper practice test.

Number of questions:
{$questionCount}

Difficulty:
{$difficulty}

Topic:
{$topic}

Include:

1. Test instructions
2. Questions
3. Suggested time
4. Answer key
5. Scoring guidance
6. Weak-area interpretation

Do not pretend this is an official examination.
TEXT;


            case 'study_plan':

                return <<<TEXT
Create a personalized study plan.

Include:

1. Daily study routine
2. Weekly goals
3. Topics to study
4. Practice sessions
5. Quiz sessions
6. Test sessions
7. Revision sessions
8. Rest/recovery periods
9. Progress checkpoints

Requested topic:
{$topic}

Difficulty:
{$difficulty}
TEXT;


            case 'flashcards':

                return <<<TEXT
Create useful educational flashcards.

Create {$questionCount} flashcards.

Topic:
{$topic}

Difficulty:
{$difficulty}

Use this format:

FLASHCARD 1
Front:
Back:

Keep each card concise and useful for revision.
TEXT;


            case 'ask':

            default:

                if ($prompt === '') {

                    return <<<TEXT
Answer the student's question about the current course.

If the student has not provided a question, ask them what they would like help with.

You can help with:
- explanations
- course topics
- examples
- quizzes
- tests
- revision
- study plans
- vocabulary
- practice
- assignments
- roadmap creation
TEXT;
                }

                return <<<TEXT
Answer the student's question directly and clearly.

If useful, include:

1. Simple explanation
2. Example
3. Practice activity
4. Quick quiz
5. Suggested next step

Student question:
{$prompt}
TEXT;
        }
    }
}