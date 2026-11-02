<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'consultant_id',
        'assigned_by',
        'assigned_at',
        'university_id',
        'program',
        'intake',
        'personal_statement',
        'notes',
        'fee',
        'status',
        'finalized_at',
        'admin_approval_status',
        'admin_approved_by',
        'admin_approved_at',
        'admin_notes',
        'processing_started_at',
        'completed_at',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'assigned_at' => 'datetime',
        'finalized_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(
            University::class
        );
    }

    /*
     * Consultant assigned to this application.
     */
    public function consultant(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'consultant_id'
        );
    }

    /*
     * Admin who assigned the consultant.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_by'
        );
    }

    /*
     * Admin who approved the application.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'admin_approved_by'
        );
    }

    /*
     * Payments submitted for this application.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(
            Payment::class
        );
    }

    /*
     * Latest payment.
     */
    public function latestPayment(): HasMany
    {
        return $this->hasMany(
            Payment::class
        )->latestOfMany();
    }

    /*
     * Visa procedure associated with this application.
     */
    public function visaApplication(): HasOne
    {
        return $this->hasOne(
            VisaApplication::class
        );
    }

    /*
     * Check whether this application has a verified payment.
     */
    public function hasVerifiedPayment(): bool
    {
        return $this->payments()
            ->where('status', 'verified')
            ->exists();
    }
}