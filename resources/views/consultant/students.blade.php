<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Students
            </h2>

            <p class="text-sm text-gray-500">
                View student profiles and eligibility information.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        My Students
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Students available in the consultant portal.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">

                                <th class="px-6 py-3">
                                    Name
                                </th>

                                <th class="px-6 py-3">
                                    Email
                                </th>

                                <th class="px-6 py-3">
                                    Readiness
                                </th>

                                <th class="px-6 py-3">
                                    Profile
                                </th>

                                <th class="px-6 py-3">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">

                            @forelse ($students as $student)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">
                                            {{ $student->name }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $student->email }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if ($student->studentProfile)

                                            @php
                                                $score = $student->studentProfile->readiness_score ?? 0;
                                            @endphp

                                            <span class="font-semibold
                                                {{ $score >= 70
                                                    ? 'text-green-600'
                                                    : ($score >= 40
                                                        ? 'text-yellow-600'
                                                        : 'text-red-600') }}">
                                                {{ $score }}%
                                            </span>

                                        @else

                                            <span class="text-gray-400">
                                                —
                                            </span>

                                        @endif

                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if ($student->studentProfile)

                                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                                Complete
                                            </span>

                                        @else

                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                                Incomplete
                                            </span>

                                        @endif

                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <a
                                            href="{{ route('consultant.students.show', $student) }}"
                                            class="font-medium text-indigo-600 hover:text-indigo-800"
                                        >
                                            View Profile →
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="5"
                                        class="px-6 py-10 text-center text-gray-500"
                                    >
                                        No students registered yet.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

            </div>

        </div>
    </div>

</x-app-layout>