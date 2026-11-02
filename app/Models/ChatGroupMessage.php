<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatGroupMessage extends Model
{
    protected $fillable = [
        'chat_group_id',
        'sender_user_id',
        'sender_contact_name',
        'message_text',
        'message_type',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ChatGroup::class, 'chat_group_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}