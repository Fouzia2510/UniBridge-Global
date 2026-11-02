<?php

namespace App\Services;

use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Classroom;
use Google\Service\Classroom\Student;
use Google\Service\Exception as GoogleServiceException;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class GoogleClassroomService
{
    private const CONNECT_TIMEOUT = 10;
    private const REQUEST_TIMEOUT = 20;

    /**
     * Get all Google Classroom courses.
     */
    public function getCourses(User $user): array
    {
        $classroom = $this->classroomClient($user);

        $courses = [];
        $pageToken = null;

        do {
            try {
                $params = [
                    'pageSize' => 100,
                ];

                if ($pageToken) {
                    $params['pageToken'] = $pageToken;
                }

                $response = $classroom->courses->listCourses($params);

                foreach ($response->getCourses() ?? [] as $course) {
                    $courses[] = $this->mapCourse($course);
                }

                $pageToken = $response->getNextPageToken();

            } catch (Throwable $exception) {
                report($exception);

                throw new RuntimeException(
                    $this->googleErrorMessage($exception),
                    0,
                    $exception
                );
            }

        } while ($pageToken);

        return $courses;
    }

    /**
     * Get one course with students, coursework and submissions.
     */
    public function getCourseDetails(
        User $user,
        string $courseId
    ): array {
        $classroom = $this->classroomClient($user);

        /*
         * ---------------------------------------------------------
         * COURSE
         * ---------------------------------------------------------
         */
        try {
            $course = $classroom->courses->get($courseId);

        } catch (Throwable $exception) {
            report($exception);

            throw new RuntimeException(
                $this->googleErrorMessage($exception),
                0,
                $exception
            );
        }

        /*
         * ---------------------------------------------------------
         * STUDENTS
         * ---------------------------------------------------------
         */
        $students = [];
        $studentsError = null;

        try {
            $pageToken = null;

            do {
                $params = [
                    'pageSize' => 100,
                ];

                if ($pageToken) {
                    $params['pageToken'] = $pageToken;
                }

                $response = $classroom
                    ->courses_students
                    ->listCoursesStudents(
                        $courseId,
                        $params
                    );

                foreach (
                    $response->getStudents() ?? [] as $student
                ) {
                    $students[] = $this->mapStudent(
                        $classroom,
                        $student
                    );
                }

                $pageToken = $response->getNextPageToken();

            } while ($pageToken);

        } catch (Throwable $exception) {
            report($exception);

            $studentsError =
                $this->googleErrorMessage($exception);

            Log::error(
                'Google Classroom student roster request failed.',
                [
                    'course_id' => $courseId,
                    'http_code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]
            );
        }

        /*
         * ---------------------------------------------------------
         * COURSEWORK
         * ---------------------------------------------------------
         */
        $coursework = [];
        $courseworkError = null;

        try {
            $pageToken = null;

            do {
                $params = [
                    'pageSize' => 100,
                ];

                if ($pageToken) {
                    $params['pageToken'] = $pageToken;
                }

                $response = $classroom
                    ->courses_courseWork
                    ->listCoursesCourseWork(
                        $courseId,
                        $params
                    );

                foreach (
                    $response->getCourseWork() ?? [] as $work
                ) {
                    $coursework[] =
                        $this->mapCoursework($work);
                }

                $pageToken = $response->getNextPageToken();

            } while ($pageToken);

        } catch (Throwable $exception) {
            report($exception);

            $courseworkError =
                $this->googleErrorMessage($exception);

            Log::error(
                'Google Classroom coursework request failed.',
                [
                    'course_id' => $courseId,
                    'http_code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]
            );
        }

        /*
         * ---------------------------------------------------------
         * STUDENT SUBMISSIONS
         * ---------------------------------------------------------
         */
        $submissions = [];
        $submissionsError = null;

        try {
            $pageToken = null;

            do {
                $params = [
                    'pageSize' => 100,
                ];

                if ($pageToken) {
                    $params['pageToken'] = $pageToken;
                }

                $response = $classroom
                    ->courses_courseWork_studentSubmissions
                    ->listCoursesCourseWorkStudentSubmissions(
                        $courseId,
                        '-',
                        $params
                    );

                foreach (
                    $response->getStudentSubmissions() ?? []
                    as $submission
                ) {
                    $submissions[] =
                        $this->mapSubmission(
                            $submission,
                            $coursework,
                            $students
                        );
                }

                $pageToken = $response->getNextPageToken();

            } while ($pageToken);

        } catch (Throwable $exception) {
            report($exception);

            $submissionsError =
                $this->googleErrorMessage($exception);

            Log::error(
                'Google Classroom student submissions request failed.',
                [
                    'course_id' => $courseId,
                    'http_code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]
            );
        }

        return [
            'course' => $this->mapCourse($course),
            'students' => $students,
            'coursework' => $coursework,
            'submissions' => $submissions,

            'students_error' => $studentsError,
            'coursework_error' => $courseworkError,
            'submissions_error' => $submissionsError,
        ];
    }

    /**
     * Add a student directly to a Google Classroom course.
     *
     * This uses the Classroom roster API instead of
     * invitations.create(), so it does not depend on the
     * Google user-invitation quota.
     */
    public function addStudent(
        User $user,
        string $courseId,
        string $email
    ): bool {
        $classroom = $this->classroomClient($user);

        $email = trim($email);

        $student = new Student();
        $student->setUserId($email);

        try {
            $classroom->courses_students->create(
                $courseId,
                $student
            );

            return true;

        } catch (GoogleServiceException $exception) {

            Log::error(
                'Google Classroom student enrollment failed.',
                [
                    'course_id' => $courseId,
                    'student_email' => $email,
                    'http_code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'errors' => method_exists(
                        $exception,
                        'getErrors'
                    )
                        ? $exception->getErrors()
                        : null,
                ]
            );

            throw new RuntimeException(
                $this->googleErrorMessage($exception),
                0,
                $exception
            );

        } catch (Throwable $exception) {

            Log::error(
                'Unexpected Google Classroom student enrollment error.',
                [
                    'course_id' => $courseId,
                    'student_email' => $email,
                    'message' => $exception->getMessage(),
                ]
            );

            throw new RuntimeException(
                'Unable to add the student to Google Classroom: '
                . $exception->getMessage(),
                0,
                $exception
            );
        }
    }

    /**
     * Create Google Classroom client.
     */
    private function classroomClient(User $user): Classroom
    {
        $token = $user->google_classroom_oauth_token;

        if (
            !is_array($token) ||
            empty($token['access_token'])
        ) {
            throw new RuntimeException(
                'Google Classroom is not connected. Please connect Google Classroom first.'
            );
        }

        $client = new GoogleClient();

        $client->setApplicationName('UniBridge');

        $client->setClientId(
            config('services.google_classroom.client_id')
        );

        $client->setClientSecret(
            config('services.google_classroom.client_secret')
        );

        $client->setAccessType('offline');

        $client->setScopes([
            'https://www.googleapis.com/auth/classroom.courses.readonly',
            'https://www.googleapis.com/auth/classroom.rosters',
            'https://www.googleapis.com/auth/classroom.profile.emails',
            'https://www.googleapis.com/auth/classroom.profile.photos',
            'https://www.googleapis.com/auth/classroom.student-submissions.students.readonly',
        ]);

        $client->setAccessToken($token);

        /*
         * Refresh expired access token.
         */
        if ($client->isAccessTokenExpired()) {

            $refreshToken =
                $client->getRefreshToken();

            if (!$refreshToken) {
                $refreshToken =
                    $token['refresh_token'] ?? null;
            }

            if (!$refreshToken) {
                throw new RuntimeException(
                    'Google Classroom session has expired. Please reconnect Google Classroom.'
                );
            }

            try {

                $newToken =
                    $client->fetchAccessTokenWithRefreshToken(
                        $refreshToken
                    );

                
if (
    !is_array($newToken) ||
    isset($newToken['error'])
) {
    \Log::warning('Google Classroom token refresh rejected', [
        'user_id' => $user->id,
        'error' => is_array($newToken)
            ? ($newToken['error'] ?? 'unknown_response')
            : 'invalid_response',
    ]);

    throw new RuntimeException(
        'Google Classroom access token could not be refreshed.'
    );
}


                if (empty($newToken['refresh_token'])) {
                    $newToken['refresh_token'] =
                        $refreshToken;
                }

                if (
                    empty($newToken['scope']) &&
                    !empty($token['scope'])
                ) {
                    $newToken['scope'] =
                        $token['scope'];
                }

                $user->google_classroom_oauth_token =
                    $newToken;

                $user->save();

                $client->setAccessToken($newToken);

            } catch (Throwable $exception) {

                report($exception);

                throw new RuntimeException(
                    'Google Classroom connection expired. Please reconnect Google Classroom.',
                    0,
                    $exception
                );
            }
        }

        /*
         * Configure HTTP timeout.
         */
        $client->setHttpClient(
            new \GuzzleHttp\Client([
                'connect_timeout' =>
                    self::CONNECT_TIMEOUT,

                'timeout' =>
                    self::REQUEST_TIMEOUT,
            ])
        );

        return new Classroom($client);
    }

    /**
     * Convert Google Classroom course to simple array.
     */
    private function mapCourse($course): array
    {
        return [
            'id' =>
                $course->getId(),

            'name' =>
                $course->getName(),

            'section' =>
                $course->getSection(),

            'description' =>
                $course->getDescription(),

            'descriptionHeading' =>
                $course->getDescriptionHeading(),

            'room' =>
                $course->getRoom(),

            'owner_id' =>
                $course->getOwnerId(),

            'course_state' =>
                $course->getCourseState(),

            'enrollment_code' =>
                $course->getEnrollmentCode(),

            'alternate_link' =>
                $course->getAlternateLink(),
        ];
    }

    /**
     * Convert Google Classroom student to simple array.
     */
    private function mapStudent(
        Classroom $classroom,
        $student
    ): array {
        $courseId =
            $student->getCourseId();

        $userId =
            $student->getUserId();

        $name = null;
        $email = null;
        $photoUrl = null;

        /*
         * Embedded profile.
         */
        try {

            $profile =
                $student->getProfile();

            if ($profile) {

                $nameObject =
                    $profile->getName();

                if ($nameObject) {

                    $name = trim(
                        (string) (
                            $nameObject->getFullName()
                            ?? ''
                        )
                    );
                }

                $email =
                    $profile->getEmailAddress();

                $photoUrl =
                    $profile->getPhotoUrl();
            }

        } catch (Throwable $exception) {

            Log::warning(
                'Google Classroom embedded student profile unavailable.',
                [
                    'course_id' => $courseId,
                    'user_id' => $userId,
                    'message' =>
                        $exception->getMessage(),
                ]
            );
        }

        /*
         * Full user profile lookup.
         */
        try {

            if ($userId) {

                $profile =
                    $classroom->userProfiles->get(
                        $userId
                    );

                if ($profile) {

                    $nameObject =
                        $profile->getName();

                    if ($nameObject) {

                        $fullName = trim(
                            (string) (
                                $nameObject->getFullName()
                                ?? ''
                            )
                        );

                        if ($fullName !== '') {
                            $name = $fullName;
                        }
                    }

                    $profileEmail =
                        $profile->getEmailAddress();

                    if ($profileEmail) {
                        $email = $profileEmail;
                    }

                    $profilePhoto =
                        $profile->getPhotoUrl();

                    if ($profilePhoto) {
                        $photoUrl = $profilePhoto;
                    }
                }
            }

        } catch (GoogleServiceException $exception) {

            Log::warning(
                'Google Classroom user profile lookup failed.',
                [
                    'course_id' => $courseId,
                    'user_id' => $userId,
                    'http_code' =>
                        $exception->getCode(),

                    'message' =>
                        $exception->getMessage(),
                ]
            );

        } catch (Throwable $exception) {

            Log::warning(
                'Unexpected Google Classroom user profile lookup error.',
                [
                    'course_id' => $courseId,
                    'user_id' => $userId,
                    'message' => $exception->getMessage(),
                ]
            );
        }

        return [
            'course_id' =>
                $courseId,

            'user_id' =>
                $userId,

            'profile' => [
                'name' =>
                    $name,

                'email' =>
                    $email,

                'photo_url' =>
                    $photoUrl,
            ],
        ];
    }

    /**
     * Convert Google Classroom coursework to simple array.
     */
    private function mapCoursework($work): array
    {
        $materials = [];

        foreach (
            $work->getMaterials() ?? []
            as $material
        ) {
            $materials[] =
                $this->mapMaterial($material);
        }

        return [
            'id' =>
                $work->getId(),

            'course_id' =>
                $work->getCourseId(),

            'title' =>
                $work->getTitle(),

            'description' =>
                $work->getDescription(),

            'state' =>
                $work->getState(),

            'work_type' =>
                $work->getWorkType(),

            'max_points' =>
                $work->getMaxPoints(),

            'creation_time' =>
                $work->getCreationTime(),

            'update_time' =>
                $work->getUpdateTime(),

            'due_date' =>
                $work->getDueDate(),

            'due_time' =>
                $work->getDueTime(),

            'materials' =>
                $materials,

            'alternate_link' =>
                $work->getAlternateLink(),
        ];
    }

    /**
     * Convert Google Classroom StudentSubmission
     * to simple array.
     */
    private function mapSubmission(
        $submission,
        array $coursework,
        array $students
    ): array {
        $courseWorkId =
            $submission->getCourseWorkId();

        $userId =
            $submission->getUserId();

        $courseworkItem = null;

        foreach ($coursework as $work) {

            if (
                (string) ($work['id'] ?? '')
                ===
                (string) $courseWorkId
            ) {
                $courseworkItem =
                    $work;

                break;
            }
        }

        $studentItem = null;

        foreach ($students as $student) {

            if (
                (string) ($student['user_id'] ?? '')
                ===
                (string) $userId
            ) {
                $studentItem =
                    $student;

                break;
            }
        }

        $studentProfile =
            $studentItem['profile'] ?? [];

        $state =
            $submission->getState();

        /*
         * Convert Classroom state into display label.
         */
        $status = match ($state) {

            'NEW' =>
                'Not Started',

            'CREATED' =>
                'Assigned',

            'TURNED_IN' =>
                'Turned In',

            'RETURNED' =>
                'Returned',

            default =>
                $state ?: 'Unknown',
        };

        /*
         * Assignment submission.
         */
        $assignmentSubmission =
            $submission->getAssignmentSubmission();

        $attachments = [];

        if ($assignmentSubmission) {

            foreach (
                $assignmentSubmission
                    ->getAttachments() ?? []
                as $attachment
            ) {

                $attachmentData = [
                    'title' =>
                        null,

                    'url' =>
                        null,

                    'type' =>
                        null,

                    'file_id' =>
                        null,
                ];

                /*
                 * Google Drive attachment.
                 */
                $driveFile =
                    $attachment->getDriveFile();

                if ($driveFile) {

                    $driveFileData =
                        $driveFile->getDriveFile();

                    if ($driveFileData) {

                        $attachmentData['type'] =
                            'DRIVE_FILE';

                        $attachmentData['title'] =
                            $driveFileData->getTitle();

                        $attachmentData['url'] =
                            $driveFileData->getAlternateLink();

                        $attachmentData['file_id'] =
                            $driveFileData->getId();
                    }
                }

                /*
                 * Normal link.
                 */
                $link =
                    $attachment->getLink();

                if ($link) {

                    $attachmentData['type'] =
                        'LINK';

                    $attachmentData['title'] =
                        $link->getTitle();

                    $attachmentData['url'] =
                        $link->getUrl();
                }

                /*
                 * YouTube video.
                 */
                $youtubeVideo =
                    $attachment->getYouTubeVideo();

                if ($youtubeVideo) {

                    $attachmentData['type'] =
                        'YOUTUBE_VIDEO';

                    $attachmentData['title'] =
                        $youtubeVideo->getTitle();

                    $attachmentData['url'] =
                        $youtubeVideo->getAlternateLink();
                }

                $attachments[] =
                    $attachmentData;
            }
        }

        return [
            'id' =>
                $submission->getId(),

            'course_id' =>
                $submission->getCourseId(),

            'course_work_id' =>
                $courseWorkId,

            'user_id' =>
                $userId,

            'student' => [
                'name' =>
                    $studentProfile['name']
                    ?? 'Unknown Student',

                'email' =>
                    $studentProfile['email']
                    ?? 'Not available',

                'photo_url' =>
                    $studentProfile['photo_url']
                    ?? null,
            ],

            'coursework' => [
                'id' =>
                    $courseworkItem['id']
                    ?? $courseWorkId,

                'title' =>
                    $courseworkItem['title']
                    ?? 'Unknown Assignment',

                'max_points' =>
                    $courseworkItem['max_points']
                    ?? null,
            ],

            'state' =>
                $state,

            'status' =>
                $status,

            'assigned_grade' =>
                $submission->getAssignedGrade(),

            'draft_grade' =>
                $submission->getDraftGrade(),

            'late' =>
                (bool) $submission->getLate(),

            'creation_time' =>
                $submission->getCreationTime(),

            'update_time' =>
                $submission->getUpdateTime(),

            'alternate_link' =>
                $submission->getAlternateLink(),

            'attachments' =>
                $attachments,
        ];
    }

    /**
     * Convert coursework material.
     */
    private function mapMaterial($material): array
    {
        $result = [
            'title' =>
                null,

            'type' =>
                null,

            'url' =>
                null,

            'file_id' =>
                null,
        ];

        /*
         * Google Drive file.
         */
        if ($material->getDriveFile()) {

            $result['type'] =
                'DRIVE_FILE';

            $driveFile =
                $material
                    ->getDriveFile()
                    ->getDriveFile();

            if ($driveFile) {

                $result['title'] =
                    $driveFile->getTitle();

                $result['url'] =
                    $driveFile->getAlternateLink();

                $result['file_id'] =
                    $driveFile->getId();
            }

        /*
         * Normal link.
         */
        } elseif ($material->getLink()) {

            $result['type'] =
                'LINK';

            $link =
                $material->getLink();

            $result['title'] =
                $link->getTitle();

            $result['url'] =
                $link->getUrl();

        /*
         * YouTube video.
         */
        } elseif ($material->getYouTubeVideo()) {

            $result['type'] =
                'YOUTUBE_VIDEO';

            $video =
                $material->getYouTubeVideo();

            $result['title'] =
                $video->getTitle();

            $result['url'] =
                $video->getAlternateLink();

        /*
         * Google Form.
         */
        } elseif ($material->getForm()) {

            $result['type'] =
                'FORM';

            $form =
                $material->getForm();

            $result['title'] =
                $form->getTitle();

            $result['url'] =
                $form->getFormUrl();
        }

        return $result;
    }

    /**
     * Convert Google API exceptions into
     * user-friendly messages.
     */
    private function googleErrorMessage(
        Throwable $exception
    ): string {
        $code =
            (int) $exception->getCode();

        $message =
            trim($exception->getMessage());

        $lowerMessage =
            strtolower($message);

        /*
         * Google Classroom can return HTTP 400
         * with FAILED_PRECONDITION.
         */
        if ($code === 400) {

            if (
                str_contains(
                    $lowerMessage,
                    'failed_precondition'
                ) ||
                str_contains(
                    $lowerMessage,
                    'precondition check failed'
                )
            ) {
                return
                    'Google Classroom could not complete this student enrollment because the request failed a Google Classroom precondition. The student may already be enrolled in this class, the account may not be eligible for direct enrollment, or Google Classroom may require the student to join through the class invitation/enrollment flow.';
            }

            return
                'Google Classroom rejected the request.'
                . (
                    $message !== ''
                        ? ' Google response: ' . $message
                        : ''
                );
        }

        if ($code === 401) {
            return
                'Google Classroom authorization has expired. Please reconnect Google Classroom.';
        }

        if ($code === 403) {
            return
                'Google Classroom denied this action. Make sure the connected Google account is a teacher/owner of this class and has permission for this operation.';
        }

        if ($code === 404) {
            return
                'The Google Classroom class or requested resource could not be found.';
        }

        if ($code === 409) {
            return
                'Google Classroom reported a conflict. The student may already be enrolled in this class.';
        }

        if ($code === 429) {
            return
                'Google Classroom returned HTTP 429 (rate/quota limit). The request was rejected immediately. The exact Google reason has been saved in Laravel log.';
        }

        if ($code >= 500) {
            return
                'Google Classroom is temporarily unavailable. Please try again later.';
        }

        return
            'Google Classroom request failed.'
            . (
                $message !== ''
                    ? ' Details: ' . $message
                    : ''
            );
    }
}