<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingService extends Model
{
    protected $fillable = ['booking_id', 'type', 'price', 'quantity'];

    protected $casts = ['price' => 'decimal:2'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'extra_baggage' => 'Дополнительный багаж',
            'seat_selection' => 'Выбор места',
            'special_meal' => 'Специальное питание',
            'insurance' => 'Страховка',
            'priority_boarding' => 'Приоритетная посадка',
            'business_lounge' => 'Бизнес-зал',
        };
    }
}