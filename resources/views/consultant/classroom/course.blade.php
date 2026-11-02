<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-700">
                    Google Classroom
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    {{ $course['name'] ?? 'Classroom Course' }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Manage your Google Classroom course, students, coursework and submissions.
                </p>
            </div>

            <a
                href="{{ route('consultant.classroom.courses') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
            >
                ← Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            ✓
                        </div>

                        <div>
                            <p class="font-bold text-emerald-900">
                                Success
                            </p>

                            <p class="mt-1 text-sm text-emerald-800">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Old success session support --}}
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            ✓
                        </div>

                        <div>
                            <p class="font-bold text-emerald-900">
                                Success
                            </p>

                            <p class="mt-1 text-sm text-emerald-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Error Message --}}
            @if (session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700">
                            !
                        </div>

                        <div>
                            <p class="font-bold text-red-900">
                                Classroom Error
                            </p>

                            <p class="mt-1 text-sm leading-6 text-red-800">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Validation / Controller Errors --}}
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
                    <p class="font-bold text-red-900">
                        Please fix the following:
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- Course Header --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-500 px-6 py-7 text-white sm:px-8">

                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                        <div>
                            <div class="mb-3 inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide backdrop-blur">
                                Google Classroom Class
                            </div>

                            <h1 class="text-2xl font-extrabold sm:text-3xl">
                                {{ $course['name'] ?? 'Untitled Class' }}
                            </h1>

                            @if (!empty($course['section']))
                                <p class="mt-2 text-sm font-medium text-blue-100">
                                    Section: {{ $course['section'] }}
                                </p>
                            @endif

                            @if (!empty($course['description']))
                                <p class="mt-3 max-w-3xl text-sm leading-6 text-blue-50">
                                    {{ $course['description'] }}
                                </p>
                            @elseif (!empty($course['descriptionHeading']))
                                <p class="mt-3 max-w-3xl text-sm leading-6 text-blue-50">
                                    {{ $course['descriptionHeading'] }}
                                </p>
                            @endif
                        </div>

                        <div class="rounded-2xl bg-white/10 px-5 py-4 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-wide text-blue-100">
                                Class Code
                            </p>

                            <p class="mt-1 text-xl font-extrabold tracking-wider">
                                {{ $course['enrollment_code'] ?? $course['enrollmentCode'] ?? 'N/A' }}
                            </p>
                        </div>

                    </div>
                </div>


                <div class="grid grid-cols-1 divide-y divide-slate-200 sm:grid-cols-4 sm:divide-x sm:divide-y-0">

                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Status
                        </p>

                        <p class="mt-2 font-extrabold text-slate-900">
                            {{ $course['course_state'] ?? $course['courseState'] ?? 'Active' }}
                        </p>
                    </div>

                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Students
                        </p>

                        <p class="mt-2 font-extrabold text-slate-900">
                            {{ count($students ?? []) }}
                        </p>
                    </div>

                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Coursework
                        </p>

                        <p class="mt-2 font-extrabold text-slate-900">
                            {{ count($coursework ?? []) }}
                        </p>
                    </div>

                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Submissions
                        </p>

                        <p class="mt-2 font-extrabold text-slate-900">
                            {{ count($submissions ?? []) }}
                        </p>
                    </div>

                </div>
            </div>


            {{-- Add Student --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                    <div>
                        <p class="text-sm font-bold text-indigo-600">
                            Student Management
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Add Student to Classroom
                        </h3>

                        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                            Enter the student's Google account email. An invitation will be sent to join this Google Classroom.
                        </p>
                    </div>

                    <div class="rounded-xl bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700">
                        Google Classroom Invitation
                    </div>

                </div>


                <form
                    id="addStudentForm"
                    method="POST"
                    action="{{ route('consultant.classroom.course.students.add', ['courseId' => $course['id']]) }}"
                    class="mt-6"
                >
                    @csrf

                    <div class="flex flex-col gap-3 sm:flex-row">

                        <div class="flex-1">

                            <label
                                for="student_email"
                                class="mb-2 block text-sm font-bold text-slate-700"
                            >
                                Student Google Email
                            </label>

                            <input
                                id="student_email"
                                name="email"
                                type="email"
                                required
                                autocomplete="email"
                                value="{{ old('email') }}"
                                placeholder="student@gmail.com"
                                class="block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('email')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        <div class="sm:self-end">

                            <button
                                id="addStudentButton"
                                type="submit"
                                class="inline-flex min-h-[48px] w-full items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-3 text-sm font-extrabold text-white shadow-sm transition hover:from-indigo-700 hover:to-blue-700 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto"
                            >

                                <svg
                                    id="addStudentSpinner"
                                    class="mr-2 hidden h-5 w-5 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>

                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                    ></path>
                                </svg>

                                <span id="addStudentButtonText">
                                    Send Invitation
                                </span>

                            </button>

                        </div>

                    </div>
                </form>

            </div>


            {{-- Enrolled Students --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5 sm:px-8">

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <p class="text-sm font-bold text-indigo-600">
                                Classroom People
                            </p>

                            <h3 class="text-xl font-extrabold text-slate-950">
                                Enrolled Students
                            </h3>
                        </div>

                        <span class="inline-flex w-fit rounded-full bg-indigo-50 px-3 py-1 text-xs font-extrabold text-indigo-700">
                            {{ count($students ?? []) }}
                            Student{{ count($students ?? []) === 1 ? '' : 's' }}
                        </span>

                    </div>

                </div>


                @if (!empty($students))

                    <div class="divide-y divide-slate-200">

                        @foreach ($students as $student)

                            @php
                                $studentProfile = $student['profile'] ?? [];

                                $studentName =
                                    $studentProfile['name']
                                    ?? $student['name']
                                    ?? 'Unknown Student';

                                $studentEmail =
                                    $studentProfile['email']
                                    ?? $student['email']
                                    ?? 'Not available';

                                $studentPhoto =
                                    $studentProfile['photo_url']
                                    ?? $student['photo_url']
                                    ?? null;

                                $studentUserId =
                                    $student['user_id']
                                    ?? $student['id']
                                    ?? 'Not available';
                            @endphp

                            <div class="px-6 py-5 sm:px-8">

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                    <div class="flex min-w-0 items-center gap-4">

                                        @if ($studentPhoto)

                                            <img
                                                src="{{ $studentPhoto }}"
                                                alt="{{ $studentName }}"
                                                class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-indigo-100"
                                            >

                                        @else

                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 text-lg font-extrabold text-white">
                                                {{ strtoupper(substr($studentName, 0, 1)) }}
                                            </div>

                                        @endif

                                        <div class="min-w-0">

                                            <p class="truncate text-base font-extrabold text-slate-900">
                                                {{ $studentName }}
                                            </p>

                                            <p class="truncate text-sm text-slate-500">
                                                {{ $studentEmail }}
                                            </p>

                                        </div>

                                    </div>


                                    <div class="sm:text-right">

                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Google User ID
                                        </p>

                                        <p class="mt-1 max-w-xs break-all text-xs font-semibold text-slate-600">
                                            {{ $studentUserId }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="px-6 py-12 text-center sm:px-8">

                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                            👥
                        </div>

                        <h4 class="mt-4 text-lg font-extrabold text-slate-900">
                            No students enrolled yet
                        </h4>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            Add a student's Google account email above to send an invitation.
                        </p>

                    </div>

                @endif

            </div>


            {{-- Student Submissions --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5 sm:px-8">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <p class="text-sm font-bold text-indigo-600">
                                Google Classroom
                            </p>

                            <h3 class="text-xl font-extrabold text-slate-950">
                                Student Submissions & Grades
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                View assignment submission status, grades and submitted materials.
                            </p>
                        </div>

                        <span class="inline-flex w-fit rounded-full bg-violet-50 px-3 py-1 text-xs font-extrabold text-violet-700">
                            {{ count($submissions ?? []) }}
                            Submission{{ count($submissions ?? []) === 1 ? '' : 's' }}
                        </span>

                    </div>

                </div>


                {{-- Submission API Error --}}
                @if (!empty($submissions_error))

                    <div class="m-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:m-8">

                        <div class="flex items-start gap-3">

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 font-extrabold text-amber-700">
                                !
                            </div>

                            <div>

                                <p class="font-bold text-amber-900">
                                    Submissions could not be loaded
                                </p>

                                <p class="mt-1 text-sm leading-6 text-amber-800">
                                    {{ $submissions_error }}
                                </p>

                            </div>

                        </div>

                    </div>

                @endif


                @if (!empty($submissions))

                    <div class="divide-y divide-slate-200">

                        @foreach ($submissions as $submission)

                            @php
                                $studentData = $submission['student'] ?? [];
                                $courseworkData = $submission['coursework'] ?? [];

                                $submissionName =
                                    $studentData['name']
                                    ?? $submission['student_name']
                                    ?? $submission['name']
                                    ?? 'Unknown Student';

                                $submissionEmail =
                                    $studentData['email']
                                    ?? $submission['student_email']
                                    ?? $submission['email']
                                    ?? 'Not available';

                                $assignmentTitle =
                                    $courseworkData['title']
                                    ?? $submission['coursework_title']
                                    ?? $submission['title']
                                    ?? 'Untitled Assignment';

                                $submissionStatus =
                                    $submission['status']
                                    ?? $submission['state']
                                    ?? 'Unknown';

                                $assignedGrade =
                                    $submission['assigned_grade']
                                    ?? null;

                                $draftGrade =
                                    $submission['draft_grade']
                                    ?? null;

                                $maxPoints =
                                    $courseworkData['max_points']
                                    ?? $submission['max_points']
                                    ?? null;

                                $alternateLink =
                                    $submission['alternate_link']
                                    ?? $submission['alternateLink']
                                    ?? null;

                                $attachments =
                                    $submission['attachments']
                                    ?? [];

                                $isLate =
                                    !empty($submission['late']);

                                $statusLower =
                                    strtolower((string) $submissionStatus);
                            @endphp


                            <div class="px-6 py-6 sm:px-8">

                                <div class="flex flex-col gap-6">


                                    {{-- Submission Header --}}
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

                                        <div class="flex min-w-0 items-start gap-4">

                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-lg font-extrabold text-white">
                                                {{ strtoupper(substr($submissionName, 0, 1)) }}
                                            </div>

                                            <div class="min-w-0">

                                                <h4 class="text-lg font-extrabold text-slate-900">
                                                    {{ $submissionName }}
                                                </h4>

                                                <p class="mt-1 text-sm text-slate-500">
                                                    {{ $submissionEmail }}
                                                </p>

                                                <p class="mt-3 text-sm font-bold text-slate-700">
                                                    Assignment:
                                                    <span class="font-extrabold text-indigo-700">
                                                        {{ $assignmentTitle }}
                                                    </span>
                                                </p>

                                            </div>

                                        </div>


                                        {{-- Status --}}
                                        <div class="flex flex-wrap gap-2">

                                            @if (str_contains($statusLower, 'turned'))

                                                <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-extrabold text-emerald-700">
                                                    ✓ Turned In
                                                </span>

                                            @elseif (str_contains($statusLower, 'return'))

                                                <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-extrabold text-blue-700">
                                                    ↩ Returned
                                                </span>

                                            @elseif (str_contains($statusLower, 'assign'))

                                                <span class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-extrabold text-amber-700">
                                                    Assigned
                                                </span>

                                            @elseif (str_contains($statusLower, 'not started'))

                                                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-extrabold text-slate-600">
                                                    Not Started
                                                </span>

                                            @else

                                                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-extrabold text-slate-600">
                                                    {{ $submissionStatus }}
                                                </span>

                                            @endif


                                            @if ($isLate)

                                                <span class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-extrabold text-red-700">
                                                    Late
                                                </span>

                                            @endif

                                        </div>

                                    </div>


                                    {{-- Submission Details --}}
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                                Grade
                                            </p>

                                            <p class="mt-2 text-lg font-extrabold text-slate-900">

                                                @if ($assignedGrade !== null)

                                                    {{ $assignedGrade }}

                                                    @if ($maxPoints !== null)
                                                        / {{ $maxPoints }}
                                                    @endif

                                                @elseif ($draftGrade !== null)

                                                    {{ $draftGrade }}

                                                    @if ($maxPoints !== null)
                                                        / {{ $maxPoints }}
                                                    @endif

                                                    <span class="block text-xs font-bold text-amber-600">
                                                        Draft grade
                                                    </span>

                                                @else

                                                    <span class="text-sm text-slate-500">
                                                        Not graded
                                                    </span>

                                                @endif

                                            </p>

                                        </div>


                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                                Status
                                            </p>

                                            <p class="mt-2 text-sm font-extrabold text-slate-900">
                                                {{ $submissionStatus }}
                                            </p>

                                        </div>


                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                                Maximum Points
                                            </p>

                                            <p class="mt-2 text-lg font-extrabold text-slate-900">
                                                {{ $maxPoints ?? '—' }}
                                            </p>

                                        </div>

                                    </div>


                                    {{-- Submitted Attachments --}}
                                    @if (!empty($attachments))

                                        <div>

                                            <p class="mb-3 text-xs font-extrabold uppercase tracking-wide text-slate-400">
                                                Submitted Materials
                                            </p>

                                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                                @foreach ($attachments as $attachment)

                                                    @php
                                                        $attachmentTitle =
                                                            $attachment['title']
                                                            ?? $attachment['name']
                                                            ?? 'Submitted File';

                                                        $attachmentUrl =
                                                            $attachment['url']
                                                            ?? $attachment['alternate_link']
                                                            ?? $attachment['link']
                                                            ?? null;
                                                    @endphp

                                                    @if ($attachmentUrl)

                                                        <a
                                                            href="{{ $attachmentUrl }}"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700"
                                                        >

                                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                                                ↗
                                                            </span>

                                                            <span class="min-w-0 flex-1 break-words leading-5">
                                                                {{ $attachmentTitle }}
                                                            </span>

                                                        </a>

                                                    @else

                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-600">
                                                            {{ $attachmentTitle }}
                                                        </div>

                                                    @endif

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif


                                    {{-- Classroom Link --}}
                                    @if ($alternateLink)

                                        <div>

                                            <a
                                                href="{{ $alternateLink }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:from-indigo-700 hover:to-blue-700"
                                            >
                                                Open in Google Classroom ↗
                                            </a>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                @elseif (empty($submissions_error))

                    <div class="px-6 py-12 text-center sm:px-8">

                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                            📝
                        </div>

                        <h4 class="mt-4 text-lg font-extrabold text-slate-900">
                            No student submissions yet
                        </h4>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            Student assignment submissions will appear here when they are available in Google Classroom.
                        </p>

                    </div>

                @endif

            </div>


            {{-- Coursework --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5 sm:px-8">

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <p class="text-sm font-bold text-indigo-600">
                                Google Classroom
                            </p>

                            <h3 class="text-xl font-extrabold text-slate-950">
                                Coursework
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Assignments, materials and classroom activities.
                            </p>
                        </div>

                        <span class="inline-flex w-fit rounded-full bg-cyan-50 px-3 py-1 text-xs font-extrabold text-cyan-700">
                            {{ count($coursework ?? []) }}
                            Item{{ count($coursework ?? []) === 1 ? '' : 's' }}
                        </span>

                    </div>

                </div>


                @if (!empty($coursework))

                    <div class="divide-y divide-slate-200">

                        @foreach ($coursework as $work)

                            <div class="px-6 py-6 sm:px-8">

                                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">


                                    {{-- Coursework Information --}}
                                    <div class="min-w-0 flex-1">

                                        <div class="flex flex-wrap items-center gap-2">

                                            <h4 class="text-lg font-extrabold text-slate-900">
                                                {{ $work['title'] ?? 'Untitled Coursework' }}
                                            </h4>

                                            @if (!empty($work['state']))

                                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                                    {{ $work['state'] }}
                                                </span>

                                            @endif

                                        </div>


                                        @if (!empty($work['description']))

                                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                                {{ $work['description'] }}
                                            </p>

                                        @endif


                                        <div class="mt-4 flex flex-wrap gap-2 text-xs">

                                            @if (isset($work['max_points']))

                                                <span class="rounded-lg bg-slate-100 px-3 py-1.5 font-bold text-slate-600">
                                                    Points: {{ $work['max_points'] }}
                                                </span>

                                            @endif

                                            @if (!empty($work['due_date']))

                                                <span class="rounded-lg bg-blue-50 px-3 py-1.5 font-bold text-blue-700">
                                                    Due: {{ $work['due_date'] }}
                                                </span>

                                            @endif

                                        </div>

                                    </div>


                                    {{-- Materials --}}
                                    @if (!empty($work['materials']))

                                        <div class="w-full lg:max-w-sm">

                                            <p class="mb-2 text-xs font-extrabold uppercase tracking-wide text-slate-400">
                                                Materials
                                            </p>


                                            <div class="space-y-3">

                                                @foreach ($work['materials'] as $material)

                                                    @php
                                                        $materialTitle =
                                                            $material['title']
                                                            ?? $material['name']
                                                            ?? 'Open Material';

                                                        $materialUrl =
                                                            $material['url']
                                                            ?? $material['alternate_link']
                                                            ?? $material['link']
                                                            ?? null;

                                                        $fileId =
                                                            $material['file_id']
                                                            ?? null;

                                                        $materialType =
                                                            strtolower(
                                                                (string) (
                                                                    $material['type']
                                                                    ?? ''
                                                                )
                                                            );

                                                        $extension =
                                                            strtolower(
                                                                pathinfo(
                                                                    $materialTitle,
                                                                    PATHINFO_EXTENSION
                                                                )
                                                            );

                                                        $isImage =
                                                            in_array(
                                                                $extension,
                                                                [
                                                                    'jpg',
                                                                    'jpeg',
                                                                    'png',
                                                                    'gif',
                                                                    'webp',
                                                                    'bmp',
                                                                    'svg'
                                                                ],
                                                                true
                                                            );

                                                        if (
                                                            !$isImage &&
                                                            in_array(
                                                                $materialType,
                                                                [
                                                                    'image',
                                                                    'drive_image',
                                                                    'google_drive_image'
                                                                ],
                                                                true
                                                            )
                                                        ) {
                                                            $isImage = true;
                                                        }

                                                        $previewUrl = null;

                                                        if (
                                                            $fileId &&
                                                            $isImage
                                                        ) {
                                                            $previewUrl =
                                                                'https://drive.google.com/thumbnail?id=' .
                                                                urlencode($fileId) .
                                                                '&sz=w1200';
                                                        }
                                                    @endphp


                                                    @if ($isImage && $previewUrl)

                                                        {{-- Image Material --}}
                                                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                                                            <div class="flex items-start justify-between gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3">

                                                                <div class="min-w-0 flex-1">

                                                                    <p class="break-words text-sm font-extrabold leading-5 text-slate-900">
                                                                        {{ $materialTitle }}
                                                                    </p>

                                                                    <p class="mt-1 text-xs font-semibold text-slate-500">
                                                                        Image Preview
                                                                    </p>

                                                                </div>


                                                                @if ($materialUrl)

                                                                    <a
                                                                        href="{{ $materialUrl }}"
                                                                        target="_blank"
                                                                        rel="noopener noreferrer"
                                                                        class="inline-flex shrink-0 items-center justify-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-extrabold text-white shadow-sm transition hover:bg-indigo-700"
                                                                    >
                                                                        Open File ↗
                                                                    </a>

                                                                @endif

                                                            </div>


                                                            <div class="flex h-56 w-full items-center justify-center overflow-hidden bg-slate-100 p-3 sm:h-64">

                                                                <img
                                                                    src="{{ $previewUrl }}"
                                                                    alt="{{ $materialTitle }}"
                                                                    class="max-h-full max-w-full rounded-xl object-contain shadow-sm"
                                                                    loading="lazy"
                                                                >

                                                            </div>

                                                        </div>


                                                    @elseif ($materialUrl)

                                                        {{-- Normal File / Link --}}
                                                        <a
                                                            href="{{ $materialUrl }}"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700"
                                                        >

                                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm">
                                                                ↗
                                                            </span>

                                                            <span class="min-w-0 flex-1 break-words leading-5">
                                                                {{ $materialTitle }}
                                                            </span>

                                                        </a>


                                                    @else

                                                        {{-- Material Without URL --}}
                                                        <div class="break-words rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-600">
                                                            {{ $materialTitle }}
                                                        </div>

                                                    @endif

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>


                @else

                    <div class="px-6 py-12 text-center sm:px-8">

                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                            📚
                        </div>

                        <h4 class="mt-4 text-lg font-extrabold text-slate-900">
                            No coursework available
                        </h4>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            Coursework and assignments from Google Classroom will appear here.
                        </p>

                    </div>

                @endif

            </div>

        </div>
    </div>


    {{-- Prevent duplicate invitation clicks --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const form = document.getElementById('addStudentForm');
            const button = document.getElementById('addStudentButton');
            const spinner = document.getElementById('addStudentSpinner');
            const buttonText = document.getElementById('addStudentButtonText');
            const emailInput = document.getElementById('student_email');

            if (!form || !button) {
                return;
            }

            form.addEventListener('submit', function () {

                button.disabled = true;

                if (spinner) {
                    spinner.classList.remove('hidden');
                }

                if (buttonText) {
                    buttonText.textContent = 'Sending Invitation...';
                }

                if (emailInput) {
                    emailInput.readOnly = true;
                }

            });

        });
    </script>

</x-app-layout>