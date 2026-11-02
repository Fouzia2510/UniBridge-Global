<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('University & Course Finder') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Profile Recommendation Message --}}
            @if ($profile)

                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-5 mb-6">

                    <h3 class="text-lg font-semibold text-indigo-900">
                        🎯 Smart University Recommendations
                    </h3>

                    <p class="text-sm text-indigo-700 mt-1">
                        Universities are ranked according to your academic profile,
                        English score, preferences and budget.
                    </p>

                </div>

            @else

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-5 mb-6">

                    <h3 class="text-lg font-semibold text-yellow-900">
                        Complete Your Profile
                    </h3>

                    <p class="text-sm text-yellow-700 mt-1">
                        Complete your student profile to get personalized university recommendations.
                    </p>

                    <a
                        href="{{ route('student.profile.edit') }}"
                        class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        Complete Profile →
                    </a>

                </div>

            @endif


            {{-- Filter Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Search Universities
                </h3>

                <form
                    method="GET"
                    action="{{ route('student.universities.index') }}"
                >

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

                        {{-- Country --}}
                        <div>

                            <x-input-label
                                for="country"
                                value="Country"
                            />

                            <select
                                id="country"
                                name="country"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                            >

                                <option value="">
                                    Any
                                </option>

                                @foreach ($countries as $country)

                                    <option
                                        value="{{ $country }}"
                                        @selected(request('country') == $country)
                                    >
                                        {{ $country }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Degree Level --}}
                        <div>

                            <x-input-label
                                for="degree_level"
                                value="Degree Level"
                            />

                            <select
                                id="degree_level"
                                name="degree_level"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                            >

                                <option value="">
                                    Any
                                </option>

                                @foreach ($degreeLevels as $level)

                                    <option
                                        value="{{ $level }}"
                                        @selected(request('degree_level') == $level)
                                    >
                                        {{ $level }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Subject --}}
                        <div>

                            <x-input-label
                                for="subject"
                                value="Subject"
                            />

                            <x-text-input
                                id="subject"
                                name="subject"
                                class="block mt-1 w-full"
                                type="text"
                                placeholder="e.g. Computer Science"
                                :value="request('subject')"
                            />

                        </div>


                        {{-- Maximum Tuition --}}
                        <div>

                            <x-input-label
                                for="max_tuition"
                                value="Max Tuition (USD)"
                            />

                            <x-text-input
                                id="max_tuition"
                                name="max_tuition"
                                class="block mt-1 w-full"
                                type="number"
                                placeholder="e.g. 30000"
                                :value="request('max_tuition')"
                            />

                        </div>


                        {{-- Scholarship --}}
                        <div class="flex items-end">

                            <label class="flex items-center gap-2">

                                <input
                                    type="checkbox"
                                    name="scholarship_only"
                                    value="1"
                                    @checked(request('scholarship_only'))
                                    class="rounded border-gray-300"
                                >

                                <span class="text-sm text-gray-700">
                                    Scholarship available
                                </span>

                            </label>

                        </div>

                    </div>


                    <div class="flex gap-3 mt-4">

                        <x-primary-button>
                            Search
                        </x-primary-button>

                        <a
                            href="{{ route('student.universities.index') }}"
                            class="text-gray-600 hover:text-gray-800 text-sm self-center"
                        >
                            Clear filters
                        </a>

                    </div>

                </form>

            </div>


            {{-- Results Count --}}
            <div class="flex justify-between items-center mb-4">

                <p class="text-sm text-gray-600">
                    {{ $universities->count() }} universities found
                </p>

                @if ($profile)

                    <p class="text-sm text-indigo-600">
                        Sorted by your profile match
                    </p>

                @endif

            </div>


            {{-- University Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                @forelse ($universities as $uni)

                    <div class="bg-white shadow-sm rounded-lg p-6">

                        {{-- Header --}}
                        <div class="flex justify-between items-start mb-2">

                            <div>

                                <h3 class="font-semibold text-lg text-gray-900">
                                    {{ $uni->name }}
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $uni->city ? $uni->city . ', ' : '' }}
                                    {{ $uni->country }}
                                </p>

                            </div>


                            @if ($uni->ranking)

                                <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full">
                                    Rank #{{ $uni->ranking }}
                                </span>

                            @endif

                        </div>


                        {{-- Match Score --}}
                        @if ($profile)

                            <div class="mt-4 mb-4">

                                <div class="flex justify-between items-center mb-1">

                                    <span class="text-sm font-medium text-gray-700">
                                        Profile Match
                                    </span>

                                    <span class="text-sm font-bold text-indigo-600">
                                        {{ $uni->match_score }}%
                                    </span>

                                </div>


                                <div class="w-full bg-gray-200 rounded-full h-2">

                                    <div
                                        class="bg-indigo-600 h-2 rounded-full"
                                        style="width: {{ $uni->match_score }}%"
                                    ></div>

                                </div>

                            </div>


                            {{-- Eligibility Status --}}
                            <div class="mb-4">

                                @if ($uni->eligibility_status === 'Eligible')

                                    <span class="inline-flex items-center text-xs font-medium bg-green-100 text-green-800 px-3 py-1 rounded-full">
                                        ✅ Eligible
                                    </span>

                                @elseif ($uni->eligibility_status === 'Possible')

                                    <span class="inline-flex items-center text-xs font-medium bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full">
                                        ⚠️ Possible
                                    </span>

                                @else

                                    <span class="inline-flex items-center text-xs font-medium bg-red-100 text-red-800 px-3 py-1 rounded-full">
                                        ❌ Not Eligible
                                    </span>

                                @endif

                            </div>

                        @endif


                        {{-- Description --}}
                        <p class="text-sm text-gray-700 mb-4">
                            {{ $uni->description }}
                        </p>


                        {{-- University Details --}}
                        <div class="grid grid-cols-2 gap-3 text-sm mb-4">

                            <div>
                                <span class="text-gray-500">
                                    Degree:
                                </span>

                                {{ $uni->degree_level }}
                            </div>


                            <div>
                                <span class="text-gray-500">
                                    Subject:
                                </span>

                                {{ $uni->subject }}
                            </div>


                            <div>
                                <span class="text-gray-500">
                                    Tuition:
                                </span>

                                ${{ number_format($uni->tuition_fee) }}/yr
                            </div>


                            <div>
                                <span class="text-gray-500">
                                    Living cost:
                                </span>

                                ${{ number_format($uni->estimated_living_cost) }}/yr
                            </div>


                            <div>
                                <span class="text-gray-500">
                                    Min CGPA:
                                </span>

                                {{ number_format($uni->min_cgpa, 2) }}
                            </div>


                            <div>
                                <span class="text-gray-500">
                                    Min {{ $uni->english_test_type }}:
                                </span>

                                {{ number_format($uni->min_english_score, 2) }}
                            </div>


                            <div>
                                <span class="text-gray-500">
                                    Intake:
                                </span>

                                {{ $uni->intake }}
                            </div>

                        </div>


                        {{-- Features --}}
                        <div class="flex flex-wrap gap-2 mb-5">

                            @if ($uni->scholarship_available)

                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                    🎓 Scholarship
                                </span>

                            @endif


                            @if ($uni->post_study_work)

                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                    💼 Post-study work
                                </span>

                            @endif

                        </div>


                        {{-- View Details Button --}}
                        <div class="border-t pt-4">

                            <a
                                href="{{ route('student.universities.show', $uni) }}"
                                class="inline-flex items-center justify-center w-full px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                            >
                                View Details →
                            </a>

                        </div>

                    </div>

                @empty

                    <div class="col-span-2 text-center py-10">

                        <p class="text-gray-500">
                            No universities match your filters.
                        </p>

                        <p class="text-sm text-gray-400 mt-1">
                            Try adjusting your search criteria.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>
    </div>

</x-app-layout>