<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-red-700">
                    UniBridge
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Visa Procedure
                </h2>

                <p class="mt-1 text-sm font-medium text-slate-600">
                    Review your visa application progress and current status.
                </p>
            </div>

            <a
                href="{{ route('student.visa.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
            >
                All Visa Procedures
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-bold">Please check the following:</p>

                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">

                <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-red-700">
                                {{ $visaApplication->country }}
                            </p>

                            <h3 class="mt-1 text-2xl font-extrabold text-slate-950">
                                {{ $visaApplication->visa_type }}
                            </h3>

                            <p class="mt-2 text-sm text-slate-600">
                                {{ $visaApplication->application?->university?->name ?? 'University not available' }}
                            </p>
                        </div>

                        <span class="inline-flex w-fit rounded-full bg-slate-100 px-4 py-2 text-xs font-extrabold text-slate-700">
                            {{ ucwords(str_replace('_', ' ', $visaApplication->status)) }}
                        </span>
                    </div>

                    <div class="mt-8 grid gap-5 sm:grid-cols-2">

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Program
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-800">
                                {{ $visaApplication->application?->program ?? 'Not available' }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Intake
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-800">
                                {{ $visaApplication->application?->intake ?? 'Not specified' }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Started
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-800">
                                {{ $visaApplication->created_at?->format('d M Y') ?? '—' }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Submitted
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-800">
                                {{ $visaApplication->submitted_at?->format('d M Y, h:i A') ?? 'Not submitted' }}
                            </p>
                        </div>

                    </div>

                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-extrabold text-slate-950">
                        Procedure Status
                    </h3>

                    <div class="mt-5 space-y-4">

                        @php
                            $statusSteps = [
                                'documents_required' => 'Documents Required',
                                'documents_submitted' => 'Documents Submitted',
                                'under_review' => 'Under Review',
                                'ready_to_apply' => 'Ready to Apply',
                                'submitted' => 'Submitted',
                                'processing' => 'Processing',
                                'approved' => 'Approved',
                            ];

                            $statusOrder = [
                                'not_started',
                                'documents_required',
                                'documents_submitted',
                                'under_review',
                                'ready_to_apply',
                                'submitted',
                                'processing',
                                'approved',
                                'rejected',
                            ];

                            $currentIndex = array_search(
                                $visaApplication->status,
                                $statusOrder,
                                true
                            );
                        @endphp

                        @foreach ($statusSteps as $statusKey => $statusLabel)
                            @php
                                $stepIndex = array_search(
                                    $statusKey,
                                    $statusOrder,
                                    true
                                );

                                $isComplete = $currentIndex !== false
                                    && $stepIndex !== false
                                    && $stepIndex <= $currentIndex;

                                $isCurrent = $visaApplication->status === $statusKey;
                            @endphp

                            <div class="flex items-center gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                    {{ $isComplete ? 'bg-red-700 text-white' : 'bg-slate-100 text-slate-400' }}">
                                    @if ($isComplete)
                                        <span class="text-sm font-black">?</span>
                                    @else
                                        <span class="text-xs font-black">•</span>
                                    @endif
                                </div>

                                <div>
                                    <p class="text-sm font-bold
                                        {{ $isCurrent ? 'text-red-700' : ($isComplete ? 'text-slate-800' : 'text-slate-400') }}">
                                        {{ $statusLabel }}
                                    </p>

                                    @if ($isCurrent)
                                        <p class="text-xs font-medium text-slate-500">
                                            Current status
                                        </p>
                                    @endif
                                </div>

                            </div>
                        @endforeach

                        @if ($visaApplication->status === 'rejected')
                            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                                <p class="text-sm font-extrabold text-red-800">
                                    Visa Procedure Rejected
                                </p>

                                <p class="mt-1 text-xs leading-5 text-red-700">
                                    Please review the admin notes below and follow the required next steps.
                                </p>
                            </div>
                        @endif

                    </div>
                </div>

            </div>

            <div class="grid gap-6 lg:grid-cols-2">

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-extrabold text-slate-950">
                        Student Notes
                    </h3>

                    <div class="mt-4 rounded-xl bg-slate-50 p-4">
                        @if ($visaApplication->student_notes)
                            <p class="whitespace-pre-line text-sm leading-6 text-slate-700">
                                {{ $visaApplication->student_notes }}
                            </p>
                        @else
                            <p class="text-sm text-slate-500">
                                No additional notes were provided.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-extrabold text-slate-950">
                        Admin Notes
                    </h3>

                    <div class="mt-4 rounded-xl bg-slate-50 p-4">
                        @if ($visaApplication->admin_notes)
                            <p class="whitespace-pre-line text-sm leading-6 text-slate-700">
                                {{ $visaApplication->admin_notes }}
                            </p>
                        @else
                            <p class="text-sm text-slate-500">
                                No admin notes have been added yet.
                            </p>
                        @endif
                    </div>
                </div>

            </div>

            @if (in_array($visaApplication->status, [
                'documents_required',
                'documents_submitted',
                'ready_to_apply',
            ], true))
                <div class="rounded-2xl border border-red-200 bg-red-50 p-6">

                    <h3 class="text-lg font-extrabold text-red-900">
                        Submit Visa Application
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-red-800">
                        Make sure your required documents have been uploaded and reviewed before submitting your visa application.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('student.visa.submit', $visaApplication) }}"
                        class="mt-5"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-red-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-red-800"
                        >
                            Submit Visa Application
                        </button>
                    </form>

                </div>
            @endif

            @if ($visaApplication->status === 'approved')
                <div class="rounded-2xl border border-green-200 bg-green-50 p-6">
                    <h3 class="text-lg font-extrabold text-green-900">
                        Visa Approved
                    </h3>

                    <p class="mt-2 text-sm text-green-800">
                        Your visa procedure has been marked as approved.
                    </p>

                    @if ($visaApplication->decision_at)
                        <p class="mt-3 text-xs font-bold text-green-700">
                            Decision date:
                            {{ $visaApplication->decision_at->format('d M Y, h:i A') }}
                        </p>
                    @endif
                </div>
            @endif

        </div>
    </div>

</x-app-layout>
