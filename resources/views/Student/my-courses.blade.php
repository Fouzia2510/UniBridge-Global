<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('status') }}
                </div>
            @endif


            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- Browse Courses --}}
            <a
                href="{{ route('student.courses.index') }}"
                class="inline-block mb-6 text-indigo-600 hover:text-indigo-800 text-sm font-medium"
            >
                ← Browse All Courses
            </a>


            {{-- Enrolled Courses --}}
            @forelse ($enrollments as $enrollment)

                <div class="bg-white shadow-sm rounded-lg p-6 mb-4">

                    {{-- Course Header --}}
                    <div class="flex justify-between items-start mb-3">

                        <h3 class="font-semibold text-lg text-gray-900">
                            {{ $enrollment->course->title }}
                        </h3>


                        {{-- Status Badge --}}
                        @if ($enrollment->progress_percent >= 100)

                            <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full font-medium">
                                ✓ Completed
                            </span>

                        @elseif ($enrollment->progress_percent >= 90)

                            <span class="text-xs bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full font-medium">
                                🏆 Certificate Eligible
                            </span>

                        @elseif ($enrollment->progress_percent > 0)

                            <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-medium">
                                In Progress
                            </span>

                        @else

                            <span class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-full font-medium">
                                Not Started
                            </span>

                        @endif

                    </div>


                    {{-- Progress --}}
                    <div class="mb-4">

                        <div class="flex justify-between text-sm mb-1">

                            <span class="text-gray-500">
                                Progress
                            </span>

                            <span class="font-medium">
                                {{ $enrollment->progress_percent }}%
                            </span>

                        </div>


                        <div class="w-full bg-gray-200 rounded-full h-2.5">

                            <div
                                class="bg-indigo-600 h-2.5 rounded-full"
                                style="width: {{ $enrollment->progress_percent }}%"
                            ></div>

                        </div>

                    </div>


                    {{-- Update Progress --}}
                    <form
                        method="POST"
                        action="{{ route('student.my-courses.update', $enrollment) }}"
                        class="flex items-center gap-3 mb-4"
                    >

                        @csrf
                        @method('PATCH')


                        <label
                            for="progress_{{ $enrollment->id }}"
                            class="text-sm text-gray-600"
                        >
                            Update progress:
                        </label>


                        <input
                            type="range"
                            id="progress_{{ $enrollment->id }}"
                            name="progress_percent"
                            min="0"
                            max="100"
                            step="1"
                            value="{{ $enrollment->progress_percent }}"
                            class="flex-1"
                        >


                        <span
                            id="progress_value_{{ $enrollment->id }}"
                            class="text-sm font-medium w-12 text-center"
                        >
                            {{ $enrollment->progress_percent }}%
                        </span>


                        <x-primary-button>
                            Save
                        </x-primary-button>

                    </form>


                    {{-- Certificate --}}
                    @if ($enrollment->progress_percent >= 90)

                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">

                            <p class="text-sm text-yellow-800 mb-3">
                                🏆 Your certificate is ready!
                            </p>


                            <a
                                href="{{ route('student.my-courses.certificate', $enrollment) }}"
                                class="inline-block bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 text-sm font-medium"
                            >
                                🏆 Download Certificate
                            </a>

                        </div>

                    @else

                        <p class="text-sm text-gray-500 mt-3">
                            Complete 90% of this course to unlock your certificate.
                        </p>

                    @endif

                </div>

            @empty

                <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500">

                    You haven't enrolled in any courses yet.

                    <a
                        href="{{ route('student.courses.index') }}"
                        class="text-indigo-600 hover:text-indigo-800 font-medium"
                    >
                        Browse courses →
                    </a>

                </div>

            @endforelse

        </div>

    </div>


    {{-- Live Progress Percentage --}}
    <script>

        document.querySelectorAll('input[type="range"]').forEach(function (slider) {

            slider.addEventListener('input', function () {

                const valueElement = document.getElementById(
                    'progress_value_' + this.id.replace('progress_', '')
                );

                if (valueElement) {
                    valueElement.textContent = this.value + '%';
                }

            });

        });

    </script>

</x-app-layout>