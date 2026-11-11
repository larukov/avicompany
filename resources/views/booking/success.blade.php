@extends('layouts.app')

@section('title', 'Билет оформлен — SkyAir')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <div class="fs-1 text-success mb-3">✓</div>
                        <h2 class="fw-bold mb-3">Билеты успешно оформлены!</h2>
                        <p class="text-muted mb-4">На ваш email отправлено подтверждение бронирования</p>

                        <div class="bg-light rounded p-4 mb-4">
                            <h5 class="fw-bold mb-3">Код бронирования: <span
                                    class="text-primary">{{ $booking->booking_code }}</span></h5>
                            <div class="row g-3 text-start">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Рейс</small>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($booking->ticket->airline_logo_url)
                                            <img src="{{ $booking->ticket->airline_logo_url }}"
                                                alt="{{ $booking->ticket->airline }}"
                                                style="height: 24px; max-width: 50px; object-fit: contain;">
                                        @endif
                                        <strong>{{ $booking->ticket->flight_number }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Маршрут</small>
                                    <strong>{{ $booking->ticket->departure_city }} →
                                        {{ $booking->ticket->arrival_city }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Дата</small>
                                    <strong>{{ $booking->ticket->departure_time->format('d.m.Y H:i') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Класс</small>
                                    <strong>{{ ucfirst($booking->class) }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Пассажиров</small>
                                    <strong>{{ $booking->passengers_count }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Сумма</small>
                                    <strong class="text-primary">{{ number_format($booking->total_price, 0, '.', ' ') }}
                                        ₽</strong>
                                </div>
                                @if ($booking->miles_used > 0)
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Скидка милями</small>
                                        <strong class="text-success">−{{ number_format($booking->miles_used, 0, '.', ' ') }}
                                            миль</strong>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Миль начислено</small>
                                    <strong>+{{ number_format($booking->miles_earned, 0, '.', ' ') }} миль</strong>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('profile') }}#tab-bookings" class="btn btn-outline-primary">
                                <i class="bi bi-person me-2"></i>Мои бронирования
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="bi bi-house me-2"></i>На главную
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection