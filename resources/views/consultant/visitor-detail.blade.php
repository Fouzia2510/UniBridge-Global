<x-app-layout>

    @php
        $visitorName =
            $visitor->user?->name
            ?? $visitor->visitor_name
            ?? 'Guest Visitor';

        $visitorEmail =
            $visitor->user?->email
            ?? $visitor->visitor_email
            ?? 'No email';

        $visitorRole =
            $visitorRole
            ?? $visitor->user?->role;

        $visitType = ucfirst(
            str_replace(
                '_',
                ' ',
                $visitor->visit_type ?? 'online'
            )
        );

        $source = ucfirst(
            str_replace(
                '_',
                ' ',
                $visitor->source ?? 'profile'
            )
        );

        $country =
            $visitorRole === 'student'
                ? (
                    $visitor->country_interest
                    ?: $profile?->preferred_country
                    ?: 'Not specified'
                )
                : 'Not applicable';

        $program =
            $visitorRole === 'student'
                ? (
                    $visitor->program_interest
                    ?: $profile?->preferred_subject
                    ?: 'Not specified'
                )
                : 'Not applicable';

        $applicationStatus =
            $latestApplication?->status
            ? ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $latestApplication->status
                )
            )
            : 'No application';

        $statusClass = match ($latestApplication?->status) {
            'accepted' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-red-100 text-red-700',
            'under_review' => 'bg-blue-100 text-blue-700',
            'submitted' => 'bg-indigo-100 text-indigo-700',
            'withdrawn' => 'bg-slate-200 text-slate-700',
            default => 'bg-slate-100 text-slate-600',
        };

        $leadStatus = $visitor->lead_status ?: 'new';

        $leadStatusLabel = match ($leadStatus) {
            'new' => 'New',
            'contacted' => 'Contacted',
            'interested' => 'Interested',
            'converted' => 'Converted',
            'not_interested' => 'Not Interested',
            default => 'New',
        };

        $leadStatusClass = match ($leadStatus) {
            'new' => 'bg-slate-100 text-slate-700',
            'contacted' => 'bg-blue-100 text-blue-700',
            'interested' => 'bg-amber-100 text-amber-700',
            'converted' => 'bg-emerald-100 text-emerald-700',
            'not_interested' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-700',
        };
    @endphp


    <x-slot name="header">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <p class="text-sm font-bold text-indigo-600">
                    Consultant Portal
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Visitor Intelligence
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Detailed visitor information and activity.
                </p>

            </div>


            <a
                href="{{ url('/consultant/visitors') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800"
            >
                ← Back to Visitors
            </a>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">


            {{-- Success Message --}}
            @if (session('status'))

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">

                    {{ session('status') }}

                </div>

            @endif


            {{-- Validation Errors --}}
            @if ($errors->any())

                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4">

                    <p class="mb-2 font-bold text-red-700">
                        Please fix the following:
                    </p>

                    <ul class="list-disc space-y-1 pl-5 text-sm text-red-600">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- Visitor Header --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 px-6 py-7 sm:px-8">

                    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                        <div>

                            <p class="text-sm font-semibold text-indigo-300">
                                Visitor Profile
                            </p>

                            <h1 class="mt-1 text-3xl font-extrabold text-white">
                                {{ $visitorName }}
                            </h1>

                            <p class="mt-2 text-sm text-slate-300">
                                {{ $visitorEmail }}
                            </p>

                        </div>


                        <div class="flex flex-wrap gap-2">

                            @if ($visitorRole === 'student')

                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                    Registered Student
                                </span>

                            @elseif ($visitorRole === 'consultant')

                                <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1.5 text-xs font-bold text-indigo-700">
                                    Registered Consultant
                                </span>

                            @elseif ($visitorRole === 'admin')

                                <span class="inline-flex rounded-full bg-purple-100 px-3 py-1.5 text-xs font-bold text-purple-700">
                                    Registered Admin
                                </span>

                            @else

                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700">
                                    Guest Visitor
                                </span>

                            @endif


                            <span class="inline-flex rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold text-white">
                                {{ $visitType }}
                            </span>


                            <span class="inline-flex rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold text-white">
                                {{ $source }}
                            </span>

                        </div>

                    </div>

                </div>


                {{-- Activity Stats --}}
                <div class="grid grid-cols-1 divide-y divide-slate-200 sm:grid-cols-4 sm:divide-x sm:divide-y-0">

                    <div class="p-6">

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Visit Count
                        </p>

                        <p class="mt-2 text-2xl font-extrabold text-slate-950">
                            {{ $visitCount }}
                        </p>

                    </div>


                    <div class="p-6">

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Profile Completion
                        </p>

                        @if ($visitorRole === 'student')

                            <p class="mt-2 text-2xl font-extrabold text-slate-950">
                                {{ $profileCompletion }}%
                            </p>

                        @else

                            <p class="mt-2 text-2xl font-extrabold text-slate-400">
                                —
                            </p>

                        @endif

                    </div>


                    <div class="p-6">

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Applications
                        </p>

                        @if ($visitorRole === 'student')

                            <p class="mt-2 text-2xl font-extrabold text-slate-950">
                                {{ $applications->count() }}
                            </p>

                        @else

                            <p class="mt-2 text-2xl font-extrabold text-slate-400">
                                —
                            </p>

                        @endif

                    </div>


                    <div class="p-6">

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Readiness
                        </p>

                        @if ($visitorRole === 'student' && $profile?->readiness_score !== null)

                            <p class="mt-2 text-2xl font-extrabold text-slate-950">
                                {{ $profile->readiness_score }}%
                            </p>

                        @else

                            <p class="mt-2 text-2xl font-extrabold text-slate-400">
                                —
                            </p>

                        @endif

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- LEAD MANAGEMENT --}}
            {{-- ========================================================= --}}

            <div class="overflow-hidden rounded-3xl border border-indigo-100 bg-white shadow-sm">

                <div class="border-b border-indigo-100 bg-indigo-50 px-6 py-5">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <p class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">
                                Lead Management
                            </p>

                            <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                                Manage Visitor Lead
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Track communication, interest and conversion progress.
                            </p>

                        </div>


                        <span class="inline-flex w-fit rounded-full px-4 py-2 text-xs font-extrabold {{ $leadStatusClass }}">
                            Current: {{ $leadStatusLabel }}
                        </span>

                    </div>

                </div>


                <div class="p-6">

                    <form
                        method="POST"
                        action="{{ route('consultant.visitors.lead', $visitor) }}"
                        class="space-y-6"
                    >

                        @csrf


                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                            {{-- Lead Status --}}
                            <div>

                                <label
                                    for="lead_status"
                                    class="mb-2 block text-sm font-bold text-slate-800"
                                >
                                    Lead Status
                                </label>


                                <select
                                    id="lead_status"
                                    name="lead_status"
                                    class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                    <option
                                        value="new"
                                        @selected($leadStatus == 'new')
                                    >
                                        New
                                    </option>


                                    <option
                                        value="contacted"
                                        @selected($leadStatus == 'contacted')
                                    >
                                        Contacted
                                    </option>


                                    <option
                                        value="interested"
                                        @selected($leadStatus == 'interested')
                                    >
                                        Interested
                                    </option>


                                    <option
                                        value="converted"
                                        @selected($leadStatus == 'converted')
                                    >
                                        Converted
                                    </option>


                                    <option
                                        value="not_interested"
                                        @selected($leadStatus == 'not_interested')
                                    >
                                        Not Interested
                                    </option>

                                </select>

                            </div>


                            {{-- Current Lead Status --}}
                            <div>

                                <label class="mb-2 block text-sm font-bold text-slate-800">
                                    Current Lead Status
                                </label>

                                <div class="flex min-h-[48px] items-center">

                                    <span class="inline-flex rounded-full px-4 py-2 text-sm font-extrabold {{ $leadStatusClass }}">
                                        {{ $leadStatusLabel }}
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- Consultant Remarks --}}
                        <div>

                            <label
                                for="consultant_remarks"
                                class="mb-2 block text-sm font-bold text-slate-800"
                            >
                                Consultant Remarks
                            </label>


                            <textarea
                                id="consultant_remarks"
                                name="consultant_remarks"
                                rows="5"
                                maxlength="5000"
                                placeholder="Add notes about this visitor, communication, interest, follow-up or conversion..."
                                class="block w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('consultant_remarks', $visitor->consultant_remarks) }}</textarea>


                            <p class="mt-2 text-xs text-slate-400">
                                Maximum 5000 characters.
                            </p>

                        </div>


                        {{-- Lead Dates --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                            <div class="rounded-2xl bg-slate-50 p-4">

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Contacted At
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ $visitor->contacted_at?->format('d M Y, h:i A') ?? 'Not contacted yet' }}
                                </p>

                            </div>


                            <div class="rounded-2xl bg-slate-50 p-4">

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Converted At
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ $visitor->converted_at?->format('d M Y, h:i A') ?? 'Not converted' }}
                                </p>

                            </div>

                        </div>


                        {{-- Save --}}
                        <div class="flex justify-end">

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Save Lead Information
                            </button>

                        </div>

                    </form>

                </div>

            </div>


            {{-- Student Information --}}
            @if ($visitorRole === 'student')

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">


                    {{-- Student Profile --}}
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm lg:col-span-2">

                        <div class="border-b border-slate-200 px-6 py-5">

                            <h3 class="text-lg font-extrabold text-slate-950">
                                Student Profile
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Academic and personal profile information.
                            </p>

                        </div>


                        @if ($profile)

                            <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">

                                <div>

                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Degree
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">
                                        {{ $profile->degree ?: 'Not specified' }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Field of Study
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">
                                        {{ $profile->field_of_study ?: 'Not specified' }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        CGPA
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">
                                        {{ $profile->cgpa ?? 'Not specified' }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Graduation Year
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">
                                        {{ $profile->graduation_year ?? 'Not specified' }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        English Test
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">
                                        {{ $profile->english_test ?: 'Not specified' }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        English Score
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">
                                        {{ $profile->english_score ?? 'Not specified' }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Work Experience
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">
                                        {{ $profile->work_experience_years ?? 0 }} years
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Preferred Degree Level
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">
                                        {{ $profile->preferred_degree_level ?: 'Not specified' }}
                                    </p>

                                </div>

                            </div>

                        @else

                            <div class="p-6">

                                <div class="rounded-2xl bg-slate-50 p-5">

                                    <p class="font-bold text-slate-800">
                                        No Student Profile Found
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        This registered student has not completed a student profile yet.
                                    </p>

                                </div>

                            </div>

                        @endif

                    </div>


                    {{-- Study Interest --}}
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-200 px-6 py-5">

                            <h3 class="text-lg font-extrabold text-slate-950">
                                Study Interest
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Student's preferred study direction.
                            </p>

                        </div>


                        <div class="space-y-5 p-6">

                            <div>

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Country
                                </p>

                                <p class="mt-1 font-bold text-slate-900">
                                    {{ $country }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Program / Subject
                                </p>

                                <p class="mt-1 font-bold text-slate-900">
                                    {{ $program }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Preferred Intake
                                </p>

                                <p class="mt-1 font-bold text-slate-900">
                                    {{ $profile?->preferred_intake ?: 'Not specified' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Budget
                                </p>

                                <p class="mt-1 font-bold text-slate-900">
                                    {{ $profile?->budget ?: 'Not specified' }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Application Summary --}}
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-200 px-6 py-5">

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                            <div>

                                <h3 class="text-lg font-extrabold text-slate-950">
                                    Application Summary
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Latest application information for this student.
                                </p>

                            </div>


                            @if ($latestApplication)

                                <span class="inline-flex w-fit rounded-full px-3 py-1.5 text-xs font-bold {{ $statusClass }}">
                                    {{ $applicationStatus }}
                                </span>

                            @else

                                <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">
                                    No application
                                </span>

                            @endif

                        </div>

                    </div>


                    @if ($latestApplication)

                        <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-3">

                            <div>

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    University
                                </p>

                                <p class="mt-1 font-bold text-slate-900">
                                    {{ $latestApplication->university?->name ?? 'Not specified' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Consultant
                                </p>

                                <p class="mt-1 font-bold text-slate-900">
                                    {{ $latestApplication->consultant?->name ?? 'Not assigned' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Status
                                </p>

                                <p class="mt-1 font-bold text-slate-900">
                                    {{ $applicationStatus }}
                                </p>

                            </div>

                        </div>

                    @else

                        <div class="p-6">

                            <div class="rounded-2xl bg-slate-50 p-5">

                                <p class="font-bold text-slate-800">
                                    No application found
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    This student has not submitted an application yet.
                                </p>

                            </div>

                        </div>

                    @endif

                </div>


                {{-- Application History --}}
                @if ($applications->count() > 0)

                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-200 px-6 py-5">

                            <h3 class="text-lg font-extrabold text-slate-950">
                                Application History
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                All applications submitted by this student.
                            </p>

                        </div>


                        <div class="divide-y divide-slate-200">

                            @foreach ($applications as $application)

                                @php

                                    $historyStatus = ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $application->status ?? 'unknown'
                                        )
                                    );

                                    $historyClass = match ($application->status) {

                                        'accepted' =>
                                            'bg-emerald-100 text-emerald-700',

                                        'rejected' =>
                                            'bg-red-100 text-red-700',

                                        'under_review' =>
                                            'bg-blue-100 text-blue-700',

                                        'submitted' =>
                                            'bg-indigo-100 text-indigo-700',

                                        'withdrawn' =>
                                            'bg-slate-200 text-slate-700',

                                        default =>
                                            'bg-slate-100 text-slate-600',

                                    };

                                @endphp


                                <div class="flex flex-col gap-4 p-6 md:flex-row md:items-center md:justify-between">

                                    <div>

                                        <p class="font-bold text-slate-900">
                                            {{ $application->university?->name ?? 'University not specified' }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Consultant:
                                            {{ $application->consultant?->name ?? 'Not assigned' }}
                                        </p>

                                        @if ($application->created_at)

                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ $application->created_at->format('d M Y, h:i A') }}
                                            </p>

                                        @endif

                                    </div>


                                    <span class="inline-flex w-fit rounded-full px-3 py-1.5 text-xs font-bold {{ $historyClass }}">
                                        {{ $historyStatus }}
                                    </span>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif

            @endif


            {{-- Visit Activity --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5">

                    <h3 class="text-lg font-extrabold text-slate-950">
                        Visit Activity
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Visitor activity recorded by UniBridge.
                    </p>

                </div>


                <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2 lg:grid-cols-4">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            First Visit
                        </p>

                        <p class="mt-1 font-semibold text-slate-900">
                            {{ $firstVisit?->visited_at?->format('d M Y, h:i A') ?? 'Not available' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Last Visit
                        </p>

                        <p class="mt-1 font-semibold text-slate-900">
                            {{ $lastVisit?->visited_at?->format('d M Y, h:i A') ?? 'Not available' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Visit Type
                        </p>

                        <p class="mt-1 font-semibold text-slate-900">
                            {{ $visitType }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Last Activity
                        </p>

                        <p class="mt-1 font-semibold text-slate-900">
                            {{ $lastActivity?->format('d M Y, h:i A') ?? 'Not available' }}
                        </p>

                    </div>

                </div>

            </div>


            {{-- Visitor Information --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">


                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-200 px-6 py-5">

                        <h3 class="text-lg font-extrabold text-slate-950">
                            Visitor Information
                        </h3>

                    </div>


                    <div class="space-y-5 p-6">

                        <div>

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Name
                            </p>

                            <p class="mt-1 font-semibold text-slate-900">
                                {{ $visitorName }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Email
                            </p>

                            <p class="mt-1 break-all font-semibold text-slate-900">
                                {{ $visitorEmail }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Role
                            </p>

                            <p class="mt-1 font-semibold capitalize text-slate-900">
                                {{ $visitorRole ?? 'Guest' }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                IP Address
                            </p>

                            <p class="mt-1 font-semibold text-slate-900">
                                {{ $visitor->ip_address ?? 'Not available' }}
                            </p>

                        </div>

                    </div>

                </div>


                {{-- Consultant Remarks --}}
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-200 px-6 py-5">

                        <h3 class="text-lg font-extrabold text-slate-950">
                            Consultant Remarks
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Notes saved against this visitor.
                        </p>

                    </div>


                    <div class="p-6">

                        @if ($visitor->consultant_remarks)

                            <div class="rounded-2xl bg-slate-50 p-5">

                                <p class="whitespace-pre-line text-sm leading-6 text-slate-700">
                                    {{ $visitor->consultant_remarks }}
                                </p>

                            </div>

                        @else

                            <div class="rounded-2xl bg-slate-50 p-5">

                                <p class="font-semibold text-slate-700">
                                    No remarks added.
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    Lead notes can now be managed from the Lead Management section above.
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

            </div>


        </div>

    </div>

</x-app-layout>