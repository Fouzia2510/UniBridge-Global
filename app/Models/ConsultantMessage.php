<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultantMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'consultant_id',
        'subject',
        'message',
        'consultant_reply',
        'replied_at',
        'is_read',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }
}