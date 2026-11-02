<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilitySlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultant_id',
        'date',
        'time',
        'is_booked',
    ];

    protected $casts = [
        'date' => 'date',
        'is_booked' => 'boolean',
    ];

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    public function startsAt(): CarbonImmutable
    {
        return CarbonImmutable::parse(
            $this->date->toDateString().' '.$this->time,
            config('app.timezone')
        );
    }

    public function endsAt(): CarbonImmutable
    {
        return $this->startsAt()->addMinutes(Booking::MEETING_DURATION_MINUTES);
    }

    public function hasEnded(): bool
    {
        return now(config('app.timezone'))->greaterThanOrEqualTo($this->endsAt());
    }
}