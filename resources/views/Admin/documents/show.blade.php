<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-sm font-bold text-indigo-700">
                    UniBridge Administration
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Review Document
                </h2>

                <p class="mt-1 text-sm text-slate-600">
                    Review the uploaded document and verify or reject it.
                </p>
            </div>

            <a
                href="{{ route('admin.documents.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800"
            >
                ← Back to Documents
            </a>

        </div>
    </x-slot>

    <div class="py-8">

        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Success message --}}
            @if (session('status'))

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">

                    @if (session('status') === 'document-approved')
                        Document approved successfully.

                    @elseif (session('status') === 'document-rejected')
                        Document rejected successfully.

                    @else
                        Action completed successfully.
                    @endif

                </div>

            @endif


            {{-- User information --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <p class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">
                            Uploaded By
                        </p>

                        <h3 class="mt-1 text-2xl font-extrabold text-slate-950">
                            {{ $document->user?->name ?? 'Unknown User' }}
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $document->user?->email ?? 'No email available' }}
                        </p>

                    </div>

                    @if ($document->user?->role)

                        <span class="inline-flex w-fit rounded-full bg-indigo-100 px-4 py-2 text-sm font-extrabold capitalize text-indigo-700">
                            {{ $document->user->role }}
                        </span>

                    @endif

                </div>

            </div>


            {{-- Document details --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-6">

                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Document Title
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            {{ $document->title }}
                        </h3>
                    </div>


                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                        <div class="rounded-xl bg-slate-50 p-4">

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Document Type
                            </p>

                            <p class="mt-1 font-bold capitalize text-slate-900">
                                {{ str_replace('_', ' ', $document->document_type) }}
                            </p>

                        </div>


                        <div class="rounded-xl bg-slate-50 p-4">

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Original File
                            </p>

                            <p class="mt-1 break-all font-bold text-slate-900">
                                {{ $document->original_name }}
                            </p>

                        </div>


                        <div class="rounded-xl bg-slate-50 p-4">

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Uploaded
                            </p>

                            <p class="mt-1 font-bold text-slate-900">
                                {{ $document->created_at?->format('d M Y, h:i A') }}
                            </p>

                        </div>

                    </div>


                    {{-- Current status --}}
                    <div>

                        <p class="mb-2 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Verification Status
                        </p>

                        @if ($document->verification_status === 'approved')

                            <span class="inline-flex rounded-full bg-emerald-100 px-4 py-2 text-sm font-extrabold text-emerald-700">
                                ✓ Approved
                            </span>

                        @elseif ($document->verification_status === 'rejected')

                            <span class="inline-flex rounded-full bg-rose-100 px-4 py-2 text-sm font-extrabold text-rose-700">
                                ✕ Rejected
                            </span>

                        @else

                            <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-sm font-extrabold text-amber-700">
                                ● Pending Review
                            </span>

                        @endif

                    </div>


                    {{-- Secure download --}}
                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                            <div>

                                <h4 class="font-extrabold text-blue-950">
                                    Secure Document Access
                                </h4>

                                <p class="mt-1 text-sm leading-6 text-blue-800">
                                    This document is stored privately and can only be accessed
                                    through the authorized admin download route.
                                </p>

                            </div>

                            <a
                                href="{{ route('admin.documents.download', $document) }}"
                                class="inline-flex shrink-0 items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-blue-700"
                            >
                                Download Document
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Verification actions --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="mb-5">

                    <h3 class="text-xl font-extrabold text-slate-950">
                        Administrator Review
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Add an optional note when approving, or provide a reason when rejecting.
                    </p>

                </div>


                <div class="space-y-5">

                    <div>

                        <label
                            for="admin_notes"
                            class="mb-2 block text-sm font-bold text-slate-700"
                        >
                            Admin Notes
                        </label>

                        <textarea
                            id="admin_notes"
                            rows="5"
                            maxlength="2000"
                            placeholder="Write your review notes here..."
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ $document->admin_notes }}</textarea>

                        <p class="mt-2 text-xs text-slate-500">
                            Maximum 2000 characters.
                        </p>

                    </div>


                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">

                        {{-- Approve --}}
                        <form
                            method="POST"
                            action="{{ route('admin.documents.approve', $document) }}"
                        >

                            @csrf
                            @method('PATCH')

                            <input
                                type="hidden"
                                name="admin_notes"
                                id="approve_notes"
                            >

                            <button
                                type="submit"
                                onclick="
                                    document.getElementById('approve_notes').value =
                                    document.getElementById('admin_notes').value;
                                "
                                class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-emerald-700 sm:w-auto"
                            >
                                ✓ Approve Document
                            </button>

                        </form>


                        {{-- Reject --}}
                        <form
                            method="POST"
                            action="{{ route('admin.documents.reject', $document) }}"
                            onsubmit="
                                const note =
                                document.getElementById('admin_notes').value.trim();

                                if (!note) {
                                    alert('Please enter a rejection reason.');
                                    return false;
                                }

                                document.getElementById('reject_notes').value = note;

                                return confirm(
                                    'Are you sure you want to reject this document?'
                                );
                            "
                        >

                            @csrf
                            @method('PATCH')

                            <input
                                type="hidden"
                                name="admin_notes"
                                id="reject_notes"
                            >

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-rose-600 px-6 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-rose-700 sm:w-auto"
                            >
                                ✕ Reject Document
                            </button>

                        </form>

                    </div>

                </div>

            </div>


            {{-- Previous admin review --}}
            @if ($document->admin_notes || $document->verified_at)

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">

                    <h3 class="font-extrabold text-slate-950">
                        Previous Review
                    </h3>

                    @if ($document->admin_notes)

                        <div class="mt-4 rounded-xl bg-white p-4">

                            <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                Admin Note
                            </p>

                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">
                                {{ $document->admin_notes }}
                            </p>

                        </div>

                    @endif

                    @if ($document->verified_at)

                        <p class="mt-4 text-xs font-semibold text-slate-500">
                            Reviewed:
                            {{ $document->verified_at->format('d M Y, h:i A') }}
                        </p>

                    @endif

                </div>

            @endif


            {{-- Privacy --}}
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">

                <div class="flex gap-3">

                    <div class="text-lg">
                        🔒
                    </div>

                    <div>

                        <h3 class="font-extrabold text-amber-950">
                            Privacy & Security
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-amber-900">
                            Identity and education documents are sensitive information.
                            Keep them private and access them only through authorized
                            UniBridge administrator controls.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>