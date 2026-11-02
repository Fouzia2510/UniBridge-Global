<x-app-layout>

    <x-slot name="header">
        <div>
            <p class="text-sm font-bold text-red-700">
                UniBridge
            </p>

            <h2 class="text-2xl font-extrabold text-slate-950">
                Visa Application Details
            </h2>

            <p class="mt-1 text-sm font-medium text-slate-600">
                Review and update this student's visa procedure.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-bold">
                        Please correct the following:
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <a
                    href="{{ route('admin.visa.index') }}"
                    class="inline-flex items-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                >
                    ? Back to Visa Applications
                </a>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">

                <div class="space-y-6 lg:col-span-2">

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Visa Procedure
                                </p>

                                <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                                    Visa Case #{{ $visaApplication->id }}
                                </h3>
                            </div>

                            <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-bold capitalize text-slate-700">
                                {{ str_replace('_', ' ', $visaApplication->status) }}
                            </span>

                        </div>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Destination Country
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $visaApplication->country }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Visa Type
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $visaApplication->visa_type }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Created
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $visaApplication->created_at?->format('d M Y, h:i A') ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Submitted
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $visaApplication->submitted_at?->format('d M Y, h:i A') ?? 'Not submitted' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Decision
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $visaApplication->decision_at?->format('d M Y, h:i A') ?? 'No decision yet' }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-extrabold text-slate-950">
                            Student Information
                        </h3>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Student
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $visaApplication->student?->name ?? 'Student not available' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Email
                                </p>

                                <p class="mt-1 break-all text-sm font-semibold text-slate-800">
                                    {{ $visaApplication->student?->email ?? 'Email not available' }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-extrabold text-slate-950">
                            University Application
                        </h3>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    University
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $visaApplication->application?->university?->name ?? 'University not available' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Program
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $visaApplication->application?->program ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Intake
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $visaApplication->application?->intake ?? 'Not specified' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Application Status
                                </p>

                                <p class="mt-1 text-sm font-semibold capitalize text-slate-800">
                                    {{ str_replace('_', ' ', $visaApplication->application?->status ?? '—') }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-extrabold text-slate-950">
                            Student Notes
                        </h3>

                        <div class="mt-4 rounded-xl bg-slate-50 p-4">
                            <p class="whitespace-pre-line text-sm leading-6 text-slate-700">
                                {{ $visaApplication->student_notes ?: 'No additional notes were provided.' }}
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-extrabold text-slate-950">
                            Payment Status
                        </h3>

                        @php
                            $verifiedPayment = $visaApplication->application?->payments
                                ?->firstWhere('status', 'verified');
                        @endphp

                        @if ($verifiedPayment)
                            <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4">
                                <p class="text-sm font-bold text-green-800">
                                    Application payment verified
                                </p>

                                <p class="mt-1 text-sm text-green-700">
                                    {{ $verifiedPayment->currency ?? 'PKR' }}
                                    {{ number_format((float) $verifiedPayment->amount, 2) }}
                                </p>
                            </div>
                        @else
                            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <p class="text-sm font-bold text-amber-800">
                                    No verified payment found
                                </p>

                                <p class="mt-1 text-sm text-amber-700">
                                    The application payment has not been verified yet.
                                </p>
                            </div>
                        @endif
                    </div>

                </div>

                <div class="lg:col-span-1">

                    <div class="sticky top-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <h3 class="text-lg font-extrabold text-slate-950">
                            Admin Update
                        </h3>

                        <p class="mt-1 text-sm text-slate-600">
                            Update the current visa procedure status and add internal notes.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('admin.visa.submit', $visaApplication) }}"
                            class="mt-6 space-y-5"
                        >
                            @csrf

                            <div>
                                <label
                                    for="status"
                                    class="block text-sm font-bold text-slate-800"
                                >
                                    Visa Status
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-red-600 focus:ring-red-600"
                                >
                                    @foreach ([
                                        'not_started' => 'Not Started',
                                        'documents_required' => 'Documents Required',
                                        'documents_submitted' => 'Documents Submitted',
                                        'under_review' => 'Under Review',
                                        'ready_to_apply' => 'Ready to Apply',
                                        'submitted' => 'Submitted',
                                        'processing' => 'Processing',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                    ] as $value => $label)
                                        <option
                                            value="{{ $value }}"
                                            @selected($visaApplication->status === $value)
                                        >
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('status')
                                    <p class="mt-1 text-sm font-medium text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="admin_notes"
                                    class="block text-sm font-bold text-slate-800"
                                >
                                    Admin Notes
                                </label>

                                <textarea
                                    id="admin_notes"
                                    name="admin_notes"
                                    rows="7"
                                    maxlength="5000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-red-600 focus:ring-red-600"
                                    placeholder="Add internal notes about this visa procedure..."
                                >{{ old('admin_notes', $visaApplication->admin_notes) }}</textarea>

                                @error('admin_notes')
                                    <p class="mt-1 text-sm font-medium text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-red-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-red-800"
                            >
                                Update Visa Procedure
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>
