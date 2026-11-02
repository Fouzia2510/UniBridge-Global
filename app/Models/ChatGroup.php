<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'avatar_path',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ChatGroupMember::class, 'chat_group_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatGroupMessage::class, 'chat_group_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatGroupMessage::class, 'chat_group_id')
            ->latestOfMany();
    }
}