<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">
                    Student Portal
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Google Classroom
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    View your enrolled classes, coursework and Google Classroom activity.
                </p>
            </div>

            <a
                href="{{ route('student.dashboard') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                ← Student Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                    <div class="font-bold">
                        Google Classroom Error
                    </div>

                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Header Banner --}}
            <div class="overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-500 p-6 text-white shadow-xl sm:p-8">

                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                    <div class="max-w-2xl">
                        <div class="mb-3 inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white">
                            Google Classroom
                        </div>

                        <h1 class="text-2xl font-extrabold sm:text-3xl">
                            Your Classes
                        </h1>

                        <p class="mt-2 text-sm leading-6 text-white/85 sm:text-base">
                            Classes available through your Google Classroom account.
                            Open a class to view coursework and your submissions.
                        </p>
                    </div>

                    <div>
                        <a
                            href="{{ route('google.classroom.connect') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-extrabold text-indigo-700 shadow-lg transition hover:bg-indigo-50"
                        >
                            Reconnect Google Classroom
                        </a>
                    </div>

                </div>
            </div>

            @php
                /*
                |--------------------------------------------------------------------------
                | StudentGoogleClassroomService returns an array.
                | Normalize it here so the Blade works whether the
                | service returns an array or a Laravel Collection.
                |--------------------------------------------------------------------------
                */

                $courseList = is_array($courses)
                    ? $courses
                    : $courses->toArray();

                $courseCount = count($courseList);
            @endphp

            {{-- Course List Header --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h3 class="text-xl font-extrabold text-slate-950">
                        My Classes
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Classes available through your Google Classroom account.
                    </p>
                </div>

                <div class="rounded-xl bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-700">
                    {{ $courseCount }}
                    {{ $courseCount === 1 ? 'Class' : 'Classes' }}
                </div>

            </div>

            {{-- No Courses --}}
            @if($courseCount === 0)

                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">

                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-2xl text-indigo-600">
                        🎓
                    </div>

                    <h3 class="text-xl font-extrabold text-slate-950">
                        No Classes Found
                    </h3>

                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                        No Google Classroom classes are currently available for your account.
                        If your teacher has already added you to a class, try reconnecting Google Classroom.
                    </p>

                    <a
                        href="{{ route('google.classroom.connect') }}"
                        class="mt-6 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        Connect Google Classroom
                    </a>

                </div>

            @else

                {{-- Courses Grid --}}
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

                    @foreach($courseList as $course)

                        @php
                            $courseId = $course['id'] ?? null;
                            $courseName = $course['name'] ?? 'Untitled Class';
                            $section = $course['section'] ?? null;
                            $description = $course['description'] ?? null;
                            $room = $course['room'] ?? null;
                            $enrollmentCode = $course['enrollment_code'] ?? null;
                            $state = $course['state'] ?? 'UNKNOWN';
                        @endphp

                        <div class="flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                            {{-- Card Top --}}
                            <div class="bg-gradient-to-br from-indigo-600 to-blue-600 p-6 text-white">

                                <div class="flex items-start justify-between gap-4">

                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-2xl">
                                        🎓
                                    </div>

                                    <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold">
                                        {{ $state }}
                                    </span>

                                </div>

                                <h3 class="mt-5 text-xl font-extrabold leading-tight">
                                    {{ $courseName }}
                                </h3>

                                @if($section)
                                    <p class="mt-1 text-sm font-medium text-white/80">
                                        {{ $section }}
                                    </p>
                                @endif

                            </div>

                            {{-- Card Body --}}
                            <div class="flex flex-1 flex-col p-6">

                                @if($description)
                                    <p class="line-clamp-3 text-sm leading-6 text-slate-600">
                                        {{ $description }}
                                    </p>
                                @else
                                    <p class="text-sm italic text-slate-400">
                                        No class description available.
                                    </p>
                                @endif

                                <div class="mt-5 space-y-3">

                                    @if($room)
                                        <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                                            <span class="text-base">
                                                📍
                                            </span>

                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                                    Room
                                                </p>

                                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                                    {{ $room }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($enrollmentCode)
                                        <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                                            <span class="text-base">
                                                🔑
                                            </span>

                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                                    Enrollment Code
                                                </p>

                                                <p class="mt-1 font-mono text-sm font-bold text-slate-700">
                                                    {{ $enrollmentCode }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                </div>

                                {{-- Action --}}
                                <div class="mt-auto pt-6">

                                    @if($courseId)

                                        <a
                                            href="{{ route('student.classroom.course', ['courseId' => $courseId]) }}"
                                            class="flex w-full items-center justify-center rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                        >
                                            View Class
                                            <span class="ml-2">
                                                →
                                            </span>
                                        </a>

                                    @else

                                        <div class="rounded-xl bg-red-50 px-4 py-3 text-center text-sm font-semibold text-red-600">
                                            Class ID unavailable.
                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>
    </div>

</x-app-layout>