<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <p class="text-sm text-indigo-600 font-semibold">
                    {{ $course->title }}
                </p>

                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $lesson->title }}
                </h2>

            </div>

            <a
                href="{{ route('student.my-courses.module', [
                    'course' => $course,
                    'module' => $module
                ]) }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200"
            >
                ← Back to Module
            </a>

        </div>

    </x-slot>


    <div class="py-10">

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">


            {{-- Success Message --}}

            @if (session('status'))

                <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-5 py-4">

                    <div class="font-semibold text-green-800">
                        ✓ {{ session('status') }}
                    </div>

                </div>

            @endif


            {{-- Lesson Header --}}

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-6">

                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">

                    <div class="flex-1">

                        <div class="flex flex-wrap items-center gap-2 mb-4">

                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold">
                                {{ ucfirst($lesson->lesson_type ?: 'Lesson') }}
                            </span>

                            @if ($lesson->duration_minutes)

                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">
                                    {{ $lesson->duration_minutes }} minutes
                                </span>

                            @endif

                            @if ($lesson->is_preview)

                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                                    Preview
                                </span>

                            @endif

                            @if ($isCompleted)

                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                    ✓ Completed
                                </span>

                            @endif

                        </div>


                        <h1 class="text-3xl font-bold text-gray-900">
                            {{ $lesson->title }}
                        </h1>


                        <p class="text-sm text-gray-500 mt-2">
                            Module:
                            <span class="font-semibold text-gray-700">
                                {{ $module->title }}
                            </span>
                        </p>


                        @if ($lesson->description)

                            <p class="text-gray-600 leading-7 mt-5">
                                {{ $lesson->description }}
                            </p>

                        @endif

                    </div>


                    {{-- Completion Status --}}

                    <div class="flex-shrink-0">

                        @if ($isCompleted)

                            <div class="rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-center">

                                <div class="text-2xl">
                                    ✓
                                </div>

                                <div class="text-sm font-bold text-green-800 mt-1">
                                    Lesson Completed
                                </div>

                            </div>

                        @else

                            <div class="rounded-xl bg-indigo-50 border border-indigo-200 px-5 py-4 text-center">

                                <div class="text-2xl">
                                    📖
                                </div>

                                <div class="text-sm font-bold text-indigo-800 mt-1">
                                    In Progress
                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </div>


            {{-- Video Section --}}

            @if ($lesson->video_url)

                @php

                    $videoUrl = $lesson->video_url;

                    $youtubeId = null;

                    if (
                        preg_match(
                            '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\?\/]+)/',
                            $videoUrl,
                            $matches
                        )
                    ) {
                        $youtubeId = $matches[1];
                    }

                @endphp


                @if ($youtubeId)

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">

                        <div class="mb-4">

                            <h2 class="text-xl font-bold text-gray-900">
                                Lesson Video
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Watch the lesson video before marking this lesson as completed.
                            </p>

                        </div>


                        <div class="relative w-full overflow-hidden rounded-xl bg-black aspect-video">

                            <iframe
                                class="absolute inset-0 w-full h-full"
                                src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                title="{{ $lesson->title }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                            ></iframe>

                        </div>

                    </div>

                @else

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">

                        <h2 class="text-xl font-bold text-gray-900 mb-2">
                            Lesson Video
                        </h2>

                        <a
                            href="{{ $lesson->video_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700"
                        >
                            Open Video →
                        </a>

                    </div>

                @endif

            @endif


            {{-- Lesson Content --}}

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-6">

                <h2 class="text-xl font-bold text-gray-900 mb-5">
                    Lesson Content
                </h2>


                @if ($lesson->content)

                    <div class="prose max-w-none text-gray-700 leading-7 whitespace-pre-line">
                        {{ $lesson->content }}
                    </div>

                @else

                    <div class="rounded-xl bg-gray-50 border border-gray-200 p-5 text-gray-500">
                        No written content is available for this lesson.
                    </div>

                @endif

            </div>


            {{-- Resource --}}

            @if ($lesson->resource_url)

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">

                    <h2 class="text-lg font-bold text-gray-900 mb-2">
                        Learning Resource
                    </h2>

                    <p class="text-sm text-gray-500 mb-4">
                        Additional material for this lesson.
                    </p>

                    <a
                        href="{{ $lesson->resource_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200"
                    >
                        Open Resource →
                    </a>

                </div>

            @endif


            {{-- Mark Complete --}}

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-8">

                @if ($isCompleted)

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                        <div>

                            <div class="text-lg font-bold text-green-700">
                                ✓ Lesson Completed
                            </div>

                            <p class="text-sm text-gray-500 mt-1">
                                This lesson has already been counted toward your course progress.
                            </p>

                        </div>

                        <a
                            href="{{ route('student.my-courses.module', [
                                'course' => $course,
                                'module' => $module
                            ]) }}"
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700"
                        >
                            Continue Module →
                        </a>

                    </div>

                @else

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

                        <div>

                            <h2 class="text-lg font-bold text-gray-900">
                                Finished this lesson?
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Mark it as completed to update your course progress.
                            </p>

                        </div>


                        <form
                            method="POST"
                            action="{{ route('student.my-courses.lesson.complete', [
                                'course' => $course,
                                'module' => $module,
                                'lesson' => $lesson
                            ]) }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700"
                            >
                                ✓ Mark as Completed
                            </button>

                        </form>

                    </div>

                @endif

            </div>


            {{-- Navigation --}}

            <div class="flex flex-col sm:flex-row gap-3">

                <a
                    href="{{ route('student.my-courses.module', [
                        'course' => $course,
                        'module' => $module
                    ]) }}"
                    class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200"
                >
                    ← Back to Module
                </a>

                <a
                    href="{{ route('student.my-courses.show', $course) }}"
                    class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700"
                >
                    All Modules
                </a>

            </div>


        </div>

    </div>

</x-app-layout>
