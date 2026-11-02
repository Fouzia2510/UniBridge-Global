<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit University Application') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Page Heading --}}
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">
                        University Application
                    </h1>

                    <p class="mt-1 text-sm text-gray-600">
                        Complete the form below to submit your application.
                    </p>
                </div>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">
                        <h3 class="font-semibold mb-2">
                            Please correct the following errors:
                        </h3>

                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Application Form --}}
                <form
                    method="POST"
                    action="{{ route('student.applications.store') }}"
                >
                    @csrf

                    {{-- University --}}
                    <div class="mb-5">

                        <x-input-label
                            for="university_id"
                            value="University"
                        />

                        @if ($selectedUniversity)
                            <div class="mt-2 p-4 bg-blue-50 border border-blue-200 rounded-lg">

                                <div class="font-semibold text-gray-800">
                                    {{ $selectedUniversity->name }}
                                </div>

                                <div class="text-sm text-gray-600 mt-1">
                                    {{ $selectedUniversity->city }},
                                    {{ $selectedUniversity->country }}
                                </div>

                                <div class="text-sm text-gray-600 mt-1">
                                    Rank #{{ $selectedUniversity->ranking }}
                                </div>

                                <input
                                    type="hidden"
                                    name="university_id"
                                    value="{{ $selectedUniversity->id }}"
                                >

                            </div>
                        @else

                            <select
                                id="university_id"
                                name="university_id"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                                required
                            >
                                <option value="">
                                    Select a university
                                </option>

                                @foreach ($universities as $university)
                                    <option
                                        value="{{ $university->id }}"
                                        @selected(old('university_id') == $university->id)
                                    >
                                        {{ $university->name }}
                                        —
                                        {{ $university->city }},
                                        {{ $university->country }}
                                    </option>
                                @endforeach
                            </select>

                        @endif

                    </div>


                    {{-- Program --}}
                    <div class="mb-5">

                        <x-input-label
                            for="program"
                            value="Program"
                        />

                        <x-text-input
                            id="program"
                            name="program"
                            type="text"
                            class="block mt-1 w-full"
                            value="{{ old('program', $selectedUniversity?->subject ?? '') }}"
                            placeholder="e.g. MSc Computer Science"
                            required
                        />

                        <p class="mt-1 text-xs text-gray-500">
                            Enter the degree/program you want to apply for.
                        </p>

                    </div>


                    {{-- Intake --}}
                    <div class="mb-5">

                        <x-input-label
                            for="intake"
                            value="Preferred Intake"
                        />

                        <x-text-input
                            id="intake"
                            name="intake"
                            type="text"
                            class="block mt-1 w-full"
                            value="{{ old('intake', $selectedUniversity?->intake ?? '') }}"
                            placeholder="e.g. September 2026"
                        />

                    </div>


                    {{-- Personal Statement --}}
                    <div class="mb-5">

                        <x-input-label
                            for="personal_statement"
                            value="Personal Statement"
                        />

                        <textarea
                            id="personal_statement"
                            name="personal_statement"
                            rows="6"
                            maxlength="5000"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Tell us about your academic background, goals and why you want to study this program..."
                        >{{ old('personal_statement') }}</textarea>

                        <p class="mt-1 text-xs text-gray-500">
                            Maximum 5000 characters.
                        </p>

                    </div>


                    {{-- Notes --}}
                    <div class="mb-6">

                        <x-input-label
                            for="notes"
                            value="Additional Notes"
                        />

                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            maxlength="2000"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Add any additional information you want the university consultant to know..."
                        >{{ old('notes') }}</textarea>

                        <p class="mt-1 text-xs text-gray-500">
                            Maximum 2000 characters.
                        </p>

                    </div>


                    {{-- Information Box --}}
                    <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">

                        <h3 class="font-semibold text-gray-800 mb-2">
                            📋 Application Information
                        </h3>

                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>
                                • Your application will initially be marked as
                                <strong>Submitted</strong>.
                            </li>

                            <li>
                                • You can track your application from
                                <strong>My Applications</strong>.
                            </li>

                            <li>
                                • Make sure the information you provide is accurate.
                            </li>
                        </ul>

                    </div>


                    {{-- Buttons --}}
                    <div class="flex items-center gap-3">

                        <x-primary-button>
                            Submit Application
                        </x-primary-button>

                        <a
                            href="{{ route('student.applications.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300"
                        >
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>