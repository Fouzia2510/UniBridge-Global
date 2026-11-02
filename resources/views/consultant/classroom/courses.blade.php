<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Google Classroom
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    View and manage your Google Classroom classes through UniBridge.
                </p>
            </div>

            <a
                href="{{ route('consultant.dashboard') }}"
                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800"
            >
                â† Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                    <div class="font-semibold text-red-800">
                        Google Classroom Error
                    </div>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Page Header Card --}}
            <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-500 px-6 py-8 text-white sm:px-8">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <div class="mb-3 inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">
                                Google Classroom Integration
                            </div>

                            <h1 class="text-3xl font-bold">
                                Your Classes
                            </h1>

                            <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-50">
                                View your Google Classroom classes, enrolled students,
                                coursework and learning materials directly through UniBridge.
                            </p>
                        </div>

                        <div class="shrink-0 rounded-2xl bg-white/15 px-5 py-4 text-center backdrop-blur-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-100">
                                Connected Classes
                            </p>

                            <p class="mt-1 text-3xl font-bold">
                                {{ count($courses ?? []) }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>

            {{-- Classes --}}
            @if (!empty($courses))

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">

                    @foreach ($courses as $course)

                        @php
                            $courseId = $course['id'] ?? null;
                            $courseName = $course['name'] ?? 'Untitled Class';
                            $courseSection = $course['section'] ?? null;
                            $courseSubject = $course['subject'] ?? null;
                            $courseRoom = $course['room'] ?? null;
                            $courseState = $course['course_state'] ?? null;
                            $courseDescription = $course['description'] ?? null;
                            $courseLink = $course['alternate_link'] ?? null;
                            $enrollmentCode = $course['enrollment_code'] ?? null;
                        @endphp

                        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                            {{-- Card Header --}}
                            <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-500 px-6 py-6 text-white">

                                <div class="flex items-start justify-between gap-4">

                                    <div class="min-w-0">

                                        <div class="mb-3 inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">
                                            Google Classroom
                                        </div>

                                        <h2 class="break-words text-xl font-bold">
                                            {{ $courseName }}
                                        </h2>

                                        @if ($courseSection)
                                            <p class="mt-1 text-sm text-blue-100">
                                                {{ $courseSection }}
                                            </p>
                                        @endif

                                    </div>

                                    @if ($courseState)
                                        <span class="shrink-0 rounded-full bg-white/20 px-2.5 py-1 text-xs font-semibold">
                                            {{ $courseState }}
                                        </span>
                                    @endif

                                </div>

                            </div>

                            {{-- Card Body --}}
                            <div class="p-6">

                                @if ($courseDescription)
                                    <p class="mb-5 line-clamp-3 text-sm leading-6 text-gray-600">
                                        {{ $courseDescription }}
                                    </p>
                                @else
                                    <p class="mb-5 text-sm text-gray-400">
                                        No class description provided.
                                    </p>
                                @endif

                                <div class="space-y-3">

                                    {{-- Subject --}}
                                    <div class="flex items-center justify-between gap-4 rounded-lg bg-gray-50 px-4 py-3">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Subject
                                        </span>

                                        <span class="text-right text-sm font-semibold text-gray-900">
                                            {{ $courseSubject ?: 'Not specified' }}
                                        </span>
                                    </div>

                                    {{-- Room --}}
                                    <div class="flex items-center justify-between gap-4 rounded-lg bg-gray-50 px-4 py-3">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Room
                                        </span>

                                        <span class="text-right text-sm font-semibold text-gray-900">
                                            {{ $courseRoom ?: 'Not specified' }}
                                        </span>
                                    </div>

                                    {{-- Course ID --}}
                                    <div class="rounded-lg bg-gray-50 px-4 py-3">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Course ID
                                        </span>

                                        <p class="mt-1 break-all font-mono text-xs font-semibold text-gray-800">
                                            {{ $courseId ?: 'Not available' }}
                                        </p>
                                    </div>

                                    {{-- Enrollment Code --}}
                                    @if ($enrollmentCode)
                                        <div class="flex items-center justify-between gap-4 rounded-lg bg-gray-50 px-4 py-3">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Class Code
                                            </span>

                                            <span class="font-mono text-sm font-bold text-gray-900">
                                                {{ $enrollmentCode }}
                                            </span>
                                        </div>
                                    @endif

                                </div>

                                {{-- Actions --}}
                                <div class="mt-6 flex flex-col gap-3 sm:flex-row">

                                    @if ($courseId)

                                        <a href="{{ route('consultant.classroom.course', ['courseId' => $courseId]) }}" class="relative z-50 inline-flex flex-1 items-center justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700" style="pointer-events:auto; cursor:pointer; position:relative; z-index:9999; text-decoration:none;">
                                            View Class Details â†’
                                        </a>

                                    @endif

                                    @if ($courseLink)

                                        <a
                                            href="{{ $courseLink }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                        >
                                            Google Classroom â†—
                                        </a>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                {{-- No Classes --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div class="px-6 py-16 text-center">

                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 text-3xl">
                            ðŸ“š
                        </div>

                        <h3 class="mt-5 text-xl font-bold text-gray-900">
                            No Google Classroom classes found
                        </h3>

                        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-gray-500">
                            No active Google Classroom classes were returned for the
                            connected Google account.
                        </p>

                        <a
                            href="{{ route('classroom.providers.google.connect') }}"
                            class="mt-6 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                        >
                            Reconnect Google Classroom
                        </a>

                    </div>

                </div>

            @endif

        </div>
    </div>

</x-app-layout>

