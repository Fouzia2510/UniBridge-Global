<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Universities
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-5 rounded-lg bg-green-100 p-4 text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-lg bg-red-100 p-4 text-red-800">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        University Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        View and manage universities listed on UniBridge.
                    </p>
                </div>

                <a href="{{ route('admin.universities.create') }}"
                   class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700">
                    + Add University
                </a>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-600">University</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-600">Country / City</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-600">Program</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-600">Tuition Fee</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-600">Scholarship</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-600">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($universities as $university)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $university->name }}
                                        </div>
                                        <div class="mt-1 text-sm text-gray-500">
                                            Rank: {{ $university->ranking ?? 'N/A' }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-sm text-gray-700">
                                        {{ $university->country }}<br>
                                        {{ $university->city }}
                                    </td>

                                    <td class="px-5 py-4 text-sm text-gray-700">
                                        {{ $university->degree_level }}<br>
                                        {{ $university->subject }}
                                    </td>

                                    <td class="px-5 py-4 text-sm text-gray-700">
                                        {{ number_format((float) $university->tuition_fee, 2) }}
                                    </td>

                                    <td class="px-5 py-4 text-sm">
                                        @if ($university->scholarship_available)
                                            <span class="font-semibold text-green-700">Available</span>
                                        @else
                                            <span class="text-gray-500">Not listed</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        <a href="{{ route('admin.universities.edit', $university) }}"
                                           class="font-semibold text-indigo-600 hover:text-indigo-800">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-gray-500">
                                        No universities found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-5 py-4">
                    {{ $universities->links() }}
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                Existing university records and student applications are preserved.
            </p>
        </div>
    </div>
</x-app-layout>
