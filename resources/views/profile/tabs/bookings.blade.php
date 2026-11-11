<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 fw-bold">🎫 Мои бронирования</h5>
    </div>
    <div class="card-body">
        @php
            $grouped = [
                'all' => $bookings,
                'upcoming' => $bookings->whereIn('status', ['pending', 'confirmed', 'checked_in']),
                'completed' => $bookings->whereIn('status', ['completed', 'boarded', 'no_show']),
                'cancelled' => $bookings->where('status', 'cancelled'),
            ];
            $filters = [
                'all' => 'Все',
                'upcoming' => 'Предстоящие',
                'completed' => 'Завершённые',
                'cancelled' => 'Отменённые',
            ];
            $statusMap = [
                'confirmed' => '✓ Подтверждён',
                'pending' => '⏳ Ожидает оплаты',
                'checked_in' => '◷ Зарегистрирован',
                'boarded' => '✈️ На борту',
                'no_show' => '✕ Не явился',
                'cancelled' => '✕ Отменён',
                'completed' => '✓ Завершён',
            ];
            $statusTypeMap = [
                'confirmed' => 'success',
                'pending' => 'warning',
                'checked_in' => 'info',
                'boarded' => 'primary',
                'no_show' => 'danger',
                'cancelled' => 'danger',
                'completed' => 'secondary',
            ];
        @endphp

        @if ($bookings->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-ticket-perforated fs-1 d-block mb-3"></i>
                <h5>У вас пока нет бронирований</h5>
                <p>Найдите подходящий рейс и оформите первый билет.</p>
            </div>
        @else
            <ul class="nav nav-pills booking-filter mb-4" id="bookingFilter">
                @foreach ($filters as $key => $label)
                    <li class="nav-item me-2">
                        <button class="nav-link rounded-pill {{ $loop->first ? 'active' : '' }} booking-filter-btn"
                            data-filter="{{ $key }}">
                            {{ $label }} ({{ $grouped[$key]->count() }})
                        </button>
                    </li>
                @endforeach
            </ul>

            <div id="bookingsList">
                @foreach ($bookings as $booking)
                    @php
                        $ticket = $booking->ticket;
                        $group = match ($booking->status) {
                            'pending', 'confirmed', 'checked_in' => 'upcoming',
                            'completed', 'boarded', 'no_show' => 'completed',
                            'cancelled' => 'cancelled',
                        };
                        $dateLabel = $ticket?->departure_time
                            ? $ticket->departure_time->format('d F Y, H:i')
                            : '—';
                    @endphp

                    <div class="booking-item" data-group="{{ $group }}">
                        <x-booking-card
                            :code="$booking->booking_code"
                            :flight="$ticket?->flight_number ?? '—'"
                            :class_type="ucfirst($booking->class)"
                            :status="$statusMap[$booking->status] ?? $booking->status"
                            :status_type="$statusTypeMap[$booking->status] ?? 'default'"
                            :from_city="$ticket ? $ticket->departure_city . ' ' . $ticket->departure_airport : '—'"
                            :from_time="$ticket?->departure_time?->format('H:i') ?? '—'"
                            :from_date="$dateLabel"
                            :to_city="$ticket ? $ticket->arrival_city . ' ' . $ticket->arrival_airport : '—'"
                            :to_time="$ticket?->arrival_time?->format('H:i') ?? '—'"
                            :to_date="$ticket?->arrival_time?->format('d F Y, H:i') ?? '—'"
                            :duration="$ticket?->duration_formatted ?? '—'"
                            :passengers="$booking->passengers_count"
                            :price="number_format($booking->total_price, 0, '.', ' ') . ' ₽'"
                        >
                            <button class="btn btn-sm btn-outline-primary js-booking-details"
                                data-code="{{ $booking->booking_code }}"
                                data-passengers='{{ $booking->passengers->toJson() }}'
                                data-services='{{ $booking->services->toJson() }}'>
                                📋 Подробнее
                            </button>
                        </x-booking-card>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>