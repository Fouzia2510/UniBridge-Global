<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-700">
                    Student Portal
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    {{ $course['name'] ?? 'Google Classroom Course' }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    View your Google Classroom course, assignments and submissions.
                </p>
            </div>

            <a
                href="{{ route('student.classroom.courses') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
            >
                ← My Classes
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                    <p class="font-bold">Google Classroom Error</p>

                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $course = is_array($course ?? null)
                    ? $course
                    : [];

                $courseName = $course['name'] ?? 'Google Classroom Course';
                $courseSection = $course['section'] ?? null;
                $courseDescription = $course['description'] ?? null;
                $courseRoom = $course['room'] ?? null;
                $courseCode = $course['enrollment_code'] ?? null;
                $courseState = strtoupper(
                    $course['course_state']
                    ?? $course['state']
                    ?? 'ACTIVE'
                );

                $courseworkList = is_array($coursework ?? null)
                    ? $coursework
                    : [];

                $submissionList = is_array($submissions ?? null)
                    ? $submissions
                    : [];

                $courseworkCount = count($courseworkList);
                $submissionCount = count($submissionList);
            @endphp

            <div class="overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-500 p-6 text-white shadow-xl sm:p-8">

                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">

                    <div class="max-w-3xl">

                        <div class="mb-3 inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide">
                            Google Classroom
                        </div>

                        <h1 class="text-3xl font-extrabold">
                            {{ $courseName }}
                        </h1>

                        @if($courseSection)
                            <p class="mt-2 text-sm font-semibold text-white/80">
                                {{ $courseSection }}
                            </p>
                        @endif

                        @if($courseDescription)
                            <p class="mt-4 text-sm leading-6 text-white/85">
                                {{ $courseDescription }}
                            </p>
                        @endif

                    </div>

                    <div class="shrink-0">

                        <span class="inline-flex rounded-full bg-white/15 px-4 py-2 text-sm font-bold">
                            {{ $courseState }}
                        </span>

                    </div>

                </div>

            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Course
                    </p>

                    <p class="mt-2 text-lg font-extrabold text-slate-900">
                        {{ $courseName }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Assignments
                    </p>

                    <p class="mt-2 text-2xl font-extrabold text-indigo-600">
                        {{ $courseworkCount }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        My Submissions
                    </p>

                    <p class="mt-2 text-2xl font-extrabold text-indigo-600">
                        {{ $submissionCount }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Class Code
                    </p>

                    <p class="mt-2 font-mono text-lg font-extrabold text-slate-900">
                        {{ $courseCode ?? '�' }}
                    </p>
                </div>

            </div>

            @if($courseRoom)

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Classroom / Room
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-700">
                        {{ $courseRoom }}
                    </p>

                </div>

            @endif

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h3 class="text-xl font-extrabold text-slate-950">
                        Coursework & Assignments
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        View assignments and your submission status.
                    </p>
                </div>

                <a
                    href="{{ route('student.classroom.course.assignments', ['courseId' => $course['id'] ?? '']) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    View All Assignments
                </a>

            </div>

            @if(!empty($coursework_error))

                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
                    <p class="font-bold">Coursework could not be loaded.</p>
                    <p class="mt-1">
                        {{ $coursework_error }}
                    </p>
                </div>

            @elseif($courseworkCount === 0)

                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">

                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                        🗂️
                    </div>

                    <h3 class="text-lg font-extrabold text-slate-900">
                        No Coursework Found
                    </h3>

                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                        There are currently no assignments available for this class.
                    </p>

                </div>

            @else

                <div class="grid gap-5 lg:grid-cols-2">

                    @foreach($courseworkList as $work)

                        @php
                            $workTitle = $work['title'] ?? 'Untitled Assignment';
                            $workDescription = $work['description'] ?? null;
                            $workState = strtoupper($work['state'] ?? 'PUBLISHED');
                            $workDue = $work['due_date'] ?? null;
                            $workPoints = $work['max_points'] ?? null;
                            $workId = $work['id'] ?? null;
                        @endphp

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                            <div class="flex items-start justify-between gap-4">

                                <div class="flex min-w-0 items-start gap-4">

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-xl">
                                        📝
                                    </div>

                                    <div class="min-w-0">

                                        <h4 class="text-lg font-extrabold text-slate-900">
                                            {{ $workTitle }}
                                        </h4>

                                        <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-400">
                                            {{ $workState }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                            @if($workDescription)

                                <p class="mt-4 text-sm leading-6 text-slate-600">
                                    {{ $workDescription }}
                                </p>

                            @endif

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">

                                @if($workDue)

                                    <div class="rounded-xl bg-slate-50 p-3">

                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Due
                                        </p>

                                        <p class="mt-1 text-sm font-semibold text-slate-700">
                                            {{ $workDue }}
                                        </p>

                                    </div>

                                @endif

                                @if($workPoints !== null)

                                    <div class="rounded-xl bg-slate-50 p-3">

                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Maximum Points
                                        </p>

                                        <p class="mt-1 text-sm font-semibold text-slate-700">
                                            {{ $workPoints }}
                                        </p>

                                    </div>

                                @endif

                            </div>

                            @if($workId)

                                <div class="mt-5">

                                    <a
                                       href="{{ route('student.classroom.assignment', [
    'courseId' => $course['id'] ?? '',
    'assignmentId' => $work['id'] ?? '',
]) }}"
                                        class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700"
                                    >
                                        View Assignment
                                        <span class="ml-2">→</span>
                                    </a>

                                </div>

                            @endif

                        </div>

                    @endforeach

                </div>

            @endif

            <div>

                <h3 class="text-xl font-extrabold text-slate-950">
                    My Submission Activity
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Your latest Google Classroom submission information.
                </p>

            </div>

            @if(!empty($submissions_error))

                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
                    <p class="font-bold">Submission data could not be loaded.</p>
                    <p class="mt-1">
                        {{ $submissions_error }}
                    </p>
                </div>

            @elseif($submissionCount === 0)

                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">

                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                        📝
                    </div>

                    <h3 class="text-lg font-extrabold text-slate-900">
                        No Submission Activity
                    </h3>

                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                        Your Google Classroom submissions will appear here when available.
                    </p>

                </div>

            @else

                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    <div class="divide-y divide-slate-100">

                        @foreach($submissionList as $submission)

                            @php
                                $submissionState = strtoupper(
                                    $submission['state'] ?? 'UNKNOWN'
                                );

                                $assignedGrade =
                                    $submission['assigned_grade']
                                    ?? null;

                                $courseWorkId =
                                    $submission['course_work_id']
                                    ?? null;

                                $submissionId =
                                    $submission['id']
                                    ?? null;
                            @endphp

                            <div class="p-5 sm:p-6">

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                    <div>

                                        <p class="text-sm font-bold text-indigo-600">
                                            Submission
                                        </p>

                                        <p class="mt-1 font-mono text-xs text-slate-400">
                                            {{ $submissionId ?? 'Not available' }}
                                        </p>

                                    </div>

                                    <div class="flex flex-wrap gap-2">

                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
    {{ ucwords(strtolower(str_replace('_', ' ', $submissionState ?? 'Unknown'))) }}
</span>
                                        @if($assignedGrade !== null)

                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                                Grade: {{ $assignedGrade }}
                                            </span>

                                        @else

                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                                                Not Graded
                                            </span>

                                        @endif

                                    </div>

                                </div>

                                @if($courseWorkId)

                                    <p class="mt-4 text-sm text-slate-500">
                                        Coursework ID:
                                        <span class="font-mono font-semibold text-slate-700">
                                            {{ $courseWorkId }}
                                        </span>
                                    </p>

                                @endif

                            </div>

                        @endforeach

                    </div>

                </div>

            @endif

        </div>
    </div>

</x-app-layout>
