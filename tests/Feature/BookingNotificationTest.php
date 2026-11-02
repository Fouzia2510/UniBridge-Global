<?php

namespace Tests\Feature;

use App\Http\Controllers\BookingController;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingEventNotification;
use App\Services\GoogleMeetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class BookingNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_submission_notifies_student_and_consultant(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
        ]);

        $consultant = User::factory()->create([
            'role' => User::ROLE_CONSULTANT,
            'is_approved' => true,
        ]);

        $slot = AvailabilitySlot::create([
            'consultant_id' => $consultant->id,
            'date' => now()->addDays(2)->toDateString(),
            'time' => '14:00',
            'is_booked' => false,
        ]);

        $request = Request::create('/student/bookings', 'POST', [
            'consultant_id' => $consultant->id,
            'slot_id' => $slot->id,
            'notes' => 'Automated notification test',
        ]);

        $request->setUserResolver(fn () => $student);

        $response = app(BookingController::class)->store($request);

        $booking = Booking::where('student_id', $student->id)
            ->where('consultant_id', $consultant->id)
            ->firstOrFail();

        $this->assertSame('pending', $booking->status);
        $this->assertTrue($slot->fresh()->is_booked);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'notifiable_type' => User::class,
            'type' => BookingEventNotification::class,
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $consultant->id,
            'notifiable_type' => User::class,
            'type' => BookingEventNotification::class,
        ]);

        $this->assertSame(
            'booking_created',
            $student->notifications()->latest()->first()->data['type']
        );

        $this->assertSame(
            'booking_created',
            $consultant->notifications()->latest()->first()->data['type']
        );
    }

    public function test_confirming_booking_notifies_student(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $consultant = User::factory()->create([
            'role' => User::ROLE_CONSULTANT,
        ]);

        $booking = $this->createBooking($student, $consultant);

        $googleMeetService = \Mockery::mock(GoogleMeetService::class);

        $googleMeetService->shouldReceive('createMeeting')
            ->once()
            ->withArgs(fn ($user) => $user->id === $consultant->id)
            ->andReturn([
                'name' => 'spaces/test-space',
                'meetingUri' => 'https://meet.google.com/test-meeting',
            ]);

        $request = Request::create(
            "/consultant/bookings/{$booking->id}/status",
            'PATCH',
            ['status' => 'confirmed']
        );

        $request->setUserResolver(fn () => $consultant);

        app(BookingController::class)->updateStatus(
            $request,
            $booking,
            $googleMeetService
        );

        $this->assertSame('confirmed', $booking->fresh()->status);
        $this->assertSame('google_meet', $booking->fresh()->meeting_provider);
        $this->assertSame('spaces/test-space', $booking->fresh()->meeting_space_name);
        $this->assertSame('https://meet.google.com/test-meeting', $booking->fresh()->meeting_uri);

        $notification = $student->notifications()
            ->latest()
            ->first();

        $this->assertNotNull($notification);
        $this->assertSame(
            'booking_confirmed',
            $notification->data['type']
        );
        $this->assertSame(
            $booking->id,
            $notification->data['booking_id']
        );
    }

    public function test_cancelling_booking_notifies_student_and_releases_slot(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $consultant = User::factory()->create([
            'role' => User::ROLE_CONSULTANT,
        ]);

        $booking = $this->createBooking($student, $consultant);

        $slot = AvailabilitySlot::create([
            'consultant_id' => $consultant->id,
            'date' => $booking->preferred_date->toDateString(),
            'time' => $booking->preferred_time,
            'is_booked' => true,
        ]);

        $googleMeetService = \Mockery::mock(GoogleMeetService::class);

        $request = Request::create(
            "/consultant/bookings/{$booking->id}/status",
            'PATCH',
            ['status' => 'cancelled']
        );

        $request->setUserResolver(fn () => $consultant);

        app(BookingController::class)->updateStatus(
            $request,
            $booking,
            $googleMeetService
        );

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertFalse($slot->fresh()->is_booked);

        $notification = $student->notifications()
            ->latest()
            ->first();

        $this->assertNotNull($notification);
        $this->assertSame(
            'booking_cancelled',
            $notification->data['type']
        );
        $this->assertSame(
            $booking->id,
            $notification->data['booking_id']
        );
    }

    private function createBooking(
        User $student,
        User $consultant
    ): Booking {
        return Booking::create([
            'student_id' => $student->id,
            'consultant_id' => $consultant->id,
            'preferred_date' => now()->addDays(2)->toDateString(),
            'preferred_time' => '14:00',
            'notes' => 'Automated test',
            'status' => 'pending',
        ]);
    }
}
