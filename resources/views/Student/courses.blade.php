<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-indigo-600">
                    Student Academy
                </p>

                <h2 class="text-2xl font-bold text-slate-900">
                    Academy Courses
                </h2>

                <p class="mt-1 text-sm text-slate-600">
                    Explore available courses and build your skills with UniBridge.
                </p>
            </div>

            <a
                href="{{ route('student.dashboard') }}"
                class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
            >
                ← Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-8 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 p-6 text-white shadow-sm">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-wide text-indigo-100">
                        Learn & Grow
                    </p>

                    <h3 class="mt-2 text-2xl font-bold">
                        Improve your skills with UniBridge Academy
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-indigo-100">
                        Choose a course, enroll, and continue your learning journey
                        through structured modules and lessons.
                    </p>
                </div>
            </div>

            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">
                        Available Courses
                    </h3>

                    <p class="mt-1 text-sm text-slate-600">
                        Select a course to view its details and enroll.
                    </p>
                </div>

                <div class="rounded-full bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700">
                    {{ $courses->count() }} Courses
                </div>
            </div>

            @if ($courses->count() > 0)

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                    @foreach ($courses as $course)

                        @php
                            $isEnrolled = in_array(
                                $course->id,
                                $enrolledCourseIds ?? []
                            );
                        @endphp

                        <div class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                            <div class="h-2 bg-indigo-600"></div>

                            <div class="flex flex-1 flex-col p-6">

                                <div class="mb-4 flex items-start justify-between gap-3">

                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-xl font-bold text-indigo-700">
                                        🎓
                                    </div>

                                    @if ($isEnrolled)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                            Enrolled
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            Available
                                        </span>
                                    @endif

                                </div>

                                <h4 class="text-lg font-bold text-slate-900">
                                    {{ $course->title }}
                                </h4>

                                <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">
                                    {{ $course->description ?: 'Start learning this course through UniBridge Academy.' }}
                                </p>

                                <div class="mt-5 grid grid-cols-2 gap-3">

                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <p class="text-xs font-medium text-slate-500">
                                            Mode
                                        </p>

                                        <p class="mt-1 text-sm font-semibold text-slate-800">
                                            {{ $course->mode ?: 'Online' }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <p class="text-xs font-medium text-slate-500">
                                            Duration
                                        </p>

                                        <p class="mt-1 text-sm font-semibold text-slate-800">
                                            {{ $course->duration ?: 'Flexible' }}
                                        </p>
                                    </div>

                                </div>

                                <div class="mt-6">

                                    @if ($isEnrolled)

                                        <a
                                            href="{{ route('student.courses.show', $course) }}"
                                            class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                        >
                                            Continue Course →
                                        </a>

                                    @else

                                        <div class="flex flex-col gap-2">

                                            <a
                                                href="{{ route('student.courses.show', $course) }}"
                                                class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                            >
                                                View Course
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('student.courses.enroll', $course) }}"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                                >
                                                    Enroll Now
                                                </button>
                                            </form>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 text-2xl">
                        🎓
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-slate-900">
                        No courses available
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">
                        There are currently no Academy courses available.
                        Please check again later.
                    </p>

                </div>

            @endif

        </div>
    </div>

</x-app-layout>