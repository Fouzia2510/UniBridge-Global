<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\AvailabilitySlot;
use App\Models\MeetingJoinEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingJoinTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_confirmed_student_can_open_a_live_meeting_and_activity_is_saved(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-30 14:30:00', config('app.timezone')));
        [$student, $consultant, $booking] = $this->makeBooking('14:00');

        $response = $this->actingAs($student)
            ->withSession(['two_factor_verified' => true])
            ->withHeaders(['User-Agent' => 'MeetingJoinTrackingTest'])
            ->get(route('bookings.join', $booking));

        $response->assertRedirect('https://meet.google.com/example-room');
        $this->assertDatabaseHas('meeting_join_events', [
            'booking_id' => $booking->id,
            'user_id' => $student->id,
            'user_agent' => 'MeetingJoinTrackingTest',
        ]);
    }

    public function test_expired_meeting_cannot_be_joined_and_is_not_logged(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-30 15:00:00', config('app.timezone')));
        [$student, , $booking] = $this->makeBooking('14:00');

        $response = $this->actingAs($student)
            ->withSession(['two_factor_verified' => true])
            ->from(route('student.bookings.index'))
            ->get(route('bookings.join', $booking));

        $response->assertRedirect(route('student.bookings.index'));
        $response->assertSessionHasErrors('meeting');
        $this->assertDatabaseCount('meeting_join_events', 0);
    }

    public function test_unrelated_user_cannot_open_another_students_meeting(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-30 14:30:00', config('app.timezone')));
        [, , $booking] = $this->makeBooking('14:00');
        $otherStudent = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->actingAs($otherStudent)
            ->withSession(['two_factor_verified' => true])
            ->get(route('bookings.join', $booking))
            ->assertForbidden();

        $this->assertDatabaseCount('meeting_join_events', 0);
    }

    public function test_expired_meeting_card_shows_time_passed_without_join_link(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-30 15:00:00', config('app.timezone')));
        [$student, , $booking] = $this->makeBooking('14:00');

        $this->actingAs($student)
            ->withSession(['two_factor_verified' => true])
            ->get(route('student.bookings.index'))
            ->assertOk()
            ->assertSee('Time passed')
            ->assertDontSee('Join Google Meet');
    }

    public function test_elapsed_slot_cannot_be_booked(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-30 15:00:00', config('app.timezone')));
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
        ]);
        $consultant = User::factory()->create(['role' => User::ROLE_CONSULTANT]);
        $slot = AvailabilitySlot::create([
            'consultant_id' => $consultant->id,
            'date' => '2026-10-30',
            'time' => '14:00',
            'is_booked' => false,
        ]);

        $this->actingAs($student)
            ->withSession(['two_factor_verified' => true])
            ->post(route('student.bookings.store'), [
                'consultant_id' => $consultant->id,
                'slot_id' => $slot->id,
            ])
            ->assertRedirect(route('student.bookings.create', ['consultant_id' => $consultant->id]))
            ->assertSessionHasErrors('slot_id');

        $this->assertDatabaseCount('bookings', 0);
        $this->assertFalse($slot->fresh()->is_booked);
    }

    public function test_admin_can_view_recorded_meeting_join_activity(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-30 14:30:00', config('app.timezone')));
        [$student, , $booking] = $this->makeBooking('14:00');

        MeetingJoinEvent::create([
            'booking_id' => $booking->id,
            'user_id' => $student->id,
            'joined_at' => now(),
        ]);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);

        $this->actingAs($admin)
            ->withSession(['two_factor_verified' => true])
            ->get(route('meeting-attendance.index'))
            ->assertOk()
            ->assertSee($student->name)
            ->assertSee($booking->consultant->name);
    }

    private function makeBooking(string $time): array
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
        ]);
        $consultant = User::factory()->create([
            'role' => User::ROLE_CONSULTANT,
            'is_approved' => true,
        ]);
        $booking = Booking::create([
            'student_id' => $student->id,
            'consultant_id' => $consultant->id,
            'preferred_date' => '2026-10-30',
            'preferred_time' => $time,
            'status' => 'confirmed',
            'meeting_provider' => 'google_meet',
            'meeting_uri' => 'https://meet.google.com/example-room',
        ]);

        return [$student, $consultant, $booking];
    }
}
