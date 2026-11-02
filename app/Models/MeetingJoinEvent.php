<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingJoinEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'joined_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
