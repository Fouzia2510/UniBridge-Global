<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-sm font-bold text-indigo-700">
                    Student Portal
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    {{ $course['name'] ?? 'Google Classroom' }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    View your Google Classroom assignments and submission status.
                </p>
            </div>

            <a
                href="{{ route('student.classroom.course', ['courseId' => $course['id'] ?? '']) }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
            >
                &lt;- Back to Class
            </a>

        </div>
    </x-slot>

    <div class="py-8">

        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if($errors->any())

                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">

                    <p class="font-bold">
                        Google Classroom Error
                    </p>

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

                $courseName = $course['name'] ?? 'Google Classroom';
                $courseId = $course['id'] ?? '';

                $courseSection = $course['section'] ?? null;

                $courseworkList = is_array($coursework ?? null)
                    ? $coursework
                    : [];

                $submissionList = is_array($submissions ?? null)
                    ? $submissions
                    : [];

                $courseworkCount = count($courseworkList);

            @endphp

            <div class="overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-500 p-6 text-white shadow-xl sm:p-8">

                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                    <div>

                        <div class="mb-3 inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide">
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

                    </div>

                    <div class="rounded-2xl bg-white/15 px-5 py-4">

                        <p class="text-xs font-bold uppercase tracking-wide text-white/70">
                            Assignments
                        </p>

                        <p class="mt-1 text-3xl font-extrabold">
                            {{ $courseworkCount }}
                        </p>

                    </div>

                </div>

            </div>

            <div>

                <h3 class="text-xl font-extrabold text-slate-950">
                    All Assignments
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Review your assignments and Google Classroom submission status.
                </p>

            </div>

            @if(empty($courseworkList))

                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                        ??
                    </div>

                    <h3 class="mt-4 text-lg font-extrabold text-slate-900">
                        No Assignments Found
                    </h3>

                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                        There are currently no assignments available in this Google Classroom course.
                    </p>

                </div>

            @else

                <div class="grid gap-5 lg:grid-cols-2">

                    @foreach($courseworkList as $work)

                        @php

                            $workId = $work['id'] ?? null;
                            $workTitle = $work['title'] ?? 'Untitled Assignment';
                            $workDescription = $work['description'] ?? null;

                            $workState = strtoupper(
                                $work['state'] ?? 'PUBLISHED'
                            );

                            $workDue = $work['due_date'] ?? null;
                            $workPoints = $work['max_points'] ?? null;

                            $matchingSubmission = null;

                            if ($workId) {

                                foreach ($submissionList as $submission) {

                                    if (
                                        (string)($submission['coursework_id'] ?? '')
                                        ===
                                        (string)$workId
                                    ) {
                                        $matchingSubmission = $submission;
                                        break;
                                    }

                                }

                            }

                            $submissionState =
                                $matchingSubmission['state'] ?? null;

                            $assignedGrade =
                                $matchingSubmission['assigned_grade'] ?? null;

                        @endphp

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                            <div class="flex items-start gap-4">

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-xl">
                                    📝
                                </div>

                                <div class="min-w-0 flex-1">

                                    <h4 class="text-lg font-extrabold text-slate-900">
                                        {{ $workTitle }}
                                    </h4>

                                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-400">
                                        {{ $workState }}
                                    </p>

                                </div>

                            </div>

                            @if($workDescription)

                                <div class="mt-5 rounded-2xl bg-slate-50 p-4">

                                    <p class="text-sm leading-6 text-slate-600">
                                        {{ $workDescription }}
                                    </p>

                                </div>

                            @endif

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">

                                @if($workDue)

                                    <div class="rounded-xl border border-slate-100 bg-white p-4">

                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Due Date
                                        </p>

                                        <p class="mt-1 text-sm font-semibold text-slate-700">
                                            {{ $workDue }}
                                        </p>

                                    </div>

                                @endif

                                @if($workPoints !== null)

                                    <div class="rounded-xl border border-slate-100 bg-white p-4">

                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Maximum Points
                                        </p>

                                        <p class="mt-1 text-sm font-semibold text-slate-700">
                                            {{ $workPoints }}
                                        </p>

                                    </div>

                                @endif

                            </div>

                            <div class="mt-5 rounded-2xl border border-slate-200 p-4">

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    My Submission
                                </p>

                                @if($matchingSubmission)

                                    <div class="mt-3 flex flex-wrap items-center gap-2">

                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                            {{ strtoupper($submissionState ?? 'UNKNOWN') }}
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

                                @else

                                    <div class="mt-3">

                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                            No Submission Record
                                        </span>

                                    </div>

                                @endif

                            </div>

                            @if($workId)

                                <div class="mt-5">

                                    <p class="text-xs text-slate-400">
                                        Assignment ID
                                    </p>

                                    <p class="mt-1 break-all font-mono text-xs font-semibold text-slate-600">
                                        {{ $workId }}
                                    </p>

                                </div>

                            @endif

                        </div>

                    @endforeach

                </div>

            @endif

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h3 class="text-lg font-extrabold text-slate-950">
                            My Submission Activity
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Google Classroom submission records for this class.
                        </p>

                    </div>

                    <div class="rounded-xl bg-indigo-50 px-4 py-2">

                        <span class="text-sm font-bold text-indigo-700">
                            {{ count($submissionList) }} Submission(s)
                        </span>

                    </div>

                </div>

            </div>

            <div class="pt-2">

                <a
                    href="{{ route('student.classroom.course', ['courseId' => $courseId]) }}"
                    class="inline-flex items-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                >
                    &lt;- Back to Class
                </a>

            </div>

        </div>

    </div>

</x-app-layout>

