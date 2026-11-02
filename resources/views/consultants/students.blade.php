<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Students') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="text-left text-sm text-gray-500">
                            <th class="py-2 pr-4">Name</th>
                            <th class="py-2 pr-4">Email</th>
                            <th class="py-2 pr-4">Readiness Score</th>
                            <th class="py-2 pr-4">Profile Status</th>
                            <th class="py-2 pr-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($students as $student)
                            <tr class="text-sm">
                                <td class="py-3 pr-4 font-medium text-gray-900">{{ $student->name }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $student->email }}</td>
                                <td class="py-3 pr-4">
                                    @if ($student->studentProfile)
                                        <span class="font-semibold {{ $student->studentProfile->readiness_score >= 70 ? 'text-green-600' : ($student->studentProfile->readiness_score >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $student->studentProfile->readiness_score ?? 0 }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4">
                                    @if ($student->studentProfile)
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Complete</span>
                                    @else
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Incomplete</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4">
                                    <a href="{{ route('consultant.students.show', $student) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        View Profile →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">No students registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>