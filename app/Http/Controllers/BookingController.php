<?php

namespace App\Http\Controllers;

use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\MeetingJoinEvent;
use App\Models\User;
use App\Notifications\BookingEventNotification;
use App\Services\GoogleMeetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class BookingController extends Controller
{
    /**
     * Show the consultant booking page for students.
     */
    public function create(Request $request): View
    {
        $consultants = User::where(
                'role',
                User::ROLE_CONSULTANT
            )
            ->where('is_approved', true)
            ->orderBy('name')
            ->get();

        $selectedConsultantId = $request->query(
            'consultant_id'
        );

        $availableSlots = collect();

        if ($selectedConsultantId) {

            $selectedConsultant = User::where(
                    'id',
                    $selectedConsultantId
                )
                ->where(
                    'role',
                    User::ROLE_CONSULTANT
                )
                ->where('is_approved', true)
                ->first();

            if ($selectedConsultant) {

                $availableSlots = AvailabilitySlot::where(
                        'consultant_id',
                        $selectedConsultant->id
                    )
                    ->where(
                        'is_booked',
                        false
                    )
                    ->whereDate(
                        'date',
                        '>=',
                        now()->toDateString()
                    )
                    ->orderBy('date')
                    ->orderBy('time')
                    ->get()
                    ->filter(fn (AvailabilitySlot $slot) => $slot->startsAt()->isFuture())
                    ->values();
            }
        }

        return view(
            'student.book-consultant',
            [
                'consultants' => $consultants,
                'availableSlots' => $availableSlots,
                'selectedConsultantId' => $selectedConsultantId,
            ]
        );
    }

    /**
     * Store a student consultation booking.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'consultant_id' => [
                'required',
                'exists:users,id',
            ],

            'slot_id' => [
                'required',
                'exists:availability_slots,id',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $consultant = User::where(
                'id',
                $validated['consultant_id']
            )
            ->where(
                'role',
                User::ROLE_CONSULTANT
            )
            ->where('is_approved', true)
            ->first();

        abort_if(
            ! $consultant,
            404,
            'Consultant not found.'
        );

        $outcome = DB::transaction(function () use ($validated, $request, $consultant) {
            $slot = AvailabilitySlot::where('id', $validated['slot_id'])
                ->where('consultant_id', $consultant->id)
                ->lockForUpdate()
                ->first();

            if (! $slot || $slot->is_booked || ! $slot->startsAt()->isFuture()) {
                return ['error' => 'slot'];
            }

            $existingBooking = Booking::where('student_id', $request->user()->id)
                ->where('preferred_date', $slot->date)
                ->where('preferred_time', $slot->time)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($existingBooking) {
                return ['error' => 'duplicate'];
            }

            $booking = Booking::create([
                'student_id' => $request->user()->id,
                'consultant_id' => $consultant->id,
                'preferred_date' => $slot->date,
                'preferred_time' => $slot->time,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            $slot->update(['is_booked' => true]);

            return ['booking' => $booking];
        });

        if (isset($outcome['error'])) {
            $route = $outcome['error'] === 'duplicate'
                ? 'student.bookings.index'
                : 'student.bookings.create';

            return redirect()
                ->route($route, $route === 'student.bookings.create'
                    ? ['consultant_id' => $consultant->id]
                    : [])
                ->withErrors([
                    $outcome['error'] === 'duplicate' ? 'booking' : 'slot_id' => $outcome['error'] === 'duplicate'
                        ? 'You already have a booking for this date and time.'
                        : 'This consultation slot is no longer available. Please select another slot.',
                ]);
        }

        $booking = $outcome['booking'];

$request->user()->notify(
    new BookingEventNotification(
        'Booking request submitted',
        'Your consultation booking request has been submitted and is waiting for consultant confirmation.',
        'booking_created',
        $booking->id
    )
);

$consultant->notify(
    new BookingEventNotification(
        'New consultation booking',
        'A student has requested a consultation on '
            . $booking->preferred_date
            . ' at '
            . $booking->preferred_time
            . '. Please review the booking in your dashboard.',
        'booking_created',
        $booking->id
    )
);


        return redirect()
            ->route(
                'student.bookings.index'
            )
            ->with(
                'status',
                'Booking request sent successfully! Waiting for consultant confirmation.'
            );
    }

    /**
     * Student's bookings.
     */
    public function studentIndex(
        Request $request
    ): View {
        $bookings = Booking::where(
                'student_id',
                $request->user()->id
            )
            ->with(['consultant', 'joinEvents.user'])
            ->orderByDesc('preferred_date')
            ->orderByDesc('preferred_time')
            ->get();

        return view(
            'student.bookings',
            [
                'bookings' => $bookings,
            ]
        );
    }

    /**
     * Consultant's bookings.
     */
    public function consultantIndex(
        Request $request
    ): View {
        $bookings = Booking::where(
                'consultant_id',
                $request->user()->id
            )
            ->with(['student', 'joinEvents.user'])
            ->orderBy('preferred_date')
            ->orderBy('preferred_time')
            ->get();

        return view(
            'consultant.bookings',
            [
                'bookings' => $bookings,
            ]
        );
    }

    /**
     * Consultant updates booking status.
     *
     * When a booking is confirmed:
     * - A Google Meet is created if one does not already exist.
     * - This also works for an already-confirmed booking
     *   that does not yet have a Google Meet link.
     * - The booking remains confirmed if Google Meet creation fails.
     */
    public function updateStatus(
        Request $request,
        Booking $booking,
        GoogleMeetService $googleMeetService
    ): RedirectResponse {

        abort_if(
            $booking->consultant_id !== $request->user()->id,
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,confirmed,completed,cancelled',
            ],
        ]);

        $oldStatus = $booking->status;
        $newStatus = $validated['status'];

        if (
            $newStatus === 'confirmed'
            && now(config('app.timezone'))->greaterThanOrEqualTo($booking->meetingStartsAt())
        ) {
            return redirect()
                ->route('consultant.bookings.index')
                ->withErrors([
                    'booking' => 'This meeting has already started and can no longer be confirmed.',
                ]);
        }

