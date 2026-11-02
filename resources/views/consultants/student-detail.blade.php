<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Profile') }} — {{ $student->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <a href="{{ route('consultant.students.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mb-4 inline-block">
                    ← Back to students
                </a>

                <div class="mb-6">
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium">{{ $student->email }}</p>
                </div>

                @if ($profile)
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Readiness Score</h3>
                        <div class="text-3xl font-bold {{ $profile->readiness_score >= 70 ? 'text-green-600' : ($profile->readiness_score >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $profile->readiness_score ?? 0 }}%
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                        <div><span class="text-gray-500">Degree:</span> {{ $profile->degree ?? '—' }}</div>
                        <div><span class="text-gray-500">Field of Study:</span> {{ $profile->field_of_study ?? '—' }}</div>
                        <div><span class="text-gray-500">CGPA:</span> {{ $profile->cgpa ?? '—' }}</div>
                        <div><span class="text-gray-500">Graduation Year:</span> {{ $profile->graduation_year ?? '—' }}</div>
                        <div><span class="text-gray-500">English Test:</span> {{ $profile->english_test ?? '—' }}</div>
                        <div><span class="text-gray-500">English Score:</span> {{ $profile->english_score ?? '—' }}</div>
                        <div><span class="text-gray-500">Preferred Country:</span> {{ $profile->preferred_country ?? '—' }}</div>
                        <div><span class="text-gray-500">Preferred Degree Level:</span> {{ $profile->preferred_degree_level ?? '—' }}</div>
                        <div><span class="text-gray-500">Preferred Subject:</span> {{ $profile->preferred_subject ?? '—' }}</div>
                        <div><span class="text-gray-500">Budget:</span> {{ $profile->budget ? '$'.number_format($profile->budget) : '—' }}</div>
                        <div><span class="text-gray-500">Preferred Intake:</span> {{ $profile->preferred_intake ?? '—' }}</div>
                        <div><span class="text-gray-500">Work Experience:</span> {{ $profile->work_experience_years ?? '—' }} years</div>
                    </div>

                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-900 mb-2">✅ Strengths</h4>
                        @if (count($summary['strengths']))
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                @foreach ($summary['strengths'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm">None identified.</p>
                        @endif
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">⚠️ Gaps</h4>
                        @if (count($summary['gaps']))
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                @foreach ($summary['gaps'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm">No major gaps found.</p>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">This student hasn't completed their profile yet.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>