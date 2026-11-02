<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingEventNotification;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class SendMeetingNotifications extends Command
{
    protected $signature = 'meetings:send-notifications';

    protected $description = 'Send one-hour meeting reminders and post-meeting notifications';

    public function handle(): int
    {
        $now = CarbonImmutable::now(config('app.timezone'));
        $sent = 0;
        $failed = 0;

        Booking::query()
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereNotNull('meeting_uri')
            ->whereDate('preferred_date', '<=', $now->toDateString())
            ->with(['student', 'consultant'])
            ->orderBy('id')
            ->chunkById(100, function ($bookings) use ($now, &$sent, &$failed): void {
                foreach ($bookings as $booking) {
                    $start = $booking->meetingStartsAt();
                    $reminderDueAt = $start->subHour();
                    $end = $booking->meetingEndsAt();

                    if ($booking->status === 'confirmed'
                        && $reminderDueAt->lessThanOrEqualTo($now)
                        && $start->greaterThan($now)
                    ) {
                        $message = 'Consultation with '
                            .($booking->consultant?->name ?? 'your consultant')
                            .' and student '
                            .($booking->student?->name ?? 'the student')
                            .' starts at '.$start->format('d M Y, h:i A')
                            .' ('.config('app.timezone').').';

                        $sent += $this->notifyOnce(
                            $booking,
                            'booking_reminder',
                            'Meeting in one hour',
                            $message,
                            $failed
                        );
                    }

                    if ($end->lessThanOrEqualTo($now)) {
                        $message = 'The scheduled consultation between '
                            .($booking->student?->name ?? 'the student')
                            .' and '
                            .($booking->consultant?->name ?? 'the consultant')
                            .' ended at '.$end->format('d M Y, h:i A')
                            .' ('.config('app.timezone').').';

                        $sent += $this->notifyOnce(
                            $booking,
                            'booking_meeting_ended',
                            'Meeting time has ended',
                            $message,
                            $failed
                        );
                    }
                }
            });

        $this->info("Meeting notifications sent: {$sent}; failed: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function notifyOnce(
        Booking $booking,
        string $eventType,
        string $title,
        string $message,
        int &$failed
    ): int {
        try {
            return DB::transaction(function () use ($booking, $eventType, $title, $message): int {
                $currentBooking = Booking::query()
                    ->whereKey($booking->id)
                    ->lockForUpdate()
                    ->first();

                if (! $currentBooking || ! in_array($currentBooking->status, ['confirmed', 'completed'], true)) {
                    return 0;
                }

                if ($eventType === 'booking_reminder' && $currentBooking->status !== 'confirmed') {
                    return 0;
                }

                $claimed = DB::table('booking_notification_deliveries')->insertOrIgnore([
                    'booking_id' => $currentBooking->id,
                    'event_type' => $eventType,
                    'sent_at' => now(config('app.timezone')),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($claimed === 0) {
                    return 0;
                }

                $recipients = collect([$currentBooking->student, $currentBooking->consultant])
                    ->filter()
                    ->concat(User::query()->where('role', User::ROLE_ADMIN)->get())
                    ->unique('id');

                foreach ($recipients as $recipient) {
                    $recipient->notify(new BookingEventNotification(
                        $title,
                        $message,
                        $eventType,
                        $currentBooking->id
                    ));
                }

                return $recipients->count();
            });
        } catch (Throwable $exception) {
            $failed++;
            report($exception);

            return 0;
        }
    }
}
