<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-indigo-600">
                    UniBridge
                </p>

                <h2 class="text-2xl font-bold text-gray-900">
                    Find Universities
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Explore universities and find programs that match your study-abroad goals.
                </p>
            </div>

            <a
                href="{{ route('student.dashboard') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
            >
                ← Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Search --}}
            <div class="mb-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

                <div class="mb-5">
                    <h3 class="text-lg font-bold text-gray-900">
                        Search Universities
                    </h3>

                    <p class="mt-1 text-sm text-gray-600">
                        Search by university name, country, city or subject.
                    </p>
                </div>

                <form
                    method="GET"
                    action="{{ route('student.universities.index') }}"
                    class="grid gap-4 md:grid-cols-4"
                >

                    <div>
                        <label
                            for="search"
                            class="mb-1 block text-sm font-semibold text-gray-700"
                        >
                            Search
                        </label>

                        <input
                            id="search"
                            name="search"
                            type="text"
                            value="{{ request('search') }}"
                            placeholder="University name..."
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label
                            for="country"
                            class="mb-1 block text-sm font-semibold text-gray-700"
                        >
                            Country
                        </label>

                        <select
                            id="country"
                            name="country"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">All Countries</option>

                            @foreach(($countries ?? []) as $country)
                                <option
                                    value="{{ $country }}"
                                    @selected(request('country') === $country)
                                >
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            for="subject"
                            class="mb-1 block text-sm font-semibold text-gray-700"
                        >
                            Subject
                        </label>

                        <input
                            id="subject"
                            name="subject"
                            type="text"
                            value="{{ request('subject') }}"
                            placeholder="Computer Science..."
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div class="flex items-end">
                        <button
                            type="submit"
                            class="w-full rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-indigo-700"
                        >
                            Search Universities
                        </button>
                    </div>

                </form>

            </div>


            {{-- AI Recommendation --}}
            <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 shadow-sm">

                <div class="px-6 py-7 text-white">

                    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                        <div>

                            <div class="mb-2 flex items-center gap-2">
                                <span class="text-2xl">✨</span>

                                <span class="text-sm font-bold uppercase tracking-wide text-indigo-100">
                                    UniBridge AI
                                </span>
                            </div>

                            <h3 class="text-2xl font-bold">
                                Find universities for your profile
                            </h3>

                            <p class="mt-2 max-w-2xl text-sm leading-6 text-indigo-100">
                                Ask UniBridge AI to analyze your academic profile,
                                preferences and available university data.
                            </p>

                        </div>

                        <button
                            type="button"
                            id="aiRecommendButton"
                            class="inline-flex shrink-0 items-center justify-center rounded-lg bg-white px-5 py-3 text-sm font-bold text-indigo-700 shadow-sm hover:bg-indigo-50"
                        >
                            ✨ Get AI Recommendations
                        </button>

                    </div>

                </div>

            </div>


            {{-- AI Results --}}
            <div
                id="aiRecommendationBox"
                class="mb-8 hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200"
            >

                <div class="border-b border-gray-200 px-6 py-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-xl">
                            ✨
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                UniBridge AI Recommendations
                            </h3>

                            <p class="text-sm text-gray-500">
                                Based on your current student profile and UniBridge university data.
                            </p>
                        </div>

                    </div>

                </div>

                <div class="px-6 py-6">

                    <div
                        id="aiRecommendationLoading"
                        class="hidden rounded-xl bg-gray-50 px-5 py-6 text-center"
                    >
                        <div class="text-sm font-semibold text-gray-700">
                            Analyzing your profile...
                        </div>

                        <div class="mt-2 text-xs text-gray-500">
                            UniBridge AI is checking available university information.
                        </div>
                    </div>

                    <div
                        id="aiRecommendationError"
                        class="hidden rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700"
                    ></div>

                    <div
                        id="aiRecommendationResult"
                        class="whitespace-pre-line text-sm leading-7 text-gray-700"
                    ></div>

                </div>

                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">

                    <p class="text-xs leading-5 text-gray-500">
                        AI recommendations are informational only. Always verify
                        admission requirements, fees and application information
                        with the university's official website.
                    </p>

                </div>

            </div>


            {{-- University Results --}}
            <div class="mb-5 flex items-center justify-between">

                <div>
                    <h3 class="text-xl font-bold text-gray-900">
                        Available Universities
                    </h3>

                    <p class="mt-1 text-sm text-gray-600">
                        Browse universities available in the UniBridge database.
                    </p>
                </div>

                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                    {{ method_exists($universities, 'total') ? $universities->total() : $universities->count() }}
                    Universities
                </span>

            </div>


            @if($universities->count())

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                    @foreach($universities as $university)

                        <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">

                            <div class="border-b border-gray-200 bg-gradient-to-r from-slate-50 to-indigo-50 px-5 py-5">

                                <div class="flex items-start justify-between gap-3">

                                    <div>

                                        <h4 class="text-lg font-bold text-gray-900">
                                            {{ $university->name }}
                                        </h4>

                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ $university->city ?? 'City not specified' }},
                                            {{ $university->country ?? 'Country not specified' }}
                                        </p>

                                    </div>

                                    @if($university->ranking)
                                        <span class="shrink-0 rounded-full bg-white px-3 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                            #{{ $university->ranking }}
                                        </span>
                                    @endif

                                </div>

                            </div>


                            <div class="flex-1 px-5 py-5">

                                <div class="grid grid-cols-2 gap-3">

                                    <div class="rounded-lg bg-gray-50 p-3">
                                        <p class="text-xs font-semibold text-gray-500">
                                            Degree
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-gray-900">
                                            {{ $university->degree_level ?? 'Not specified' }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg bg-gray-50 p-3">
                                        <p class="text-xs font-semibold text-gray-500">
                                            Subject
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-gray-900">
                                            {{ $university->subject ?? 'Not specified' }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg bg-gray-50 p-3">
                                        <p class="text-xs font-semibold text-gray-500">
                                            Min CGPA
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-gray-900">
                                            {{ $university->min_cgpa ?? 'Not specified' }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg bg-gray-50 p-3">
                                        <p class="text-xs font-semibold text-gray-500">
                                            English
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-gray-900">
                                            @if($university->min_english_score)
                                                {{ $university->english_test_type ?? 'English' }}
                                                {{ $university->min_english_score }}
                                            @else
                                                Not specified
                                            @endif
                                        </p>
                                    </div>

                                </div>


                                <div class="mt-4 space-y-2 text-sm text-gray-600">

                                    @if($university->tuition_fee)
                                        <div class="flex justify-between gap-3">
                                            <span>Tuition</span>
                                            <span class="font-semibold text-gray-900">
                                                {{ $university->tuition_fee }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($university->estimated_living_cost)
                                        <div class="flex justify-between gap-3">
                                            <span>Living Cost</span>
                                            <span class="font-semibold text-gray-900">
                                                {{ $university->estimated_living_cost }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($university->intake)
                                        <div class="flex justify-between gap-3">
                                            <span>Intake</span>
                                            <span class="font-semibold text-gray-900">
                                                {{ $university->intake }}
                                            </span>
                                        </div>
                                    @endif

                                </div>


                                <div class="mt-4 flex flex-wrap gap-2">

                                    @if($university->scholarship_available)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                            Scholarship Available
                                        </span>
                                    @endif

                                    @if($university->post_study_work)
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                            Post-Study Work
                                        </span>
                                    @endif

                                </div>

                            </div>


                            <div class="border-t border-gray-200 px-5 py-4">

                                <a
                                    href="{{ route('student.universities.show', $university) }}"
                                    class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-indigo-700"
                                >
                                    View University →
                                </a>

                            </div>

                        </div>

                    @endforeach

                </div>


                @if(method_exists($universities, 'links'))

                    <div class="mt-8">
                        {{ $universities->withQueryString()->links() }}
                    </div>

                @endif

            @else

                <div class="rounded-2xl bg-white px-6 py-12 text-center shadow-sm ring-1 ring-gray-200">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-2xl">
                        🎓
                    </div>

                    <h3 class="mt-4 text-lg font-bold text-gray-900">
                        No universities found
                    </h3>

                    <p class="mt-2 text-sm text-gray-600">
                        Try changing your search filters or country selection.
                    </p>

                </div>

            @endif

        </div>
    </div>


    {{-- AI JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const button = document.getElementById('aiRecommendButton');
            const box = document.getElementById('aiRecommendationBox');
            const loading = document.getElementById('aiRecommendationLoading');
            const error = document.getElementById('aiRecommendationError');
            const result = document.getElementById('aiRecommendationResult');

            if (!button) {
                return;
            }

            button.addEventListener('click', async function () {

                box.classList.remove('hidden');

                loading.classList.remove('hidden');
                error.classList.add('hidden');
                result.textContent = '';

                button.disabled = true;
                button.textContent = 'Analyzing...';

                try {

                    const response = await fetch(
                        '{{ route('api.gemini.generate') }}',
                        {
                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },

                            body: JSON.stringify({
                                prompt: 'Based on my student profile and the universities available in the UniBridge database, recommend suitable universities for me. Compare my CGPA, English score, preferred country, preferred degree level, preferred subject, budget and intake with the available university requirements. For each suitable university, explain briefly why it may match my profile. Only use universities that actually exist in the UniBridge database.'
                            })
                        }
                    );

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message || 'Unable to get AI recommendations.'
                        );
                    }

                    result.textContent = data.text || 'No recommendation was returned.';

                } catch (exception) {

                    error.textContent =
                        exception.message ||
                        'Something went wrong while contacting UniBridge AI.';

                    error.classList.remove('hidden');

                } finally {

                    loading.classList.add('hidden');

                    button.disabled = false;
                    button.textContent = '✨ Get AI Recommendations';
                }

            });

        });
    </script>

</x-app-layout>