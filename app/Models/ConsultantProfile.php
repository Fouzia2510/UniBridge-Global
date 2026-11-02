<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultantProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'qualification',
        'specialization',
        'experience_years',
        'countries',
        'languages',
        'about',

        // Consultant requirements
        'budget_range',
        'preferred_country',
        'preferred_destinations',
        'client_demands',
        'consultation_type',
        'availability',
        'meeting_preference',

        // Admin approval
        'approval_status',
        'admin_notes',
        'approved_at',
    ];

    protected $casts = [
        'experience_years' => 'decimal:1',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}