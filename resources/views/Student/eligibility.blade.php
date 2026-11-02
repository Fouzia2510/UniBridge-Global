<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Eligibility Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Admission Likelihood</h3>
                        <p class="text-sm text-gray-500">Based on your current profile.</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        {{ $summary['admission_likelihood'] === 'High' ? 'bg-green-100 text-green-800' : ($summary['admission_likelihood'] === 'Moderate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $summary['admission_likelihood'] }}
                    </span>
                </div>

                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-2">✅ Strengths</h4>
                    @if (count($summary['strengths']))
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            @foreach ($summary['strengths'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-sm">No strengths identified yet — complete more of your profile.</p>
                    @endif
                </div>

                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-2">⚠️ Gaps</h4>
                    @if (count($summary['gaps'] ?? []))
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                          @foreach (($summary['gaps'] ?? []) as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-sm">No major gaps found. Great job!</p>
                    @endif
                </div>

                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-2">💡 Recommendations</h4>
                    @if (count($summary['recommendations']))
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            @foreach ($summary['recommendations'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-sm">Nothing to recommend right now.</p>
                    @endif
                </div>

                <a href="{{ route('student.profile.edit') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    ← Update your profile
                </a>

            </div>
        </div>
    </div>
</x-app-layout>