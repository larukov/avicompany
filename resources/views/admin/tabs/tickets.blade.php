<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold">Управление билетами <small class="text-muted fw-normal">({{ $totalTickets }})</small></div>
    <div class="card-body">
        <div class="input-group mb-3">
            <span class="input-group-text">🔍</span>
            <input type="text" class="form-control" id="ticketSearch" placeholder="Поиск по коду бронирования или пассажиру...">
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Код</th>
                        <th>Пассажир</th>
                        <th>Рейс</th>
                        <th>Маршрут</th>
                        <th>Класс</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="ticketsTableBody">
                    @forelse ($bookings as $booking)
                        @php
                            $passengerNames = $booking->passengers->map(fn($p) => $p->lastname . ' ' . mb_substr($p->firstname, 0, 1) . '.')->implode(', ');
                            $classLabels = ['economy' => 'Эконом', 'comfort' => 'Комфорт', 'business' => 'Бизнес'];
                            $statusMap = [
                                'pending' => ['👀 Ожидает', 'bg-secondary'],
                                'confirmed' => ['✓ Подтверждён', 'bg-success'],
                                'checked_in' => ['◷ Зарегистрирован', 'bg-info text-dark'],
                                'boarded' => ['✈️ На борту', 'bg-primary'],
                                'no_show' => ['✕ Не явился', 'bg-danger'],
                                'cancelled' => ['✕ Отменён', 'bg-dark'],
                                'completed' => ['✓ Завершён', 'bg-success'],
                            ];
                            [$statusLabel, $statusColor] = $statusMap[$booking->status] ?? [$booking->status, 'bg-secondary'];
                            $bookingRoute = $booking->ticket ? $booking->ticket->departure_city . ' → ' . $booking->ticket->arrival_city : '—';
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $booking->booking_code }}</td>
                            <td>{{ $passengerNames ?: '—' }}</td>
                            <td><span class="fw-bold text-primary">{{ $booking->ticket?->flight_number ?? '—' }}</span></td>
                            <td>{{ $bookingRoute }}</td>
                            <td>{{ $classLabels[$booking->class] ?? $booking->class }}</td>
                            <td><span class="badge badge-ticket {{ $statusColor }}">{{ $statusLabel }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editBookingModal{{ $booking->id }}">🔄</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Билетов пока нет</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>