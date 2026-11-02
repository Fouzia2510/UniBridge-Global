<x-app-layout>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('University Applications') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    University Applications
                </h1>

                <p class="mt-1 text-sm text-gray-600">
                    Review and manage applications submitted by students.
                </p>
            </div>

            {{-- Applications --}}
            @forelse ($applications as $application)

                <div class="border border-gray-200 rounded-lg p-5 mb-4">

                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">

                        {{-- Student + University Information --}}
                        <div class="flex-1">

                            <h3 class="text-lg font-bold text-gray-800">
                                {{ $application->university->name }}
                            </h3>

                            <p class="text-sm text-gray-600 mt-1">
                                {{ $application->university->city }},
                                {{ $application->university->country }}
                            </p>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">

                                <div>
                                    <span class="font-semibold text-gray-700">
                                        Student:
                                    </span>

                                    <span class="text-gray-600">
                                        {{ $application->student->name }}
                                    </span>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">
                                        Email:
                                    </span>

                                    <span class="text-gray-600">
                                        {{ $application->student->email }}
                                    </span>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">
                                        Program:
                                    </span>

                                    <span class="text-gray-600">
                                        {{ $application->program }}
                                    </span>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">
                                        Intake:
                                    </span>

                                    <span class="text-gray-600">
                                        {{ $application->intake ?: 'Not specified' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-700">
                                        Applied:
                                    </span>

                                    <span class="text-gray-600">
                                        {{ $application->created_at->format('d M Y, h:i A') }}
                                    </span>
                                </div>

                            </div>

                        </div>


                        {{-- Status --}}
                        <div class="lg:text-right">

                            @php
                                $statusClasses = match ($application->status) {
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'submitted' => 'bg-blue-100 text-blue-800',
                                    'under_review' => 'bg-yellow-100 text-yellow-800',
                                    'accepted' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'withdrawn' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp

                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                {{ ucwords(str_replace('_', ' ', $application->status)) }}
                            </span>

                        </div>

                    </div>


                    {{-- Actions --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 flex flex-wrap gap-3">

                        {{-- View Details --}}
                        <a
                            href="{{ route('admin.applications.show', $application) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                        >
                            View Details
                        </a>


                        {{-- Status Update --}}
                        @if (!in_array($application->status, ['accepted', 'rejected', 'withdrawn']))

                            <form
                                method="POST"
                                action="{{ route('admin.applications.status', $application) }}"
                                class="flex flex-wrap items-center gap-2"
                            >
                                @csrf
                                @method('PATCH')

                                <select
                                    name="status"
                                    class="border-gray-300 rounded-md shadow-sm text-sm"
                                    required
                                >
                                    <option value="">
                                        Update Status
                                    </option>

                                    <option value="under_review">
                                        Under Review
                                    </option>

                                    <option value="accepted">
                                        Accepted
                                    </option>

                                    <option value="rejected">
                                        Rejected
                                    </option>

                                    <option value="withdrawn">
                                        Withdrawn
                                    </option>
                                </select>

                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                >
                                    Update
                                </button>

                            </form>

                        @endif

                    </div>

                </div>

            @empty

                {{-- Empty State --}}
                <div class="text-center py-12">

                    <div class="text-5xl mb-4">
                        📄
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800">
                        No Applications Found
                    </h3>

                    <p class="mt-2 text-sm text-gray-600">
                        No students have submitted university applications yet.
                    </p>

                </div>

            @endforelse

        </div>

    </div>
</div>

</x-app-layout>
