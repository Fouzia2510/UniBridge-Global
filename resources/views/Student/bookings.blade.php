<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-indigo-600">
                    Student Portal
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    My Meetings
                </h2>

                <p class="mt-1 text-sm text-slate-600">
                    View your consultation bookings and join confirmed meetings.
                </p>
            </div>

            <a
                href="{{ route('student.bookings.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
            >
                Book a Consultation
            </a>
            <a href="{{ route('meeting-attendance.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
                Join Activity
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-800">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($bookings->isEmpty())

                <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-2xl">
                        📅
                    </div>

                    <h3 class="mt-4 text-lg font-bold text-slate-900">
                        No Consultation Meetings
                    </h3>

                    <p class="mt-2 text-sm text-slate-600">
                        You do not have any consultation bookings yet.
                    </p>

                    <a
                        href="{{ route('student.bookings.create') }}"
                        class="mt-5 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700"
                    >
                        Find a Consultant
                    </a>
                </div>

            @else

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                    @foreach ($bookings as $booking)

                        @php
                            $status = strtolower((string) $booking->status);

                            $statusClasses = match ($status) {
                                'confirmed' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-amber-100 text-amber-700',
                                'cancelled', 'rejected' => 'bg-red-100 text-red-700',
                                'completed' => 'bg-blue-100 text-blue-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp

                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                            <div class="border-b border-slate-100 bg-slate-50 px-6 py-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">
                                            Consultation Meeting
                                        </p>

                                        <h3 class="mt-1 text-xl font-extrabold text-slate-950">
                                            {{ $booking->consultant->name ?? 'Consultant' }}
                                        </h3>

                                        @if (!empty($booking->consultant?->email))
                                            <p class="mt-1 text-sm text-slate-600">
                                                {{ $booking->consultant->email }}
                                            </p>
                                        @endif
                                    </div>

                                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold {{ $statusClasses }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                    @if ($booking->meetingHasEnded() && $status !== 'cancelled')
                                        <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                            Time passed
                                        </span>
                                    @endif

                                </div>
                            </div>

                            <div class="space-y-5 px-6 py-6">

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                            Date
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-slate-900">
                                            {{ $booking->preferred_date
                                                ? \Carbon\Carbon::parse($booking->preferred_date)->format('d M Y')
                                                : '—' }}
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                            Time ({{ config('app.timezone') }})
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-slate-900">
                                            {{ $booking->preferred_time
                                                ? \Carbon\Carbon::parse($booking->preferred_time)->format('h:i A')
                                                : '—' }}
                                        </p>
                                    </div>

                                </div>

                                @if ($status === 'confirmed')

                                    <div class="rounded-xl border border-green-200 bg-green-50 p-5">

                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100">
                                                ✓
                                            </span>

                                            <div>
                                                <p class="text-sm font-extrabold text-green-800">
                                                    Meeting Confirmed
                                                </p>

                                                <p class="text-xs text-green-700">
                                                    Your consultant has confirmed this consultation.
                                                </p>
                                            </div>
                                        </div>

                                        @if (!empty($booking->meeting_uri))
                                            @if ($booking->meetingHasEnded())
                                                <p class="mt-4 rounded-lg bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700">The meeting time has passed. Joining is no longer available.</p>
                                            @elseif ($booking->meetingIsJoinable())
                                                <a href="{{ route('bookings.join', $booking) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-green-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-green-700">
                                                    Join Google Meet
                                                </a>
                                            @else
                                                <p class="mt-4 rounded-lg bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-800">Join will be available from {{ $booking->meetingStartsAt()->format('h:i A') }} ({{ config('app.timezone') }}).</p>
                                            @endif
                                        @else
                                            <p class="mt-4 text-sm font-semibold text-amber-700">
                                                Google Meet link is not available yet.
                                            </p>
                                        @endif

                                    </div>

                                @elseif ($status === 'pending')

                                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                                        <p class="text-sm font-bold text-amber-800">
                                            Waiting for Consultant Confirmation
                                        </p>

                                        <p class="mt-1 text-xs text-amber-700">
                                            Your booking request has been sent. The consultant must confirm the meeting.
                                        </p>
                                    </div>

                                @elseif ($status === 'completed')

                                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                                        <p class="text-sm font-bold text-blue-800">
                                            Meeting Completed
                                        </p>

                                        <p class="mt-1 text-xs text-blue-700">
                                            This consultation has been marked as completed.
                                        </p>
                                    </div>

                                @elseif (in_array($status, ['cancelled', 'rejected']))

                                    <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                                        <p class="text-sm font-bold text-red-800">
                                            Meeting {{ ucfirst($status) }}
                                        </p>
                                    </div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>
    </div>

</x-app-layout>
