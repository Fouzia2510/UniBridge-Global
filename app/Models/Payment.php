<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'student_id',
        'payment_method',
        'bank_name',
        'amount',
        'currency',
        'transaction_reference',
        'payer_reference',
        'proof_path',
        'status',
        'verified_by',
        'verified_at',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    /*
     * The application this payment belongs to.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /*
     * Student who submitted the payment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /*
     * Admin who verified/rejected the payment.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /*
     * Human-readable payment method.
     */
    public function methodLabel(): string
    {
        return match ($this->payment_method) {
            'jazzcash' => 'JazzCash',
            'easypaisa' => 'Easypaisa',
            'nayapay' => 'NayaPay',
            'raast' => 'Raast',
            'bank_transfer' => 'Bank Transfer',
            'western_union' => 'Western Union',
            default => ucfirst(str_replace('_', ' ', $this->payment_method)),
        };
    }

    /*
     * Human-readable status.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Verification',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
            default => ucfirst($this->status),
        };
    }
}