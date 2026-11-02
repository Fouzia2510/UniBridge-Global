<x-app-layout>

    <x-slot name="header">

        @php
            /*
            |--------------------------------------------------------------------------
            | Google Meet Connection
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | Google Classroom Connection
            |--------------------------------------------------------------------------
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


        {{-- Header --}}
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            {{-- Title --}}
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Consultant Portal
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Manage consultation bookings and Google services.
                </p>
            </div>


            {{-- Google Connections --}}
            <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2 lg:w-auto">

                {{-- Google Meet --}}
                @if ($googleMeetConnected)

                    <div
                        class="inline-flex items-center justify-center gap-2 rounded-xl
                        border border-green-200 bg-green-50 px-5 py-3
                        text-sm font-semibold text-green-700"
                    >

                        <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>

                        Google Meet Connected

                    </div>

                @else

                    <a
                        href="{{ route('google.meet.connect') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl
                        bg-blue-600 px-5 py-3 text-sm font-semibold text-white
                        shadow-sm transition hover:bg-blue-700"
                    >

                        <span class="h-2.5 w-2.5 rounded-full bg-white"></span>

                        Connect Google Meet

                    </a>

                @endif


                {{-- Google Classroom --}}
                @if ($googleClassroomConnected)

                    <div
                        class="inline-flex items-center justify-center gap-2 rounded-xl
                        border border-green-200 bg-green-50 px-5 py-3
                        text-sm font-semibold text-green-700"
                    >

                        <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>

                        Google Classroom Connected

                    </div>

                @else

                    <a
                        href="{{ route('google.classroom.connect') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl
                        bg-indigo-600 px-5 py-3 text-sm font-semibold text-white
                        shadow-sm transition hover:bg-indigo-700"
                    >

                        <span class="h-2.5 w-2.5 rounded-full bg-white"></span>

                        Connect Google Classroom

                    </a>

                @endif

            </div>

        </div>

    </x-slot>


    {{-- Main Content --}}
    <div class="bg-slate-50 py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex justify-end">
                <a href="{{ route('meeting-attendance.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    View Meeting Join Activity
                </a>
            </div>


            {{-- Session Status --}}
            @if (session('status'))

                <div
                    class="mb-6 rounded-2xl border border-blue-200
                    bg-blue-50 px-5 py-4 text-sm font-medium text-blue-800"
                >
                    {{ session('status') }}
                </div>

            @endif


            {{-- Validation Errors --}}
            @if ($errors->any())

                <div
                    class="mb-6 rounded-2xl border border-red-200
                    bg-red-50 px-5 py-4"
                >

                    <p class="font-semibold text-red-800">
                        Please check the following:
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- Page Heading --}}
            <div class="mb-6">

                <h1 class="text-3xl font-bold text-gray-900">
                    Consultation Bookings
                </h1>

                <p class="mt-2 text-gray-600">
                    Review and manage your consultation meetings with students.
                </p>

            </div>


            {{-- Google Meet Information --}}
            <div
                class="mb-5 rounded-2xl border border-blue-100
                bg-blue-50 px-5 py-4 shadow-sm"
            >

                <p class="text-sm leading-6 text-blue-800">

                    <span class="font-semibold">
                        Google Meet:
                    </span>

                    @if ($googleMeetConnected)

                        Your Google account is connected.
                        When you confirm a student booking,
                        UniBridge can create a Google Meet link
                        for that consultation.

                    @else

                        Connect your Google account once.
                        When you confirm a student booking,
                        UniBridge will automatically create
                        a Google Meet link for that consultation.

                    @endif

                </p>

            </div>


            {{-- Google Classroom Information --}}
            <div
                class="mb-8 rounded-2xl border border-indigo-100
                bg-indigo-50 px-5 py-4 shadow-sm"
            >

                <p class="text-sm leading-6 text-indigo-800">

                    <span class="font-semibold">
                        Google Classroom:
                    </span>

                    @if ($googleClassroomConnected)

                        Your Google Classroom account is connected.
                        UniBridge can use the connected Classroom account
                        for Classroom-related consultant features.

                    @else

                        Connect your Google Classroom account
                        to enable Classroom-related features.

                    @endif

                </p>

            </div>


            {{-- Bookings List --}}
            <div class="space-y-6">

                @forelse ($bookings as $booking)


                    {{-- Booking Card --}}
                    <div
                        class="overflow-hidden rounded-2xl border
                        border-gray-200 bg-white shadow-sm"
                    >


                        {{-- Booking Header --}}
                        <div
                            class="border-b border-gray-100
                            bg-white px-6 py-5"
                        >

                            <div
                                class="flex flex-col gap-4
                                md:flex-row md:items-center
                                md:justify-between"
                            >

                                {{-- Student --}}
                                <div>

                                    <h3 class="text-lg font-bold text-gray-900">

                                        Consultation with
                                        {{ $booking->student->name ?? 'Student' }}

                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">

                                        {{ $booking->student->email ?? '' }}

                                    </p>

                                </div>


                                {{-- Status --}}
                                <div>

                                    @if ($booking->status === 'confirmed')

                                        <span
                                            class="inline-flex items-center
                                            rounded-full bg-green-100
                                            px-3 py-1 text-sm font-semibold
                                            text-green-700"
                                        >
                                            Confirmed
                                        </span>

                                    @elseif ($booking->status === 'pending')

                                        <span
                                            class="inline-flex items-center
                                            rounded-full bg-yellow-100
                                            px-3 py-1 text-sm font-semibold
                                            text-yellow-700"
                                        >
                                            Pending
                                        </span>

                                    @elseif ($booking->status === 'cancelled')

                                        <span
                                            class="inline-flex items-center
                                            rounded-full bg-red-100
                                            px-3 py-1 text-sm font-semibold
                                            text-red-700"
                                        >
                                            Cancelled
                                        </span>

                                    @elseif ($booking->status === 'completed')

                                        <span
                                            class="inline-flex items-center
                                            rounded-full bg-blue-100
                                            px-3 py-1 text-sm font-semibold
                                            text-blue-700"
                                        >
                                            Completed
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex items-center
                                            rounded-full bg-gray-100
                                            px-3 py-1 text-sm font-semibold
                                            text-gray-700"
                                        >
                                            {{ ucfirst($booking->status) }}
                                        </span>

                                    @endif

                                    @if ($booking->meetingHasEnded() && $booking->status !== 'cancelled')
                                        <span class="mt-2 inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">Time passed</span>
                                    @endif

                                </div>

                            </div>

                        </div>


                        {{-- Booking Details --}}
                        <div class="px-6 py-6">


                            {{-- Date / Time / Status --}}
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">


                                {{-- Date --}}
                                <div>

                                    <p
                                        class="text-xs font-semibold
                                        uppercase tracking-wide text-gray-400"
                                    >
                                        Date
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">

                                        @if ($booking->preferred_date)

                                            {{ \Carbon\Carbon::parse($booking->preferred_date)->format('d M Y') }}

                                        @else

                                            —

                                        @endif

                                    </p>

                                </div>


                                {{-- Time --}}
                                <div>

                                    <p
                                        class="text-xs font-semibold
                                        uppercase tracking-wide text-gray-400"
                                    >
                                        Time ({{ config('app.timezone') }})
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">

                                        @if ($booking->preferred_time)

                                            {{ \Carbon\Carbon::parse($booking->preferred_time)->format('h:i A') }}

                                        @else

                                            —

                                        @endif

                                    </p>

                                </div>


                                {{-- Booking Status --}}
                                <div>

                                    <p
                                        class="text-xs font-semibold
                                        uppercase tracking-wide text-gray-400"
                                    >
                                        Booking Status
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">

                                        {{ ucfirst($booking->status) }}

                                    </p>

                                </div>

                            </div>


                            {{-- Notes --}}
                            @if (!empty($booking->notes))

                                <div
                                    class="mt-6 rounded-xl border
                                    border-gray-100 bg-gray-50 p-4"
                                >

                                    <p
                                        class="text-xs font-semibold uppercase
                                        tracking-wide text-gray-400"
                                    >
                                        Student Notes
                                    </p>

                                    <p class="mt-2 text-sm leading-6 text-gray-700">
                                        {{ $booking->notes }}
                                    </p>

                                </div>

                            @endif


                            {{-- Google Meet --}}
                            @if ($booking->meeting_uri)

                                <div
                                    class="mt-6 rounded-2xl
                                    border border-green-200
                                    bg-green-50 p-5"
                                >

                                    <div
                                        class="flex flex-col gap-4
                                        md:flex-row md:items-center
                                        md:justify-between"
                                    >

                                        {{-- Meet Status --}}
                                        <div>

                                            <div class="flex items-center gap-2">

                                                <span
                                                    class="h-2.5 w-2.5
                                                    rounded-full bg-green-500"
                                                ></span>

                                                <p
                                                    class="font-bold
                                                    text-green-800"
                                                >
                                                    Google Meet Ready
                                                </p>

                                            </div>


                                            @if ($booking->meeting_created_at)

                                                <p
                                                    class="mt-1 text-sm
                                                    text-green-700"
                                                >

                                                    Meeting created:
                                                    {{ \Carbon\Carbon::parse($booking->meeting_created_at)->format('d M Y, h:i A') }}

                                                </p>

                                            @endif

                                        </div>


                                        {{-- Join Meet --}}
                                        @if ($booking->meetingHasEnded())
                                            <span class="rounded-xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700">Meeting time passed</span>
                                        @elseif ($booking->meetingIsJoinable())
                                            <a href="{{ route('bookings.join', $booking) }}" class="inline-flex items-center justify-center rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-green-700">Join Google Meet</a>
                                        @else
                                            <span class="rounded-xl bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-800">Opens at {{ $booking->meetingStartsAt()->format('h:i A') }}</span>
                                        @endif

                                    </div>

                                        <p class="mt-3 text-xs text-green-800">
                                            {{ $booking->joinEvents->count() }} join-link {{ \Illuminate\Support\Str::plural('open', $booking->joinEvents->count()) }} recorded. This records opening the link, not verified Google Meet attendance.
                                        </p>

                                </div>


                            @elseif ($booking->status === 'confirmed')

                                <div
                                    class="mt-6 rounded-2xl
                                    border border-yellow-200
                                    bg-yellow-50 p-5"
                                >

                                    <p class="font-semibold text-yellow-800">
                                        Google Meet is not available yet.
                                    </p>

                                    <p class="mt-1 text-sm text-yellow-700">
                                        The meeting link will appear here
                                        when it is created.
                                    </p>

                                </div>

                            @endif


                            {{-- Booking Actions --}}
                            <div class="mt-6 flex flex-wrap gap-3">


                                {{-- Confirm Booking --}}
                                @if ($booking->status === 'pending')

                                    <form
                                        method="POST"
                                        action="{{ route('consultant.bookings.status', $booking) }}"
                                    >

                                        @csrf

                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="confirmed"
                                        >

                                        <button
                                            type="submit"
                                            class="rounded-xl bg-green-600
                                            px-5 py-3 text-sm font-semibold
                                            text-white shadow-sm transition
                                            hover:bg-green-700"
                                        >
                                            Confirm Booking
                                        </button>

                                    </form>

                                @endif


                                {{-- Completed --}}
                                @if ($booking->status === 'confirmed')

                                    <form
                                        method="POST"
                                        action="{{ route('consultant.bookings.status', $booking) }}"
                                    >

                                        @csrf

                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="completed"
                                        >

                                        <button
                                            type="submit"
                                            class="rounded-xl border border-blue-200
                                            bg-blue-50 px-5 py-3
                                            text-sm font-semibold text-blue-700
                                            transition hover:bg-blue-100"
                                        >
                                            Mark Completed
                                        </button>

                                    </form>

                                @endif

                            </div>

                        </div>

                    </div>


                @empty


                    {{-- No Bookings --}}
                    <div
                        class="rounded-2xl border border-gray-200
                        bg-white px-6 py-12 text-center shadow-sm"
                    >

                        <div class="text-4xl">
                            📅
                        </div>

                        <h3
                            class="mt-4 text-lg font-bold text-gray-900"
                        >
                            No Consultation Bookings
                        </h3>

                        <p class="mt-2 text-sm text-gray-500">
                            You currently have no consultation bookings.
                        </p>

                    </div>


                @endforelse

            </div>

        </div>

    </div>

</x-app-layout>