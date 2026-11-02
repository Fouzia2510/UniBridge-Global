<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultant_id',
        'user_id',
        'visitor_name',
        'visitor_email',
        'source',
        'visit_type',
        'country_interest',
        'program_interest',
        'remarks',
        'consultant_remarks',
        'lead_status',
        'contacted_at',
        'converted_at',
        'ip_address',
        'user_agent',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'contacted_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'consultant_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}