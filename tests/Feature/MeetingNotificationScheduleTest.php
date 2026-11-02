<?php

namespace Tests\Feature;

use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingNotificationScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_new_slots_appear_before_older_slots(): void
    {
        $consultant = User::factory()->create([
            'role' => User::ROLE_CONSULTANT,
            'is_approved' => true,
        ]);
        $older = AvailabilitySlot::create([
            'consultant_id' => $consultant->id,
            'date' => now()->addDays(8)->toDateString(),
            'time' => '09:00',
            'is_booked' => false,
        ]);
        $newest = AvailabilitySlot::create([
            'consultant_id' => $consultant->id,
            'date' => now()->addDays(2)->toDateString(),
            'time' => '10:00',
            'is_booked' => false,
        ]);

        $this->actingAs($consultant)
            ->withSession(['two_factor_verified' => true])
            ->get(route('consultant.availability.index'))
            ->assertOk()
            ->assertSeeInOrder([
                $newest->date->format('l, d M Y'),
                $older->date->format('l, d M Y'),
            ]);
    }

    public function test_one_hour_reminder_is_sent_once_to_student_consultant_and_admin(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-30 13:00:00', config('app.timezone')));
        [$student, $consultant, $admin, $booking] = $this->bookingWithAdmin('14:00');

        $this->artisan('meetings:send-notifications')->assertSuccessful();
        $this->artisan('meetings:send-notifications')->assertSuccessful();

        foreach ([$student, $consultant, $admin] as $recipient) {
            $this->assertSame(1, $recipient->notifications()
                ->where('data->type', 'booking_reminder')
                ->where('data->booking_id', $booking->id)
                ->count());
        }

        $this->assertDatabaseHas('booking_notification_deliveries', [
            'booking_id' => $booking->id,
            'event_type' => 'booking_reminder',
        ]);
    }

    public function test_completed_meeting_notification_is_sent_once_to_all_participants(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-30 15:00:00', config('app.timezone')));
        [$student, $consultant, $admin, $booking] = $this->bookingWithAdmin('14:00', 'completed');

        $this->artisan('meetings:send-notifications')->assertSuccessful();
        $this->artisan('meetings:send-notifications')->assertSuccessful();

        foreach ([$student, $consultant, $admin] as $recipient) {
            $this->assertSame(1, $recipient->notifications()
                ->where('data->type', 'booking_meeting_ended')
                ->where('data->booking_id', $booking->id)
                ->count());
        }
    }

    public function test_cancelled_meeting_does_not_send_reminder_or_follow_up(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-30 15:00:00', config('app.timezone')));
        [$student, $consultant, $admin] = $this->bookingWithAdmin('14:00', 'cancelled');

        $this->artisan('meetings:send-notifications')->assertSuccessful();

        foreach ([$student, $consultant, $admin] as $recipient) {
            $this->assertSame(0, $recipient->notifications()
                ->whereIn('data->type', ['booking_reminder', 'booking_meeting_ended'])
                ->count());
        }
    }

    public function test_meeting_without_a_generated_link_does_not_send_timed_notifications(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-30 13:00:00', config('app.timezone')));
        [$student, $consultant, $admin, $booking] = $this->bookingWithAdmin('14:00');
        $booking->update(['meeting_uri' => null]);

        $this->artisan('meetings:send-notifications')->assertSuccessful();

        foreach ([$student, $consultant, $admin] as $recipient) {
            $this->assertSame(0, $recipient->notifications()
                ->where('data->booking_id', $booking->id)
                ->whereIn('data->type', ['booking_reminder', 'booking_meeting_ended'])
                ->count());
        }
    }

    private function bookingWithAdmin(string $time, string $status = 'confirmed'): array
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $consultant = User::factory()->create(['role' => User::ROLE_CONSULTANT]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $booking = Booking::create([
            'student_id' => $student->id,
            'consultant_id' => $consultant->id,
            'preferred_date' => '2026-10-30',
            'preferred_time' => $time,
            'status' => $status,
            'meeting_provider' => 'google_meet',
            'meeting_uri' => 'https://meet.google.com/example-room',
        ]);

        return [$student, $consultant, $admin, $booking];
    }
}
