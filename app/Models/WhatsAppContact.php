<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WhatsAppContact extends Model
{
    protected $table = 'whatsapp_contacts';

    protected $fillable = [
        'phone_number',
        'contact_name',
        'contact_type',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function consents(): HasMany
    {
        return $this->hasMany(
            WhatsAppContactConsent::class,
            'whatsapp_contact_id'
        )
            ->orderByDesc('recorded_at')
            ->orderByDesc('id');
    }

    public function latestConsent(): HasOne
    {
        return $this->hasOne(
            WhatsAppContactConsent::class,
            'whatsapp_contact_id'
        )->latestOfMany('recorded_at');
    }

    public function isOptedIn(): bool
    {
        $latest = $this->latestConsent()->first();

        return $latest?->status === 'opted_in';
    }

    public function isOptedOut(): bool
    {
        $latest = $this->latestConsent()->first();

        return $latest?->status === 'opted_out';
    }
}
