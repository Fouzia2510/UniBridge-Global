<?php

namespace App\Services;

use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Classroom;
use Google\Service\Classroom\Course;
use Google\Service\Classroom\CourseWork;
use Google\Service\Classroom\StudentSubmission;
use Google\Service\Exception as GoogleServiceException;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class StudentGoogleClassroomService
{
    /*
    |--------------------------------------------------------------------------
    | Student Google Classroom Scopes
    |--------------------------------------------------------------------------
    */

    private const SCOPES = [
        'https://www.googleapis.com/auth/classroom.courses.readonly',
        'https://www.googleapis.com/auth/classroom.coursework.me.readonly',
        'https://www.googleapis.com/auth/classroom.student-submissions.me.readonly',
        'https://www.googleapis.com/auth/classroom.profile.emails',
        'https://www.googleapis.com/auth/classroom.profile.photos',
    ];


    /*
    |--------------------------------------------------------------------------
    | Get Student Courses
    |--------------------------------------------------------------------------
    */

    public function getCourses(User $user): array
    {
        $classroom = $this->classroomClient($user);

        $courses = [];

        $pageToken = null;

        do {

            $params = [
                'pageSize' => 100,
            ];

            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            $response = $classroom
                ->courses
                ->listCourses($params);

            foreach ($response->getCourses() ?? [] as $course) {

                $courses[] = $this->mapCourse(
                    $course
                );
            }

            $pageToken =
                $response->getNextPageToken();

        } while ($pageToken);

        return $courses;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Student Course Details
    |--------------------------------------------------------------------------
    */

    public function getCourseDetails(
        User $user,
        string $courseId
    ): array {

        $classroom = $this->classroomClient($user);

        /*
        |--------------------------------------------------------------------------
        | Course
        |--------------------------------------------------------------------------
        */

        $courseResponse =
            $classroom->courses->get($courseId);

        $course =
            $this->mapCourse($courseResponse);


        /*
        |--------------------------------------------------------------------------
        | Coursework
        |--------------------------------------------------------------------------
        */

        $coursework = [];

        $courseworkError = null;

        try {

            $coursework =
                $this->getCoursework(
                    $classroom,
                    $courseId
                );

        } catch (Throwable $exception) {

            report($exception);

            $courseworkError =
                $this->googleErrorMessage(
                    $exception
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Student's Own Submissions
        |--------------------------------------------------------------------------
        */

        $submissions = [];

        $submissionsError = null;

        try {

            $submissions =
                $this->getStudentSubmissions(
                    $classroom,
                    $courseId,
                    $coursework
                );

        } catch (Throwable $exception) {

            report($exception);

            $submissionsError =
                $this->googleErrorMessage(
                    $exception
                );
        }


        return [
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
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Get Coursework
    |--------------------------------------------------------------------------
    */

    private function getCoursework(
        Classroom $classroom,
        string $courseId
    ): array {

        $coursework = [];

        $pageToken = null;

        do {

            $params = [
                'courseWorkStates' => [
                    'PUBLISHED',
                ],
                'pageSize' => 100,
            ];

            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            $response =
                $classroom
                    ->courses_courseWork
                    ->listCoursesCourseWork(
                        $courseId,
                        $params
                    );

            foreach (
                $response->getCourseWork() ?? []
                as $work
            ) {

                $coursework[] =
                    $this->mapCoursework($work);
            }

            $pageToken =
                $response->getNextPageToken();

        } while ($pageToken);

        return $coursework;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Student's Own Submissions
    |--------------------------------------------------------------------------
    */

    private function getStudentSubmissions(
        Classroom $classroom,
        string $courseId,
        array $coursework
    ): array {

        $submissions = [];

        $pageToken = null;

        do {

            $params = [
                'pageSize' => 100,
            ];

            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            /*
             * "-" means the authenticated student.
             *
             * For a student OAuth token Google only returns
             * that student's own submissions.
             */

            $response =
                $classroom
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
                        $coursework
                    );
            }

            $pageToken =
                $response->getNextPageToken();

        } while ($pageToken);

        return $submissions;
    }


    /*
    |--------------------------------------------------------------------------
    | Map Course
    |--------------------------------------------------------------------------
    */

    private function mapCourse(
        Course $course
    ): array {

        return [
            'id' =>
                $course->getId(),

            'name' =>
                $course->getName() ?? 'Untitled Course',

            'section' =>
                $course->getSection() ?? '',

            'description' =>
                $course->getDescription() ?? '',

            'room' =>
                $course->getRoom() ?? '',

            'owner_id' =>
                $course->getOwnerId() ?? '',

            'enrollment_code' =>
                $course->getEnrollmentCode() ?? '',

            'course_state' =>
                $course->getCourseState() ?? '',

            'alternate_link' =>
                $course->getAlternateLink() ?? '',

            'creation_time' =>
                $course->getCreationTime() ?? null,

            'update_time' =>
                $course->getUpdateTime() ?? null,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Map Coursework
    |--------------------------------------------------------------------------
    */

    private function mapCoursework(
        CourseWork $work
    ): array {

        $materials = [];

        foreach (
            $work->getMaterials() ?? []
            as $material
        ) {

            $materials[] =
                $this->mapMaterial(
                    $material
                );
        }

        return [
            'id' =>
                $work->getId(),

            'course_id' =>
                $work->getCourseId(),

            'title' =>
                $work->getTitle() ?? 'Untitled Assignment',

            'description' =>
                $work->getDescription() ?? '',

            'state' =>
                $work->getState() ?? '',

            'work_type' =>
                $work->getWorkType() ?? '',

            'max_points' =>
                $work->getMaxPoints(),

            'creation_time' =>
                $work->getCreationTime() ?? null,

            'update_time' =>
                $work->getUpdateTime() ?? null,

            'due_date' =>
                $this->formatDueDate(
                    $work->getDueDate(),
                    $work->getDueTime()
                ),

            'due_date_raw' =>
                $work->getDueDate(),

            'due_time_raw' =>
                $work->getDueTime(),

            'materials' =>
                $materials,

            'alternate_link' =>
                $work->getAlternateLink() ?? '',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Map Student Submission
    |--------------------------------------------------------------------------
    */

    private function mapSubmission(
        StudentSubmission $submission,
        array $coursework
    ): array {

        $courseworkId =
            $submission->getCourseWorkId();

        $courseworkItem = null;

        foreach (
            $coursework as $work
        ) {

            if (
                (string) ($work['id'] ?? '') ===
                (string) $courseworkId
            ) {

                $courseworkItem =
                    $work;

                break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Submission State
        |--------------------------------------------------------------------------
        */

        $state =
            $submission->getState() ?? 'NEW';

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
                ucwords(
                    strtolower(
                        str_replace(
                            '_',
                            ' ',
                            $state
                        )
                    )
                ),
        };


        /*
        |--------------------------------------------------------------------------
        | Grade
        |--------------------------------------------------------------------------
        */

        $assignedGrade =
            $submission->getAssignedGrade();

        $draftGrade =
            $submission->getDraftGrade();


        /*
        |--------------------------------------------------------------------------
        | Attachments
        |--------------------------------------------------------------------------
        */

        $attachments = [];

        $assignmentSubmission =
            $submission->getAssignmentSubmission();

        if ($assignmentSubmission) {

            foreach (
                $assignmentSubmission->getAttachments() ?? []
                as $attachment
            ) {

                $attachments[] = [
                    'title' =>
                        $attachment->getTitle() ?? '',

                    'link' =>
                        $attachment->getLink()?->getUrl() ?? '',

                    'drive_file_id' =>
                        $attachment
                            ->getDriveFile()
                            ?->getId() ?? '',
                ];
            }
        }


        return [
            'id' =>
                $submission->getId(),

            'course_id' =>
                $submission->getCourseId(),

            'coursework_id' =>
                $courseworkId,

            'coursework_title' =>
                $courseworkItem['title'] ?? 'Assignment',

            'state' =>
                $state,

            'status' =>
                $status,

            'assigned_grade' =>
                $assignedGrade,

            'draft_grade' =>
                $draftGrade,

            'late' =>
                (bool) $submission->getLate(),

            'creation_time' =>
                $submission->getCreationTime() ?? null,

            'update_time' =>
                $submission->getUpdateTime() ?? null,

            'alternate_link' =>
                $submission->getAlternateLink() ?? '',

            'attachments' =>
                $attachments,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Map Classroom Material
    |--------------------------------------------------------------------------
    */

    private function mapMaterial(
        $material
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Drive File
        |--------------------------------------------------------------------------
        */

        $driveFile =
            $material->getDriveFile();

        if ($driveFile) {

            $drive =
                $driveFile->getDriveFile();

            return [
                'type' =>
                    'drive',

                'title' =>
                    $drive?->getTitle() ?? 'Google Drive File',

                'url' =>
                    $drive?->getAlternateLink() ?? '',

                'id' =>
                    $drive?->getId() ?? '',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Link
        |--------------------------------------------------------------------------
        */

        $link =
            $material->getLink();

        if ($link) {

            return [
                'type' =>
                    'link',

                'title' =>
                    $link->getTitle() ?? 'Link',

                'url' =>
                    $link->getUrl() ?? '',

                'id' =>
                    '',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | YouTube Video
        |--------------------------------------------------------------------------
        */

        $youtube =
            $material->getYoutubeVideo();

        if ($youtube) {

            return [
                'type' =>
                    'youtube',

                'title' =>
                    $youtube->getTitle() ?? 'YouTube Video',

                'url' =>
                    $youtube->getAlternateLink() ?? '',

                'id' =>
                    $youtube->getId() ?? '',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Google Form
        |--------------------------------------------------------------------------
        */

        $form =
            $material->getForm();

        if ($form) {

            return [
                'type' =>
                    'form',

                'title' =>
                    $form->getTitle() ?? 'Google Form',

                'url' =>
                    $form->getFormUrl() ?? '',

                'id' =>
                    $form->getFormUrl() ?? '',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Unknown
        |--------------------------------------------------------------------------
        */

        return [
            'type' =>
                'unknown',

            'title' =>
                'Classroom Material',

            'url' =>
                '',

            'id' =>
                '',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Format Due Date
    |--------------------------------------------------------------------------
    */

    private function formatDueDate(
        $dueDate,
        $dueTime
    ): ?string {

        if (!$dueDate) {
            return null;
        }

        try {

            $year =
                $dueDate->getYear();

            $month =
                $dueDate->getMonth();

            $day =
                $dueDate->getDay();

            $hour = 23;

            $minute = 59;

            $second = 0;

            if ($dueTime) {

                $hour =
                    $dueTime->getHours() ?? 0;

                $minute =
                    $dueTime->getMinutes() ?? 0;

                $second =
                    $dueTime->getSeconds() ?? 0;
            }

            return sprintf(
                '%04d-%02d-%02d %02d:%02d:%02d',
                $year,
                $month,
                $day,
                $hour,
                $minute,
                $second
            );

        } catch (Throwable $exception) {

            return null;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Google Classroom Client
    |--------------------------------------------------------------------------
    */

    private function classroomClient(
        User $user
    ): Classroom {

        $token =
            $user->google_classroom_oauth_token;

        if (
            !is_array($token) ||
            empty($token['access_token'])
        ) {

            throw new RuntimeException(
                'Google Classroom is not connected.'
            );
        }


        $client =
            new GoogleClient();

        $client->setApplicationName(
            'UniBridge'
        );

        $client->setScopes(
            self::SCOPES
        );

        $client->setAccessType(
            'offline'
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

        $client->setAccessToken(
            $token
        );


        /*
        |--------------------------------------------------------------------------
        | Refresh expired token
        |--------------------------------------------------------------------------
        */

        if (
            $client->isAccessTokenExpired()
        ) {

            $refreshToken =
                $client->getRefreshToken();

            if (!$refreshToken) {

                $refreshToken =
                    $token['refresh_token'] ?? null;
            }

            if (!$refreshToken) {

                throw new RuntimeException(
                    'Google Classroom authorization has expired. Please connect Google Classroom again.'
                );
            }

            $newToken =
                $client->fetchAccessTokenWithRefreshToken(
                    $refreshToken
                );

            if (
                !is_array($newToken) ||
                isset($newToken['error']) ||
                empty($newToken['access_token'])
            ) {

                throw new RuntimeException(
                    'Google Classroom access token could not be refreshed.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Preserve refresh token
            |--------------------------------------------------------------------------
            */

            if (
                empty($newToken['refresh_token'])
            ) {

                $newToken['refresh_token'] =
                    $refreshToken;
            }


            /*
            |--------------------------------------------------------------------------
            | Save refreshed token
            |--------------------------------------------------------------------------
            */

            $user->google_classroom_oauth_token =
                $newToken;

            $user->save();

            $client->setAccessToken(
                $newToken
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Classroom API Service
        |--------------------------------------------------------------------------
        */

        return new Classroom(
            $client
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Google API Error Message
    |--------------------------------------------------------------------------
    */

    private function googleErrorMessage(
        Throwable $exception
    ): string {

        if (
            $exception instanceof GoogleServiceException
        ) {

            $code =
                $exception->getCode();

            return match (true) {

                $code === 401 =>
                    'Google Classroom authorization has expired. Please connect Google Classroom again.',

                $code === 403 =>
                    'Google Classroom denied access to this information. Please make sure your Google account has permission to access this class.',

                $code === 404 =>
                    'The Google Classroom course or requested information could not be found.',

                $code === 429 =>
                    'Google Classroom rate limit reached. Please wait a moment and try again.',

                $code >= 500 =>
                    'Google Classroom is temporarily unavailable. Please try again later.',

                default =>
                    $exception->getMessage(),
            };
        }

        Log::error(
            'Student Google Classroom error',
            [
                'message' =>
                    $exception->getMessage(),

                'exception' =>
                    get_class($exception),
            ]
        );

        return $exception->getMessage();
    }
}