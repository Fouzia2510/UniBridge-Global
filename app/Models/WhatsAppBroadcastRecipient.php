<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppBroadcastRecipient extends Model
{
    protected $table = 'whatsapp_broadcast_recipients';

    protected $fillable = [
        'whatsapp_broadcast_id',
        'whatsapp_group_member_id',
        'contact_name',
        'phone_number',
        'status',
        'provider_message_id',
        'error_message',
        'provider_payload',
        'attempted_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'provider_payload' => 'array',
            'attempted_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function broadcast(): BelongsTo
    {
        return $this->belongsTo(
            WhatsAppBroadcast::class,
            'whatsapp_broadcast_id'
        );
    }

    public function groupMember(): BelongsTo
    {
        return $this->belongsTo(
            WhatsAppGroupMember::class,
            'whatsapp_group_member_id'
        );
    }
}
