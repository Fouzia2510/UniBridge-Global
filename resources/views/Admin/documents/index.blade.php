<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-700">
                    UniBridge Administration
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Document Verification
                </h2>

                <p class="mt-1 text-sm text-slate-600">
                    Review and verify documents uploaded by students and consultants.
                </p>
            </div>

            <a
                href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800"
            >
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">
                    {{ session('status') === 'document-approved'
                        ? 'Document approved successfully.'
                        : (session('status') === 'document-rejected'
                            ? 'Document rejected successfully.'
                            : 'Action completed successfully.') }}
                </div>
            @endif

            {{-- Summary cards --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <a
                    href="{{ route('admin.documents.index') }}"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm font-bold text-slate-500">
                        All Documents
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-slate-950">
                        {{ $counts['all'] }}
                    </p>

                    <p class="mt-1 text-xs font-semibold text-slate-500">
                        Total uploaded documents
                    </p>
                </a>

                <a
                    href="{{ route('admin.documents.index', ['status' => 'pending']) }}"
                    class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm font-bold text-amber-700">
                        Pending
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-amber-900">
                        {{ $counts['pending'] }}
                    </p>

                    <p class="mt-1 text-xs font-semibold text-amber-700">
                        Waiting for review
                    </p>
                </a>

                <a
                    href="{{ route('admin.documents.index', ['status' => 'approved']) }}"
                    class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm font-bold text-emerald-700">
                        Approved
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-emerald-900">
                        {{ $counts['approved'] }}
                    </p>

                    <p class="mt-1 text-xs font-semibold text-emerald-700">
                        Verified documents
                    </p>
                </a>

                <a
                    href="{{ route('admin.documents.index', ['status' => 'rejected']) }}"
                    class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm font-bold text-rose-700">
                        Rejected
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-rose-900">
                        {{ $counts['rejected'] }}
                    </p>

                    <p class="mt-1 text-xs font-semibold text-rose-700">
                        Documents requiring correction
                    </p>
                </a>

            </div>

            {{-- Filter --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                    <div>
                        <h3 class="text-lg font-extrabold text-slate-950">
                            Uploaded Documents
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Review documents submitted by UniBridge users.
                        </p>
                    </div>

                    <form method="GET" action="{{ route('admin.documents.index') }}">
                        <label
                            for="status"
                            class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Filter Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            onchange="this.form.submit()"
                            class="rounded-xl border-slate-300 text-sm font-semibold text-slate-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">All Documents</option>

                            <option
                                value="pending"
                                @selected($currentStatus === 'pending')
                            >
                                Pending
                            </option>

                            <option
                                value="approved"
                                @selected($currentStatus === 'approved')
                            >
                                Approved
                            </option>

                            <option
                                value="rejected"
                                @selected($currentStatus === 'rejected')
                            >
                                Rejected
                            </option>
                        </select>
                    </form>

                </div>
            </div>

            {{-- Documents table --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                @if ($documents->count() > 0)

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">

                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        User
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Document
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Type
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Status
                                    </th>

                                    <th class="px-6 py-4 text-right text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                @foreach ($documents as $document)

                                    <tr class="transition hover:bg-slate-50">

                                        {{-- User --}}
                                        <td class="px-6 py-5 align-top">
                                            <div class="font-bold text-slate-900">
                                                {{ $document->user?->name ?? 'Unknown User' }}
                                            </div>

                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $document->user?->email ?? '—' }}
                                            </div>

                                            @if ($document->user?->role)
                                                <span class="mt-2 inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-bold capitalize text-indigo-700">
                                                    {{ $document->user->role }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Document --}}
                                        <td class="px-6 py-5 align-top">
                                            <div class="font-bold text-slate-900">
                                                {{ $document->title }}
                                            </div>

                                            <div class="mt-1 max-w-xs truncate text-xs text-slate-500">
                                                {{ $document->original_name }}
                                            </div>

                                            <div class="mt-1 text-xs text-slate-400">
                                                Uploaded
                                                {{ $document->created_at?->format('d M Y, h:i A') }}
                                            </div>
                                        </td>

                                        {{-- Type --}}
                                        <td class="px-6 py-5 align-top">
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold capitalize text-slate-700">
                                                {{ str_replace('_', ' ', $document->document_type) }}
                                            </span>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-6 py-5 align-top">

                                            @if ($document->verification_status === 'approved')

                                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-700">
                                                    Approved
                                                </span>

                                            @elseif ($document->verification_status === 'rejected')

                                                <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-extrabold text-rose-700">
                                                    Rejected
                                                </span>

                                            @else

                                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-extrabold text-amber-700">
                                                    Pending
                                                </span>

                                            @endif

                                        </td>

                                        {{-- Action --}}
                                        <td class="px-6 py-5 text-right align-top">

                                            <a
                                                href="{{ route('admin.documents.show', $document) }}"
                                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                                            >
                                                Review
                                            </a>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $documents->links() }}
                    </div>

                @else

                    <div class="px-6 py-14 text-center">

                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">
                            <span class="text-2xl">📄</span>
                        </div>

                        <h3 class="mt-4 text-lg font-extrabold text-slate-900">
                            No documents found
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            There are no documents matching the selected filter.
                        </p>

                    </div>

                @endif

            </div>

            {{-- Privacy notice --}}
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">

                <div class="flex gap-3">

                    <div class="mt-0.5 text-lg">
                        🔒
                    </div>

                    <div>
                        <h3 class="font-extrabold text-blue-900">
                            Document Privacy
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-blue-800">
                            Uploaded identity and education documents are stored privately.
                            Only authorized users and administrators can access them through
                            the secure document routes.
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>