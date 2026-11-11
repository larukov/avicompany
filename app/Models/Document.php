<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type',
        'series',
        'number',
        'issued_by',
        'issued_at',
        'expires_at',
        'file_path',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'passport_rf' => 'Паспорт РФ',
            'passport_international' => 'Загранпаспорт',
            'driver_license' => 'Водительское удостоверение',
            'other' => 'Другой документ',
        };
    }
}
