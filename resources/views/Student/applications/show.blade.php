<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Application Details
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Review your university application, payment status and progress.
                </p>
            </div>

            <a
                href="{{ route('student.applications.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
            >
                ← Back to Applications
            </a>
        </div>
    </x-slot>

    @php
        $latestPayment = $application->payments
            ? $application->payments->sortByDesc('created_at')->first()
            : null;

        $visaApplication = $application->visaApplication;

        $applicationStatus = match ($application->status) {
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
            default => ucfirst(str_replace('_', ' ', $application->status)),
        };

        $applicationStatusClass = match ($application->status) {
            'accepted' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'rejected' => 'bg-red-100 text-red-800 border-red-200',
            'withdrawn' => 'bg-gray-100 text-gray-700 border-gray-200',
            'under_review' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-blue-100 text-blue-800 border-blue-200',
        };

        $visaStatusLabel = $visaApplication
            ? ucfirst(str_replace('_', ' ', $visaApplication->status))
            : null;

        $visaStatusClass = match ($visaApplication?->status) {
            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'rejected' => 'bg-red-100 text-red-800 border-red-200',
            'submitted', 'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
            'under_review', 'documents_submitted' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };

        $visaPaymentVerified = $latestPayment
            && $latestPayment->status === 'verified';

        $visaApplicationAllowed = $visaPaymentVerified
            && in_array($application->status, ['accepted', 'under_review'], true);
    @endphp

    <div class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="text-xl">✓</div>

                        <div>
                            <p class="font-semibold text-emerald-800">
                                Success
                            </p>

                            <p class="mt-1 text-sm text-emerald-700">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Validation / Error Messages --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                    <p class="font-semibold text-red-800">
                        Please check the following:
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Application Header --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">

                <div class="border-b border-gray-200 bg-gradient-to-r from-indigo-50 via-white to-blue-50 p-6 sm:p-8">

                    <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">

                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                                University Application
                            </p>

                            <h1 class="mt-2 text-3xl font-bold text-gray-900">
                                Application #{{ $application->id }}
                            </h1>

                            <p class="mt-2 text-sm text-gray-600">
                                Submitted on
                                {{ optional($application->created_at)->format('d M Y, h:i A') }}
                            </p>
                        </div>

                        <div>
                            <span class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-bold {{ $applicationStatusClass }}">
                                {{ $applicationStatus }}
                            </span>
                        </div>

                    </div>

                </div>

                <div class="p-6 sm:p-8">

                    {{-- University & Program --}}
                    <div class="grid gap-6 md:grid-cols-2">

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-500">
                                University
                            </p>

                            <h2 class="mt-2 text-xl font-bold text-gray-900">
                                {{ $application->university->name ?? 'University' }}
                            </h2>

                            @if ($application->university)
                                <p class="mt-2 text-sm text-gray-600">
                                    📍
                                    {{ $application->university->city ?? '—' }},
                                    {{ $application->university->country ?? '—' }}
                                </p>
                            @endif
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-500">
                                Program
                            </p>

                            <h2 class="mt-2 text-xl font-bold text-gray-900">
                                {{ $application->program }}
                            </h2>

                            <p class="mt-2 text-sm text-gray-600">
                                Intake:
                                <span class="font-semibold text-gray-800">
                                    {{ $application->intake ?: 'Not specified' }}
                                </span>
                            </p>
                        </div>

                    </div>

                    {{-- Application Information --}}
                    <div class="mt-8">

                        <h3 class="text-lg font-bold text-gray-900">
                            Application Information
                        </h3>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                            <div class="rounded-xl border border-gray-200 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Application ID
                                </p>

                                <p class="mt-1 text-sm font-bold text-gray-900">
                                    #{{ $application->id }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-gray-200 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Application Status
                                </p>

                                <p class="mt-1 text-sm font-bold text-gray-900">
                                    {{ $applicationStatus }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-gray-200 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Application Fee
                                </p>

                                <p class="mt-1 text-sm font-bold text-gray-900">
                                    PKR {{ number_format((float) $application->fee, 2) }}
                                </p>
                            </div>

                        </div>

                    </div>

                    {{-- Payment Section --}}
                    <div class="mt-10">

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    Application Payment
                                </h3>

                                <p class="mt-1 text-sm text-gray-600">
                                    Your application fee payment and verification status.
                                </p>
                            </div>

                            @if ($latestPayment)
                                @php
                                    $paymentStatusClass = match ($latestPayment->status) {
                                        'verified' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                        default => 'bg-amber-100 text-amber-800 border-amber-200',
                                    };
                                @endphp

                                <span class="inline-flex w-fit items-center rounded-full border px-3 py-1.5 text-xs font-bold {{ $paymentStatusClass }}">
                                    {{ $latestPayment->statusLabel() }}
                                </span>
                            @endif

                        </div>

                        @if ($latestPayment)

                            <div class="mt-5 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Payment Method
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-900">
                                            {{ $latestPayment->methodLabel() }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Amount
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-900">
                                            {{ $latestPayment->currency }}
                                            {{ number_format((float) $latestPayment->amount, 2) }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Transaction Reference
                                        </p>

                                        <p class="mt-1 break-all font-semibold text-gray-900">
                                            {{ $latestPayment->transaction_reference }}
                                        </p>
                                    </div>

                                    @if ($latestPayment->payer_reference)
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Payer Reference
                                            </p>

                                            <p class="mt-1 break-all font-semibold text-gray-900">
                                                {{ $latestPayment->payer_reference }}
                                            </p>
                                        </div>
                                    @endif

                                    @if ($latestPayment->bank_name)
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Bank
                                            </p>

                                            <p class="mt-1 font-semibold text-gray-900">
                                                {{ $latestPayment->bank_name }}
                                            </p>
                                        </div>
                                    @endif

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Submitted
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-900">
                                            {{ optional($latestPayment->created_at)->format('d M Y, h:i A') }}
                                        </p>
                                    </div>

                                </div>

                                {{-- Verified --}}
                                @if ($latestPayment->status === 'verified')

                                    <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-5">

                                        <div class="flex items-start gap-3">

                                            <div class="text-xl font-bold text-emerald-600">
                                                ✓
                                            </div>

                                            <div>
                                                <h4 class="font-bold text-emerald-800">
                                                    Payment Verified
                                                </h4>

                                                <p class="mt-1 text-sm text-emerald-700">
                                                    Your application fee has been verified by UniBridge administration.
                                                    Your application can proceed to the next stage.
                                                </p>

                                                @if ($latestPayment->verified_at)
                                                    <p class="mt-2 text-xs font-semibold text-emerald-700">
                                                        Verified on:
                                                        {{ $latestPayment->verified_at->format('d M Y, h:i A') }}
                                                    </p>
                                                @endif
                                            </div>

                                        </div>

                                    </div>

                                {{-- Pending --}}
                                @elseif ($latestPayment->status === 'pending')

                                    <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-5">

                                        <div class="flex items-start gap-3">

                                            <div class="text-xl">
                                                ⏳
                                            </div>

                                            <div>
                                                <h4 class="font-bold text-amber-800">
                                                    Payment Pending Verification
                                                </h4>

                                                <p class="mt-1 text-sm text-amber-700">
                                                    Your payment has been submitted successfully and is waiting
                                                    for verification by the UniBridge administration team.
                                                </p>
                                            </div>

                                        </div>

                                    </div>

                                {{-- Rejected --}}
                                @elseif ($latestPayment->status === 'rejected')

                                    <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-5">

                                        <div class="flex items-start gap-3">

                                            <div class="text-xl">
                                                !
                                            </div>

                                            <div class="flex-1">

                                                <h4 class="font-bold text-red-800">
                                                    Payment Rejected
                                                </h4>

                                                <p class="mt-1 text-sm text-red-700">
                                                    Your previous payment submission was rejected.
                                                    Please review the administrator's note and submit a new payment.
                                                </p>

                                                @if ($latestPayment->admin_notes)
                                                    <div class="mt-3 rounded-lg bg-white p-3 ring-1 ring-red-100">
                                                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">
                                                            Admin Note
                                                        </p>

                                                        <p class="mt-1 text-sm text-gray-700">
                                                            {{ $latestPayment->admin_notes }}
                                                        </p>
                                                    </div>
                                                @endif

                                                <div class="mt-4">
                                                    <a
                                                        href="{{ route('student.applications.payment', $application) }}"
                                                        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700"
                                                    >
                                                        Submit New Payment
                                                    </a>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @endif

                                {{-- Payment Proof --}}
                                @if ($latestPayment->proof_path)

                                    <div class="mt-6 border-t border-gray-200 pt-5">

                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Payment Proof
                                        </p>

                                        <a
                                            href="{{ asset('storage/' . $latestPayment->proof_path) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="mt-2 inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-indigo-600 hover:bg-gray-50"
                                        >
                                            View Payment Proof
                                        </a>

                                    </div>

                                @endif

                            </div>

                        @else

                            {{-- No Payment --}}
                            <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-6">

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                    <div>
                                        <h4 class="font-bold text-amber-900">
                                            Payment Required
                                        </h4>

                                        <p class="mt-1 text-sm text-amber-800">
                                            No application fee payment has been submitted yet.
                                        </p>
                                    </div>

                                    <a
                                        href="{{ route('student.applications.payment', $application) }}"
                                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-indigo-700"
                                    >
                                        Pay Application Fee
                                    </a>

                                </div>

                            </div>

                        @endif

                    </div>

                    {{-- Visa Procedure --}}
                    <div class="mt-10">

                        <div class="overflow-hidden rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 via-white to-blue-50">

                            <div class="border-b border-indigo-100 p-6">

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                    <div>
                                        <p class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">
                                            Next Immigration Stage
                                        </p>

                                        <h3 class="mt-1 text-2xl font-extrabold text-slate-950">
                                            Visa Procedure
                                        </h3>

                                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                            Complete your visa preparation through UniBridge, including
                                            visa information, required documents, document review and
                                            final submission.
                                        </p>
                                    </div>

                                    @if ($visaApplication)
                                        <span class="inline-flex w-fit shrink-0 items-center rounded-full border px-3 py-1.5 text-xs font-extrabold capitalize {{ $visaStatusClass }}">
                                            {{ $visaStatusLabel }}
                                        </span>
                                    @endif

                                </div>

                            </div>

                            <div class="p-6">

                                @if ($visaApplication)

                                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                                Destination Country
                                            </p>

                                            <p class="mt-1 font-bold text-slate-900">
                                                {{ $visaApplication->country }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                                Visa Type
                                            </p>

                                            <p class="mt-1 font-bold text-slate-900">
                                                {{ $visaApplication->visa_type }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                                Procedure Status
                                            </p>

                                            <p class="mt-1 font-bold capitalize text-slate-900">
                                                {{ $visaStatusLabel }}
                                            </p>
                                        </div>

                                    </div>

                                    <div class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-5">

                                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                            <div>
                                                <h4 class="font-extrabold text-blue-950">
                                                    Continue Your Visa Procedure
                                                </h4>

                                                <p class="mt-1 text-sm leading-6 text-blue-800">
                                                    Open your visa case to review its current status,
                                                    complete the required information and continue the
                                                    document preparation process.
                                                </p>
                                            </div>

                                            <a
                                                href="{{ route('student.visa.show', $visaApplication) }}"
                                                class="inline-flex shrink-0 items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-indigo-700"
                                            >
                                                View Visa Procedure
                                            </a>

                                        </div>

                                    </div>

                                @elseif ($visaApplicationAllowed)

                                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">

                                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                            <div>
                                                <h4 class="font-extrabold text-emerald-950">
                                                    Visa Procedure Available
                                                </h4>

                                                <p class="mt-1 text-sm leading-6 text-emerald-800">
                                                    Your application payment has been verified and your
                                                    application has reached the required stage.
                                                    You can now start your visa procedure.
                                                </p>
                                            </div>

                                            <a
                                                href="{{ route('student.visa.create', $application) }}"
                                                class="inline-flex shrink-0 items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-indigo-700"
                                            >
                                                Start Visa Procedure
                                            </a>

                                        </div>

                                    </div>

                                @elseif (!$visaPaymentVerified)

                                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">

                                        <div class="flex items-start gap-3">

                                            <div class="text-xl">
                                                🔒
                                            </div>

                                            <div>
                                                <h4 class="font-extrabold text-amber-950">
                                                    Visa Procedure Locked
                                                </h4>

                                                <p class="mt-1 text-sm leading-6 text-amber-800">
                                                    Your application fee must be verified by UniBridge
                                                    administration before the visa procedure can begin.
                                                </p>

                                                @if (!$latestPayment)
                                                    <p class="mt-2 text-xs font-semibold text-amber-700">
                                                        No payment has been submitted yet.
                                                    </p>
                                                @elseif ($latestPayment->status === 'pending')
                                                    <p class="mt-2 text-xs font-semibold text-amber-700">
                                                        Your payment is currently waiting for administrator verification.
                                                    </p>
                                                @elseif ($latestPayment->status === 'rejected')
                                                    <p class="mt-2 text-xs font-semibold text-amber-700">
                                                        Please submit a new application fee payment after reviewing
                                                        the administrator's rejection note.
                                                    </p>
                                                @endif
                                            </div>

                                        </div>

                                    </div>

                                @else

                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                                        <div class="flex items-start gap-3">

                                            <div class="text-xl">
                                                ⏳
                                            </div>

                                            <div>
                                                <h4 class="font-extrabold text-slate-950">
                                                    Visa Procedure Not Available Yet
                                                </h4>

                                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                                    Your payment has been verified. Your university
                                                    application must reach the required review stage
                                                    before the visa procedure can be started.
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                    {{-- Personal Statement --}}
                    <div class="mt-10">

                        <h3 class="text-lg font-bold text-gray-900">
                            Personal Statement
                        </h3>

                        <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-5">

                            @if ($application->personal_statement)
                                <p class="whitespace-pre-line text-sm leading-7 text-gray-700">
                                    {{ $application->personal_statement }}
                                </p>
                            @else
                                <p class="text-sm italic text-gray-500">
                                    No personal statement provided.
                                </p>
                            @endif

                        </div>

                    </div>

                    {{-- Additional Notes --}}
                    <div class="mt-8">

                        <h3 class="text-lg font-bold text-gray-900">
                            Additional Notes
                        </h3>

                        <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-5">

                            @if ($application->notes)
                                <p class="whitespace-pre-line text-sm leading-7 text-gray-700">
                                    {{ $application->notes }}
                                </p>
                            @else
                                <p class="text-sm italic text-gray-500">
                                    No additional notes provided.
                                </p>
                            @endif

                        </div>

                    </div>

                    {{-- Application Progress --}}
                    <div class="mt-10">

                        <h3 class="text-lg font-bold text-gray-900">
                            Application Progress
                        </h3>

                        <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-5">

                            <div class="grid gap-4 sm:grid-cols-4">

                                {{-- Submitted --}}
                                <div class="rounded-xl border border-gray-200 p-4">

                                    <div class="flex items-center gap-2">

                                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                                            1
                                        </span>

                                        <span class="text-sm font-bold text-gray-900">
                                            Submitted
                                        </span>

                                    </div>

                                    <p class="mt-2 text-xs text-gray-500">
                                        Application received by UniBridge.
                                    </p>

                                </div>

                                {{-- Payment --}}
                                <div class="rounded-xl border border-gray-200 p-4">

                                    <div class="flex items-center gap-2">

                                        @if ($latestPayment && $latestPayment->status === 'verified')

                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">
                                                ✓
                                            </span>

                                        @else

                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600">
                                                2
                                            </span>

                                        @endif

                                        <span class="text-sm font-bold text-gray-900">
                                            Payment
                                        </span>

                                    </div>

                                    <p class="mt-2 text-xs text-gray-500">

                                        @if ($latestPayment && $latestPayment->status === 'verified')

                                            Payment verified.

                                        @elseif ($latestPayment && $latestPayment->status === 'pending')

                                            Waiting for verification.

                                        @else

                                            Payment required.

                                        @endif

                                    </p>

                                </div>

                                {{-- Review --}}
                                <div class="rounded-xl border border-gray-200 p-4">

                                    <div class="flex items-center gap-2">

                                        @if ($application->status === 'under_review' || $application->status === 'accepted')

                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                                                ✓
                                            </span>

                                        @else

                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600">
                                                3
                                            </span>

                                        @endif

                                        <span class="text-sm font-bold text-gray-900">
                                            Review
                                        </span>

                                    </div>

                                    <p class="mt-2 text-xs text-gray-500">
                                        Application review and processing stage.
                                    </p>

                                </div>

                                {{-- Decision --}}
                                <div class="rounded-xl border border-gray-200 p-4">

                                    <div class="flex items-center gap-2">

                                        @if ($application->status === 'accepted')

                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">
                                                ✓
                                            </span>

                                        @elseif ($application->status === 'rejected')

                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-xs font-bold text-red-700">
                                                !
                                            </span>

                                        @else

                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600">
                                                4
                                            </span>

                                        @endif

                                        <span class="text-sm font-bold text-gray-900">
                                            Decision
                                        </span>

                                    </div>

                                    <p class="mt-2 text-xs text-gray-500">
                                        Current status:
                                        {{ $applicationStatus }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- Withdraw Application --}}
                    @if (!in_array($application->status, ['accepted', 'rejected', 'withdrawn']))

                        <div class="mt-10 border-t border-gray-200 pt-6">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                <div>
                                    <h3 class="font-bold text-gray-900">
                                        Application Actions
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-600">
                                        You may withdraw this application while it is still active.
                                    </p>
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('student.applications.update', $application) }}"
                                    onsubmit="return confirm('Are you sure you want to withdraw this application?');"
                                >

                                    @csrf
                                    @method('PUT')

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="withdrawn"
                                    >

                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50"
                                    >
                                        Withdraw Application
                                    </button>

                                </form>

                            </div>

                        </div>

                    @endif

                    {{-- Bottom Navigation --}}
                    <div class="mt-10 flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-between">

                        <a
                            href="{{ route('student.applications.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >
                            ← Back to Applications
                        </a>

                        @if (!$latestPayment || $latestPayment->status === 'rejected')

                            <a
                                href="{{ route('student.applications.payment', $application) }}"
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-indigo-700"
                            >
                                {{ $latestPayment && $latestPayment->status === 'rejected'
                                    ? 'Submit New Payment'
                                    : 'Pay Application Fee' }}
                            </a>

                        @elseif ($latestPayment->status === 'verified')

                            @if ($visaApplication)

                                <a
                                    href="{{ route('student.visa.show', $visaApplication) }}"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-indigo-700"
                                >
                                    View Visa Procedure
                                </a>

                            @elseif ($visaApplicationAllowed)

                                <a
                                    href="{{ route('student.visa.create', $application) }}"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-indigo-700"
                                >
                                    Start Visa Procedure
                                </a>

                            @else

                                <span class="inline-flex items-center justify-center rounded-lg bg-emerald-50 px-5 py-2.5 text-sm font-bold text-emerald-700">
                                    ✓ Payment Verified
                                </span>

                            @endif

                        @else

                            <span class="inline-flex items-center justify-center rounded-lg bg-amber-50 px-5 py-2.5 text-sm font-bold text-amber-700">
                                ⏳ Payment Under Verification
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>