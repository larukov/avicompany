@props([
    'code',
    'flight',
    'class_type',
    'status',
    'status_type' => 'default',
    'from_city',
    'from_airport' => null,
    'from_time',
    'from_date',
    'to_city',
    'to_airport' => null,
    'to_time',
    'to_date',
    'duration',
    'type' => 'direct',
    'passengers' => 1,
    'price' => null,
    'alert' => null,
    'alert_type' => 'warning',
])

@php
    $colors = [
        'success' => 'bg-success',
        'warning' => 'bg-warning text-dark',
        'danger' => 'bg-danger',
        'info' => 'bg-info text-dark',
        'secondary' => 'bg-secondary',
        'default' => 'bg-primary',
    ];
    $color = $colors[$status_type] ?? $colors['default'];
@endphp

<div class="card booking-card {{ $status_type }} mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <span class="badge {{ $color }}">{{ $status }}</span>
                <h6 class="mt-2 mb-0">Код бронирования: <strong>{{ $code }}</strong></h6>
            </div>
            <div class="text-end">
                <small class="text-muted">Рейс {{ $flight }} • {{ $class_type }}</small>
            </div>
        </div>

        @if ($alert)
            <div class="alert alert-{{ $alert_type }} py-2 mb-3">
                <small>️ <strong>Внимание:</strong> {{ $alert }}</small>
            </div>
        @endif

        <div class="row align-items-center">
            <div class="col-md-5 text-center">
                <h4 class="fw-bold mb-0">{{ $from_city }}</h4>
                @if ($from_airport)
                    <small class="text-muted">{{ $from_airport }}</small>
                @endif
                <div class="fs-5 fw-bold mt-2">{{ $from_time }}</div>
                <small>{{ $from_date }}</small>
            </div>
            <div class="col-md-2 text-center">
                <div class="text-muted">✈️</div>
                <small>{{ $duration }} • {{ $type === 'direct' ? 'Прямой' : $type }}</small>
            </div>
            <div class="col-md-5 text-center">
                <h4 class="fw-bold mb-0">{{ $to_city }}</h4>
                @if ($to_airport)
                    <small class="text-muted">{{ $to_airport }}</small>
                @endif
                <div class="fs-5 fw-bold mt-2">{{ $to_time }}</div>
                <small>{{ $to_date }}</small>
            </div>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <small class="text-muted">👥 Пассажиры: {{ $passengers }}
                    @if ($price)
                        • Сумма: <strong>{{ $price }}</strong>
                    @endif
                </small>
            </div>
            <div class="d-flex gap-2">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>