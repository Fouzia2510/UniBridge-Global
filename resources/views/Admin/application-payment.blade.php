<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-700">
                    Payment Verification
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Application Payment
                </h2>

                <p class="mt-1 text-sm text-slate-600">
                    Review the student's application fee payment.
                </p>
            </div>

            <a
                href="{{ route('admin.applications.show', $application) }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
            >
                ← Back to Application
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

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

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- Application Summary --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-1">

                    <h3 class="text-lg font-extrabold text-slate-950">
                        Application Summary
                    </h3>

                    <div class="mt-5 space-y-4">

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Application
                            </p>

                            <p class="mt-1 font-bold text-slate-900">
                                #{{ $application->id }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Student
                            </p>

                            <p class="mt-1 font-bold text-slate-900">
                                {{ $application->student?->name ?? 'N/A' }}
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ $application->student?->email ?? 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                University
                            </p>

                            <p class="mt-1 font-bold text-slate-900">
                                {{ $application->university?->name ?? 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Program
                            </p>

                            <p class="mt-1 font-semibold text-slate-800">
                                {{ $application->program }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Application Fee
                            </p>

                            <p class="mt-1 text-xl font-extrabold text-indigo-700">
                                PKR {{ number_format((float) $application->fee, 2) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Application Status
                            </p>

                            <span class="mt-1 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-extrabold capitalize text-amber-800">
                                {{ str_replace('_', ' ', $application->status) }}
                            </span>
                        </div>

                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">

                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-950">
                                Payment Details
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Latest payment submission for this application.
                            </p>
                        </div>

                        @if ($payment)
                            @php
                                $paymentStatusClasses = match ($payment->status) {
                                    'verified' => 'bg-emerald-100 text-emerald-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp

                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-extrabold capitalize {{ $paymentStatusClasses }}">
                                {{ str_replace('_', ' ', $payment->status) }}
                            </span>
                        @endif
                    </div>

                    @if ($payment)

                        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Payment Method
                                </p>

                                <p class="mt-1 font-semibold capitalize text-slate-900">
                                    {{ str_replace('_', ' ', $payment->payment_method) }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Amount
                                </p>

                                <p class="mt-1 font-extrabold text-slate-900">
                                    {{ $payment->currency ?? 'PKR' }}
                                    {{ number_format((float) $payment->amount, 2) }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Transaction Reference
                                </p>

                                <p class="mt-1 break-all font-semibold text-slate-900">
                                    {{ $payment->transaction_reference }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Payer Reference
                                </p>

                                <p class="mt-1 break-all font-semibold text-slate-900">
                                    {{ $payment->payer_reference ?: 'Not provided' }}
                                </p>
                            </div>

                            @if ($payment->bank_name)
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Bank
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-900">
                                        {{ $payment->bank_name }}
                                    </p>
                                </div>
                            @endif

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Submitted
                                </p>

                                <p class="mt-1 font-semibold text-slate-900">
                                    {{ $payment->created_at?->format('d M Y, h:i A') }}
                                </p>
                            </div>

                        </div>

                        @if ($payment->admin_notes)
                            <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Admin Notes
                                </p>

                                <p class="mt-2 whitespace-pre-line text-sm text-slate-700">
                                    {{ $payment->admin_notes }}
                                </p>
                            </div>
                        @endif

                        @if ($payment->proof_path)
                            <div class="mt-6">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Payment Proof
                                </p>

                                <a
                                    href="{{ route('admin.applications.payment.proof', ['application' => $application, 'payment' => $payment]) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-2 inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-800 transition hover:border-indigo-400 hover:text-indigo-700"
                                >
                                    View Payment Proof
                                </a>
                            </div>
                        @endif

                        @if ($payment->status === 'pending')

                            <div class="mt-8 border-t border-slate-200 pt-6">

                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                    <p class="font-bold text-amber-900">
                                        Payment requires verification
                                    </p>

                                    <p class="mt-1 text-sm text-amber-800">
                                        Verify the payment only after checking the submitted payment information and proof.
                                    </p>
                                </div>

                                <div class="mt-5 flex flex-col gap-3 sm:flex-row">

                                    <form
                                        method="POST"
                                        action="{{ route('admin.applications.payment.verify', $application) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-extrabold text-white transition hover:bg-emerald-700 sm:w-auto"
                                        >
                                            Verify Payment
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.applications.payment.reject', $application) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="inline-flex w-full items-center justify-center rounded-xl bg-red-600 px-5 py-2.5 text-sm font-extrabold text-white transition hover:bg-red-700 sm:w-auto"
                                        >
                                            Reject Payment
                                        </button>
                                    </form>

                                </div>

                            </div>

                        @elseif ($payment->status === 'verified')

                            <div class="mt-8 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                                <p class="font-bold text-emerald-900">
                                    Payment Verified
                                </p>

                                <p class="mt-1 text-sm text-emerald-800">
                                    This application payment has already been verified by admin.
                                </p>
                            </div>

                        @elseif ($payment->status === 'rejected')

                            <div class="mt-8 rounded-xl border border-red-200 bg-red-50 p-4">
                                <p class="font-bold text-red-900">
                                    Payment Rejected
                                </p>

                                <p class="mt-1 text-sm text-red-800">
                                    This payment submission was rejected.
                                </p>
                            </div>

                        @endif

                    @else

                        <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-200">

                                <svg
                                    class="h-7 w-7 text-slate-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>

                            </div>

                            <h4 class="mt-4 text-lg font-extrabold text-slate-950">
                                No Payment Submitted
                            </h4>

                            <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                                The student has not submitted an application fee payment for this application yet.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>
    </div>

</x-app-layout>