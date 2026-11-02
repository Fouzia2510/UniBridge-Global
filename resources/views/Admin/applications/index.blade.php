<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('University Applications') }}
        </h2>
    </x-slot>

```
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('status'))
            <div class="mb-6 rounded-lg bg-green-100 border border-green-300 p-4 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">

                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">
                        All University Applications
                    </h3>

                    <p class="mt-2 text-gray-600">
                        Review and manage applications submitted by students.
                    </p>
                </div>

                @if ($applications->count())

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Student
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        University
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Program
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Intake
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Status
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach ($applications as $application)

                                    <tr class="hover:bg-gray-50">

                                        <td class="px-4 py-4">
                                            <div class="font-medium text-gray-900">
                                                {{ $application->student->name ?? 'N/A' }}
                                            </div>

                                            <div class="text-sm text-gray-500">
                                                {{ $application->student->email ?? 'N/A' }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 text-gray-700">
                                            {{ $application->university->name ?? 'N/A' }}
                                        </td>

                                        <td class="px-4 py-4 text-gray-700">
                                            {{ $application->program }}
                                        </td>

                                        <td class="px-4 py-4 text-gray-700">
                                            {{ $application->intake ?? 'Not specified' }}
                                        </td>

                                        <td class="px-4 py-4">
                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                {{ ucwords(str_replace('_', ' ', $application->status)) }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            <a
                                                href="{{ route('admin.applications.show', $application) }}"
                                                class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                                            >
                                                View Details
                                            </a>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="p-6 text-center bg-gray-50 rounded-lg">
                        <p class="text-gray-600">
                            No university applications found.
                        </p>
                    </div>

                @endif

            </div>
        </div>

    </div>
</div>
```

</x-app-layout>
