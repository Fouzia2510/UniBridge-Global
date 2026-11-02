<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Book a Consultant') }}
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Choose a consultant and book a consultation at a time they have made available.
            </p>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Page Introduction --}}
            <div class="mb-8">

                <div class="flex items-center gap-3 mb-2">

                    <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 012-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>

                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Book a Consultant
                        </h1>

                        <p class="text-sm text-gray-500">
                            Choose a consultant and select one of their available consultation times.
                        </p>
                    </div>

                </div>

            </div>


            {{-- Success Message --}}
            @if (session('status'))

                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4">

                    <div class="flex items-start gap-3">

                        <div class="text-green-600 text-lg">
                            ✓
                        </div>

                        <div>
                            <p class="font-semibold text-green-800">
                                Success
                            </p>

                            <p class="text-sm text-green-700 mt-1">
                                {{ session('status') }}
                            </p>
                        </div>

                    </div>

                </div>

            @endif


            {{-- Validation Errors --}}
            @if ($errors->any())

                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4">

                    <p class="font-semibold text-red-800 mb-2">
                        Please fix the following:
                    </p>

                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">

                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- Main Card --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">


                {{-- STEP 1 --}}
                <div class="p-6 sm:p-8 border-b border-gray-200">

                    <div class="flex items-start gap-4 mb-6">

                        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                            1
                        </div>

                        <div>

                            <h3 class="text-lg font-bold text-gray-900">
                                Select Consultant
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Choose the consultant you want to speak with.
                            </p>

                        </div>

                    </div>


                    <form
                        method="GET"
                        action="{{ route('student.bookings.create') }}"
                    >

                        <label
                            for="consultant_id"
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Consultant
                        </label>


                        <select
                            id="consultant_id"
                            name="consultant_id"
                            onchange="this.form.submit()"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                            <option value="">
                                Select a consultant
                            </option>


                            @forelse ($consultants as $consultant)

                                <option
                                    value="{{ $consultant->id }}"
                                    {{ (string) $selectedConsultantId === (string) $consultant->id ? 'selected' : '' }}
                                >
                                    {{ $consultant->name }}
                                </option>

                            @empty

                                <option value="" disabled>
                                    No consultants available
                                </option>

                            @endforelse

                        </select>


                        <p class="text-xs text-gray-500 mt-2">
                            Once you select a consultant, their currently available dates and times will appear below.
                        </p>

                    </form>

                </div>



                {{-- STEP 2 --}}
                @if ($selectedConsultantId)

                    <div class="p-6 sm:p-8">

                        <div class="flex items-start gap-4 mb-6">

                            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                                2
                            </div>

                            <div>

                                <h3 class="text-lg font-bold text-gray-900">
                                    Available Date & Time
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Choose any available consultation slot created by the consultant.
                                </p>

                            </div>

                        </div>


                        {{-- NO SLOTS --}}
                        @if ($availableSlots->isEmpty())

                            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-6">

                                <div class="flex items-start gap-4">

                                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                        ⚠️
                                    </div>

                                    <div>

                                        <h4 class="font-bold text-yellow-900">
                                            No Available Slots
                                        </h4>

                                        <p class="text-sm text-yellow-800 mt-1">
                                            This consultant has not currently added any available consultation time.
                                        </p>

                                        <p class="text-xs text-yellow-700 mt-2">
                                            Please choose another consultant or check again later.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        @else


                            {{-- BOOKING FORM --}}
                            <form
                                method="POST"
                                action="{{ route('student.bookings.store') }}"
                            >

                                @csrf


                                {{-- Consultant --}}
                                <input
                                    type="hidden"
                                    name="consultant_id"
                                    value="{{ $selectedConsultantId }}"
                                >


                                {{-- Available Slots --}}
                                <div class="mb-7">

                                    <label
                                        for="slot_id"
                                        class="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Available Consultation Slots
                                    </label>


                                    <select
                                        id="slot_id"
                                        name="slot_id"
                                        required
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >

                                        <option value="">
                                            Select a date and time
                                        </option>


                                        @foreach ($availableSlots as $slot)

                                            <option value="{{ $slot->id }}">

                                                {{ \Carbon\Carbon::parse($slot->date)->format('l, d M Y') }}

                                                —

                                                {{ \Carbon\Carbon::parse($slot->time)->format('h:i A') }}

                                            </option>

                                        @endforeach

                                    </select>


                                    <p class="text-xs text-gray-500 mt-2">
                                        Each consultation lasts {{ \App\Models\Booking::MEETING_DURATION_MINUTES }} minutes. Slot times use {{ config('app.timezone') }} and expire after the scheduled period.
                                    </p>

                                </div>



                                {{-- Notes --}}
                                <div class="mb-7">

                                    <label
                                        for="notes"
                                        class="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Message / Notes
                                        <span class="font-normal text-gray-400">
                                            (Optional)
                                        </span>
                                    </label>


                                    <textarea
                                        id="notes"
                                        name="notes"
                                        rows="4"
                                        maxlength="1000"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Tell the consultant what you would like help with..."
                                    >{{ old('notes') }}</textarea>


                                    <p class="text-xs text-gray-500 mt-2">
                                        Maximum 1000 characters.
                                    </p>

                                </div>



                                {{-- Booking Information --}}
                                <div class="mb-7 rounded-xl border border-indigo-100 bg-indigo-50 p-5">

                                    <div class="flex items-start gap-3">

                                        <div class="text-lg">
                                            📅
                                        </div>

                                        <div>

                                            <h4 class="font-bold text-indigo-900">
                                                Booking Information
                                            </h4>

                                            <ul class="text-sm text-indigo-800 mt-2 space-y-1">

                                                <li>
                                                    • Your booking request will initially be
                                                    <strong>Pending</strong>.
                                                </li>

                                                <li>
                                                    • The consultant will review your request.
                                                </li>

                                                <li>
                                                    • Once accepted, the booking becomes
                                                    <strong>Confirmed</strong>.
                                                </li>

                                                <li>
                                                    • You can track your consultation from
                                                    <strong>My Meetings</strong>.
                                                </li>

                                            </ul>

                                        </div>

                                    </div>

                                </div>



                                {{-- Buttons --}}
                                <div class="flex flex-col sm:flex-row gap-3">

                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                                    >

                                        <svg class="w-5 h-5 mr-2"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 4v16m8-8H4"/>

                                        </svg>

                                        Request Booking

                                    </button>


                                    <a
                                        href="{{ route('student.bookings.create') }}"
                                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
                                    >
                                        Change Consultant
                                    </a>

                                </div>

                            </form>

                        @endif

                    </div>

                @endif

            </div>



            {{-- Help Section --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">


                {{-- Contact --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-4">
                        💬
                    </div>

                    <h3 class="font-bold text-gray-900">
                        Need to Contact a Consultant?
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Choose a consultant above and use their available consultation booking option.
                    </p>

                </div>


                {{-- Virtual Assistance --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
                        🤖
                    </div>

                    <h3 class="font-bold text-gray-900">
                        Virtual Assistance
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        UniBridge assistance can help you understand universities, applications and consultation options.
                    </p>

                </div>

            </div>



            {{-- Back --}}
            <div class="mt-8">

                <a
                    href="{{ route('student.dashboard') }}"
                    class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                >
                    ← Back to Student Dashboard
                </a>

            </div>

        </div>

    </div>

</x-app-layout>