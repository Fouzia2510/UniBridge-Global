<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-indigo-600">
                    Admin Control Center
                </p>

                <h2 class="text-2xl font-bold text-slate-900">
                    Application Details
                </h2>

                <p class="text-sm text-slate-500">
                    Review application information, payment status, consultant assignment and application progress.
                </p>
            </div>

            <a
                href="{{ route('admin.applications') }}"
                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50"
            >
                ← Back to Applications
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation / Error Messages --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                    <p class="font-semibold text-red-800">
                        Please fix the following:
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $status = $application->status ?? 'submitted';

                $statusClasses = match ($status) {
                    'submitted' => 'bg-blue-100 text-blue-800',
                    'under_review' => 'bg-yellow-100 text-yellow-800',
                    'accepted' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'withdrawn' => 'bg-slate-100 text-slate-700',
                    default => 'bg-slate-100 text-slate-700',
                };

                $latestPayment = $application->payments
                    ->sortByDesc('created_at')
                    ->first();

                $paymentStatus = $latestPayment?->status ?? 'not_submitted';

                $paymentStatusClasses = match ($paymentStatus) {
                    'verified' => 'bg-green-100 text-green-800',
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    default => 'bg-slate-100 text-slate-700',
                };
            @endphp

            {{-- Main Application Card --}}
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">

                {{-- Header --}}
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <h3 class="text-xl font-bold text-slate-900">
                                Application #{{ $application->id }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Submitted by {{ $application->student?->name ?? 'Unknown Student' }}
                            </p>
                        </div>

                        <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold {{ $statusClasses }}">
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </span>

                    </div>
                </div>

                {{-- Application Information --}}
                <div class="border-b border-slate-200 px-6 py-6">

                    <h4 class="text-lg font-bold text-slate-900">
                        Application Information
                    </h4>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Student
                            </p>

                            <p class="mt-1 font-semibold text-slate-900">
                                {{ $application->student?->name ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Email
                            </p>

                            <p class="mt-1 text-slate-700">
                                {{ $application->student?->email ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                University
                            </p>

                            <p class="mt-1 font-semibold text-slate-900">
                                {{ $application->university?->name ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Location
                            </p>

                            <p class="mt-1 text-slate-700">
                                {{ $application->university?->city ?? '—' }}
                                @if ($application->university?->country)
                                    , {{ $application->university->country }}
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Program
                            </p>

                            <p class="mt-1 text-slate-700">
                                {{ $application->program ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Intake
                            </p>

                            <p class="mt-1 text-slate-700">
                                {{ $application->intake ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Application Fee
                            </p>

                            <p class="mt-1 font-semibold text-slate-900">
                                {{ $application->fee !== null ? number_format((float) $application->fee, 2) : '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Applied On
                            </p>

                            <p class="mt-1 text-slate-700">
                                {{ $application->created_at?->format('d M Y, h:i A') ?? '—' }}
                            </p>
                        </div>

                    </div>
                </div>

                {{-- Consultant Assignment --}}
                <div class="border-b border-slate-200 bg-indigo-50/40 px-6 py-6">

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">

                        <div>
                            <h4 class="text-lg font-bold text-slate-900">
                                Consultant Assignment
                            </h4>

                            <p class="mt-1 text-sm text-slate-500">
                                Assign an approved consultant to handle this student's application.
                            </p>
                        </div>

                        @if ($application->consultant)
                            <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-800">
                                Consultant Assigned
                            </span>
                        @else
                            <span class="inline-flex w-fit rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-800">
                                Not Assigned
                            </span>
                        @endif

                    </div>

                    @if ($application->consultant)

                        <div class="mt-5 rounded-lg border border-green-200 bg-white p-5">

                            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Assigned Consultant
                                    </p>

                                    <p class="mt-1 font-bold text-slate-900">
                                        {{ $application->consultant->name }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Consultant Email
                                    </p>

                                    <p class="mt-1 text-slate-700">
                                        {{ $application->consultant->email }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Assigned On
                                    </p>

                                    <p class="mt-1 text-slate-700">
                                        {{ $application->assigned_at?->format('d M Y, h:i A') ?? '—' }}
                                    </p>
                                </div>

                            </div>

                            @if ($application->assignedBy)
                                <div class="mt-4 border-t border-slate-100 pt-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Assigned By
                                    </p>

                                    <p class="mt-1 text-sm text-slate-700">
                                        {{ $application->assignedBy->name }}
                                    </p>
                                </div>
                            @endif

                        </div>

                    @endif

                    <form
                        action="{{ route('admin.applications.consultant.assign', $application) }}"
                        method="POST"
                        class="mt-5"
                    >
                        @csrf
                        @method('PATCH')

                        <label
                            for="consultant_id"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            {{ $application->consultant ? 'Change Consultant' : 'Select Consultant' }}
                        </label>

                        <div class="mt-2 flex flex-col gap-3 sm:flex-row">

                            <select
                                id="consultant_id"
                                name="consultant_id"
                                required
                                class="w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:max-w-xl"
                            >
                                <option value="">
                                    Select an approved consultant
                                </option>

                                @forelse ($consultants as $consultant)
                                    <option
                                        value="{{ $consultant->id }}"
                                        @selected($application->consultant_id == $consultant->id)
                                    >
                                        {{ $consultant->name }} — {{ $consultant->email }}
                                    </option>
                                @empty
                                    <option value="" disabled>
                                        No approved consultants available
                                    </option>
                                @endforelse
                            </select>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                {{ $application->consultant ? 'Reassign Consultant' : 'Assign Consultant' }}
                            </button>

                        </div>

                        <p class="mt-2 text-xs text-slate-500">
                            Only approved consultant accounts are available for assignment.
                        </p>

                    </form>

                </div>

                {{-- Payment Verification --}}
                <div class="border-b border-slate-200 px-6 py-6">

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <h4 class="text-lg font-bold text-slate-900">
                                Payment Verification
                            </h4>

                            <p class="mt-1 text-sm text-slate-500">
                                Review the latest payment submitted for this application.
                            </p>
                        </div>

                        <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold {{ $paymentStatusClasses }}">
                            {{ ucwords(str_replace('_', ' ', $paymentStatus)) }}
                        </span>

                    </div>

                    @if ($latestPayment)

                        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Amount
                                </p>

                                <p class="mt-1 font-bold text-slate-900">
                                    {{ $latestPayment->amount ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Payment Method
                                </p>

                                <p class="mt-1 text-slate-700">
                                    {{ $latestPayment->payment_method ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Submitted On
                                </p>

                                <p class="mt-1 text-slate-700">
                                    {{ $latestPayment->created_at?->format('d M Y, h:i A') ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Status
                                </p>

                                <p class="mt-1 font-semibold text-slate-900">
                                    {{ ucwords(str_replace('_', ' ', $latestPayment->status ?? 'unknown')) }}
                                </p>
                            </div>

                        </div>

                        @if (!empty($latestPayment->proof_path))
                            <div class="mt-5">
                                <p class="text-sm font-semibold text-slate-700">
                                    Payment Proof
                                </p>

                                <a
                                    href="{{ asset('storage/' . $latestPayment->proof_path) }}"
                                    target="_blank"
                                    class="mt-2 inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-indigo-600 hover:bg-slate-50"
                                >
                                    View Payment Proof →
                                </a>
                            </div>
                        @endif

                        @if ($latestPayment->status === 'pending')

                            <div class="mt-6 flex flex-col gap-3 sm:flex-row">

                                <form
                                    action="{{ route('admin.applications.payment.verify', $application) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-green-700 sm:w-auto"
                                    >
                                        ✓ Verify Payment
                                    </button>
                                </form>

                                <a
                                    href="{{ route('admin.applications.payment', $application) }}"
                                    class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-5 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50"
                                >
                                    Reject / Review Payment
                                </a>

                            </div>

                        @endif

                    @else

                        <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 px-4 py-5 text-sm text-slate-600">
                            No payment has been submitted for this application yet.
                        </div>

                    @endif

                </div>

                {{-- Personal Statement --}}
                <div class="border-b border-slate-200 px-6 py-6">

                    <h4 class="text-lg font-bold text-slate-900">
                        Personal Statement
                    </h4>

                    <div class="mt-4 rounded-lg bg-slate-50 p-5 text-sm leading-7 text-slate-700">
                        @if ($application->personal_statement)
                            {!! nl2br(e($application->personal_statement)) !!}
                        @else
                            <span class="text-slate-400">
                                No personal statement provided.
                            </span>
                        @endif
                    </div>

                </div>

                {{-- Additional Notes --}}
                <div class="border-b border-slate-200 px-6 py-6">

                    <h4 class="text-lg font-bold text-slate-900">
                        Additional Notes
                    </h4>

                    <div class="mt-4 rounded-lg bg-slate-50 p-5 text-sm leading-7 text-slate-700">
                        @if ($application->notes)
                            {!! nl2br(e($application->notes)) !!}
                        @else
                            <span class="text-slate-400">
                                No additional notes provided.
                            </span>
                        @endif
                    </div>

                </div>

                {{-- Update Application Status --}}
                <div class="px-6 py-6">

                    <h4 class="text-lg font-bold text-slate-900">
                        Update Application Status
                    </h4>

                    <p class="mt-1 text-sm text-slate-500">
                        Change the current processing status of this application.
                    </p>

                    <form
                        action="{{ route('admin.applications.status', $application) }}"
                        method="POST"
                        class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-end"
                    >
                        @csrf
                        @method('PATCH')

                        <div class="w-full sm:max-w-sm">

                            <label
                                for="status"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Application Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="mt-2 w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="submitted" @selected($status === 'submitted')>
                                    Submitted
                                </option>

                                <option value="under_review" @selected($status === 'under_review')>
                                    Under Review
                                </option>

                                <option value="accepted" @selected($status === 'accepted')>
                                    Accepted
                                </option>

                                <option value="rejected" @selected($status === 'rejected')>
                                    Rejected
                                </option>

                                <option value="withdrawn" @selected($status === 'withdrawn')>
                                    Withdrawn
                                </option>
                            </select>

                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-slate-800"
                        >
                            Update Status
                        </button>

                    </form>

                    <div class="mt-5">
                        <a
                            href="{{ route('admin.applications.payment', $application) }}"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                        >
                            View Full Payment Details →
                        </a>
                    </div>

                </div>

            </div>

            {{-- Back Button --}}
            <div>
                <a
                    href="{{ route('admin.applications') }}"
                    class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    ← Back to Applications
                </a>
            </div>

        </div>
    </div>

</x-app-layout>