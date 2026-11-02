<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppGroupMember extends Model
{
    protected $table = 'whatsapp_group_members';

    protected $fillable = [
        'whatsapp_group_id',
        'user_id',
        'contact_name',
        'phone_number',
        'contact_type',
        'whatsapp_opt_in',
        'opted_in_at',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_opt_in' => 'boolean',
            'opted_in_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(
            WhatsAppGroup::class,
            'whatsapp_group_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
