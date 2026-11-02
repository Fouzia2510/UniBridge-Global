<?php

namespace App\Http\Controllers;

use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    /**
     * Show the consultant's availability slots.
     */
    public function index(Request $request): View
    {
        $slots = AvailabilitySlot::where('consultant_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('consultant.availability', [
            'slots' => $slots,
        ]);
    }

    /**
     * Add a new availability slot.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'time' => [
                'required',
                'date_format:H:i',
            ],
        ]);

        $startsAt = \Carbon\CarbonImmutable::parse(
            $validated['date'].' '.$validated['time'],
            config('app.timezone')
        );

        if ($startsAt->lessThanOrEqualTo(now(config('app.timezone')))) {
            return redirect()
                ->route('consultant.availability.index')
                ->withErrors([
                    'time' => 'Availability must start in the future.',
                ]);
        }

        $existingSlot = AvailabilitySlot::where('consultant_id', $request->user()->id)
            ->whereDate('date', $validated['date'])
            ->where('time', $validated['time'])
            ->first();

        if ($existingSlot) {
            return redirect()
                ->route('consultant.availability.index')
                ->withErrors([
                    'time' => 'This availability slot already exists.',
                ]);
        }

        AvailabilitySlot::create([
            'consultant_id' => $request->user()->id,
            'date' => $validated['date'],
            'time' => $validated['time'],
            'is_booked' => false,
        ]);

        return redirect()
            ->route('consultant.availability.index')
            ->with('status', 'Availability slot added successfully!');
    }

    /**
     * Remove an availability slot.
     */
    public function destroy(
        Request $request,
        AvailabilitySlot $slot
    ): RedirectResponse {
        abort_if(
            $slot->consultant_id !== $request->user()->id,
            403
        );

        $hasActiveBooking = Booking::where('consultant_id', $slot->consultant_id)
            ->whereDate('preferred_date', $slot->date)
            ->where('preferred_time', $slot->time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($slot->is_booked && $hasActiveBooking) {
            return redirect()
                ->route('consultant.availability.index')
                ->withErrors([
                    'slot' => 'A booked slot cannot be removed.',
                ]);
        }

        $slot->delete();

        return redirect()
            ->route('consultant.availability.index')
            ->with('status', 'Availability slot removed successfully!');
    }

    /** Return a consultant's currently bookable slots for the student booking UI. */
    public function studentAvailability(User $consultant)
    {
        abort_unless($consultant->role === User::ROLE_CONSULTANT, 404);

        $slots = AvailabilitySlot::where('consultant_id', $consultant->id)
            ->where('is_booked', false)
            ->whereDate('date', '>=', now(config('app.timezone'))->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->filter(fn (AvailabilitySlot $slot) => $slot->startsAt()->isFuture())
            ->map(fn (AvailabilitySlot $slot) => [
                'id' => $slot->id,
                'date' => $slot->date->toDateString(),
                'time' => $slot->time,
                'starts_at' => $slot->startsAt()->toIso8601String(),
                'ends_at' => $slot->endsAt()->toIso8601String(),
            ])
            ->values();

        return response()->json(['data' => $slots]);
    }
}