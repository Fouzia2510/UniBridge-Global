<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Consultant Dashboard
            </h2>

            <p class="text-sm text-gray-500">
                Manage your consultation bookings, students and availability.
            </p>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif


            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4">

                    <div class="text-sm font-semibold text-red-700">
                        Connection error
                    </div>

                    <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif


            {{-- Welcome --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div>

                        <p class="text-sm font-medium text-indigo-600 mb-1">
                            Welcome back
                        </p>

                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ auth()->user()->name }}
                        </h1>

                        <p class="text-gray-500 mt-1">
                            Manage your UniBridge consultant activities from here.
                        </p>

                    </div>

                    <div class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-50 text-indigo-700 text-sm font-semibold">
                        Consultant Account
                    </div>

                </div>

            </div>


            {{-- Google Integrations --}}
            @php

                /*
                 * Google Meet connection status.
                 */
                $googleMeetConnected = false;

                try {
                    $googleMeetConnected = !empty(
                        auth()->user()->google_oauth_token
                    );
                } catch (\Throwable $e) {
                    $googleMeetConnected = false;
                }


                /*
                 * Google Classroom connection status.
                 */
                $googleClassroomConnected = false;

                try {
                    $googleClassroomConnected = !empty(
                        auth()->user()->google_classroom_oauth_token
                    );
                } catch (\Throwable $e) {
                    $googleClassroomConnected = false;
                }

            @endphp


            <div class="mb-8">

                <div class="mb-4">

                    <h2 class="text-xl font-bold text-gray-900">
                        Google Integrations
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Manage your Google Meet and Google Classroom connections.
                    </p>

                </div>


                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


                    {{-- Google Meet --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5">

                            <div class="flex items-start gap-4">

                                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">

                                    <svg
                                        class="w-6 h-6 text-green-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 10l4.5-2.5v9L15 14v-4z"
                                        />

                                        <rect
                                            x="3"
                                            y="6"
                                            width="12"
                                            height="12"
                                            rx="2"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        />
                                    </svg>

                                </div>


                                <div>

                                    <h3 class="text-xl font-bold text-gray-900">
                                        Google Meet
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Connect Google Meet to automatically create meeting links for confirmed consultations.
                                    </p>


                                    @if ($googleMeetConnected)

                                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-semibold">

                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                                            Google Meet Connected

                                        </div>

                                    @else

                                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-sm font-semibold">

                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>

                                            Not Connected

                                        </div>

                                    @endif

                                </div>

                            </div>


                            <div class="flex-shrink-0">

                                @if ($googleMeetConnected)

                                    <div class="text-sm font-semibold text-emerald-600">
                                        ✓ Connection Ready
                                    </div>

                                @else

                                    <a
                                        href="{{ route('google.meet.connect') }}"
                                        class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition shadow-sm"
                                    >
                                        Connect Google Meet
                                    </a>

                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- Google Classroom --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5">

                            <div class="flex items-start gap-4">

                                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">

                                    <svg
                                        class="w-6 h-6 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 3l8 4.5v5c0 4.5-3.4 7.8-8 8.5-4.6-.7-8-4-8-8.5v-5L12 3z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4"
                                        />

                                    </svg>

                                </div>


                                <div>

                                    <h3 class="text-xl font-bold text-gray-900">
                                        Google Classroom
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Connect your Google account to access your Google Classroom courses through UniBridge.
                                    </p>


                                    @if ($googleClassroomConnected)

                                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-semibold">

                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                                            Google Classroom Connected

                                        </div>

                                    @else

                                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-sm font-semibold">

                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>

                                            Not Connected

                                        </div>

                                    @endif

                                </div>

                            </div>


                            <div class="flex-shrink-0">

                                @if ($googleClassroomConnected)

                                    <div class="text-sm font-semibold text-emerald-600">
                                        ✓ Connection Ready
                                    </div>

                                @else

                                    <a
                                        href="{{ route('google.classroom.connect') }}"
                                        class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition shadow-sm"
                                    >
                                        Connect Google Classroom
                                    </a>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">


                {{-- Availability --}}
                <a
                    href="{{ route('consultant.availability.index') }}"
                    class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition"
                >

                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center mb-4">

                        <svg
                            class="w-6 h-6 text-indigo-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>

                    </div>

                    <h3 class="text-lg font-bold text-gray-900">
                        Availability
                    </h3>

                    <p class="text-sm text-gray-500 mt-2">
                        Add and manage the dates and times when students can book consultations.
                    </p>

                    <div class="mt-4 text-sm font-semibold text-indigo-600">
                        Manage Availability →
                    </div>

                </a>


                {{-- Bookings --}}
                <a
                    href="{{ route('consultant.bookings.index') }}"
                    class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition"
                >

                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4">

                        <svg
                            class="w-6 h-6 text-emerald-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 7h6m-6 4h6"
                            />

                        </svg>

                    </div>

                    <h3 class="text-lg font-bold text-gray-900">
                        Consultation Bookings
                    </h3>

                    <p class="text-sm text-gray-500 mt-2">
                        Review student booking requests and update their consultation status.
                    </p>

                    <div class="mt-4 text-sm font-semibold text-emerald-600">
                        View Bookings →
                    </div>

                </a>


                {{-- Students --}}
                <a
                    href="{{ route('consultant.students.index') }}"
                    class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition"
                >

                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center mb-4">

                        <svg
                            class="w-6 h-6 text-purple-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                            />

                        </svg>

                    </div>

                    <h3 class="text-lg font-bold text-gray-900">
                        My Students
                    </h3>

                    <p class="text-sm text-gray-500 mt-2">
                        View students who have consultation bookings with you.
                    </p>

                    <div class="mt-4 text-sm font-semibold text-purple-600">
                        View Students →
                    </div>

                </a>

            </div>


            {{-- Getting Started --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

                <h2 class="text-xl font-bold text-gray-900">
                    Getting Started
                </h2>

                <p class="text-sm text-gray-500 mt-1 mb-6">
                    Follow these steps to start receiving student consultation requests.
                </p>


                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">


                    <div class="border border-gray-200 rounded-xl p-5">

                        <div class="text-sm font-bold text-indigo-600 mb-2">
                            STEP 01
                        </div>

                        <h3 class="font-semibold text-gray-900">
                            Set Availability
                        </h3>

                        <p class="text-sm text-gray-500 mt-2">
                            Add future dates and times when you are available for consultations.
                        </p>

                    </div>


                    <div class="border border-gray-200 rounded-xl p-5">

                        <div class="text-sm font-bold text-indigo-600 mb-2">
                            STEP 02
                        </div>

                        <h3 class="font-semibold text-gray-900">
                            Receive Requests
                        </h3>

                        <p class="text-sm text-gray-500 mt-2">
                            Students can select your available time slots and submit booking requests.
                        </p>

                    </div>


                    <div class="border border-gray-200 rounded-xl p-5">

                        <div class="text-sm font-bold text-indigo-600 mb-2">
                            STEP 03
                        </div>

                        <h3 class="font-semibold text-gray-900">
                            Confirm Bookings
                        </h3>

                        <p class="text-sm text-gray-500 mt-2">
                            Review incoming requests and confirm or update their status.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>