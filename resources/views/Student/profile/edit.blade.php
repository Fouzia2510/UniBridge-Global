<x-app-layout>

<x-slot name="header">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">
            My Profile
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Complete your academic profile to improve university matching and eligibility assessment.
        </p>
    </div>
</x-slot>

<div class="py-10">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

        {{-- Success Message --}}
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-sm font-semibold text-emerald-800">
                    {{ session('status') }}
                </p>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="mb-2 text-sm font-bold text-red-800">
                    Please correct the following errors:
                </p>

                <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('student.profile.update') }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- Academic History --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        Academic History
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Provide your academic background.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2">

                    {{-- Degree --}}
                    <div>
                        <label
                            for="degree"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Degree
                        </label>

                        <input
                            id="degree"
                            name="degree"
                            type="text"
                            value="{{ old('degree', $profile->degree ?? '') }}"
                            placeholder="e.g. BS Computer Science"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                    </div>

                    {{-- Field of Study --}}
                    <div>
                        <label
                            for="field_of_study"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Field of Study
                        </label>

                        <input
                            id="field_of_study"
                            name="field_of_study"
                            type="text"
                            value="{{ old('field_of_study', $profile->field_of_study ?? '') }}"
                            placeholder="e.g. Computer Science"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                    </div>

                    {{-- CGPA --}}
                    <div>
                        <label
                            for="cgpa"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            CGPA (0 - 4)
                        </label>

                        <input
                            id="cgpa"
                            name="cgpa"
                            type="number"
                            min="0"
                            max="4"
                            step="0.01"
                            value="{{ old('cgpa', $profile->cgpa ?? '') }}"
                            placeholder="e.g. 3.20"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                    </div>

                    {{-- Graduation Year --}}
                    <div>
                        <label
                            for="graduation_year"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Graduation Year
                        </label>

                        <input
                            id="graduation_year"
                            name="graduation_year"
                            type="number"
                            min="2000"
                            max="2100"
                            value="{{ old('graduation_year', $profile->graduation_year ?? '') }}"
                            placeholder="e.g. 2027"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                    </div>

                </div>
            </div>

            {{-- English Test --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        English Test Score
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Enter your English proficiency information.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2">

                    {{-- English Test --}}
                    <div>
                        <label
                            for="english_test"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Test Type
                        </label>

                        <select
                            id="english_test"
                            name="english_test"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                            <option value="">Select</option>

                            <option
                                value="IELTS"
                                @selected(old('english_test', $profile->english_test ?? '') === 'IELTS')
                            >
                                IELTS
                            </option>

                            <option
                                value="PTE"
                                @selected(old('english_test', $profile->english_test ?? '') === 'PTE')
                            >
                                PTE
                            </option>

                            <option
                                value="Other"
                                @selected(old('english_test', $profile->english_test ?? '') === 'Other')
                            >
                                Other
                            </option>

                            <option
                                value="None"
                                @selected(old('english_test', $profile->english_test ?? '') === 'None')
                            >
                                None
                            </option>
                        </select>
                    </div>

                    {{-- English Score --}}
                    <div>
                        <label
                            for="english_score"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Score
                        </label>

                        <input
                            id="english_score"
                            name="english_score"
                            type="number"
                            min="0"
                            max="9"
                            step="0.5"
                            value="{{ old('english_score', $profile->english_score ?? '') }}"
                            placeholder="e.g. 7.0"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                    </div>

                </div>
            </div>

            {{-- Preferences --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        Preferences
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Enter your study-abroad preferences.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2">

                    {{-- Preferred Country --}}
                    <div>
                        <label
                            for="preferred_country"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Preferred Country
                        </label>

                        <input
                            id="preferred_country"
                            name="preferred_country"
                            type="text"
                            value="{{ old('preferred_country', $profile->preferred_country ?? '') }}"
                            placeholder="e.g. Australia"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                    </div>

                    {{-- Preferred Degree Level --}}
                    <div>
                        <label
                            for="preferred_degree_level"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Preferred Degree Level
                        </label>

                        <select
                            id="preferred_degree_level"
                            name="preferred_degree_level"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                            <option value="">Select</option>

                            <option
                                value="Bachelor"
                                @selected(old('preferred_degree_level', $profile->preferred_degree_level ?? '') === 'Bachelor')
                            >
                                Bachelor
                            </option>

                            <option
                                value="Masters"
                                @selected(old('preferred_degree_level', $profile->preferred_degree_level ?? '') === 'Masters')
                            >
                                Masters
                            </option>

                            <option
                                value="PhD"
                                @selected(old('preferred_degree_level', $profile->preferred_degree_level ?? '') === 'PhD')
                            >
                                PhD
                            </option>
                        </select>
                    </div>

                    {{-- Preferred Subject --}}
                    <div>
                        <label
                            for="preferred_subject"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Preferred Subject
                        </label>

                        <input
                            id="preferred_subject"
                            name="preferred_subject"
                            type="text"
                            value="{{ old('preferred_subject', $profile->preferred_subject ?? '') }}"
                            placeholder="e.g. Information Technology"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                    </div>

                    {{-- Budget --}}
                    <div>
                        <label
                            for="budget"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Budget (USD)
                        </label>

                        <input
                            id="budget"
                            name="budget"
                            type="number"
                            min="0"
                            step="100"
                            value="{{ old('budget', $profile->budget ?? '') }}"
                            placeholder="e.g. 50000"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                    </div>

                    {{-- Preferred Intake --}}
                    <div>
                        <label
                            for="preferred_intake"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Preferred Intake
                        </label>

                        <input
                            id="preferred_intake"
                            name="preferred_intake"
                            type="text"
                            value="{{ old('preferred_intake', $profile->preferred_intake ?? '') }}"
                            placeholder="e.g. February 2027"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                    </div>

                    {{-- Work Experience --}}
                    <div>
                        <label
                            for="work_experience_years"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Work Experience (years)
                        </label>

                        <input
                            id="work_experience_years"
                            name="work_experience_years"
                            type="number"
                            min="0"
                            max="50"
                            step="1"
                            value="{{ old('work_experience_years', $profile->work_experience_years ?? '') }}"
                            placeholder="e.g. 1"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                    </div>

                </div>
            </div>

            {{-- Save Profile --}}
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-7 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Save Profile
                </button>
            </div>

        </form>
    </div>
</div>

</x-app-layout>
