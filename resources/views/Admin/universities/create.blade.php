<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add University
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                <h1 class="mb-2 text-2xl font-bold text-gray-900">
                    Add a University
                </h1>
                <p class="mb-6 text-sm text-gray-600">
                    Enter the university details to list it on UniBridge.
                </p>

                @if ($errors->any())
                    <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-800">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.universities.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        @php
                            $fields = [
                                ['name', 'University Name', 'text'],
                                ['country', 'Country', 'text'],
                                ['city', 'City', 'text'],
                                ['degree_level', 'Degree Level', 'text'],
                                ['subject', 'Subject / Program', 'text'],
                                ['tuition_fee', 'Annual Tuition Fee', 'number'],
                                ['estimated_living_cost', 'Estimated Living Cost', 'number'],
                                ['min_cgpa', 'Minimum CGPA (out of 4)', 'number'],
                                ['min_english_score', 'Minimum English Score', 'number'],
                                ['english_test_type', 'English Test Type', 'text'],
                                ['intake', 'Intake', 'text'],
                                ['ranking', 'World Ranking', 'number'],
                                ['contact_email', 'Contact Email', 'email'],
                                ['contact_phone', 'Contact Phone', 'text'],
                                ['website_url', 'Official Website URL', 'url'],
                                ['application_url', 'Application URL', 'url'],
                            ];
                        @endphp

                        @foreach ($fields as [$name, $label, $type])
                            <div>
                                <label for="{{ $name }}" class="mb-1 block text-sm font-semibold text-gray-700">
                                    {{ $label }}
                                    @if (!in_array($name, ['ranking', 'contact_email', 'contact_phone', 'website_url', 'application_url']))
                                        <span class="text-red-600">*</span>
                                    @endif
                                </label>

                                <input
                                    id="{{ $name }}"
                                    name="{{ $name }}"
                                    type="{{ $type }}"
                                    value="{{ old($name) }}"
                                    @if (in_array($name, ['tuition_fee', 'estimated_living_cost', 'min_cgpa', 'min_english_score']))
                                        step="0.01" min="0"
                                    @endif
                                    @if ($name === 'min_cgpa') max="4" @endif
                                    @if ($name === 'ranking') min="1" @endif
                                    @if (!in_array($name, ['ranking', 'contact_email', 'contact_phone', 'website_url', 'application_url']))
                                        required
                                    @endif
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error($name)
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <div>
                            <label for="scholarship_available" class="mb-1 block text-sm font-semibold text-gray-700">
                                Scholarship Available?
                            </label>
                            <select id="scholarship_available" name="scholarship_available"
                                    class="w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="0">No / Not listed</option>
                                <option value="1" @selected(old('scholarship_available') == '1')>Yes</option>
                            </select>
                        </div>

                        <div>
                            <label for="post_study_work" class="mb-1 block text-sm font-semibold text-gray-700">
                                Post-study Work Option?
                            </label>
                            <select id="post_study_work" name="post_study_work"
                                    class="w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="0">No / Not listed</option>
                                <option value="1" @selected(old('post_study_work') == '1')>Yes</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="mb-1 block text-sm font-semibold text-gray-700">
                                Description
                            </label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full rounded-lg border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <button type="submit"
                                class="rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700">
                            Save University
                        </button>

                        <a href="{{ route('admin.universities.index') }}"
                           class="rounded-lg border border-gray-300 px-5 py-3 font-semibold text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
