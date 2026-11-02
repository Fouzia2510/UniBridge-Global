<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const MEETING_DURATION_MINUTES = 60;

    protected $fillable = [
        'student_id',
        'consultant_id',
        'preferred_date',
        'preferred_time',
        'notes',
        'status',
        'meeting_provider',
        'meeting_space_name',
        'meeting_uri',
        'meeting_created_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'meeting_created_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    public function joinEvents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MeetingJoinEvent::class)->orderBy('joined_at');
    }

    public function meetingStartsAt(): CarbonImmutable
    {
        return CarbonImmutable::parse(
            $this->preferred_date->toDateString().' '.$this->preferred_time,
            config('app.timezone')
        );
    }

    public function meetingEndsAt(): CarbonImmutable
    {
        return $this->meetingStartsAt()->addMinutes(self::MEETING_DURATION_MINUTES);
    }

    public function meetingHasEnded(): bool
    {
        return now(config('app.timezone'))->greaterThanOrEqualTo($this->meetingEndsAt());
    }

    public function meetingIsJoinable(): bool
    {
        $now = now(config('app.timezone'));

        return $this->status === 'confirmed'
            && ! empty($this->meeting_uri)
            && $now->greaterThanOrEqualTo($this->meetingStartsAt())
            && $now->lessThan($this->meetingEndsAt());
    }
}