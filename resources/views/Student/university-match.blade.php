<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-sm font-bold text-indigo-600">
                    Student Portal
                </p>

                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">
                    University Match Engine
                </h2>

                <p class="mt-1 text-sm font-medium text-slate-600">
                    Discover universities that match your academic profile.
                </p>
            </div>

            <a
                href="{{ route('student.dashboard') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Dashboard
            </a>

        </div>
    </x-slot>


    <div class="unibridge-match-page min-h-screen bg-slate-50 py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">


            {{-- HERO --}}
            <div class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-500 shadow-lg">

                <div class="px-6 py-8 sm:px-8 lg:px-10">

                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                        <div class="max-w-3xl">

                            <div class="mb-4 inline-flex items-center gap-3 rounded-full bg-white/15 px-4 py-2 ring-1 ring-white/20">

                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-slate-900">

                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 3h5A1.5 1.5 0 0116 4.5v1A1.5 1.5 0 0114.5 7h-5A1.5 1.5 0 018 5.5v-1A1.5 1.5 0 019.5 3z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h-.5A2.5 2.5 0 003 8.5v11A2.5 2.5 0 005.5 22h13a2.5 2.5 0 002.5-2.5v-11A2.5 2.5 0 0018.5 6H18"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8M8 16h5"/>
                                    </svg>

                                </span>

                                <span class="text-sm font-bold !text-white">
                                    Smart University Matching
                                </span>

                            </div>


                            <h1 class="text-3xl font-black tracking-tight !text-white sm:text-4xl">
                                Discover Your Best-Fit Universities
                            </h1>


                            <p class="mt-4 max-w-2xl text-sm font-medium leading-7 !text-indigo-50 sm:text-base">
                                UniBridge analyzes your academic profile, English score,
                                destination and study preferences to identify suitable universities.
                            </p>

                        </div>


                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-white shadow-lg">

                            <svg class="h-10 w-10 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l1.4 4.2L18 8.5l-4.6 1.3L12 14l-1.4-4.2L6 8.5l4.6-1.3L12 3z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l.7 2.3L22 17l-2.3.7L19 20l-.7-2.3L16 17l2.3-.7L19 14z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 14l.6 1.9L7.5 16.5l-1.9.6L5 19l-.6-1.9-1.9-.6 1.9-.6L5 14z"/>
                            </svg>

                        </div>

                    </div>

                </div>

            </div>


            {{-- PROFILE --}}
            @if($profile)

                <div class="mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex items-start gap-3">

                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">

                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 21a8 8 0 0116 0"/>
                                    </svg>

                                </div>

                                <div>

                                    <h2 class="text-xl font-extrabold !text-slate-900">
                                        Your Matching Profile
                                    </h2>

                                    <p class="mt-1 text-sm font-medium !text-slate-600">
                                        Details used by UniBridge AI for your recommendations.
                                    </p>

                                </div>

                            </div>


                            <a
                                href="{{ route('student.profile.edit') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-700"
                            >

                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.1 2.1 0 013 3L8 18l-4 1 1-4L16.5 3.5z"/>
                                </svg>

                                Edit Profile
                            </a>

                        </div>

                    </div>


                    <div class="p-6">

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">


                            {{-- CGPA --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                                <div class="mb-4 flex items-center gap-3">

                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5M4 19h16"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16v-5M12 16V8M16 16v-3"/>
                                        </svg>

                                    </div>

                                    <span class="text-sm font-bold !text-slate-700">
                                        CGPA
                                    </span>

                                </div>

                                <p class="text-2xl font-black !text-slate-900">
                                    {{ $profile->cgpa ?? '—' }}
                                </p>

                            </div>


                            {{-- ENGLISH --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                                <div class="mb-4 flex items-center gap-3">

                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8M8 12h5M8 16h6"/>
                                        </svg>

                                    </div>

                                    <span class="text-sm font-bold !text-slate-700">
                                        English
                                    </span>

                                </div>

                                <p class="text-2xl font-black !text-slate-900">
                                    {{ $profile->english_score ?? '—' }}
                                </p>

                                @if($profile->english_test)

                                    <p class="mt-1 text-xs font-bold !text-slate-500">
                                        {{ $profile->english_test }}
                                    </p>

                                @endif

                            </div>


                            {{-- DESTINATION --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                                <div class="mb-4 flex items-center gap-3">

                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-cyan-700">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"/>
                                        </svg>

                                    </div>

                                    <span class="text-sm font-bold !text-slate-700">
                                        Destination
                                    </span>

                                </div>

                                <p class="text-xl font-black !text-slate-900">
                                    {{ $profile->preferred_country ?? '—' }}
                                </p>

                            </div>


                            {{-- DEGREE --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                                <div class="mb-4 flex items-center gap-3">

                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10l9-5 9 5-9 5-9-5z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 12.5V17c2.5 2 7.5 2 10 0v-4.5"/>
                                        </svg>

                                    </div>

                                    <span class="text-sm font-bold !text-slate-700">
                                        Degree
                                    </span>

                                </div>

                                <p class="text-base font-black leading-6 !text-slate-900">
                                    {{ $profile->degree ?? $profile->preferred_degree_level ?? '—' }}
                                </p>

                            </div>


                            {{-- SUBJECT --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                                <div class="mb-4 flex items-center gap-3">

                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 17V7l6-3 6 3v10"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 10h6M9 13h6"/>
                                        </svg>

                                    </div>

                                    <span class="text-sm font-bold !text-slate-700">
                                        Subject
                                    </span>

                                </div>

                                <p class="text-base font-black leading-6 !text-slate-900">
                                    {{ $profile->preferred_subject ?? '—' }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            @endif


            {{-- RECOMMENDATIONS HEADER --}}
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-black uppercase tracking-wide text-indigo-700">

                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-600 text-white">

                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l1.4 4.2L18 8.5l-4.6 1.3L12 14l-1.4-4.2L6 8.5l4.6-1.3L12 3z"/>
                            </svg>

                        </span>

                        AI Recommendations

                    </div>


                    <h2 class="text-2xl font-black !text-slate-900">
                        University Matches
                    </h2>

                    <p class="mt-1 text-sm font-medium !text-slate-600">
                        Universities ranked according to your profile compatibility.
                    </p>

                </div>


                <div class="inline-flex w-fit items-center rounded-xl border border-indigo-200 bg-white px-4 py-2.5 shadow-sm">

                    <span class="mr-2 text-sm font-bold !text-slate-700">
                        Matches
                    </span>

                    <span class="rounded-lg bg-indigo-600 px-2.5 py-1 text-sm font-black text-white">
                        {{ $matches->count() }}
                    </span>

                </div>

            </div>


            {{-- MATCHES --}}
            <div class="space-y-7">

                @forelse($matches as $match)

                    @php
                        $university = $match['university'];
                        $matchScore = $match['match_score'];
                        $reasons = $match['reasons'];
                        $warnings = $match['warnings'];
                    @endphp


                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-md">


                        {{-- UNIVERSITY HEADER --}}
                        <div class="bg-slate-900 px-6 py-6 sm:px-8">

                            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">


                                <div class="flex items-start gap-4">

                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-indigo-700 shadow-sm">

                                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 21V9l7-5 7 5v12"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 21v-5h6v5M8 11h.01M12 11h.01M16 11h.01"/>
                                        </svg>

                                    </div>


                                    <div>

                                        <div class="mb-2 flex flex-wrap items-center gap-2">

                                            <span class="rounded-full bg-indigo-600 px-3 py-1 text-xs font-bold text-white">
                                                {{ $university->country }}
                                            </span>


                                            @if($university->degree_level)

                                                <span class="rounded-full bg-slate-700 px-3 py-1 text-xs font-bold text-white">
                                                    {{ $university->degree_level }}
                                                </span>

                                            @endif

                                        </div>


                                        <h3 class="text-2xl font-black !text-white sm:text-3xl">
                                            {{ $university->name }}
                                        </h3>


                                        <p class="mt-2 flex items-center gap-2 text-sm font-semibold !text-slate-300">

                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6 7-12a7 7 0 10-14 0c0 6 7 12 7 12z"/>
                                                <circle cx="12" cy="9" r="2.5"/>
                                            </svg>

                                            {{ $university->city }}, {{ $university->country }}

                                        </p>

                                    </div>

                                </div>


                                {{-- SCORE --}}
                                <div class="rounded-2xl border border-slate-600 bg-slate-800 px-6 py-4 text-center">

                                    <p class="text-xs font-bold uppercase tracking-widest !text-slate-300">
                                        Match Score
                                    </p>

                                    <div class="mt-1 flex items-center justify-center gap-2">

                                        <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.2 6.8H21l-5.5 4 2.1 6.7-5.6-4.1-5.6 4.1 2.1-6.7-5.5-4h6.8L12 3z"/>
                                        </svg>

                                        <p class="text-4xl font-black !text-white">
                                            {{ $matchScore }}%
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- DETAILS --}}
                        <div class="p-6 sm:p-8">

                            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">


                                {{-- RANKING --}}
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.1 4.4L19 8l-3.5 3.4.8 4.8-4.3-2.3-4.3 2.3.8-4.8L5 8l4.9-.6L12 3z"/>
                                        </svg>

                                    </div>

                                    <p class="text-xs font-bold uppercase tracking-wide !text-slate-500">
                                        Global Ranking
                                    </p>

                                    <p class="mt-1 text-xl font-black !text-slate-900">
                                        {{ $university->ranking ? '#' . $university->ranking : 'Not available' }}
                                    </p>

                                </div>


                                {{-- TUITION --}}
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H7"/>
                                        </svg>

                                    </div>

                                    <p class="text-xs font-bold uppercase tracking-wide !text-slate-500">
                                        Tuition Fee
                                    </p>

                                    <p class="mt-1 text-xl font-black !text-slate-900">
                                        {{ $university->tuition_fee !== null ? number_format($university->tuition_fee) : 'Not available' }}
                                    </p>

                                </div>


                                {{-- CGPA --}}
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5M4 19h16"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16v-5M12 16V8M16 16v-3"/>
                                        </svg>

                                    </div>

                                    <p class="text-xs font-bold uppercase tracking-wide !text-slate-500">
                                        Minimum CGPA
                                    </p>

                                    <p class="mt-1 text-xl font-black !text-slate-900">
                                        {{ $university->min_cgpa ?? '—' }}
                                    </p>

                                </div>


                                {{-- ENGLISH --}}
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-cyan-700">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8M8 12h5M8 16h6"/>
                                        </svg>

                                    </div>

                                    <p class="text-xs font-bold uppercase tracking-wide !text-slate-500">
                                        Minimum English
                                    </p>

                                    <p class="mt-1 text-xl font-black !text-slate-900">
                                        {{ $university->min_english_score ?? '—' }}
                                    </p>

                                </div>

                            </div>


                            {{-- ANALYSIS --}}
                            <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">


                                {{-- WHY MATCHES --}}
                                <div class="overflow-hidden rounded-2xl border border-emerald-200 bg-emerald-50">

                                    <div class="border-b border-emerald-200 bg-emerald-100 px-5 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white">

                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/>
                                                </svg>

                                            </div>

                                            <div>

                                                <h4 class="font-black !text-emerald-950">
                                                    Why This Matches
                                                </h4>

                                                <p class="text-xs font-semibold !text-emerald-700">
                                                    Profile compatibility
                                                </p>

                                            </div>

                                        </div>

                                    </div>


                                    <div class="p-5">

                                        @if(count($reasons))

                                            <ul class="space-y-3">

                                                @foreach($reasons as $reason)

                                                    <li class="flex items-start gap-3">

                                                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-slate-900">

                                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/>
                                                            </svg>

                                                        </span>

                                                        <span class="text-sm font-semibold leading-6 !text-emerald-950">
                                                            {{ $reason }}
                                                        </span>

                                                    </li>

                                                @endforeach

                                            </ul>

                                        @else

                                            <p class="text-sm font-semibold !text-emerald-900">
                                                No specific matching reasons were recorded.
                                            </p>

                                        @endif

                                    </div>

                                </div>


                                {{-- WARNINGS --}}
                                <div class="overflow-hidden rounded-2xl border border-amber-200 bg-amber-50">

                                    <div class="border-b border-amber-200 bg-amber-100 px-5 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-slate-900">

                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4M12 16h.01"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.3 3.8L2.9 17a2 2 0 001.7 3h14.8a2 2 0 001.7-3L13.7 3.8a2 2 0 00-3.4 0z"/>
                                                </svg>

                                            </div>

                                            <div>

                                                <h4 class="font-black !text-slate-900">
                                                    Important Considerations
                                                </h4>

                                                <p class="text-xs font-semibold !text-amber-700">
                                                    Review before applying
                                                </p>

                                            </div>

                                        </div>

                                    </div>


                                    <div class="p-5">

                                        @if(count($warnings))

                                            <ul class="space-y-3">

                                                @foreach($warnings as $warning)

                                                    <li class="flex items-start gap-3">

                                                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 text-slate-900">

                                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4M12 16h.01"/>
                                                            </svg>

                                                        </span>

                                                        <span class="text-sm font-semibold leading-6 !text-amber-950">
                                                            {{ $warning }}
                                                        </span>

                                                    </li>

                                                @endforeach

                                            </ul>

                                        @else

                                            <div class="flex items-center gap-3">

                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white">

                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/>
                                                    </svg>

                                                </span>

                                                <p class="text-sm font-bold !text-emerald-900">
                                                    No important considerations were identified.
                                                </p>

                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </div>


                            {{-- ACTIONS --}}
                            <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">

                                <a
                                    href="{{ route('student.universities.show', $university) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-black text-slate-800 shadow-sm hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700"
                                >

                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"/>
                                        <circle cx="12" cy="12" r="2.5"/>
                                    </svg>

                                    View University →
                                </a>


                                <a
                                    href="{{ route('student.applications.create', $university) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-black text-white shadow-sm hover:bg-indigo-700"
                                >

                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                    </svg>

                                    Start Application →
                                </a>

                            </div>

                        </div>

                    </div>

                @empty


                    {{-- EMPTY STATE --}}
                    <div class="rounded-3xl border border-slate-200 bg-white px-6 py-14 text-center shadow-sm">

                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700">

                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 21V9l7-5 7 5v12"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 21v-5h6v5M8 11h.01M12 11h.01M16 11h.01"/>
                            </svg>

                        </div>

                        <h3 class="mt-5 text-xl font-black !text-slate-900">
                            No University Matches Found
                        </h3>

                        <p class="mx-auto mt-2 max-w-lg text-sm font-medium leading-6 !text-slate-600">
                            Update your student profile preferences and try the matching engine again.
                        </p>

                        <a
                            href="{{ route('student.profile.edit') }}"
                            class="mt-6 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-black text-white hover:bg-indigo-700"
                        >
                            Update Profile
                        </a>

                    </div>

                @endforelse

            </div>


            {{-- BOTTOM NAVIGATION --}}
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <a
                    href="{{ route('student.dashboard') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-700 shadow-sm hover:bg-slate-50"
                >

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>

                    Back to Dashboard
                </a>


                <a
                    href="{{ route('student.universities.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-black text-white hover:bg-indigo-700"
                >
                    Explore All Universities

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/>
                    </svg>

                </a>

            </div>


            {{-- DISCLAIMER --}}
            <div class="mt-6 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">

                <div class="flex items-start gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4M12 17h.01"/>
                            <circle cx="12" cy="12" r="9"/>
                        </svg>

                    </div>

                    <p class="text-xs font-medium leading-6 !text-slate-600">

                        <span class="font-black !text-slate-900">
                            UniBridge AI Matching:
                        </span>

                        Match scores are based on the information available in your student profile.
                        Always review university requirements and application details before applying.

                    </p>

                </div>

            </div>


        </div>

    </div>


    {{-- FINAL VISIBILITY OVERRIDES --}}
    <style>
        .unibridge-match-page h1,
        .unibridge-match-page h2,
        .unibridge-match-page h3,
        .unibridge-match-page h4 {
            opacity: 1 !important;
        }

        .unibridge-match-page .text-slate-900 {
            color: #0f172a !important;
        }

        .unibridge-match-page .text-slate-800 {
            color: #1e293b !important;
        }

        .unibridge-match-page .text-slate-700 {
            color: #334155 !important;
        }

        .unibridge-match-page .text-slate-600 {
            color: #475569 !important;
        }

        .unibridge-match-page .text-slate-500 {
            color: #64748b !important;
        }

        .unibridge-match-page .text-white {
            color: #ffffff !important;
        }

        .unibridge-match-page .text-emerald-950 {
            color: #064e3b !important;
        }

        .unibridge-match-page .text-emerald-900 {
            color: #064e3b !important;
        }

        .unibridge-match-page .text-emerald-700 {
            color: #047857 !important;
        }

        .unibridge-match-page .text-amber-950 {
            color: #78350f !important;
        }

        .unibridge-match-page .text-amber-700 {
            color: #b45309 !important;
        }

        .unibridge-match-page svg {
            flex-shrink: 0;
        }
    </style>

</x-app-layout>