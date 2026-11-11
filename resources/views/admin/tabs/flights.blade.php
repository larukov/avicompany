<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-bold">Управление рейсами <small
                class="text-muted fw-normal">({{ $flights->count() }})</small></span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#flightModal">+ Добавить
            рейс</button>
    </div>
    <div class="card-body">
        <div class="input-group mb-3">
            <span class="input-group-text">🔍</span>
            <input type="text" class="form-control" id="flightSearch"
                placeholder="Поиск по номеру рейса или маршруту...">
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Рейс</th>
                        <th>Маршрут</th>
                        <th>Дата и время</th>
                        <th>Самолёт</th>
                        <th>Продано / Всего</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="flightsTableBody">
                    @forelse ($flights as $flight)
                        @php
                            $sold = $flight->total_seats - max($flight->available_seats_economy, 0);
                            $pct = $flight->total_seats ? round(($sold / $flight->total_seats) * 100) : 0;
                            $statusColors = [
                                'scheduled' => 'bg-info text-dark',
                                'boarding' => 'bg-warning text-dark',
                                'departed' => 'bg-success',
                                'arrived' => 'bg-secondary',
                                'delayed' => 'bg-warning text-dark',
                                'cancelled' => 'bg-danger',
                            ];
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($flight->airline_logo_url)
                                        <img src="{{ $flight->airline_logo_url }}" alt="{{ $flight->airline }}"
                                            style="height: 24px; max-width: 40px; object-fit: contain;">
                                    @endif
                                    <span class="fw-bold text-primary">{{ $flight->flight_number }}</span>
                                </div>
                                <small class="text-muted">{{ $flight->airline }}</small>
                            </td>
                            <td>{{ $flight->departure_city }} → {{ $flight->arrival_city }}</td>
                            <td>{{ $flight->departure_time->format('d.m.Y H:i') }}</td>
                            <td>{{ $flight->aircraft }}</td>
                            <td style="min-width:120px;">
                                <div>{{ $sold }} / {{ $flight->total_seats }}</div>
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar {{ $pct > 75 ? 'bg-danger' : ($pct > 45 ? 'bg-warning' : 'bg-success') }}"
                                        style="width:{{ $pct }}%;"></div>
                                </div>
                            </td>
                            <td><span
                                    class="badge badge-ticket {{ $statusColors[$flight->status] ?? 'bg-secondary' }}">{{ $flight->status_text }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editFlightModal{{ $flight->id }}">✏️</button>
                                    <form action="{{ route('admin.flights.destroy', $flight) }}" method="POST"
                                        onsubmit="return confirm('Удалить рейс {{ $flight->flight_number }}?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Рейсов пока нет</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>