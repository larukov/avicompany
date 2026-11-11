<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0 fw-bold">⭐ Программа лояльности SkyAir Bonus</h5>
        <small class="opacity-75">Копите мили за каждый перелёт и оплачивайте ими билеты</small>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card bg-primary text-white text-center p-3 border-0">
                    <div class="fs-3 fw-bold">{{ number_format($stats['miles'], 0, '.', ' ') }}</div>
                    <small>миль на счету</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center p-3 border-0">
                    <div class="fs-3 fw-bold text-success">+ {{ number_format($milesHistory->where('type', 'earned')->sum('amount'), 0, '.', ' ') }}</div>
                    <small class="text-muted">Миль начислено</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center p-3 border-0">
                    <div class="fs-3 fw-bold">✈️ {{ $stats['flights'] }}</div>
                    <small class="text-muted">Совершено перелётов</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center p-3 border-0">
                    <div class="fs-3 fw-bold text-danger">− {{ number_format($milesHistory->where('type', 'spent')->sum('amount'), 0, '.', ' ') }}</div>
                    <small class="text-muted">Миль потрачено</small>
                </div>
            </div>
        </div>

        <h6 class="fw-bold mb-3">История начислений</h6>
        <div class="list-group mb-4">
            @forelse ($milesHistory as $entry)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $entry->type === 'earned' ? '🛫 Заработано' : '🎫 Списано' }}</strong>
                        @if ($entry->booking && $entry->booking->ticket)
                            <br>
                            <small class="text-muted">
                                {{ $entry->booking->ticket->departure_city }} → {{ $entry->booking->ticket->arrival_city }}
                                • {{ $entry->booking->booking_code }}
                                • {{ $entry->created_at->format('d.m.Y') }}
                            </small>
                        @else
                            <br>
                            <small class="text-muted">{{ $entry->created_at->format('d.m.Y') }} {{ $entry->description }}</small>
                        @endif
                    </div>
                    <span class="badge {{ $entry->type === 'earned' ? 'bg-success' : 'bg-danger' }} fs-6">
                        {{ $entry->type === 'earned' ? '+' : '−' }}{{ number_format($entry->amount, 0, '.', ' ') }} миль
                    </span>
                </div>
            @empty
                <div class="text-muted text-center py-4">
                    Пока нет операций с милями. Совершите первый перелёт — и мили появятся здесь.
                </div>
            @endforelse
        </div>

        <h6 class="fw-bold mb-3">Как получать мили</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card p-3 border-0 bg-light">
                    <h6>🛫 За перелёты</h6>
                    <p class="small text-muted mb-2">Начисляйте 10% от стоимости билета в милях</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3 border-0 bg-light">
                    <h6>💰 Оплата милями</h6>
                    <p class="small text-muted mb-2">Списывайте мили при покупке билета (1 миля = 1 ₽ скидки)</p>
                </div>
            </div>
        </div>
    </div>
</div>