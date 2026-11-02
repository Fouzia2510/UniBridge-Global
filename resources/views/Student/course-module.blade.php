<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-indigo-600">
                    {{ $course->title }}
                </p>

                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $courseModule->title }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ $courseModule->description }}
                </p>
            </div>

            <a
                href="{{ route('student.my-courses.show', $course) }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
            >
                ← Back to Course
            </a>
        </div>
    </x-slot>

    @php
        $moduleLessons = $courseModule->activeLessons;

        $moduleTotalLessons = $moduleLessons->count();

        $moduleCompletedLessons = $moduleLessons
            ->filter(fn ($lesson) => in_array($lesson->id, $completedLessonIds ?? []))
            ->count();

        $moduleProgress = $moduleTotalLessons > 0
            ? round(($moduleCompletedLessons / $moduleTotalLessons) * 100)
            : 0;
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- Module Summary --}}
            <div class="mb-8 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">

                <div class="border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-6 text-white">

                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-indigo-100">
                                Module
                            </p>

                            <h1 class="mt-1 text-2xl font-bold">
                                {{ $courseModule->title }}
                            </h1>

                            <p class="mt-2 text-sm text-indigo-100">
                                {{ $moduleTotalLessons }}
                                {{ $moduleTotalLessons === 1 ? 'Lesson' : 'Lessons' }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-white/15 px-5 py-4 text-center backdrop-blur">

                            <div class="text-3xl font-bold">
                                {{ $moduleProgress }}%
                            </div>

                            <div class="text-xs font-semibold text-indigo-100">
                                Module Progress
                            </div>

                        </div>

                    </div>

                </div>


                <div class="px-6 py-5">

                    <div class="mb-2 flex items-center justify-between text-sm">

                        <span class="font-semibold text-gray-700">
                            {{ $moduleCompletedLessons }}
                            of
                            {{ $moduleTotalLessons }}
                            lessons completed
                        </span>

                        <span class="font-bold text-indigo-600">
                            {{ $moduleProgress }}%
                        </span>

                    </div>


                    <div class="h-3 w-full overflow-hidden rounded-full bg-gray-200">

                        <div
                            class="h-full rounded-full bg-indigo-600 transition-all duration-500"
                            style="width: {{ $moduleProgress }}%;"
                        ></div>

                    </div>

                </div>

            </div>


            {{-- Lessons --}}
            <div class="space-y-8">

                @forelse($moduleLessons as $index => $lesson)

                    @php
                        $isCompleted = in_array(
                            $lesson->id,
                            $completedLessonIds ?? []
                        );

                        $videoId = null;

                        if ($lesson->video_url) {

                            $videoUrl = trim($lesson->video_url);

                            if (preg_match(
                                '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&?\/]+)/',
                                $videoUrl,
                                $matches
                            )) {
                                $videoId = $matches[1];
                            }
                        }
                    @endphp


                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">


                        {{-- Lesson Header --}}
                        <div class="border-b border-gray-200 px-6 py-5">

                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                                <div class="flex items-start gap-4">

                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full
                                        {{ $isCompleted
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-indigo-100 text-indigo-700' }}"
                                    >

                                        @if($isCompleted)
                                            ✓
                                        @else
                                            {{ $index + 1 }}
                                        @endif

                                    </div>


                                    <div>

                                        <div class="flex flex-wrap items-center gap-2">

                                            <h3 class="text-xl font-bold text-gray-900">
                                                {{ $lesson->title }}
                                            </h3>

                                            @if($isCompleted)

                                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                                    Completed
                                                </span>

                                            @endif

                                        </div>


                                        @if($lesson->description)

                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ $lesson->description }}
                                            </p>

                                        @endif


                                        <div class="mt-3 flex flex-wrap items-center gap-2">

                                            @if($lesson->lesson_type)

                                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold capitalize text-gray-700">
                                                    {{ $lesson->lesson_type }}
                                                </span>

                                            @endif


                                            @if($lesson->duration_minutes)

                                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                                    {{ $lesson->duration_minutes }} minutes
                                                </span>

                                            @endif


                                            @if($lesson->is_preview)

                                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                                    Preview
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </div>


                                <a
                                    href="{{ route('student.my-courses.lesson', [
                                        'course' => $course,
                                        'module' => $courseModule,
                                        'lesson' => $lesson
                                    ]) }}"
                                    class="inline-flex shrink-0 items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-700"
                                >
                                    Open Lesson →
                                </a>

                            </div>

                        </div>


                        {{-- VIDEO SECTION --}}
                        @if($videoId)

                            <div class="bg-gray-950 px-4 py-5 sm:px-6">

                                <div class="mx-auto max-w-5xl">

                                    <div class="mb-3 flex items-center justify-between">

                                        <h4 class="text-sm font-bold text-white">
                                            Lesson Video
                                        </h4>

                                        <span class="text-xs font-medium text-gray-300">
                                            Watch the lesson
                                        </span>

                                    </div>


                                    <div class="relative overflow-hidden rounded-xl bg-black shadow-lg">

                                        <div class="aspect-video">

                                            <iframe
                                                class="h-full w-full"
                                                src="https://www.youtube.com/embed/{{ $videoId }}"
                                                title="{{ $lesson->title }}"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                allowfullscreen
                                            ></iframe>

                                        </div>

                                    </div>

                                </div>

                            </div>


                        @elseif($lesson->video_url)

                            {{-- Non-YouTube video URL --}}
                            <div class="border-b border-gray-200 bg-gray-50 px-6 py-5">

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                    <div>

                                        <p class="font-semibold text-gray-900">
                                            Lesson Video Available
                                        </p>

                                        <p class="mt-1 text-sm text-gray-600">
                                            Open the video using the button.
                                        </p>

                                    </div>


                                    <a
                                        href="{{ $lesson->video_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700"
                                    >
                                        ▶ Open Video
                                    </a>

                                </div>

                            </div>


                        @else

                            {{-- No video URL --}}
                            <div class="border-b border-gray-200 bg-gray-50 px-6 py-5">

                                <div class="rounded-xl border border-dashed border-gray-300 bg-white px-5 py-4">

                                    <p class="text-sm font-semibold text-gray-700">
                                        No video has been added to this lesson yet.
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        Add a video URL to this lesson in the database/admin panel and it will appear here automatically.
                                    </p>

                                </div>

                            </div>

                        @endif


                        {{-- Lesson Content --}}
                        @if($lesson->content)

                            <div class="px-6 py-6">

                                <h4 class="mb-4 text-lg font-bold text-gray-900">
                                    Lesson Content
                                </h4>

                                <div class="rounded-xl bg-gray-50 p-5">

                                    <div class="whitespace-pre-line text-sm leading-7 text-gray-700">
                                        {{ $lesson->content }}
                                    </div>

                                </div>

                            </div>

                        @endif


                        {{-- Resource --}}
                        @if($lesson->resource_url)

                            <div class="border-t border-gray-200 px-6 py-5">

                                <a
                                    href="{{ $lesson->resource_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50"
                                >
                                    📄 Open Lesson Resource
                                </a>

                            </div>

                        @endif

                    </div>


                @empty

                    <div class="rounded-2xl bg-white px-6 py-12 text-center shadow-sm ring-1 ring-gray-200">

                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-2xl">
                            📚
                        </div>

                        <h3 class="mt-4 text-lg font-bold text-gray-900">
                            No lessons available
                        </h3>

                        <p class="mt-2 text-sm text-gray-600">
                            This module does not have any active lessons yet.
                        </p>

                    </div>

                @endforelse

            </div>


            {{-- Bottom Navigation --}}
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-between">

                <a
                    href="{{ route('student.my-courses.show', $course) }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50"
                >
                    ← Back to Course
                </a>


                @if($moduleLessons->isNotEmpty())

                    <a
                        href="{{ route('student.my-courses.lesson', [
                            'course' => $course,
                            'module' => $courseModule,
                            'lesson' => $moduleLessons->first()
                        ]) }}"
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700"
                    >
                        Start First Lesson →
                    </a>

                @endif

            </div>

        </div>
    </div>

</x-app-layout>