<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">
                    Consultant Portal
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Consultant Profile
                </h2>

                <p class="text-sm text-slate-500">
                    Manage your professional information and consultation expertise.
                </p>
            </div>

            <a
                href="{{ route('consultant.dashboard') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800"
            >
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    <p class="font-semibold">
                        {{ session('status') }}
                    </p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
                    <p class="mb-2 font-bold">
                        Please fix the following errors:
                    </p>

                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Profile Summary --}}
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

                    <div class="flex flex-col items-center text-center">

                        <div class="flex h-24 w-24 items-center justify-center rounded-full bg-indigo-600 text-3xl font-extrabold text-white">
                            {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                        </div>

                        <h3 class="mt-4 text-xl font-extrabold text-slate-950">
                            {{ auth()->user()->name }}
                        </h3>

                        <p class="mt-1 break-all text-sm text-slate-500">
                            {{ auth()->user()->email }}
                        </p>

                        <div class="mt-4 rounded-full bg-indigo-50 px-4 py-2 text-xs font-bold text-indigo-700">
                            Consultant
                        </div>

                        <div class="mt-6 w-full space-y-3 text-left">

                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Profile Status
                                </p>

                                <p class="mt-1 font-bold text-emerald-600">
                                    Active
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Account Status
                                </p>

                                <p class="mt-1 font-bold text-emerald-600">
                                    Approved
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Profile Form --}}
                <div class="lg:col-span-2">

                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">

                        <div class="mb-7">
                            <h3 class="text-xl font-extrabold text-slate-950">
                                Professional Information
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Add your professional information so students can understand your expertise and services.
                            </p>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('consultant.profile.update') }}"
                            class="space-y-5"
                        >

                            @csrf

                            {{-- Phone --}}
                            <div>
                                <label
                                    for="phone"
                                    class="block text-sm font-bold text-slate-700"
                                >
                                    Phone Number
                                </label>

                                <input
                                    id="phone"
                                    name="phone"
                                    type="text"
                                    value="{{ old('phone', $profile?->phone) }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. +92 300 1234567"
                                >

                                @error('phone')
                                    <p class="mt-2 text-sm font-semibold text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Qualification --}}
                            <div>
                                <label
                                    for="qualification"
                                    class="block text-sm font-bold text-slate-700"
                                >
                                    Qualification
                                </label>

                                <input
                                    id="qualification"
                                    name="qualification"
                                    type="text"
                                    value="{{ old('qualification', $profile?->qualification) }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. MBA, MS Computer Science"
                                >

                                @error('qualification')
                                    <p class="mt-2 text-sm font-semibold text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Specialization --}}
                            <div>
                                <label
                                    for="specialization"
                                    class="block text-sm font-bold text-slate-700"
                                >
                                    Specialization
                                </label>

                                <input
                                    id="specialization"
                                    name="specialization"
                                    type="text"
                                    value="{{ old('specialization', $profile?->specialization) }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. UK Admissions, Business Programs"
                                >

                                @error('specialization')
                                    <p class="mt-2 text-sm font-semibold text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Experience --}}
                            <div>
                                <label
                                    for="experience_years"
                                    class="block text-sm font-bold text-slate-700"
                                >
                                    Experience (Years)
                                </label>

                                <input
                                    id="experience_years"
                                    name="experience_years"
                                    type="number"
                                    step="0.1"
                                    min="0"
                                    max="99.9"
                                    value="{{ old('experience_years', $profile?->experience_years) }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. 5"
                                >

                                @error('experience_years')
                                    <p class="mt-2 text-sm font-semibold text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Study Countries --}}
                            <div>
                                <label
                                    for="countries"
                                    class="block text-sm font-bold text-slate-700"
                                >
                                    Study Countries
                                </label>

                                <textarea
                                    id="countries"
                                    name="countries"
                                    rows="3"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. UK, Canada, Australia, USA"
                                >{{ old('countries', $profile?->countries) }}</textarea>

                                @error('countries')
                                    <p class="mt-2 text-sm font-semibold text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Languages --}}
                            <div>
                                <label
                                    for="languages"
                                    class="block text-sm font-bold text-slate-700"
                                >
                                    Languages
                                </label>

                                <input
                                    id="languages"
                                    name="languages"
                                    type="text"
                                    value="{{ old('languages', $profile?->languages) }}"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. English, Urdu, Punjabi"
                                >

                                @error('languages')
                                    <p class="mt-2 text-sm font-semibold text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- About --}}
                            <div>
                                <label
                                    for="about"
                                    class="block text-sm font-bold text-slate-700"
                                >
                                    About You
                                </label>

                                <textarea
                                    id="about"
                                    name="about"
                                    rows="6"
                                    class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Write a short introduction about your experience and consultation expertise..."
                                >{{ old('about', $profile?->about) }}</textarea>

                                @error('about')
                                    <p class="mt-2 text-sm font-semibold text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Buttons --}}
                            <div class="flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center">

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                                >
                                    Save Profile
                                </button>

                                <a
                                    href="{{ route('consultant.dashboard') }}"
                                    class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                                >
                                    Back to Dashboard
                                </a>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</x-app-layout>