<?php

namespace App\Http\Controllers;

use App\Services\GoogleClassroomService;
use App\Services\StudentGoogleClassroomService;
use Google\Client as GoogleClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class GoogleClassroomController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Google Classroom OAuth Connect
    |--------------------------------------------------------------------------
    */

    public function connect(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $client = $this->googleClient();

        /*
        |--------------------------------------------------------------------------
        | Student-specific Google Classroom access
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'student') {

            $client->setScopes([
                'https://www.googleapis.com/auth/classroom.courses.readonly',
                'https://www.googleapis.com/auth/classroom.coursework.me.readonly',
                'https://www.googleapis.com/auth/classroom.student-submissions.me.readonly',
                'https://www.googleapis.com/auth/classroom.profile.emails',
                'https://www.googleapis.com/auth/classroom.profile.photos',
            ]);

            $oauthRole = 'student';

        } else {

            /*
            |--------------------------------------------------------------------------
            | Existing Consultant / Admin Google Classroom access
            |--------------------------------------------------------------------------
            */

            $client->setScopes([
                'https://www.googleapis.com/auth/classroom.courses.readonly',
                'https://www.googleapis.com/auth/classroom.rosters',
                'https://www.googleapis.com/auth/classroom.profile.emails',
                'https://www.googleapis.com/auth/classroom.profile.photos',
                'https://www.googleapis.com/auth/classroom.coursework.students.readonly',
                'https://www.googleapis.com/auth/classroom.student-submissions.students.readonly',
            ]);

            $oauthRole = $user->role;
        }

        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setIncludeGrantedScopes(true);

        /*
        |--------------------------------------------------------------------------
        | Create OAuth state
        |--------------------------------------------------------------------------
        */

        $state = bin2hex(random_bytes(32));

        /*
        |--------------------------------------------------------------------------
        | Save OAuth state in Laravel session
        |--------------------------------------------------------------------------
        */

        $request->session()->put(
            'google_classroom_oauth_state',
            $state
        );

        $request->session()->put(
            'google_classroom_oauth_role',
            $oauthRole
        );

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |
        | Explicitly save the session before redirecting to Google.
        | This prevents the OAuth state from being lost during
        | the Google authorization redirect.
        |--------------------------------------------------------------------------
        */

        $request->session()->save();

        /*
        |--------------------------------------------------------------------------
        | Send state to Google
        |--------------------------------------------------------------------------
        */

        $client->setState($state);

        return redirect()->away(
            $client->createAuthUrl()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Google Classroom OAuth Callback
    |--------------------------------------------------------------------------
    */

    public function callback(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Read the saved OAuth state
        |
        | DO NOT use pull() before validation.
        | We first verify the state, then remove it.
        |--------------------------------------------------------------------------
        */

        $expectedState = $request->session()->get(
            'google_classroom_oauth_state'
        );

        $oauthRole = $request->session()->get(
            'google_classroom_oauth_role'
        );

        $returnedState = (string) $request->query('state');

        /*
        |--------------------------------------------------------------------------
        | Validate OAuth state
        |--------------------------------------------------------------------------
        */

        if (
            empty($expectedState) ||
            empty($returnedState) ||
            !hash_equals(
                (string) $expectedState,
                $returnedState
            )
        ) {
            abort(
                403,
                'Invalid Google Classroom OAuth state.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | State is valid.
        | Remove OAuth state from session now.
        |--------------------------------------------------------------------------
        */

        $request->session()->forget([
            'google_classroom_oauth_state',
            'google_classroom_oauth_role',
        ]);

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | Google denied access
        |--------------------------------------------------------------------------
        */

        if ($request->filled('error')) {

            return $this->redirectWithError(
                $request,
                'Google Classroom connection was cancelled or denied.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Missing authorization code
        |--------------------------------------------------------------------------
        */

        if (!$request->filled('code')) {

            return $this->redirectWithError(
                $request,
                'Google did not return an authorization code.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch Google access token
        |--------------------------------------------------------------------------
        */

        try {

            $client = $this->googleClient();

            $token = $client->fetchAccessTokenWithAuthCode(
                $request->query('code')
            );

        } catch (Throwable $exception) {

            report($exception);

            return $this->redirectWithError(
                $request,
                'Google Classroom authorization failed. Please try again.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate token
        |--------------------------------------------------------------------------
        */

        if (
            !is_array($token) ||
            isset($token['error']) ||
            empty($token['access_token'])
        ) {

            return $this->redirectWithError(
                $request,
                'Google Classroom authorization failed. Please try again.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Preserve existing refresh token
        |--------------------------------------------------------------------------
        */

        $existingToken = $user->google_classroom_oauth_token ?? [];

        if (!is_array($existingToken)) {
            $existingToken = [];
        }

        if (
            empty($token['refresh_token']) &&
            !empty($existingToken['refresh_token'])
        ) {
            $token['refresh_token'] =
                $existingToken['refresh_token'];
        }

        /*
        |--------------------------------------------------------------------------
        | Save encrypted Google Classroom token
        |--------------------------------------------------------------------------
        */
\Log::info('Google Classroom callback token check', [
    'user_id' => $user->id,
    'has_new_access_token' => !empty($token['access_token']),
    'has_new_refresh_token' => !empty($token['refresh_token']),
    'has_old_refresh_token' => !empty($existingToken['refresh_token']),
]);
        $user->google_classroom_oauth_token = $token;

        $user->save();

        /*
        |--------------------------------------------------------------------------
        | Redirect according to role
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'student') {

            return redirect()
                ->route('student.classroom.courses')
                ->with(
                    'status',
                    'Google Classroom connected successfully.'
                );
        }

        if ($user->role === 'admin') {

            return redirect()
                ->route('admin.dashboard')
                ->with(
                    'status',
                    'Google Classroom connected successfully.'
                );
        }

        return redirect()
            ->route('consultant.classroom.courses')
            ->with(
                'status',
                'Google Classroom connected successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Consultant / Admin Courses
    |--------------------------------------------------------------------------
    */

    public function courses(
        Request $request,
        GoogleClassroomService $googleClassroomService
    ): View|RedirectResponse {

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (
            !is_array($user->google_classroom_oauth_token) ||
            empty(
                $user->google_classroom_oauth_token['access_token']
            )
        ) {
            return redirect()
                ->route('google.classroom.connect');
        }

        try {

            $courses =
                $googleClassroomService->getCourses($user);

            return view(
                'consultant.classroom.courses',
                [
                    'courses' => $courses,
                ]
            );

        } catch (Throwable $exception) {

            report($exception);

            return redirect()
                ->route('consultant.dashboard')
                ->withErrors([
                    'google_classroom' =>
                        'Google Classroom courses could not be loaded: '
                        . $exception->getMessage(),
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Consultant / Admin Course Details
    |--------------------------------------------------------------------------
    */

    public function course(
        Request $request,
        string $courseId,
        GoogleClassroomService $googleClassroomService
    ): View|RedirectResponse {

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (
            !is_array($user->google_classroom_oauth_token) ||
            empty(
                $user->google_classroom_oauth_token['access_token']
            )
        ) {
            return redirect()
                ->route('google.classroom.connect');
        }

        try {

            $courseData =
                $googleClassroomService
                    ->getCourseDetails(
                        $user,
                        $courseId
                    );

            $course =
                $courseData['course'] ?? [];

            $students =
                $courseData['students'] ?? [];

            $coursework =
                $courseData['coursework'] ?? [];

            $submissions =
                $courseData['submissions'] ?? [];

            $studentsError =
                $courseData['students_error'] ?? null;

            $courseworkError =
                $courseData['coursework_error'] ?? null;

            $submissionsError =
                $courseData['submissions_error'] ?? null;

            return view(
                'consultant.classroom.course',
                [
                    'course' =>
                        $course,

                    'students' =>
                        $students,

                    'coursework' =>
                        $coursework,

                    'submissions' =>
                        $submissions,

                    'students_error' =>
                        $studentsError,

                    'coursework_error' =>
                        $courseworkError,

                    'submissions_error' =>
                        $submissionsError,
                ]
            );

        } catch (Throwable $exception) {

            report($exception);

            return redirect()
                ->route(
                    'consultant.classroom.courses'
                )
                ->withErrors([
                    'google_classroom' =>
                        'Google Classroom class could not be loaded: '
                        . $exception->getMessage(),
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Consultant Add Student
    |--------------------------------------------------------------------------
    */

    public function addStudent(
        Request $request,
        string $courseId,
        GoogleClassroomService $googleClassroomService
    ): RedirectResponse {

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
            ],
        ]);

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (
            !is_array($user->google_classroom_oauth_token) ||
            empty(
                $user->google_classroom_oauth_token['access_token']
            )
        ) {
            return redirect()
                ->route('google.classroom.connect');
        }

        try {

            $googleClassroomService->addStudent(
                $user,
                $courseId,
                $validated['email']
            );

            return redirect()
                ->route(
                    'consultant.classroom.course',
                    [
                        'courseId' => $courseId,
                    ]
                )
                ->with(
                    'status',
                    'Student invitation sent successfully to '
                    . $validated['email']
                    . '.'
                );

        } catch (Throwable $exception) {

            report($exception);

            return redirect()
                ->route(
                    'consultant.classroom.course',
                    [
                        'courseId' => $courseId,
                    ]
                )
                ->withErrors([
                    'google_classroom' =>
                        $exception->getMessage(),
                ])
                ->withInput();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT GOOGLE CLASSROOM COURSES
    |--------------------------------------------------------------------------
    */

    public function studentCourses(
        Request $request,
        StudentGoogleClassroomService $studentGoogleClassroomService
    ): View|RedirectResponse {

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'student') {
            abort(
                403,
                'Only students can access Student Google Classroom.'
            );
        }

        if (
            !is_array($user->google_classroom_oauth_token) ||
            empty(
                $user->google_classroom_oauth_token['access_token']
            )
        ) {
            return redirect()
                ->route('google.classroom.connect');
        }

        try {

            $courses =
                $studentGoogleClassroomService
                    ->getCourses($user);

            return view(
                'student.classroom.courses',
                [
                    'courses' => $courses,
                ]
            );

        } catch (Throwable $exception) {

            report($exception);

            return redirect()
                ->route('student.dashboard')
                ->withErrors([
                    'google_classroom' =>
                        'Your Google Classroom courses could not be loaded: '
                        . $exception->getMessage(),
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT GOOGLE CLASSROOM COURSE
    |--------------------------------------------------------------------------
    */

    public function studentCourse(
        Request $request,
        string $courseId,
        StudentGoogleClassroomService $studentGoogleClassroomService
    ): View|RedirectResponse {

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'student') {
            abort(
                403,
                'Only students can access Student Google Classroom.'
            );
        }

        if (
            !is_array($user->google_classroom_oauth_token) ||
            empty(
                $user->google_classroom_oauth_token['access_token']
            )
        ) {
            return redirect()
                ->route('google.classroom.connect');
        }

        try {

            $courseData =
                $studentGoogleClassroomService
                    ->getCourseDetails(
                        $user,
                        $courseId
                    );

            $course =
                $courseData['course'] ?? [];

            $coursework =
                $courseData['coursework'] ?? [];

            $submissions =
                $courseData['submissions'] ?? [];

            $courseworkError =
                $courseData['coursework_error'] ?? null;

            $submissionsError =
                $courseData['submissions_error'] ?? null;

            return view(
                'student.classroom.course',
                [
                    'course' =>
                        $course,

                    'coursework' =>
                        $coursework,

                    'submissions' =>
                        $submissions,

                    'coursework_error' =>
                        $courseworkError,

                    'submissions_error' =>
                        $submissionsError,
                ]
            );

        } catch (Throwable $exception) {

            report($exception);

            return redirect()
                ->route(
                    'student.classroom.courses'
                )
                ->withErrors([
                    'google_classroom' =>
                        'Google Classroom course could not be loaded: '
                        . $exception->getMessage(),
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT COURSEWORK
    |--------------------------------------------------------------------------
    */

    public function studentCoursework(
        Request $request,
        string $courseId,
        StudentGoogleClassroomService $studentGoogleClassroomService
    ): View|RedirectResponse {

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'student') {
            abort(
                403,
                'Only students can access Student Google Classroom.'
            );
        }

        if (
            !is_array($user->google_classroom_oauth_token) ||
            empty(
                $user->google_classroom_oauth_token['access_token']
            )
        ) {
            return redirect()
                ->route('google.classroom.connect');
        }

        try {

            $courseData =
                $studentGoogleClassroomService
                    ->getCourseDetails(
                        $user,
                        $courseId
                    );

            $course =
                $courseData['course'] ?? [];

            $coursework =
                $courseData['coursework'] ?? [];

            $submissions =
                $courseData['submissions'] ?? [];

            return view(
                'student.classroom.coursework',
                [
                    'course' =>
                        $course,

                    'coursework' =>
                        $coursework,

                    'submissions' =>
                        $submissions,
                ]
            );

        } catch (Throwable $exception) {

            report($exception);

            return redirect()
                ->route(
                    'student.classroom.course',
                    [
                        'courseId' => $courseId,
                    ]
                )
                ->withErrors([
                    'google_classroom' =>
                        'Coursework could not be loaded: '
                        . $exception->getMessage(),
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT ASSIGNMENT DETAIL
    |--------------------------------------------------------------------------
    */

    public function studentAssignment(
        Request $request,
        string $courseId,
        string $assignmentId,
        StudentGoogleClassroomService $studentGoogleClassroomService
    ): View|RedirectResponse {

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'student') {
            abort(
                403,
                'Only students can access Student Google Classroom.'
            );
        }

        if (
            !is_array($user->google_classroom_oauth_token) ||
            empty(
                $user->google_classroom_oauth_token['access_token']
            )
        ) {
            return redirect()
                ->route('google.classroom.connect');
        }

        try {

            $courseData =
                $studentGoogleClassroomService
                    ->getCourseDetails(
                        $user,
                        $courseId
                    );

            $course =
                $courseData['course'] ?? [];

            $coursework =
                $courseData['coursework'] ?? [];

            $submissions =
                $courseData['submissions'] ?? [];

            /*
            |--------------------------------------------------------------------------
            | Find requested assignment
            |--------------------------------------------------------------------------
            */

            $assignment = null;

            foreach ($coursework as $work) {

                if (
                    (string)($work['id'] ?? '')
                    ===
                    (string)$assignmentId
                ) {
                    $assignment = $work;
                    break;
                }

            }

            /*
            |--------------------------------------------------------------------------
            | Assignment not found
            |--------------------------------------------------------------------------
            */

            if (!$assignment) {

                return redirect()
                    ->route(
                        'student.classroom.course.assignments',
                        [
                            'courseId' => $courseId,
                        ]
                    )
                    ->withErrors([
                        'google_classroom' =>
                            'The requested assignment could not be found.'
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Find student's submission
            |--------------------------------------------------------------------------
            */

            $submission = null;

            foreach ($submissions as $item) {

                if (
                    (string)($item['coursework_id'] ?? '')
                    ===
                    (string)$assignmentId
                ) {
                    $submission = $item;
                    break;
                }

            }

            /*
            |--------------------------------------------------------------------------
            | Assignment detail view
            |--------------------------------------------------------------------------
            */

            return view(
                'student.classroom.assignment',
                [
                    'course' =>
                        $course,

                    'assignment' =>
                        $assignment,

                    'submission' =>
                        $submission,
                ]
            );

        } catch (Throwable $exception) {

            report($exception);

            return redirect()
                ->route(
                    'student.classroom.course.assignments',
                    [
                        'courseId' => $courseId,
                    ]
                )
                ->withErrors([
                    'google_classroom' =>
                        'Assignment could not be loaded: '
                        . $exception->getMessage(),
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | OAuth Error Redirect
    |--------------------------------------------------------------------------
    */

    private function redirectWithError(
        Request $request,
        string $message
    ): RedirectResponse {

        $user = $request->user();

        if (!$user) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'google_classroom' => $message,
                ]);
        }

        if ($user->role === 'student') {

            return redirect()
                ->route('student.classroom.courses')
                ->withErrors([
                    'google_classroom' => $message,
                ]);
        }

        if ($user->role === 'admin') {

            return redirect()
                ->route('admin.dashboard')
                ->withErrors([
                    'google_classroom' => $message,
                ]);
        }

        return redirect()
            ->route('consultant.classroom.courses')
            ->withErrors([
                'google_classroom' => $message,
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Google Client
    |--------------------------------------------------------------------------
    */

    private function googleClient(): GoogleClient
    {
        $client = new GoogleClient();

        $client->setApplicationName(
            'UniBridge'
        );

        $client->setClientId(
            config(
                'services.google_classroom.client_id'
            )
        );

        $client->setClientSecret(
            config(
                'services.google_classroom.client_secret'
            )
        );

        $client->setRedirectUri(
            config(
                'services.google_classroom.redirect'
            )
        );

        return $client;
    }
}
