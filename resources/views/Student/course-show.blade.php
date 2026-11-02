<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $course->title }}
                </h2>

                <p class="text-sm text-gray-500">
                    {{ $course->category ?: 'Short Course' }}
                </p>
            </div>

            <a
                href="{{ route('student.my-courses.index') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200"
            >
                ← My Courses
            </a>
        </div>
    </x-slot>

    <div class="py-10">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-5 py-4 text-green-800">
                    <div class="font-semibold">
                        ✓ {{ session('status') }}
                    </div>
                </div>
            @endif

            {{-- Course Overview --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">

                <div class="p-6 sm:p-8">

                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

                        <div class="flex-1">

                            <div class="flex flex-wrap items-center gap-2 mb-3">

                                @if ($course->category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold">
                                        {{ $course->category }}
                                    </span>
                                @endif

                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">
                                    ✓ Enrolled
                                </span>

                            </div>

                            <h1 class="text-3xl font-bold text-gray-900 mb-3">
                                {{ $course->title }}
                            </h1>

                            @if ($course->description)
                                <p class="text-gray-600 leading-7 max-w-3xl">
                                    {{ $course->description }}
                                </p>
                            @endif

                        </div>

                    </div>

                    {{-- Course Information --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">

                        <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                            <div class="text-xs text-gray-500 uppercase tracking-wide">
                                Mode
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $course->mode ?: 'Not specified' }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                            <div class="text-xs text-gray-500 uppercase tracking-wide">
                                Duration
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $course->duration_weeks ?? 0 }} weeks
                            </div>
                        </div>

                        <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                            <div class="text-xs text-gray-500 uppercase tracking-wide">
                                Instructor
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $course->instructor_name ?: 'UniBridge Team' }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                            <div class="text-xs text-gray-500 uppercase tracking-wide">
                                Course Fee
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                ${{ number_format((float) $course->price, 2) }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            {{-- Progress Section --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-8">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">

                    <div>
                        <h2 class="text-xl font-bold text-gray-900">
                            Course Progress
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Complete the lessons below to increase your course progress.
                        </p>
                    </div>

                    <div class="text-right">

                        <div class="text-3xl font-bold text-indigo-600">
                            {{ $progress }}%
                        </div>

                        <div class="text-xs text-gray-500">
                            {{ $completedLessons }} of {{ $totalLessons }} lessons completed
                        </div>

                    </div>

                </div>

                {{-- Progress Bar --}}
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">

                    <div
                        class="bg-indigo-600 h-4 rounded-full transition-all duration-500"
                        style="width: {{ $progress }}%"
                    ></div>

                </div>

                <div class="flex items-center justify-between mt-3 text-sm">

                    <span class="text-green-600 font-medium">
                        Completed: {{ $progress }}%
                    </span>

                    <span class="text-gray-500">
                        Remaining: {{ $remainingPercent }}%
                    </span>

                </div>

                {{-- Certificate --}}
                <div class="mt-6">

                    @if ($enrollment->certificate_issued || $progress >= 90)

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-xl bg-green-50 border border-green-200 p-5">

                            <div>

                                <div class="font-bold text-green-800">
                                    🎓 Certificate Eligible
                                </div>

                                <p class="text-sm text-green-700 mt-1">
                                    You have reached the required progress for your course certificate.
                                </p>

                            </div>

                            <a
                                href="{{ route('student.certificate.download', $enrollment) }}"
                                class="inline-flex items-center justify-center px-5 py-2.5 bg-green-600 text-white rounded-lg font-semibold text-sm hover:bg-green-700"
                            >
                                Download Certificate
                            </a>

                        </div>

                    @else

                        <div class="rounded-xl bg-gray-50 border border-gray-200 p-5">

                            <div class="font-semibold text-gray-800">
                                🎓 Certificate
                            </div>

                            <p class="text-sm text-gray-500 mt-1">
                                Complete at least 90% of the course to become certificate eligible.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

            {{-- Modules --}}
            <div class="mb-4">

                <h2 class="text-2xl font-bold text-gray-900">
                    Course Modules
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Select a module to view its lessons.
                </p>

            </div>

            @if ($course->modules->count())

                <div class="space-y-5">

                    @foreach ($course->modules as $module)

                        @php
                            $moduleTotalLessons = $module->lessons->count();

                            $moduleCompletedLessons = $module->lessons
                                ->whereIn('id', $completedLessonIds)
                                ->count();

                            $moduleProgress = $moduleTotalLessons > 0
                                ? (int) round(
                                    ($moduleCompletedLessons / $moduleTotalLessons) * 100
                                )
                                : 0;
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                            <div class="p-6">

                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                                    <div class="flex-1">

                                        <div class="flex items-start gap-4">

                                            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                                                {{ $loop->iteration }}
                                            </div>

                                            <div>

                                                <h3 class="text-xl font-bold text-gray-900">
                                                    {{ $module->title }}
                                                </h3>

                                                @if ($module->description)
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        {{ $module->description }}
                                                    </p>
                                                @endif

                                                <div class="text-xs text-gray-500 mt-2">
                                                    {{ $moduleCompletedLessons }} / {{ $moduleTotalLessons }}
                                                    lessons completed

                                                    @if ($module->estimated_minutes)
                                                        · {{ $module->estimated_minutes }} minutes
                                                    @endif
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="flex items-center gap-3">

                                        <span class="text-sm font-semibold text-indigo-600">
                                            {{ $moduleProgress }}%
                                        </span>

                                        <a
                                            href="{{ route('student.my-courses.module', [
                                                'course' => $course,
                                                'module' => $module
                                            ]) }}"
                                            class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700"
                                        >
                                            Open Module →
                                        </a>

                                    </div>

                                </div>

                                {{-- Module Progress --}}
                                <div class="mt-5">

                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">

                                        <div
                                            class="bg-indigo-500 h-2 rounded-full"
                                            style="width: {{ $moduleProgress }}%"
                                        ></div>

                                    </div>

                                </div>

                                {{-- Lesson Preview --}}
                                @if ($module->lessons->count())

                                    <div class="mt-5 pt-5 border-t border-gray-100">

                                        <div class="text-sm font-semibold text-gray-700 mb-3">
                                            Lessons
                                        </div>

                                        <div class="space-y-2">

                                            @foreach ($module->lessons as $lesson)

                                                @php
                                                    $lessonCompleted = in_array(
                                                        $lesson->id,
                                                        $completedLessonIds
                                                    );
                                                @endphp

                                                <a
                                                    href="{{ route('student.my-courses.lesson', [
                                                        'course' => $course,
                                                        'module' => $module,
                                                        'lesson' => $lesson
                                                    ]) }}"
                                                    class="flex items-center justify-between gap-4 rounded-lg border border-gray-100 px-4 py-3 hover:bg-gray-50 transition"
                                                >

                                                    <div class="flex items-center gap-3 min-w-0">

                                                        @if ($lessonCompleted)

                                                            <span class="flex-shrink-0 w-7 h-7 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-sm font-bold">
                                                                ✓
                                                            </span>

                                                        @else

                                                            <span class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs font-bold">
                                                                {{ $loop->iteration }}
                                                            </span>

                                                        @endif

                                                        <div class="min-w-0">

                                                            <div class="font-medium text-gray-900 truncate">
                                                                {{ $lesson->title }}
                                                            </div>

                                                            <div class="text-xs text-gray-500">
                                                                {{ ucfirst($lesson->lesson_type ?: 'Lesson') }}

                                                                @if ($lesson->duration_minutes)
                                                                    · {{ $lesson->duration_minutes }} min
                                                                @endif
                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="flex-shrink-0">

                                                        @if ($lessonCompleted)

                                                            <span class="text-xs font-semibold text-green-600">
                                                                Completed
                                                            </span>

                                                        @else

                                                            <span class="text-xs font-semibold text-indigo-600">
                                                                Open →
                                                            </span>

                                                        @endif

                                                    </div>

                                                </a>

                                            @endforeach

                                        </div>

                                    </div>

                                @else

                                    <div class="mt-5 rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-500">
                                        No lessons available in this module yet.
                                    </div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">

                    <div class="text-4xl mb-3">
                        📚
                    </div>

                    <h3 class="text-lg font-bold text-gray-900">
                        No Modules Available
                    </h3>

                    <p class="text-sm text-gray-500 mt-2">
                        Modules and lessons for this course have not been added yet.
                    </p>

                </div>

            @endif

            {{-- Bottom Navigation --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-8">

                <a
                    href="{{ route('student.my-courses.index') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200"
                >
                    ← Back to My Courses
                </a>

                <a
                    href="{{ route('student.courses.index') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700"
                >
                    Browse All Courses
                </a>

            </div>

        </div>

    </div>

</x-app-layout>