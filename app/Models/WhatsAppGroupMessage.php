<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppGroupMessage extends Model
{
    protected $table = 'whatsapp_group_messages';

    protected $fillable = [
        'whatsapp_group_id',
        'sender_user_id',
        'sender_type',
        'sender_name',
        'message_text',
        'message_type',
        'status',
        'provider_message_id',
        'payload',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(
            WhatsAppGroup::class,
            'whatsapp_group_id'
        );
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'sender_user_id'
        );
    }
}