<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visa_application_id',
        'document_type',
        'title',
        'original_name',
        'storage_path',
        'google_drive_file_id',
        'google_drive_url',
        'verification_status',
        'admin_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visaApplication(): BelongsTo
    {
        return $this->belongsTo(
            VisaApplication::class,
            'visa_application_id'
        );
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'verified_by'
        );
    }

    public function isApproved(): bool
    {
        return $this->verification_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }
}