if (
    $oldStatus !== $newStatus
    && in_array($newStatus, ['confirmed', 'cancelled'], true)
) {
    $student = User::find($booking->student_id);

    if ($student) {
        if ($newStatus === 'confirmed') {
            $title = 'Booking confirmed';
            $message = 'Your consultation booking for '
                . $booking->preferred_date
                . ' at '
                . $booking->preferred_time
                . ' has been confirmed by the consultant.';
            $eventType = 'booking_confirmed';
        } else {
            $title = 'Booking cancelled';
            $message = 'Your consultation booking for '
                . $booking->preferred_date
                . ' at '
                . $booking->preferred_time
                . ' has been cancelled.';
            $eventType = 'booking_cancelled';
        }

        $student->notify(
            new BookingEventNotification(
                $title,
                $message,
                $eventType,
                $booking->id
            )
        );
    }
}


        /*
        |--------------------------------------------------------------------------
        | Update booking status
        |--------------------------------------------------------------------------
        */

        $booking->update([
            'status' => $newStatus,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Release availability slot when booking is cancelled
        |--------------------------------------------------------------------------
        */

        if (
            $newStatus === 'cancelled'
            && $oldStatus !== 'cancelled'
        ) {

            AvailabilitySlot::where(
                    'consultant_id',
                    $booking->consultant_id
                )
                ->whereDate(
                    'date',
                    $booking->preferred_date
                )
                ->where(
                    'time',
                    $booking->preferred_time
                )
                ->update([
                    'is_booked' => false,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Google Meet
        |--------------------------------------------------------------------------
        |
        | A meeting should be created when:
        |
        | 1. Booking is confirmed.
        | 2. No meeting URI already exists.
        |
        | IMPORTANT:
        | We intentionally do NOT check:
        |
        |     $oldStatus !== 'confirmed'
        |
        | This allows an already-confirmed booking without a Meet link
        | to receive its missing Google Meet link.
        |
        */

        $meetingWarning = null;

        if (
            $newStatus === 'confirmed'
            && empty($booking->meeting_uri)
        ) {

            try {

                $consultant = $request->user();

                $meeting = $googleMeetService->createMeeting(
                    $consultant
                );

                $booking->update([
                    'meeting_provider' => 'google_meet',
                    'meeting_space_name' => $meeting['name'],
                    'meeting_uri' => $meeting['meetingUri'],
                    'meeting_created_at' => now(),
                ]);

            } catch (Throwable $exception) {

                /*
                |--------------------------------------------------------------------------
                | Keep booking confirmed if Google Meet fails
                |--------------------------------------------------------------------------
                */

                Log::error(
                    'Google Meet creation failed after booking confirmation.',
                    [
                        'booking_id' => $booking->id,
                        'consultant_id' => $request->user()->id,
                        'error' => $exception->getMessage(),
                    ]
                );

                $meetingWarning =
                    'Booking is confirmed, but Google Meet could not be created. Please connect/reconnect your Google account and try again.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect with appropriate message
        |--------------------------------------------------------------------------
        */

        if ($meetingWarning) {

            return redirect()
                ->route(
                    'consultant.bookings.index'
                )
                ->with(
                    'status',
                    $meetingWarning
                );
        }

        if (
            $newStatus === 'confirmed'
            && ! empty($booking->meeting_uri)
        ) {

            return redirect()
                ->route(
                    'consultant.bookings.index'
                )
                ->with(
                    'status',
                    'Booking confirmed successfully and Google Meet link is ready.'
                );
        }

        return redirect()
            ->route(
                'consultant.bookings.index'
            )
            ->with(
                'status',
                'Booking status updated successfully!'
            );
    }

    /** Redirect an authorized participant to an active meeting and log the join-link click. */
    public function join(Request $request, Booking $booking): RedirectResponse
    {
        $user = $request->user();
        $isParticipant = in_array($user->id, [$booking->student_id, $booking->consultant_id], true);

        abort_unless($isParticipant || $user->role === User::ROLE_ADMIN, 403);

        if ($booking->meetingHasEnded()) {
            return redirect()->back()->withErrors([
                'meeting' => 'This meeting time has passed. The meeting link is no longer available.',
            ]);
        }

        if (! $booking->meetingIsJoinable()) {
            return redirect()->back()->withErrors([
                'meeting' => 'This meeting is not confirmed or is not within its scheduled time.',
            ]);
        }

        MeetingJoinEvent::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'joined_at' => now(config('app.timezone')),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->away($booking->meeting_uri);
    }

    /** Show persisted meeting join-link activity to the relevant portal. */
    public function meetingAttendance(Request $request): View
    {
        $user = $request->user();
        abort_unless(in_array($user->role, [
            User::ROLE_ADMIN,
            User::ROLE_CONSULTANT,
            User::ROLE_STUDENT,
        ], true), 403);

        $events = MeetingJoinEvent::query()
            ->with(['user', 'booking.student', 'booking.consultant'])
            ->when($user->role === User::ROLE_CONSULTANT, function ($query) use ($user) {
                $query->whereHas('booking', fn ($bookings) => $bookings->where('consultant_id', $user->id));
            })
            ->when($user->role === User::ROLE_STUDENT, function ($query) use ($user) {
                $query->whereHas('booking', fn ($bookings) => $bookings->where('student_id', $user->id));
            })
            ->latest('joined_at')
            ->paginate(30);

        return view('meeting-attendance.index', [
            'events' => $events,
            'isAdmin' => $user->role === User::ROLE_ADMIN,
        ]);
    }
}