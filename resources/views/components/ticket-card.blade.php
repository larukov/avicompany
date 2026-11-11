@props(['ticket', 'selectedClass' => null])

@php
    $minPrice = min(
        (float) $ticket->price_economy,
        (float) ($ticket->price_comfort ?? $ticket->price_economy),
        (float) ($ticket->price_business ?? $ticket->price_economy),
    );
    $buyClass = $selectedClass ?? 'economy';
    $seatsColumn = 'available_seats_' . $buyClass;
    $classSeats = (int) ($ticket->{$seatsColumn} ?? 0);
    // Показываем цену выбранного класса, если он выбран, иначе минимальную
    $displayPrice = $selectedClass
        ? (float) ($ticket->{'price_' . $selectedClass} ?? $minPrice)
        : $minPrice;
    $requested = max(1, (int) request('passengers', 1));
    $soldOut = $classSeats <= 0;
    $leftWord = match (true) {
        $classSeats == 1 => 'место',
        $classSeats >= 2 && $classSeats <= 4 => 'места',
        $classSeats >= 5 && $classSeats <= 20 => 'мест',
        $classSeats % 10 == 1 && $classSeats % 100 != 11 => 'место',
        $classSeats % 10 >= 2 && $classSeats % 10 <= 4 && ($classSeats % 100 < 10 || $classSeats % 100 >= 20) => 'места',
        default => 'мест',
    };
@endphp

<div class="border bg-white rounded-3 shadows p-3 mb-3 ticket-card">
    <div class="row align-items-center">
        {{-- Авиакомпания с логотипом --}}
        <div class="col-lg-2 text-center mb-3 mb-lg-0">
            @if ($ticket->airline_logo_url)
                <img src="{{ $ticket->airline_logo_url }}" alt="{{ $ticket->airline }}" class="airline-logo">
            @else
                <div class="airline-logo d-flex align-items-center justify-content-center bg-primary text-white rounded mx-auto"
                    style="width: 80px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                    {{ mb_substr($ticket->airline, 0, 2) }}
                </div>
            @endif
            <div class="airline-name">{{ $ticket->airline }}</div>
            <div class="flight-number">Рейс {{ $ticket->flight_number }}</div>
        </div>

        {{-- Маршрут --}}
        <div class="col-lg-7">
            <div class="flight-route">
                <div class="text-center flex-fill">
                    <div class="flight-time">{{ $ticket->departure_time->format('H:i') }}</div>
                    <div class="fw-bold">{{ $ticket->departure_airport }}</div>
                    <small class="text-muted">{{ $ticket->departure_city }}</small>
                </div>

                <div class="text-center" style="min-width: 120px;">
                    <small class="text-muted d-block">{{ $ticket->duration_formatted }}</small>
                    <div class="flight-duration-line"></div>
                    @if ($ticket->is_direct)
                        <span class="badge bg-success mt-1">Прямой рейс</span>
                    @else
                        <span class="badge bg-warning text-dark mt-1">{{ $ticket->stops }}
                            пересадк{{ $ticket->stops == 1 ? 'а' : 'и' }}</span>
                    @endif
                </div>

                <div class="text-center flex-fill">
                    <div class="flight-time">{{ $ticket->arrival_time->format('H:i') }}</div>
                    <div class="fw-bold">{{ $ticket->arrival_airport }}</div>
                    <small class="text-muted">{{ $ticket->arrival_city }}</small>
                </div>
            </div>
        </div>

        {{-- Цена и кнопка --}}
        <div class="col-lg-3 text-center">
            <div class="fs-4 fw-bold text-primary mb-1">
                {{ number_format($displayPrice, 0, '.', ' ') }} ₽</div>
            <small class="text-muted d-block mb-3">за 1 пассажира</small>
            @if ($soldOut)
                <button class="btn btn-secondary w-100" disabled>Распродано</button>
            @else
                @guest
                    <button class="btn btn-secondary w-100" disabled>авторизуйтесь</button>
                @endguest
                @auth
                    <a href="{{ route('booking.create', $ticket->id) }}?class={{ $buyClass }}&passengers={{ $requested }}"
                        class="btn btn-primary w-100">
                        Купить билет
                    </a>
                @endauth
            @endif
        </div>
    </div>

    {{-- Дополнительная информация --}}
    <div class="row mt-3 pt-3 border-top">
        <div class="col-12">
            <small class="text-muted">
                <i class="bi bi-airplane me-1"></i>{{ $ticket->aircraft }} •
                <i class="bi bi-briefcase me-1"></i>Багаж {{ $ticket->baggage_allowance }} кг
                @if ($ticket->has_meal)
                    • <i class="bi bi-cup-hot me-1"></i>Питание включено
                @endif
                @if ($ticket->has_wifi)
                    • <i class="bi bi-wifi me-1"></i>Wi-Fi
                @endif
                @if ($soldOut)
                    • <span class="text-danger fw-bold">Осталось {{ $classSeats }} мест!</span>
                @elseif ($classSeats < 10)
                    • <span class="text-danger fw-bold">Осталось {{ $classSeats }} {{ $leftWord }}!</span>
                @endif
            </small>
        </div>
    </div>
</div>
