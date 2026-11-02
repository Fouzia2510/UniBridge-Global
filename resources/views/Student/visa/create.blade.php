<x-app-layout>

    <x-slot name="header">
        <div>
            <p class="text-sm font-bold text-red-700">
                UniBridge
            </p>

            <h2 class="text-2xl font-extrabold text-slate-950">
                Visa Application
            </h2>

            <p class="mt-1 text-sm font-medium text-slate-600">
                Complete your visa information carefully and accurately.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-800">
                    <p class="font-extrabold">
                        Please correct the following:
                    </p>

                    <ul class="mt-3 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Success Message --}}
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm font-semibold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Application Summary --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            University Application
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            {{ $application->university?->name ?? 'University not available' }}
                        </h3>
                    </div>

                    <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-bold capitalize text-slate-700">
                        {{ str_replace('_', ' ', $application->status) }}
                    </span>

                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-3">

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Program
                        </p>

                        <p class="mt-1 text-sm font-bold text-slate-800">
                            {{ $application->program }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Intake
                        </p>

                        <p class="mt-1 text-sm font-bold text-slate-800">
                            {{ $application->intake ?? 'Not specified' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Application ID
                        </p>

                        <p class="mt-1 text-sm font-bold text-slate-800">
                            #{{ $application->id }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- Existing Visa Case --}}
            @if ($existingVisaApplication)

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">

                    <p class="text-xs font-extrabold uppercase tracking-wide text-amber-700">
                        Existing Visa Case
                    </p>

                    <h3 class="mt-1 text-xl font-extrabold text-amber-950">
                        Visa procedure already exists
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-amber-800">
                        A visa procedure has already been created for this university
                        application. Open the existing procedure to continue.
                    </p>

                    <div class="mt-5">
                        <a
                            href="{{ route('student.visa.show', $existingVisaApplication) }}"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-slate-800"
                        >
                            Open Visa Procedure
                        </a>
                    </div>

                </div>

            @else

                <form
                    method="POST"
                    action="{{ route('student.visa.store', $application) }}"
                    enctype="multipart/form-data"
                    class="space-y-6"
                >
                    @csrf

                    {{-- ===================================================== --}}
                    {{-- 1. VISA DETAILS --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 1
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Visa & Travel Details
                        </h3>

                        <p class="mt-1 text-sm text-slate-600">
                            Provide the basic information about the visa you intend to apply for.
                        </p>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            <div>
                                <label for="country" class="block text-sm font-bold text-slate-800">
                                    Destination Country *
                                </label>

                                <input
                                    id="country"
                                    name="country"
                                    type="text"
                                    value="{{ old('country', $application->university?->country ?? '') }}"
                                    maxlength="100"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. Australia"
                                >

                                @error('country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="visa_type" class="block text-sm font-bold text-slate-800">
                                    Visa Type / Category *
                                </label>

                                <input
                                    id="visa_type"
                                    name="visa_type"
                                    type="text"
                                    value="{{ old('visa_type', 'Student Visa') }}"
                                    maxlength="150"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. Student Visa"
                                >

                                @error('visa_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="visa_purpose" class="block text-sm font-bold text-slate-800">
                                    Purpose of Travel *
                                </label>

                                <input
                                    id="visa_purpose"
                                    name="visa_purpose"
                                    type="text"
                                    value="{{ old('visa_purpose', 'Study') }}"
                                    maxlength="255"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. Full-time study"
                                >

                                @error('visa_purpose')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="intended_arrival_date" class="block text-sm font-bold text-slate-800">
                                    Intended Arrival Date
                                </label>

                                <input
                                    id="intended_arrival_date"
                                    name="intended_arrival_date"
                                    type="date"
                                    value="{{ old('intended_arrival_date') }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('intended_arrival_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="intended_departure_date" class="block text-sm font-bold text-slate-800">
                                    Intended Departure Date
                                </label>

                                <input
                                    id="intended_departure_date"
                                    name="intended_departure_date"
                                    type="date"
                                    value="{{ old('intended_departure_date') }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('intended_departure_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="intended_duration" class="block text-sm font-bold text-slate-800">
                                    Intended Duration
                                </label>

                                <input
                                    id="intended_duration"
                                    name="intended_duration"
                                    type="text"
                                    value="{{ old('intended_duration') }}"
                                    maxlength="100"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. 2 years"
                                >
                            </div>

                            <div>
                                <label for="current_immigration_status" class="block text-sm font-bold text-slate-800">
                                    Current Immigration Status
                                </label>

                                <input
                                    id="current_immigration_status"
                                    name="current_immigration_status"
                                    type="text"
                                    value="{{ old('current_immigration_status') }}"
                                    maxlength="150"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. Citizen / Permanent Resident"
                                >
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 2. PERSONAL INFORMATION --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 2
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Personal Information
                        </h3>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            <div class="md:col-span-2">
                                <label for="full_name" class="block text-sm font-bold text-slate-800">
                                    Full Legal Name *
                                </label>

                                <input
                                    id="full_name"
                                    name="full_name"
                                    type="text"
                                    value="{{ old('full_name', auth()->user()->name) }}"
                                    maxlength="255"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="Exactly as shown on passport"
                                >

                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="father_name" class="block text-sm font-bold text-slate-800">
                                    Father's / Guardian's Name
                                </label>

                                <input
                                    id="father_name"
                                    name="father_name"
                                    type="text"
                                    value="{{ old('father_name') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="mother_name" class="block text-sm font-bold text-slate-800">
                                    Mother's Name
                                </label>

                                <input
                                    id="mother_name"
                                    name="mother_name"
                                    type="text"
                                    value="{{ old('mother_name') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-sm font-bold text-slate-800">
                                    Date of Birth *
                                </label>

                                <input
                                    id="date_of_birth"
                                    name="date_of_birth"
                                    type="date"
                                    value="{{ old('date_of_birth') }}"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-bold text-slate-800">
                                    Gender *
                                </label>

                                <select
                                    id="gender"
                                    name="gender"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="">Select gender</option>
                                    <option value="male" @selected(old('gender') === 'male')>Male</option>
                                    <option value="female" @selected(old('gender') === 'female')>Female</option>
                                    <option value="other" @selected(old('gender') === 'other')>Other</option>
                                </select>

                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nationality" class="block text-sm font-bold text-slate-800">
                                    Nationality *
                                </label>

                                <input
                                    id="nationality"
                                    name="nationality"
                                    type="text"
                                    value="{{ old('nationality') }}"
                                    maxlength="100"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. Pakistani"
                                >

                                @error('nationality')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="national_id_number" class="block text-sm font-bold text-slate-800">
                                    National ID / CNIC *
                                </label>

                                <input
                                    id="national_id_number"
                                    name="national_id_number"
                                    type="text"
                                    value="{{ old('national_id_number') }}"
                                    maxlength="100"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="National identification number"
                                >

                                @error('national_id_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="country_of_birth" class="block text-sm font-bold text-slate-800">
                                    Country of Birth
                                </label>

                                <input
                                    id="country_of_birth"
                                    name="country_of_birth"
                                    type="text"
                                    value="{{ old('country_of_birth') }}"
                                    maxlength="100"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="city_of_birth" class="block text-sm font-bold text-slate-800">
                                    City of Birth
                                </label>

                                <input
                                    id="city_of_birth"
                                    name="city_of_birth"
                                    type="text"
                                    value="{{ old('city_of_birth') }}"
                                    maxlength="150"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="marital_status" class="block text-sm font-bold text-slate-800">
                                    Marital Status
                                </label>

                                <select
                                    id="marital_status"
                                    name="marital_status"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="">Select status</option>
                                    <option value="single" @selected(old('marital_status') === 'single')>Single</option>
                                    <option value="married" @selected(old('marital_status') === 'married')>Married</option>
                                    <option value="divorced" @selected(old('marital_status') === 'divorced')>Divorced</option>
                                    <option value="widowed" @selected(old('marital_status') === 'widowed')>Widowed</option>
                                </select>
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 3. CONTACT --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 3
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Contact & Address
                        </h3>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            <div>
                                <label for="phone" class="block text-sm font-bold text-slate-800">
                                    Phone Number *
                                </label>

                                <input
                                    id="phone"
                                    name="phone"
                                    type="text"
                                    value="{{ old('phone') }}"
                                    maxlength="50"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="+92..."
                                >

                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-bold text-slate-800">
                                    Email Address *
                                </label>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', auth()->user()->email) }}"
                                    maxlength="255"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="current_address" class="block text-sm font-bold text-slate-800">
                                    Current Residential Address *
                                </label>

                                <textarea
                                    id="current_address"
                                    name="current_address"
                                    rows="3"
                                    maxlength="3000"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >{{ old('current_address') }}</textarea>

                                @error('current_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-bold text-slate-800">
                                    City *
                                </label>

                                <input
                                    id="city"
                                    name="city"
                                    type="text"
                                    value="{{ old('city') }}"
                                    maxlength="150"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="state_province" class="block text-sm font-bold text-slate-800">
                                    State / Province
                                </label>

                                <input
                                    id="state_province"
                                    name="state_province"
                                    type="text"
                                    value="{{ old('state_province') }}"
                                    maxlength="150"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-bold text-slate-800">
                                    Postal Code
                                </label>

                                <input
                                    id="postal_code"
                                    name="postal_code"
                                    type="text"
                                    value="{{ old('postal_code') }}"
                                    maxlength="50"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="residence_country" class="block text-sm font-bold text-slate-800">
                                    Country of Residence *
                                </label>

                                <input
                                    id="residence_country"
                                    name="residence_country"
                                    type="text"
                                    value="{{ old('residence_country', 'Pakistan') }}"
                                    maxlength="100"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('residence_country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 4. PASSPORT --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 4
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Passport Information
                        </h3>

                        <p class="mt-1 text-sm text-slate-600">
                            Enter passport information exactly as shown on the document.
                        </p>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            <div>
                                <label for="passport_number" class="block text-sm font-bold text-slate-800">
                                    Passport Number *
                                </label>

                                <input
                                    id="passport_number"
                                    name="passport_number"
                                    type="text"
                                    value="{{ old('passport_number') }}"
                                    maxlength="100"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('passport_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="passport_type" class="block text-sm font-bold text-slate-800">
                                    Passport Type
                                </label>

                                <input
                                    id="passport_type"
                                    name="passport_type"
                                    type="text"
                                    value="{{ old('passport_type', 'Ordinary') }}"
                                    maxlength="50"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="passport_issuing_country" class="block text-sm font-bold text-slate-800">
                                    Issuing Country *
                                </label>

                                <input
                                    id="passport_issuing_country"
                                    name="passport_issuing_country"
                                    type="text"
                                    value="{{ old('passport_issuing_country', 'Pakistan') }}"
                                    maxlength="100"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('passport_issuing_country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="passport_issuing_authority" class="block text-sm font-bold text-slate-800">
                                    Issuing Authority
                                </label>

                                <input
                                    id="passport_issuing_authority"
                                    name="passport_issuing_authority"
                                    type="text"
                                    value="{{ old('passport_issuing_authority') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="passport_issue_date" class="block text-sm font-bold text-slate-800">
                                    Passport Issue Date
                                </label>

                                <input
                                    id="passport_issue_date"
                                    name="passport_issue_date"
                                    type="date"
                                    value="{{ old('passport_issue_date') }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="passport_expiry_date" class="block text-sm font-bold text-slate-800">
                                    Passport Expiry Date *
                                </label>

                                <input
                                    id="passport_expiry_date"
                                    name="passport_expiry_date"
                                    type="date"
                                    value="{{ old('passport_expiry_date') }}"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('passport_expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="has_previous_passport" class="block text-sm font-bold text-slate-800">
                                    Previous Passport?
                                </label>

                                <select
                                    id="has_previous_passport"
                                    name="has_previous_passport"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="0" @selected(old('has_previous_passport', '0') == '0')>
                                        No
                                    </option>

                                    <option value="1" @selected(old('has_previous_passport') == '1')>
                                        Yes
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="previous_passport_number" class="block text-sm font-bold text-slate-800">
                                    Previous Passport Number
                                </label>

                                <input
                                    id="previous_passport_number"
                                    name="previous_passport_number"
                                    type="text"
                                    value="{{ old('previous_passport_number') }}"
                                    maxlength="100"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="has_other_nationality" class="block text-sm font-bold text-slate-800">
                                    Other Nationality?
                                </label>

                                <select
                                    id="has_other_nationality"
                                    name="has_other_nationality"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="0" @selected(old('has_other_nationality', '0') == '0')>
                                        No
                                    </option>

                                    <option value="1" @selected(old('has_other_nationality') == '1')>
                                        Yes
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="other_nationality" class="block text-sm font-bold text-slate-800">
                                    Other Nationality
                                </label>

                                <input
                                    id="other_nationality"
                                    name="other_nationality"
                                    type="text"
                                    value="{{ old('other_nationality') }}"
                                    maxlength="100"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 5. IMMIGRATION HISTORY --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 5
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Immigration & Travel History
                        </h3>

                        <div class="mt-6 space-y-5">

                            <div class="grid gap-5 md:grid-cols-2">

                                <div>
                                    <label for="has_previous_visa" class="block text-sm font-bold text-slate-800">
                                        Previous Visa?
                                    </label>

                                    <select
                                        id="has_previous_visa"
                                        name="has_previous_visa"
                                        class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    >
                                        <option value="0" @selected(old('has_previous_visa', '0') == '0')>
                                            No
                                        </option>

                                        <option value="1" @selected(old('has_previous_visa') == '1')>
                                            Yes
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label for="has_visa_refusal" class="block text-sm font-bold text-slate-800">
                                        Previous Visa Refusal?
                                    </label>

                                    <select
                                        id="has_visa_refusal"
                                        name="has_visa_refusal"
                                        class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    >
                                        <option value="0" @selected(old('has_visa_refusal', '0') == '0')>
                                            No
                                        </option>

                                        <option value="1" @selected(old('has_visa_refusal') == '1')>
                                            Yes
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label for="has_immigration_violation" class="block text-sm font-bold text-slate-800">
                                        Previous Immigration Violation?
                                    </label>

                                    <select
                                        id="has_immigration_violation"
                                        name="has_immigration_violation"
                                        class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    >
                                        <option value="0" @selected(old('has_immigration_violation', '0') == '0')>
                                            No
                                        </option>

                                        <option value="1" @selected(old('has_immigration_violation') == '1')>
                                            Yes
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label for="has_international_travel" class="block text-sm font-bold text-slate-800">
                                        International Travel History?
                                    </label>

                                    <select
                                        id="has_international_travel"
                                        name="has_international_travel"
                                        class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    >
                                        <option value="0" @selected(old('has_international_travel', '0') == '0')>
                                            No
                                        </option>

                                        <option value="1" @selected(old('has_international_travel') == '1')>
                                            Yes
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <div>
                                <label for="previous_visa_details" class="block text-sm font-bold text-slate-800">
                                    Previous Visa Details
                                </label>

                                <textarea
                                    id="previous_visa_details"
                                    name="previous_visa_details"
                                    rows="3"
                                    maxlength="5000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="Country, visa type, dates and outcome"
                                >{{ old('previous_visa_details') }}</textarea>
                            </div>

                            <div>
                                <label for="visa_refusal_details" class="block text-sm font-bold text-slate-800">
                                    Visa Refusal Details
                                </label>

                                <textarea
                                    id="visa_refusal_details"
                                    name="visa_refusal_details"
                                    rows="3"
                                    maxlength="5000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >{{ old('visa_refusal_details') }}</textarea>
                            </div>

                            <div>
                                <label for="immigration_violation_details" class="block text-sm font-bold text-slate-800">
                                    Immigration Violation Details
                                </label>

                                <textarea
                                    id="immigration_violation_details"
                                    name="immigration_violation_details"
                                    rows="3"
                                    maxlength="5000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >{{ old('immigration_violation_details') }}</textarea>
                            </div>

                            <div>
                                <label for="international_travel_history" class="block text-sm font-bold text-slate-800">
                                    International Travel History
                                </label>

                                <textarea
                                    id="international_travel_history"
                                    name="international_travel_history"
                                    rows="3"
                                    maxlength="5000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="Countries visited, approximate dates and purpose"
                                >{{ old('international_travel_history') }}</textarea>
                            </div>

                            <div>
                                <label for="countries_previously_visited" class="block text-sm font-bold text-slate-800">
                                    Countries Previously Visited
                                </label>

                                <input
                                    id="countries_previously_visited"
                                    name="countries_previously_visited"
                                    type="text"
                                    value="{{ old('countries_previously_visited') }}"
                                    maxlength="2000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. UAE, Saudi Arabia, Turkey"
                                >
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 6. EDUCATION --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 6
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Education & English Test
                        </h3>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            <div>
                                <label for="highest_qualification" class="block text-sm font-bold text-slate-800">
                                    Highest Qualification *
                                </label>

                                <input
                                    id="highest_qualification"
                                    name="highest_qualification"
                                    type="text"
                                    value="{{ old('highest_qualification') }}"
                                    maxlength="255"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. BS Computer Science"
                                >

                                @error('highest_qualification')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="highest_qualification_institution" class="block text-sm font-bold text-slate-800">
                                    Institution
                                </label>

                                <input
                                    id="highest_qualification_institution"
                                    name="highest_qualification_institution"
                                    type="text"
                                    value="{{ old('highest_qualification_institution') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="highest_qualification_year" class="block text-sm font-bold text-slate-800">
                                    Completion Year
                                </label>

                                <input
                                    id="highest_qualification_year"
                                    name="highest_qualification_year"
                                    type="text"
                                    value="{{ old('highest_qualification_year') }}"
                                    maxlength="20"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. 2026"
                                >
                            </div>

                            <div>
                                <label for="english_test_type" class="block text-sm font-bold text-slate-800">
                                    English Test
                                </label>

                                <input
                                    id="english_test_type"
                                    name="english_test_type"
                                    type="text"
                                    value="{{ old('english_test_type') }}"
                                    maxlength="100"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="IELTS / TOEFL / PTE"
                                >
                            </div>

                            <div>
                                <label for="english_test_score" class="block text-sm font-bold text-slate-800">
                                    English Test Score
                                </label>

                                <input
                                    id="english_test_score"
                                    name="english_test_score"
                                    type="text"
                                    value="{{ old('english_test_score') }}"
                                    maxlength="100"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. IELTS 7.0"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label for="previous_education_details" class="block text-sm font-bold text-slate-800">
                                    Previous Education Details
                                </label>

                                <textarea
                                    id="previous_education_details"
                                    name="previous_education_details"
                                    rows="4"
                                    maxlength="5000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="Schools, colleges, degrees and relevant academic history"
                                >{{ old('previous_education_details') }}</textarea>
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 7. EMPLOYMENT --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 7
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Employment History
                        </h3>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            <div>
                                <label for="employment_status" class="block text-sm font-bold text-slate-800">
                                    Employment Status
                                </label>

                                <select
                                    id="employment_status"
                                    name="employment_status"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="">Select status</option>
                                    <option value="student" @selected(old('employment_status') === 'student')>
                                        Student
                                    </option>
                                    <option value="employed" @selected(old('employment_status') === 'employed')>
                                        Employed
                                    </option>
                                    <option value="self_employed" @selected(old('employment_status') === 'self_employed')>
                                        Self-employed
                                    </option>
                                    <option value="unemployed" @selected(old('employment_status') === 'unemployed')>
                                        Unemployed
                                    </option>
                                    <option value="other" @selected(old('employment_status') === 'other')>
                                        Other
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="employer_name" class="block text-sm font-bold text-slate-800">
                                    Employer / Organization
                                </label>

                                <input
                                    id="employer_name"
                                    name="employer_name"
                                    type="text"
                                    value="{{ old('employer_name') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="job_title" class="block text-sm font-bold text-slate-800">
                                    Job Title
                                </label>

                                <input
                                    id="job_title"
                                    name="job_title"
                                    type="text"
                                    value="{{ old('job_title') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="employment_start_date" class="block text-sm font-bold text-slate-800">
                                    Employment Start Date
                                </label>

                                <input
                                    id="employment_start_date"
                                    name="employment_start_date"
                                    type="date"
                                    value="{{ old('employment_start_date') }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label for="employment_history" class="block text-sm font-bold text-slate-800">
                                    Employment History
                                </label>

                                <textarea
                                    id="employment_history"
                                    name="employment_history"
                                    rows="4"
                                    maxlength="5000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="Previous employers, roles, dates and responsibilities"
                                >{{ old('employment_history') }}</textarea>
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 8. FINANCIAL --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 8
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Financial & Sponsor Information
                        </h3>

                        <p class="mt-1 text-sm text-slate-600">
                            Provide accurate information. Supporting evidence will be uploaded separately.
                        </p>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            <div>
                                <label for="funding_source" class="block text-sm font-bold text-slate-800">
                                    Main Funding Source *
                                </label>

                                <select
                                    id="funding_source"
                                    name="funding_source"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="">Select funding source</option>
                                    <option value="self" @selected(old('funding_source') === 'self')>
                                        Self-funded
                                    </option>
                                    <option value="parents" @selected(old('funding_source') === 'parents')>
                                        Parents / Family
                                    </option>
                                    <option value="sponsor" @selected(old('funding_source') === 'sponsor')>
                                        Private Sponsor
                                    </option>
                                    <option value="scholarship" @selected(old('funding_source') === 'scholarship')>
                                        Scholarship
                                    </option>
                                    <option value="government" @selected(old('funding_source') === 'government')>
                                        Government Funding
                                    </option>
                                    <option value="other" @selected(old('funding_source') === 'other')>
                                        Other
                                    </option>
                                </select>

                                @error('funding_source')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="sponsor_name" class="block text-sm font-bold text-slate-800">
                                    Sponsor Name
                                </label>

                                <input
                                    id="sponsor_name"
                                    name="sponsor_name"
                                    type="text"
                                    value="{{ old('sponsor_name') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="sponsor_relationship" class="block text-sm font-bold text-slate-800">
                                    Sponsor Relationship
                                </label>

                                <input
                                    id="sponsor_relationship"
                                    name="sponsor_relationship"
                                    type="text"
                                    value="{{ old('sponsor_relationship') }}"
                                    maxlength="150"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. Father"
                                >
                            </div>

                            <div>
                                <label for="sponsor_occupation" class="block text-sm font-bold text-slate-800">
                                    Sponsor Occupation
                                </label>

                                <input
                                    id="sponsor_occupation"
                                    name="sponsor_occupation"
                                    type="text"
                                    value="{{ old('sponsor_occupation') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="sponsor_annual_income" class="block text-sm font-bold text-slate-800">
                                    Sponsor Annual Income
                                </label>

                                <input
                                    id="sponsor_annual_income"
                                    name="sponsor_annual_income"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('sponsor_annual_income') }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="sponsor_income_currency" class="block text-sm font-bold text-slate-800">
                                    Income Currency
                                </label>

                                <input
                                    id="sponsor_income_currency"
                                    name="sponsor_income_currency"
                                    type="text"
                                    value="{{ old('sponsor_income_currency', 'PKR') }}"
                                    maxlength="10"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="PKR"
                                >
                            </div>

                            <div>
                                <label for="available_funds" class="block text-sm font-bold text-slate-800">
                                    Available Funds
                                </label>

                                <input
                                    id="available_funds"
                                    name="available_funds"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('available_funds') }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="available_funds_currency" class="block text-sm font-bold text-slate-800">
                                    Funds Currency
                                </label>

                                <input
                                    id="available_funds_currency"
                                    name="available_funds_currency"
                                    type="text"
                                    value="{{ old('available_funds_currency', 'PKR') }}"
                                    maxlength="10"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="PKR"
                                >
                            </div>

                            <div>
                                <label for="has_scholarship" class="block text-sm font-bold text-slate-800">
                                    Scholarship?
                                </label>

                                <select
                                    id="has_scholarship"
                                    name="has_scholarship"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="0" @selected(old('has_scholarship', '0') == '0')>
                                        No
                                    </option>

                                    <option value="1" @selected(old('has_scholarship') == '1')>
                                        Yes
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="scholarship_name" class="block text-sm font-bold text-slate-800">
                                    Scholarship Name
                                </label>

                                <input
                                    id="scholarship_name"
                                    name="scholarship_name"
                                    type="text"
                                    value="{{ old('scholarship_name') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="scholarship_amount" class="block text-sm font-bold text-slate-800">
                                    Scholarship Amount
                                </label>

                                <input
                                    id="scholarship_amount"
                                    name="scholarship_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('scholarship_amount') }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="scholarship_currency" class="block text-sm font-bold text-slate-800">
                                    Scholarship Currency
                                </label>

                                <input
                                    id="scholarship_currency"
                                    name="scholarship_currency"
                                    type="text"
                                    value="{{ old('scholarship_currency') }}"
                                    maxlength="10"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="AUD / GBP / CAD"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label for="financial_details" class="block text-sm font-bold text-slate-800">
                                    Additional Financial Details
                                </label>

                                <textarea
                                    id="financial_details"
                                    name="financial_details"
                                    rows="4"
                                    maxlength="5000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="Explain your funding arrangement if additional context is required."
                                >{{ old('financial_details') }}</textarea>
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 9. ACCOMMODATION --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 9
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Accommodation & Arrival
                        </h3>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            <div>
                                <label for="accommodation_type" class="block text-sm font-bold text-slate-800">
                                    Accommodation Type
                                </label>

                                <select
                                    id="accommodation_type"
                                    name="accommodation_type"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="">Select accommodation</option>
                                    <option value="university_residence" @selected(old('accommodation_type') === 'university_residence')>
                                        University Residence
                                    </option>
                                    <option value="private_rental" @selected(old('accommodation_type') === 'private_rental')>
                                        Private Rental
                                    </option>
                                    <option value="family" @selected(old('accommodation_type') === 'family')>
                                        Family / Friends
                                    </option>
                                    <option value="hotel" @selected(old('accommodation_type') === 'hotel')>
                                        Hotel / Temporary
                                    </option>
                                    <option value="other" @selected(old('accommodation_type') === 'other')>
                                        Other
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="arrival_city" class="block text-sm font-bold text-slate-800">
                                    Arrival City
                                </label>

                                <input
                                    id="arrival_city"
                                    name="arrival_city"
                                    type="text"
                                    value="{{ old('arrival_city', $application->university?->city ?? '') }}"
                                    maxlength="150"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label for="accommodation_address" class="block text-sm font-bold text-slate-800">
                                    Accommodation Address
                                </label>

                                <textarea
                                    id="accommodation_address"
                                    name="accommodation_address"
                                    rows="3"
                                    maxlength="3000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >{{ old('accommodation_address') }}</textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="airport_or_port" class="block text-sm font-bold text-slate-800">
                                    Expected Airport / Port
                                </label>

                                <input
                                    id="airport_or_port"
                                    name="airport_or_port"
                                    type="text"
                                    value="{{ old('airport_or_port') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. Melbourne Airport"
                                >
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 10. EMERGENCY CONTACT --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 10
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Emergency Contact
                        </h3>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            <div>
                                <label for="emergency_contact_name" class="block text-sm font-bold text-slate-800">
                                    Full Name *
                                </label>

                                <input
                                    id="emergency_contact_name"
                                    name="emergency_contact_name"
                                    type="text"
                                    value="{{ old('emergency_contact_name') }}"
                                    maxlength="255"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('emergency_contact_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact_relationship" class="block text-sm font-bold text-slate-800">
                                    Relationship *
                                </label>

                                <input
                                    id="emergency_contact_relationship"
                                    name="emergency_contact_relationship"
                                    type="text"
                                    value="{{ old('emergency_contact_relationship') }}"
                                    maxlength="150"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="e.g. Father"
                                >

                                @error('emergency_contact_relationship')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact_phone" class="block text-sm font-bold text-slate-800">
                                    Phone Number *
                                </label>

                                <input
                                    id="emergency_contact_phone"
                                    name="emergency_contact_phone"
                                    type="text"
                                    value="{{ old('emergency_contact_phone') }}"
                                    maxlength="50"
                                    required
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >

                                @error('emergency_contact_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact_email" class="block text-sm font-bold text-slate-800">
                                    Email
                                </label>

                                <input
                                    id="emergency_contact_email"
                                    name="emergency_contact_email"
                                    type="email"
                                    value="{{ old('emergency_contact_email') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label for="emergency_contact_address" class="block text-sm font-bold text-slate-800">
                                    Address
                                </label>

                                <textarea
                                    id="emergency_contact_address"
                                    name="emergency_contact_address"
                                    rows="3"
                                    maxlength="3000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >{{ old('emergency_contact_address') }}</textarea>
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 11. HEALTH / INSURANCE --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 11
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Health & Insurance
                        </h3>

                        <p class="mt-1 text-sm text-slate-600">
                            Requirements may vary by destination country and visa category.
                        </p>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            <div>
                                <label for="medical_exam_required" class="block text-sm font-bold text-slate-800">
                                    Medical Examination Required?
                                </label>

                                <select
                                    id="medical_exam_required"
                                    name="medical_exam_required"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="0" @selected(old('medical_exam_required', '0') == '0')>
                                        No / Not Known
                                    </option>

                                    <option value="1" @selected(old('medical_exam_required') == '1')>
                                        Yes
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="medical_exam_status" class="block text-sm font-bold text-slate-800">
                                    Medical Examination Status
                                </label>

                                <select
                                    id="medical_exam_status"
                                    name="medical_exam_status"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="">Select status</option>
                                    <option value="not_required" @selected(old('medical_exam_status') === 'not_required')>
                                        Not Required
                                    </option>
                                    <option value="pending" @selected(old('medical_exam_status') === 'pending')>
                                        Pending
                                    </option>
                                    <option value="scheduled" @selected(old('medical_exam_status') === 'scheduled')>
                                        Scheduled
                                    </option>
                                    <option value="completed" @selected(old('medical_exam_status') === 'completed')>
                                        Completed
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="has_health_insurance" class="block text-sm font-bold text-slate-800">
                                    Health Insurance?
                                </label>

                                <select
                                    id="has_health_insurance"
                                    name="has_health_insurance"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="0" @selected(old('has_health_insurance', '0') == '0')>
                                        No
                                    </option>

                                    <option value="1" @selected(old('has_health_insurance') == '1')>
                                        Yes
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="insurance_provider" class="block text-sm font-bold text-slate-800">
                                    Insurance Provider
                                </label>

                                <input
                                    id="insurance_provider"
                                    name="insurance_provider"
                                    type="text"
                                    value="{{ old('insurance_provider') }}"
                                    maxlength="255"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="insurance_policy_number" class="block text-sm font-bold text-slate-800">
                                    Insurance Policy Number
                                </label>

                                <input
                                    id="insurance_policy_number"
                                    name="insurance_policy_number"
                                    type="text"
                                    value="{{ old('insurance_policy_number') }}"
                                    maxlength="150"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="insurance_start_date" class="block text-sm font-bold text-slate-800">
                                    Insurance Start Date
                                </label>

                                <input
                                    id="insurance_start_date"
                                    name="insurance_start_date"
                                    type="date"
                                    value="{{ old('insurance_start_date') }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                            <div>
                                <label for="insurance_end_date" class="block text-sm font-bold text-slate-800">
                                    Insurance End Date
                                </label>

                                <input
                                    id="insurance_end_date"
                                    name="insurance_end_date"
                                    type="date"
                                    value="{{ old('insurance_end_date') }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                >
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 12. ADDITIONAL INFORMATION --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 12
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Additional Information
                        </h3>

                        <div class="mt-6 space-y-5">

                            <div>
                                <label for="additional_information" class="block text-sm font-bold text-slate-800">
                                    Additional Information
                                </label>

                                <textarea
                                    id="additional_information"
                                    name="additional_information"
                                    rows="5"
                                    maxlength="5000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="Provide any additional information relevant to your visa case."
                                >{{ old('additional_information') }}</textarea>
                            </div>

                            <div>
                                <label for="student_notes" class="block text-sm font-bold text-slate-800">
                                    Notes for UniBridge
                                </label>

                                <textarea
                                    id="student_notes"
                                    name="student_notes"
                                    rows="4"
                                    maxlength="5000"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm"
                                    placeholder="Anything specific you want UniBridge to know about your case?"
                                >{{ old('student_notes') }}</textarea>

                                @error('student_notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </section>

                    {{-- ===================================================== --}}
                    {{-- 13. DECLARATION --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-red-200 bg-red-50 p-6">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 13
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-red-950">
                            Applicant Declaration
                        </h3>

                        <div class="mt-4 rounded-xl border border-red-200 bg-white p-4">
                            <p class="text-sm leading-6 text-slate-700">
                                I confirm that the information I provide in this visa application
                                is accurate and complete to the best of my knowledge. I understand
                                that inaccurate, incomplete or misleading information may affect
                                my visa application and that supporting documents may be requested
                                for review.
                            </p>
                        </div>

                        <label class="mt-5 flex items-start gap-3">
                            <input
                                type="checkbox"
                                name="declaration_confirmed"
                                value="1"
                                @checked(old('declaration_confirmed'))
                                required
                                class="mt-1 rounded border-slate-300 text-red-700 focus:ring-red-600"
                            >

                            <span class="text-sm font-bold leading-6 text-slate-800">
                                I confirm that I have read and understood the declaration above.
                            </span>
                        </label>

                        @error('declaration_confirmed')
                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </section>

                    {{-- ===================================================== --}}
                    {{-- 14. UPLOAD DOCUMENTS --}}
                    {{-- ===================================================== --}}

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                            Section 14
                        </p>

                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                            Upload Supporting Documents
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Upload clear and readable copies of the documents required
                            for your visa application. These documents will be reviewed
                            by UniBridge administration.
                        </p>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">

                            {{-- Passport --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="passport_document"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    Passport Copy
                                    <span class="text-red-700">*</span>
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Upload the passport bio-data page.
                                </p>

                                <input
                                    id="passport_document"
                                    name="passport_document"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('passport_document')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- National ID --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="national_id_document"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    CNIC / National ID
                                    <span class="text-red-700">*</span>
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Upload a clear copy of your national identification document.
                                </p>

                                <input
                                    id="national_id_document"
                                    name="national_id_document"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('national_id_document')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Admission Letter --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="admission_letter"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    University Admission / Offer Letter
                                    <span class="text-red-700">*</span>
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Upload your official university offer or admission letter.
                                </p>

                                <input
                                    id="admission_letter"
                                    name="admission_letter"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('admission_letter')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- English Test --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="english_test_document"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    English Test Certificate
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    IELTS, PTE, TOEFL or another accepted English test certificate.
                                </p>

                                <input
                                    id="english_test_document"
                                    name="english_test_document"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('english_test_document')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Academic Documents --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="academic_documents"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    Academic Documents
                                    <span class="text-red-700">*</span>
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Upload degree, transcript, marksheet or other academic evidence.
                                </p>

                                <input
                                    id="academic_documents"
                                    name="academic_documents"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('academic_documents')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Financial Evidence --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="financial_documents"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    Financial / Bank Evidence
                                    <span class="text-red-700">*</span>
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Bank statement, financial certificate or other proof of available funds.
                                </p>

                                <input
                                    id="financial_documents"
                                    name="financial_documents"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('financial_documents')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Sponsor Evidence --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="sponsor_documents"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    Sponsor Documents
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Sponsor letter, relationship evidence or sponsor financial documents.
                                </p>

                                <input
                                    id="sponsor_documents"
                                    name="sponsor_documents"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('sponsor_documents')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Scholarship --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="scholarship_document"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    Scholarship Letter
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Upload scholarship confirmation if applicable.
                                </p>

                                <input
                                    id="scholarship_document"
                                    name="scholarship_document"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('scholarship_document')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Medical --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="medical_document"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    Medical Examination Document
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Upload medical examination evidence if required.
                                </p>

                                <input
                                    id="medical_document"
                                    name="medical_document"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('medical_document')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Insurance --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="insurance_document"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    Health Insurance Document
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Upload your insurance certificate or policy document if applicable.
                                </p>

                                <input
                                    id="insurance_document"
                                    name="insurance_document"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('insurance_document')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Additional --}}
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <label
                                    for="additional_document"
                                    class="block text-sm font-extrabold text-slate-900"
                                >
                                    Additional Supporting Document
                                </label>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Upload any other document relevant to your visa case.
                                </p>

                                <input
                                    id="additional_document"
                                    name="additional_document"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm"
                                >

                                @error('additional_document')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>

                        {{-- Upload Information --}}
                        <div class="mt-6 rounded-xl border border-blue-200 bg-blue-50 p-4">

                            <div class="flex gap-3">

                                <div class="mt-0.5 text-blue-700">
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-extrabold text-blue-900">
                                        Document Upload Guidelines
                                    </p>

                                    <ul class="mt-2 list-disc space-y-1 pl-5 text-xs leading-5 text-blue-800">
                                        <li>Accepted formats: JPG, JPEG, PNG and PDF.</li>
                                        <li>Upload clear and readable documents.</li>
                                        <li>Do not upload documents belonging to another person.</li>
                                        <li>Required documents are marked with a red asterisk.</li>
                                        <li>UniBridge administration will review submitted documents.</li>
                                    </ul>
                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- ===================================================== --}}
                    {{-- ACTIONS --}}
                    {{-- ===================================================== --}}

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                            <a
                                href="{{ route('student.applications.show', $application) }}"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-extrabold text-slate-700 transition hover:bg-slate-50"
                            >
                                Back to Application
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-red-700 px-6 py-3 text-sm font-extrabold text-white transition hover:bg-red-800"
                            >
                                Submit Visa Information & Documents
                            </button>

                        </div>

                    </div>

                </form>

            @endif

        </div>
    </div>

</x-app-layout>