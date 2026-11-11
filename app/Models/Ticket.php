<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'airline',
        'airline_logo',
        'company_id',
        'aircraft',
        'departure_city',
        'departure_airport',
        'departure_airport_name',
        'departure_time',
        'arrival_city',
        'arrival_airport',
        'arrival_airport_name',
        'arrival_time',
        'duration_minutes',
        'is_direct',
        'stops',
        'price_economy',
        'price_comfort',
        'price_business',
        'total_seats',
        'available_seats_economy',
        'available_seats_comfort',
        'available_seats_business',
        'has_wifi',
        'has_meal',
        'has_entertainment',
        'baggage_allowance',
        'status',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'is_direct' => 'boolean',
        'has_wifi' => 'boolean',
        'has_meal' => 'boolean',
        'has_entertainment' => 'boolean',
        'price_economy' => 'decimal:2',
        'price_comfort' => 'decimal:2',
        'price_business' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Ссылка на логотип: поддерживает и загруженный файл, и внешний URL
    public function getAirlineLogoUrlAttribute(): ?string
    {
        $logo = $this->airline_logo ?: $this->company?->logo;

        if (!$logo) {
            return null;
        }

        return Str::startsWith($logo, ['http://', 'https://']) ? $logo : asset($logo);
    }

    // Геттер для форматирования длительности
    public function getDurationFormattedAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        return $hours . 'ч ' . $minutes . 'м';
    }

    // Геттер для типа рейса
    public function getFlightTypeAttribute(): string
    {
        if ($this->is_direct) {
            return 'Прямой рейс';
        }
        return $this->stops . ' пересадк' . ($this->stops == 1 ? 'а' : 'и');
    }

    // Геттер для статуса
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'Запланирован',
            'boarding' => 'Посадка',
            'departed' => 'Вылетел',
            'arrived' => 'Прибыл',
            'delayed' => 'Задержан',
            'cancelled' => 'Отменён',
        };
    }

    // Scope для фильтрации
    public function scopeDirect($query)
    {
        return $query->where('is_direct', true);
    }

    public function scopeWithStops($query, $stops)
    {
        return $query->where('stops', $stops);
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->where('price_economy', '>=', $min)
                     ->where('price_economy', '<=', $max);
    }

    public function scopeDepartureTime($query, $period)
    {
        return match($period) {
            'morning' => $query->whereBetween('departure_time', ['06:00:00', '12:00:00']),
            'day' => $query->whereBetween('departure_time', ['12:00:00', '18:00:00']),
            'evening' => $query->whereBetween('departure_time', ['18:00:00', '00:00:00']),
            'night' => $query->whereBetween('departure_time', ['00:00:00', '06:00:00']),
            default => $query,
        };
    }
}