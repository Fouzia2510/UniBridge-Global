<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                My University Applications
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Track your university applications, payment status, and application progress.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-sm font-semibold text-emerald-800">
                        {{ session('status') }}
                    </p>
                </div>
            @endif

            {{-- Errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                    <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Header Card --}}
            <div class="mb-6 flex flex-col gap-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h3 class="text-xl font-bold text-white">
                        Your Applications
                    </h3>

                    <p class="mt-1 text-sm text-indigo-100">
                        View application details and complete required steps.
                    </p>
                </div>

                <a
                    href="{{ route('student.universities.index') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-indigo-700 shadow-sm transition hover:bg-indigo-50"
                >
                    + New Application
                </a>

            </div>

            @if ($applications->count())

                <div class="space-y-5">

                    @foreach ($applications as $application)

                        @php
                            $status = $application->status;

                            $statusClasses = match ($status) {
                                'submitted' => 'bg-blue-100 text-blue-800',
                                'under_review' => 'bg-amber-100 text-amber-800',
                                'accepted' => 'bg-emerald-100 text-emerald-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'withdrawn' => 'bg-gray-100 text-gray-700',
                                default => 'bg-gray-100 text-gray-700',
                            };

                            $statusLabel = match ($status) {
                                'under_review' => 'Under Review',
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                                'withdrawn' => 'Withdrawn',
                                'submitted' => 'Submitted',
                                default => ucfirst(str_replace('_', ' ', $status)),
                            };

                            $latestPayment = $application->payments()
                                ->latest()
                                ->first();

                            $paymentStatus = $latestPayment?->status;

                            $paymentClasses = match ($paymentStatus) {
                                'verified' => 'bg-emerald-100 text-emerald-800',
                                'pending' => 'bg-amber-100 text-amber-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-700',
                            };

                            $paymentLabel = match ($paymentStatus) {
                                'verified' => 'Payment Verified',
                                'pending' => 'Payment Pending',
                                'rejected' => 'Payment Rejected',
                                default => 'Payment Required',
                            };
                        @endphp

                        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                            {{-- Application Header --}}
                            <div class="border-b border-gray-200 px-6 py-5">

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">
                                            {{ $application->university->name ?? 'University' }}
                                        </h3>

                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ $application->university->city ?? '' }}
                                            @if ($application->university->country ?? false)
                                                @if ($application->university->city ?? false), @endif
                                                {{ $application->university->country }}
                                            @endif
                                        </p>
                                    </div>

                                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold {{ $statusClasses }}">
                                        {{ $statusLabel }}
                                    </span>

                                </div>

                            </div>

                            {{-- Application Details --}}
                            <div class="grid gap-5 px-6 py-6 sm:grid-cols-2 lg:grid-cols-4">

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Program
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">
                                        {{ $application->program }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Intake
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">
                                        {{ $application->intake ?: 'Not specified' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Application Fee
                                    </p>

                                    <p class="mt-1 font-semibold text-indigo-600">
                                        PKR {{ number_format($application->fee ?? 3000, 2) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Applied
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">
                                        {{ $application->created_at->format('d M Y, h:i A') }}
                                    </p>
                                </div>

                            </div>

                            {{-- Payment Status --}}
                            <div class="mx-6 mb-5 rounded-xl border border-gray-200 bg-gray-50 p-4">

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">
                                            Application Payment
                                        </p>

                                        @if ($latestPayment)
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $latestPayment->methodLabel() }}
                                                •
                                                Transaction:
                                                {{ $latestPayment->transaction_reference }}
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-500">
                                                No payment has been submitted yet.
                                            </p>
                                        @endif
                                    </div>

                                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold {{ $paymentClasses }}">
                                        {{ $paymentLabel }}
                                    </span>

                                </div>

                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">

                                <a
                                    href="{{ route('student.applications.show', $application) }}"
                                    class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                                >
                                    View Details
                                </a>

                                @if (!$latestPayment || $latestPayment->status === 'rejected')

                                    <a
                                        href="{{ route('student.applications.payment', $application) }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                                    >
                                        Pay Application Fee
                                    </a>

                                @elseif ($latestPayment->status === 'pending')

                                    <span class="inline-flex items-center justify-center rounded-xl bg-amber-100 px-5 py-2.5 text-sm font-semibold text-amber-800">
                                        Payment Under Verification
                                    </span>

                                @elseif ($latestPayment->status === 'verified')

                                    <span class="inline-flex items-center justify-center rounded-xl bg-emerald-100 px-5 py-2.5 text-sm font-semibold text-emerald-800">
                                        ✓ Payment Verified
                                    </span>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                {{-- Empty State --}}
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center shadow-sm">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 text-2xl">
                        🎓
                    </div>

                    <h3 class="mt-4 text-lg font-bold text-gray-900">
                        No Applications Yet
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                        You have not submitted a university application yet.
                        Start your study-abroad journey by selecting a university.
                    </p>

                    <a
                        href="{{ route('student.universities.index') }}"
                        class="mt-6 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700"
                    >
                        + Start New Application
                    </a>

                </div>

            @endif

        </div>
    </div>

</x-app-layout>