<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-sm font-bold text-indigo-700">
                    Student Portal
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Assignment Details
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    View assignment instructions and your Google Classroom submission.
                </p>
            </div>

            <a
                href="{{ route('student.classroom.course.assignments', [
                    'courseId' => $course['id'] ?? ''
                ]) }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
            >
                &lt;- Back to Assignments
            </a>

        </div>
    </x-slot>

    <div class="py-8">

        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Errors --}}

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

                $assignment = is_array($assignment ?? null)
                    ? $assignment
                    : [];

                $submission = is_array($submission ?? null)
                    ? $submission
                    : null;

                $courseId = $course['id'] ?? '';

                $courseName = $course['name']
                    ?? 'Google Classroom';

                $courseSection = $course['section']
                    ?? null;

                $assignmentId = $assignment['id']
                    ?? '';

                $assignmentTitle = $assignment['title']
                    ?? 'Untitled Assignment';

                $assignmentDescription = $assignment['description']
                    ?? null;

                $assignmentState = strtoupper(
                    $assignment['state']
                    ?? 'PUBLISHED'
                );

                $dueDate = $assignment['due_date']
                    ?? null;

                $maxPoints = $assignment['max_points']
                    ?? null;

                $alternateLink = $assignment['alternate_link']
                    ?? '';

                $submissionState = $submission['state']
                    ?? null;

                $assignedGrade = $submission['assigned_grade']
                    ?? null;

                $draftGrade = $submission['draft_grade']
                    ?? null;

                $submissionId = $submission['id']
                    ?? null;

                $submissionLink = $submission['alternate_link']
                    ?? '';

                $late = !empty(
                    $submission['late'] ?? false
                );

                $attachments = is_array(
                    $submission['attachments'] ?? null
                )
                    ? $submission['attachments']
                    : [];

            @endphp


            {{-- Course Header --}}

            <div class="overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-500 p-6 text-white shadow-xl sm:p-8">

                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <div class="mb-3 inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide">
                            Google Classroom
                        </div>

                        <h1 class="text-2xl font-extrabold sm:text-3xl">
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
                            Assignment
                        </p>

                        <p class="mt-1 text-sm font-extrabold">
                            {{ $assignmentState }}
                        </p>

                    </div>

                </div>

            </div>


            {{-- Assignment Details --}}

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                    <div class="flex items-start gap-4">

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-lg font-extrabold text-indigo-700">
                            A
                        </div>

                        <div>

                            <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">
                                {{ $assignmentState }}
                            </p>

                            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">
                                {{ $assignmentTitle }}
                            </h1>

                        </div>

                    </div>

                </div>


                {{-- Description --}}

                @if($assignmentDescription)

                    <div class="mt-7">

                        <h3 class="text-sm font-extrabold uppercase tracking-wide text-slate-500">
                            Instructions
                        </h3>

                        <div class="mt-3 rounded-2xl bg-slate-50 p-5">

                            <p class="whitespace-pre-line text-sm leading-7 text-slate-700">
                                {{ $assignmentDescription }}
                            </p>

                        </div>

                    </div>

                @endif


                {{-- Assignment Information --}}

                <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                    @if($dueDate)

                        <div class="rounded-2xl border border-slate-200 bg-white p-5">

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Due Date
                            </p>

                            <p class="mt-2 text-sm font-extrabold text-slate-800">
                                {{ $dueDate }}
                            </p>

                        </div>

                    @endif


                    @if($maxPoints !== null)

                        <div class="rounded-2xl border border-slate-200 bg-white p-5">

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Maximum Points
                            </p>

                            <p class="mt-2 text-sm font-extrabold text-slate-800">
                                {{ $maxPoints }}
                            </p>

                        </div>

                    @endif


                    <div class="rounded-2xl border border-slate-200 bg-white p-5">

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Assignment ID
                        </p>

                        <p class="mt-2 break-all font-mono text-xs font-bold text-slate-700">
                            {{ $assignmentId ?: 'N/A' }}
                        </p>

                    </div>

                </div>


                {{-- Open in Google Classroom --}}

                @if($alternateLink)

                    <div class="mt-7">

                        <a
                            href="{{ $alternateLink }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                        >
                            Open in Google Classroom
                        </a>

                    </div>

                @endif

            </div>


            {{-- My Submission --}}

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">
                            Student Submission
                        </p>

                        <h2 class="mt-1 text-xl font-extrabold text-slate-950">
                            My Submission
                        </h2>

                    </div>

                    @if($submission)

                        <span class="inline-flex w-fit rounded-full bg-slate-100 px-4 py-2 text-xs font-extrabold text-slate-700">
                            {{ strtoupper($submissionState ?? 'UNKNOWN') }}
                        </span>

                    @else

                        <span class="inline-flex w-fit rounded-full bg-slate-100 px-4 py-2 text-xs font-extrabold text-slate-600">
                            No Submission
                        </span>

                    @endif

                </div>


                @if($submission)

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Status
                            </p>

                            <p class="mt-2 text-sm font-extrabold text-slate-800">
                                {{ strtoupper($submissionState ?? 'UNKNOWN') }}
                            </p>

                        </div>


                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Grade
                            </p>

                            @if($assignedGrade !== null)

                                <p class="mt-2 text-sm font-extrabold text-emerald-700">
                                    {{ $assignedGrade }}
                                </p>

                            @else

                                <p class="mt-2 text-sm font-extrabold text-amber-700">
                                    Not Graded
                                </p>

                            @endif

                        </div>


                        @if($submissionId)

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Submission ID
                                </p>

                                <p class="mt-2 break-all font-mono text-xs font-extrabold text-slate-700">
                                    {{ $submissionId }}
                                </p>

                            </div>

                        @endif

                    </div>


                    {{-- Late status --}}

                    @if($late)

                        <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4">

                            <p class="text-sm font-bold text-amber-800">
                                This submission is marked as late by Google Classroom.
                            </p>

                        </div>

                    @endif


                    {{-- Draft Grade --}}

                    @if($draftGrade !== null)

                        <div class="mt-5 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4">

                            <p class="text-xs font-bold uppercase tracking-wide text-blue-500">
                                Draft Grade
                            </p>

                            <p class="mt-1 text-lg font-extrabold text-blue-800">
                                {{ $draftGrade }}
                            </p>

                        </div>

                    @endif


                    {{-- Submission Attachments --}}

                    @if(!empty($attachments))

                        <div class="mt-6">

                            <h3 class="text-sm font-extrabold uppercase tracking-wide text-slate-500">
                                Submitted Attachments
                            </h3>

                            <div class="mt-3 space-y-3">

                                @foreach($attachments as $attachment)

                                    @php

                                        $attachmentTitle =
                                            $attachment['title']
                                            ?? 'Submitted File';

                                        $attachmentLink =
                                            $attachment['alternate_link']
                                            ?? $attachment['url']
                                            ?? '';

                                    @endphp

                                    <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">

                                        <p class="break-all text-sm font-bold text-slate-700">
                                            {{ $attachmentTitle }}
                                        </p>

                                        @if($attachmentLink)

                                            <a
                                                href="{{ $attachmentLink }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex w-fit items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-indigo-700"
                                            >
                                                Open
                                            </a>

                                        @endif

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endif


                    {{-- Submission Google Classroom Link --}}

                    @if($submissionLink)

                        <div class="mt-6">

                            <a
                                href="{{ $submissionLink }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                            >
                                Open My Submission
                            </a>

                        </div>

                    @endif

                @else

                    <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">

                        <h3 class="text-lg font-extrabold text-slate-900">
                            No Submission Record
                        </h3>

                        <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                            Google Classroom has not returned a submission record for this assignment yet.
                        </p>

                    </div>

                @endif

            </div>


            {{-- Navigation --}}

            <div class="flex flex-col gap-3 sm:flex-row">

                <a
                    href="{{ route('student.classroom.course', [
                        'courseId' => $courseId
                    ]) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    &lt;- Back to Class
                </a>

                <a
                    href="{{ route('student.classroom.course.assignments', [
                        'courseId' => $courseId
                    ]) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                >
                    All Assignments
                </a>

            </div>

        </div>

    </div>

</x-app-layout>