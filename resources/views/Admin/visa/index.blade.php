<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-700">
                    Visa Management
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Visa Applications
                </h2>

                <p class="mt-1 text-sm text-slate-600">
                    Review and manage student visa procedures.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">
                        Total Visa Cases
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-slate-950">
                        {{ $visaApplications->total() }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">
                        Under Review
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-indigo-700">
                        {{
                            $visaApplications->getCollection()
                                ->where('status', 'under_review')
                                ->count()
                        }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">
                        Submitted
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-emerald-700">
                        {{
                            $visaApplications->getCollection()
                                ->where('status', 'submitted')
                                ->count()
                        }}
                    </p>
                </div>

            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="text-lg font-extrabold text-slate-950">
                        All Visa Procedures
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Click a case to review its details and update its status.
                    </p>
                </div>

                @if ($visaApplications->count())

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">

                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Student
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        University
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Country
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Visa Type
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Status
                                    </th>

                                    <th class="px-6 py-4 text-right text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 bg-white">

                                @foreach ($visaApplications as $visaApplication)

                                    @php
                                        $statusClasses = match ($visaApplication->status) {
                                            'approved' => 'bg-emerald-100 text-emerald-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'submitted' => 'bg-indigo-100 text-indigo-800',
                                            'under_review' => 'bg-amber-100 text-amber-800',
                                            'ready_to_apply' => 'bg-cyan-100 text-cyan-800',
                                            'documents_submitted' => 'bg-violet-100 text-violet-800',
                                            'documents_required' => 'bg-orange-100 text-orange-800',
                                            default => 'bg-slate-100 text-slate-700',
                                        };

                                        $statusLabel = str_replace(
                                            '_',
                                            ' ',
                                            $visaApplication->status
                                        );
                                    @endphp

                                    <tr class="transition hover:bg-slate-50">

                                        <td class="px-6 py-5">
                                            <div>
                                                <p class="font-bold text-slate-950">
                                                    {{ $visaApplication->student?->name ?? 'Unknown Student' }}
                                                </p>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $visaApplication->student?->email ?? 'No email' }}
                                                </p>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            <p class="font-semibold text-slate-900">
                                                {{ $visaApplication->application?->university?->name ?? 'N/A' }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Application #{{ $visaApplication->application_id }}
                                            </p>
                                        </td>

                                        <td class="px-6 py-5">
                                            <span class="font-semibold text-slate-800">
                                                {{ $visaApplication->country }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <span class="text-sm font-semibold text-slate-700">
                                                {{ $visaApplication->visa_type }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-extrabold capitalize {{ $statusClasses }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5 text-right">
                                            <a
                                                href="{{ route('admin.visa.show', $visaApplication) }}"
                                                class="inline-flex items-center rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
                                            >
                                                View Details
                                            </a>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>
                    </div>

                    <div class="border-t border-slate-200 px-6 py-5">
                        {{ $visaApplications->links() }}
                    </div>

                @else

                    <div class="px-6 py-16 text-center">

                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">
                            <svg
                                class="h-8 w-8 text-slate-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M14 3v5h5"
                                />
                            </svg>
                        </div>

                        <h3 class="mt-5 text-lg font-extrabold text-slate-950">
                            No Visa Applications
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                            There are currently no student visa procedures in the system.
                        </p>

                    </div>

                @endif

            </div>

        </div>
    </div>

</x-app-layout>