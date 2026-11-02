<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Consultant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'company',
        'slug',
        'logo',
        'short_description',
        'city',
        'address',
        'latitude',
        'longitude',
        'website',
        'reference_url',
        'reference_label',
        'services',
        'destinations',
        'is_verified',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'services' => 'array',
            'destinations' => 'array',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * Public consultant profile belongs to a consultant user account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Automatically generate the slug when creating a consultant.
     */
    protected static function booted(): void
    {
        static::creating(function (Consultant $consultant) {
            if (empty($consultant->slug)) {
                $consultant->slug = Str::slug($consultant->company);
            }
        });
    }
}