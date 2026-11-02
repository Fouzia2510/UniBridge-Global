<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppBroadcast extends Model
{
    protected $table = 'whatsapp_broadcasts';

    protected $fillable = [
        'whatsapp_group_id',
        'created_by',
        'name',
        'message_text',
        'status',
        'recipient_count',
        'accepted_count',
        'failed_count',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(
            WhatsAppGroup::class,
            'whatsapp_group_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(
            WhatsAppBroadcastRecipient::class,
            'whatsapp_broadcast_id'
        );
    }
}
