<x-app-layout>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        University Details
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-6">
            <a
                href="{{ route('student.universities.index') }}"
                class="text-indigo-600 hover:text-indigo-800"
            >
                ← Back to University Finder
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">

            <div class="flex justify-between items-start mb-6">

                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $university->name }}
                    </h1>

                    <p class="text-gray-500 mt-2">
                        {{ $university->city ? $university->city . ', ' : '' }}
                        {{ $university->country }}
                    </p>
                </div>

                @if ($university->ranking)
                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">
                        Rank #{{ $university->ranking }}
                    </span>
                @endif

            </div>

            @if ($university->description)
                <p class="text-gray-700 mb-6">
                    {{ $university->description }}
                </p>
            @endif

            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                University Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-sm text-gray-500">Degree Level</span>
                    <p class="font-semibold">
                        {{ $university->degree_level }}
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-sm text-gray-500">Subject</span>
                    <p class="font-semibold">
                        {{ $university->subject }}
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-sm text-gray-500">Tuition Fee</span>
                    <p class="font-semibold">
                        ${{ number_format((float) $university->tuition_fee) }}/year
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-sm text-gray-500">Living Cost</span>
                    <p class="font-semibold">
                        ${{ number_format((float) $university->estimated_living_cost) }}/year
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-sm text-gray-500">Minimum CGPA</span>
                    <p class="font-semibold">
                        {{ number_format((float) $university->min_cgpa, 2) }}
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-sm text-gray-500">
                        Minimum {{ $university->english_test_type }}
                    </span>
                    <p class="font-semibold">
                        {{ number_format((float) $university->min_english_score, 2) }}
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-sm text-gray-500">Intake</span>
                    <p class="font-semibold">
                        {{ $university->intake }}
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-sm text-gray-500">Scholarship</span>

                    @if ($university->scholarship_available)
                        <p class="font-semibold text-green-600">
                            ✓ Available
                        </p>
                    @else
                        <p class="font-semibold text-gray-600">
                            Not Available
                        </p>
                    @endif
                </div>

            </div>

            <div class="mt-6 flex flex-wrap gap-3">

                @if ($university->scholarship_available)
                    <span class="bg-green-100 text-green-800 px-3 py-2 rounded-full text-sm">
                        🎓 Scholarship Available
                    </span>
                @endif

                @if ($university->post_study_work)
                    <span class="bg-blue-100 text-blue-800 px-3 py-2 rounded-full text-sm">
                        💼 Post-study Work
                    </span>
                @endif

            </div>

            {{-- Student Profile Comparison --}}
            @if (auth()->user()->studentProfile)

                @php
                    $profile = auth()->user()->studentProfile;

                    $cgpaMatch = $profile->cgpa !== null
                        ? (float) $profile->cgpa >= (float) $university->min_cgpa
                        : false;

                    $englishMatch = $profile->english_score !== null
                        ? (float) $profile->english_score >= (float) $university->min_english_score
                        : false;

                    $countryMatch = $profile->preferred_country
                        ? strtolower(trim($profile->preferred_country)) === strtolower(trim($university->country))
                        : false;

                    $degreeMatch = $profile->preferred_degree_level
                        ? strtolower(trim($profile->preferred_degree_level)) === strtolower(trim($university->degree_level))
                        : false;

                    $subjectMatch = $profile->preferred_subject
                        ? str_contains(
                            strtolower($university->subject),
                            strtolower($profile->preferred_subject)
                        ) || str_contains(
                            strtolower($profile->preferred_subject),
                            strtolower($university->subject)
                        )
                        : false;

                    $budgetMatch = $profile->budget !== null
                        ? (float) $profile->budget >= (float) $university->tuition_fee
                        : false;

                    $matches = collect([
                        $countryMatch,
                        $degreeMatch,
                        $subjectMatch,
                        $cgpaMatch,
                        $englishMatch,
                        $budgetMatch,
                    ])->filter()->count();

                    $matchScore = round(($matches / 6) * 100);

                    if ($matchScore >= 80) {
                        $status = 'Eligible';
                    } elseif ($matchScore >= 50) {
                        $status = 'Possible';
                    } else {
                        $status = 'Not Eligible';
                    }
                @endphp

                <div class="border-t mt-8 pt-8">

                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        🎯 Your Profile Match
                    </h3>

                    <p class="text-gray-500 mb-5">
                        Comparison between your profile and this university.
                    </p>

                    <div class="bg-indigo-50 rounded-lg p-5 mb-6">

                        <div class="flex justify-between items-center">

                            <div>
                                <p class="text-sm text-gray-500">
                                    Profile Match
                                </p>

                                <p class="text-3xl font-bold text-indigo-600">
                                    {{ $matchScore }}%
                                </p>
                            </div>

                            <div>

                                @if ($status === 'Eligible')
                                    <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full font-semibold">
                                        ✅ Eligible
                                    </span>
                                @elseif ($status === 'Possible')
                                    <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full font-semibold">
                                        ⚠️ Possible
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-4 py-2 rounded-full font-semibold">
                                        ❌ Not Eligible
                                    </span>
                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="space-y-3">

                        {{-- CGPA --}}
                        <div class="flex justify-between border-b pb-3">
                            <div>
                                <p class="font-medium">CGPA</p>

                                <p class="text-sm text-gray-500">
                                    Your:
                                    {{ $profile->cgpa !== null ? number_format((float) $profile->cgpa, 2) : 'Not provided' }}
                                    |
                                    Required:
                                    {{ number_format((float) $university->min_cgpa, 2) }}
                                </p>
                            </div>

                            <span class="{{ $cgpaMatch ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ $cgpaMatch ? '✓ Match' : '✗ Gap' }}
                            </span>
                        </div>

                        {{-- English --}}
                        <div class="flex justify-between border-b pb-3">
                            <div>
                                <p class="font-medium">
                                    {{ $university->english_test_type }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    Your:
                                    {{ $profile->english_score !== null ? number_format((float) $profile->english_score, 2) : 'Not provided' }}
                                    |
                                    Required:
                                    {{ number_format((float) $university->min_english_score, 2) }}
                                </p>
                            </div>

                            <span class="{{ $englishMatch ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ $englishMatch ? '✓ Match' : '✗ Gap' }}
                            </span>
                        </div>

                        {{-- Country --}}
                        <div class="flex justify-between border-b pb-3">
                            <div>
                                <p class="font-medium">Country</p>

                                <p class="text-sm text-gray-500">
                                    Your:
                                    {{ $profile->preferred_country ?: 'Not provided' }}
                                    |
                                    University:
                                    {{ $university->country }}
                                </p>
                            </div>

                            <span class="{{ $countryMatch ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ $countryMatch ? '✓ Match' : '✗ Gap' }}
                            </span>
                        </div>

                        {{-- Degree --}}
                        <div class="flex justify-between border-b pb-3">
                            <div>
                                <p class="font-medium">Degree Level</p>

                                <p class="text-sm text-gray-500">
                                    Your:
                                    {{ $profile->preferred_degree_level ?: 'Not provided' }}
                                    |
                                    University:
                                    {{ $university->degree_level }}
                                </p>
                            </div>

                            <span class="{{ $degreeMatch ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ $degreeMatch ? '✓ Match' : '✗ Gap' }}
                            </span>
                        </div>

                        {{-- Subject --}}
                        <div class="flex justify-between border-b pb-3">
                            <div>
                                <p class="font-medium">Subject</p>

                                <p class="text-sm text-gray-500">
                                    Your:
                                    {{ $profile->preferred_subject ?: 'Not provided' }}
                                    |
                                    University:
                                    {{ $university->subject }}
                                </p>
                            </div>

                            <span class="{{ $subjectMatch ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ $subjectMatch ? '✓ Match' : '✗ Gap' }}
                            </span>
                        </div>

                        {{-- Budget --}}
                        <div class="flex justify-between">
                            <div>
                                <p class="font-medium">Annual Tuition Budget</p>

                                <p class="text-sm text-gray-500">
                                    Your:
                                    {{ $profile->budget !== null ? '$' . number_format((float) $profile->budget) : 'Not provided' }}
                                    |
                                    Tuition:
                                    ${{ number_format((float) $university->tuition_fee) }}
                                </p>
                            </div>

                            <span class="{{ $budgetMatch ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ $budgetMatch ? '✓ Match' : '✗ Gap' }}
                            </span>
                        </div>

                    </div>

                </div>

            @else

                <div class="border-t mt-8 pt-8 text-center">

                    <p class="text-gray-600 mb-4">
                        Complete your student profile to see your university match.
                    </p>

                    <a
                        href="{{ route('student.profile.edit') }}"
                        class="inline-block bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700"
                    >
                        Complete Profile
                    </a>

                </div>

            @endif

            {{-- University Application --}}
            <div class="border-t mt-8 pt-8">

                <div class="bg-indigo-50 rounded-lg p-6">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                Ready to Apply?
                            </h3>

                            <p class="text-sm text-gray-600 mt-1">
                                Start your university application for
                                {{ $university->name }} through UniBridge.
                            </p>
                        </div>

                        <a
                            href="{{ route('student.applications.create', ['university' => $university->id]) }}"
                            class="inline-flex items-center justify-center bg-indigo-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-indigo-700 transition"
                        >
                            Apply Now
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

</x-app-layout>
