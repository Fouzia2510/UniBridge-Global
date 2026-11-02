<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl font-bold text-slate-900">
                Student Profile
            </h2>

            <p class="text-sm text-slate-500">
                View student information and study-abroad eligibility.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Back --}}
            <div>
                <a
                    href="{{ route('consultant.students.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                >
                    <span>←</span>
                    Back to My Students
                </a>
            </div>

            {{-- Student Header --}}
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="p-6 sm:p-8">

                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-4">

                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 text-2xl font-bold text-indigo-700">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>

                            <div>
                                <h1 class="text-2xl font-extrabold text-slate-950">
                                    {{ $student->name }}
                                </h1>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $student->email }}
                                </p>

                                <div class="mt-3 flex flex-wrap gap-2">

                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        Assigned Student
                                    </span>

                                    @if ($profile)
                                        <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                                            Profile Available
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            Profile Incomplete
                                        </span>
                                    @endif

                                </div>
                            </div>

                        </div>

                        @if ($profile)
                            <div class="rounded-2xl bg-indigo-50 px-6 py-4 text-center">
                                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">
                                    Readiness
                                </p>

                                <p class="mt-1 text-3xl font-extrabold text-indigo-700">
                                    {{ $profile->readiness_score ?? 0 }}%
                                </p>
                            </div>
                        @endif

                    </div>

                </div>
            </div>


            @if ($profile)

                {{-- Academic Information --}}
                <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">

                    <div class="border-b border-slate-200 px-6 py-5 sm:px-8">
                        <h3 class="text-lg font-bold text-slate-900">
                            Academic Information
                        </h3>
                    </div>

                    <div class="grid gap-6 p-6 sm:grid-cols-2 lg:grid-cols-4 sm:p-8">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Degree
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->degree_level ?? 'Not specified' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Field
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->field_of_study ?? 'Not specified' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                CGPA
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->cgpa ?? 'Not specified' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Graduation Year
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->graduation_year ?? 'Not specified' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                IELTS
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->ielts_score ?? 'Not specified' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Work Experience
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->work_experience_years ?? 0 }} years
                            </p>
                        </div>

                    </div>
                </div>


                {{-- Study Interest --}}
                <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">

                    <div class="border-b border-slate-200 px-6 py-5 sm:px-8">
                        <h3 class="text-lg font-bold text-slate-900">
                            Study Interest
                        </h3>
                    </div>

                    <div class="grid gap-6 p-6 sm:grid-cols-2 lg:grid-cols-3 sm:p-8">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Preferred Country
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->preferred_country ?? 'Not specified' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Preferred Subject
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->preferred_subject ?? 'Not specified' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Degree Level
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->degree_level ?? 'Not specified' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Preferred Intake
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->preferred_intake ?? 'Not specified' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Budget
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $profile->budget ?? 'Not specified' }}
                            </p>
                        </div>

                    </div>
                </div>


                {{-- Eligibility Summary --}}
                <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">

                    <div class="border-b border-slate-200 px-6 py-5 sm:px-8">
                        <h3 class="text-lg font-bold text-slate-900">
                            Eligibility Summary
                        </h3>
                    </div>

                    <div class="grid gap-6 p-6 sm:grid-cols-2 sm:p-8">

                        <div class="rounded-2xl bg-slate-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Admission likelihood
                            </p>

                            <p class="mt-2 text-xl font-bold text-slate-900">
                                {{ $summary['admission_likelihood'] ?? 'Not available' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Readiness score
                            </p>

                            <p class="mt-2 text-xl font-bold text-indigo-700">
                                {{ $summary['readiness_score'] ?? ($profile->readiness_score ?? 0) }}
                            </p>
                        </div>

                    </div>
                </div>

            @else

                {{-- No Profile --}}
                <div class="rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200">

                    <h3 class="text-lg font-bold text-slate-900">
                        Student profile is not complete
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        This student has not completed the required profile information yet.
                    </p>

                </div>

            @endif


            {{-- Assigned Applications --}}
            <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">

                <div class="border-b border-slate-200 px-6 py-5 sm:px-8">

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                Assigned Applications
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Applications assigned to you for this student.
                            </p>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">
                            {{ $applications->count() }}
                            Application{{ $applications->count() == 1 ? '' : 's' }}
                        </span>

                    </div>

                </div>

                <div class="p-6 sm:p-8">

                    @forelse ($applications as $application)

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                                <div class="space-y-3">

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            University
                                        </p>

                                        <h4 class="mt-1 text-lg font-bold text-slate-900">
                                            {{ $application->university->name ?? 'University not specified' }}
                                        </h4>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-3">

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Status
                                            </p>

                                            <p class="mt-1 font-semibold text-slate-700">
                                                {{ ucwords(str_replace('_', ' ', $application->status ?? 'Unknown')) }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Application ID
                                            </p>

                                            <p class="mt-1 font-semibold text-slate-700">
                                                #{{ $application->id }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Applied
                                            </p>

                                            <p class="mt-1 font-semibold text-slate-700">
                                                {{ $application->created_at?->format('d M Y') ?? 'Not available' }}
                                            </p>
                                        </div>

                                    </div>

                                </div>


                                <div class="shrink-0">

                                    @php
                                        $status = strtolower($application->status ?? '');
                                    @endphp

                                    @if ($status === 'accepted')

                                        <span class="inline-flex rounded-full bg-emerald-100 px-4 py-2 text-xs font-bold text-emerald-700">
                                            Accepted
                                        </span>

                                    @elseif ($status === 'submitted')

                                        <span class="inline-flex rounded-full bg-blue-100 px-4 py-2 text-xs font-bold text-blue-700">
                                            Submitted
                                        </span>

                                    @elseif ($status === 'under_review')

                                        <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-xs font-bold text-amber-700">
                                            Under Review
                                        </span>

                                    @elseif ($status === 'rejected')

                                        <span class="inline-flex rounded-full bg-red-100 px-4 py-2 text-xs font-bold text-red-700">
                                            Rejected
                                        </span>

                                    @elseif ($status === 'withdrawn')

                                        <span class="inline-flex rounded-full bg-slate-200 px-4 py-2 text-xs font-bold text-slate-700">
                                            Withdrawn
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700">
                                            {{ ucwords(str_replace('_', ' ', $application->status ?? 'Unknown')) }}
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="rounded-2xl bg-slate-50 p-6 text-center">

                            <p class="font-semibold text-slate-700">
                                No applications assigned to you.
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Applications assigned to this consultant will appear here.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>


            {{-- Consultation Meetings --}}
            <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">

                <div class="border-b border-slate-200 px-6 py-5 sm:px-8">

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                Consultation Meetings
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                View consultation bookings between you and this student.
                            </p>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">
                            {{ $meetings->count() }}
                            Meeting{{ $meetings->count() == 1 ? '' : 's' }}
                        </span>

                    </div>

                </div>

                <div class="p-6 sm:p-8">

                    @forelse ($meetings as $meeting)

                        @php
                            $meetingStatus = strtolower($meeting->status ?? 'pending');
                        @endphp

                        <div class="mb-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 last:mb-0">

                            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                                <div class="space-y-4">

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Meeting Date
                                        </p>

                                        <p class="mt-1 text-lg font-bold text-slate-900">
                                            {{ $meeting->preferred_date?->format('d M Y') ?? 'Not available' }}
                                        </p>
                                    </div>


                                    <div class="grid gap-4 sm:grid-cols-3">

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Time
                                            </p>

                                            <p class="mt-1 font-semibold text-slate-700">
                                                {{ $meeting->preferred_time
                                                    ? \Carbon\Carbon::parse($meeting->preferred_time)->format('h:i A')
                                                    : 'Not available'
                                                }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Booking ID
                                            </p>

                                            <p class="mt-1 font-semibold text-slate-700">
                                                #{{ $meeting->id }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Status
                                            </p>

                                            <p class="mt-1 font-semibold text-slate-700">
                                                {{ ucwords(str_replace('_', ' ', $meeting->status ?? 'Unknown')) }}
                                            </p>
                                        </div>

                                    </div>


                                    @if ($meeting->notes)

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Student Notes
                                            </p>

                                            <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                                                {{ $meeting->notes }}
                                            </p>
                                        </div>

                                    @endif

                                </div>


                                <div class="shrink-0">

                                    @if ($meetingStatus === 'confirmed')

                                        <span class="inline-flex rounded-full bg-emerald-100 px-4 py-2 text-xs font-bold text-emerald-700">
                                            Confirmed
                                        </span>

                                    @elseif ($meetingStatus === 'pending')

                                        <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-xs font-bold text-amber-700">
                                            Pending
                                        </span>

                                    @elseif ($meetingStatus === 'completed')

                                        <span class="inline-flex rounded-full bg-blue-100 px-4 py-2 text-xs font-bold text-blue-700">
                                            Completed
                                        </span>

                                    @elseif ($meetingStatus === 'cancelled')

                                        <span class="inline-flex rounded-full bg-red-100 px-4 py-2 text-xs font-bold text-red-700">
                                            Cancelled
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700">
                                            {{ ucwords(str_replace('_', ' ', $meeting->status ?? 'Unknown')) }}
                                        </span>

                                    @endif

                                </div>

                            </div>


                            @if ($meeting->meeting_uri)

                                <div class="mt-5 border-t border-slate-200 pt-5">

                                    @if ($meeting->meetingHasEnded())
                                        <span class="inline-flex rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">Meeting time passed</span>
                                    @elseif ($meeting->meetingIsJoinable())
                                        <a href="{{ route('bookings.join', $meeting) }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Join Google Meet</a>
                                    @else
                                        <span class="inline-flex rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-800">Join available at {{ $meeting->meetingStartsAt()->format('h:i A') }}</span>
                                    @endif

                                </div>

                            @endif

                        </div>

                    @empty

                        <div class="rounded-2xl bg-slate-50 p-6 text-center">

                            <p class="font-semibold text-slate-700">
                                No consultation meetings yet.
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Bookings between you and this student will appear here.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>


            {{-- Client Management --}}
            <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">

                <div class="p-6 sm:p-8">

                    <h3 class="text-lg font-bold text-slate-900">
                        Client Management
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Manage this assigned student's consultation workflow.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">

                        <a
                            href="{{ route('consultant.students.index') }}"
                            class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            My Students
                        </a>

                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>