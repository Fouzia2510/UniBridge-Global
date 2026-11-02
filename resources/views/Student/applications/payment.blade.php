<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Application Payment
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Complete your application payment and submit the transaction details for verification.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
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

            {{-- Application Summary --}}
            <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-5">
                    <h3 class="text-lg font-bold text-white">
                        Application Payment
                    </h3>

                    <p class="mt-1 text-sm text-indigo-100">
                        Application #{{ $application->id }}
                    </p>
                </div>

                <div class="grid gap-5 p-6 sm:grid-cols-2">

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                            University
                        </p>

                        <p class="mt-1 font-semibold text-gray-900">
                            {{ $application->university->name ?? 'University' }}
                        </p>
                    </div>

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

                        <p class="mt-1 text-xl font-bold text-indigo-600">
                            PKR {{ number_format($application->fee ?? 3000, 2) }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- Existing Payment --}}
            @if ($payment)
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">

                    <div class="flex items-start gap-3">

                        <div class="mt-0.5 text-xl">
                            💳
                        </div>

                        <div>
                            <h3 class="font-bold text-amber-900">
                                Previous Payment Submission
                            </h3>

                            <p class="mt-1 text-sm text-amber-800">
                                Payment method:
                                <strong>{{ $payment->methodLabel() }}</strong>
                            </p>

                            <p class="mt-1 text-sm text-amber-800">
                                Transaction reference:
                                <strong>{{ $payment->transaction_reference }}</strong>
                            </p>

                            <p class="mt-1 text-sm text-amber-800">
                                Status:
                                <strong>{{ $payment->statusLabel() }}</strong>
                            </p>
                        </div>

                    </div>

                </div>
            @endif

            {{-- Payment Form --}}
            <form
                method="POST"
                action="{{ route('student.applications.payment.store', $application) }}"
                enctype="multipart/form-data"
                class="space-y-6"
            >

                @csrf

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-bold text-gray-900">
                            Payment Details
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Select the payment method you used to pay the application fee.
                        </p>
                    </div>

                    <div class="space-y-6 p-6">

                        {{-- Payment Method --}}
                        <div>
                            <label
                                for="payment_method"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Payment Method
                            </label>

                            <select
                                name="payment_method"
                                id="payment_method"
                                required
                                class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    Select payment method
                                </option>

                                <option value="jazzcash"
                                    {{ old('payment_method') === 'jazzcash' ? 'selected' : '' }}>
                                    JazzCash
                                </option>

                                <option value="easypaisa"
                                    {{ old('payment_method') === 'easypaisa' ? 'selected' : '' }}>
                                    Easypaisa
                                </option>

                                <option value="nayapay"
                                    {{ old('payment_method') === 'nayapay' ? 'selected' : '' }}>
                                    NayaPay
                                </option>

                                <option value="raast"
                                    {{ old('payment_method') === 'raast' ? 'selected' : '' }}>
                                    Raast
                                </option>

                                <option value="bank_transfer"
                                    {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>
                                    Bank Transfer
                                </option>

                                <option value="western_union"
                                    {{ old('payment_method') === 'western_union' ? 'selected' : '' }}>
                                    Western Union
                                </option>
                            </select>
                        </div>

                        {{-- Bank --}}
                        <div id="bank-section" class="hidden">
                            <label
                                for="bank_name"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Bank
                            </label>

                            <select
                                name="bank_name"
                                id="bank_name"
                                class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    Select bank
                                </option>

                                <option value="Bank of Punjab"
                                    {{ old('bank_name') === 'Bank of Punjab' ? 'selected' : '' }}>
                                    Bank of Punjab
                                </option>

                                <option value="Soneri Bank"
                                    {{ old('bank_name') === 'Soneri Bank' ? 'selected' : '' }}>
                                    Soneri Bank
                                </option>

                                <option value="Other Bank"
                                    {{ old('bank_name') === 'Other Bank' ? 'selected' : '' }}>
                                    Other Bank
                                </option>
                            </select>
                        </div>

                        {{-- Transaction Reference --}}
                        <div>
                            <label
                                for="transaction_reference"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Transaction Reference / ID
                            </label>

                            <input
                                type="text"
                                name="transaction_reference"
                                id="transaction_reference"
                                value="{{ old('transaction_reference') }}"
                                required
                                placeholder="Enter transaction ID/reference"
                                class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            <p class="mt-1 text-xs text-gray-500">
                                Enter the transaction/reference number shown by your payment service.
                            </p>
                        </div>

                        {{-- Payer Reference --}}
                        <div>
                            <label
                                for="payer_reference"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Payer Account / Phone Reference
                                <span class="font-normal text-gray-400">(Optional)</span>
                            </label>

                            <input
                                type="text"
                                name="payer_reference"
                                id="payer_reference"
                                value="{{ old('payer_reference') }}"
                                placeholder="e.g. payer account or registered phone reference"
                                class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        {{-- Proof --}}
                        <div>
                            <label
                                for="proof"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Payment Proof
                                <span class="font-normal text-gray-400">
                                    (Screenshot / receipt)
                                </span>
                            </label>

                            <input
                                type="file"
                                name="proof"
                                id="proof"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="mt-2 block w-full rounded-xl border border-gray-300 bg-white p-2 text-sm text-gray-700"
                            >

                            <p class="mt-1 text-xs text-gray-500">
                                Accepted formats: JPG, JPEG, PNG or PDF. Maximum size: 5 MB.
                            </p>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label
                                for="admin_notes"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Additional Details
                                <span class="font-normal text-gray-400">(Optional)</span>
                            </label>

                            <textarea
                                name="admin_notes"
                                id="admin_notes"
                                rows="4"
                                maxlength="2000"
                                placeholder="Add any additional information for the admin..."
                                class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('admin_notes') }}</textarea>
                        </div>

                    </div>
                </div>

                {{-- Important Notice --}}
                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">

                    <h4 class="font-bold text-blue-900">
                        Payment Verification
                    </h4>

                    <p class="mt-1 text-sm leading-6 text-blue-800">
                        Your payment will remain pending until an administrator verifies
                        the submitted transaction details and payment proof.
                        The application can proceed after successful verification.
                    </p>

                </div>

                {{-- Buttons --}}
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('student.applications.show', $application) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        Submit Payment for Verification
                    </button>

                </div>

            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const paymentMethod = document.getElementById('payment_method');
            const bankSection = document.getElementById('bank-section');
            const bankName = document.getElementById('bank_name');

            function toggleBankSection() {
                if (paymentMethod.value === 'bank_transfer') {
                    bankSection.classList.remove('hidden');
                    bankName.required = true;
                } else {
                    bankSection.classList.add('hidden');
                    bankName.required = false;
                    bankName.value = '';
                }
            }

            paymentMethod.addEventListener('change', toggleBankSection);

            toggleBankSection();
        });
    </script>

</x-app-layout>