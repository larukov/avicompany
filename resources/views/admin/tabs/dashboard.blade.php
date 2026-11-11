<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <x-stat-card icon="✈️" :value="number_format($stats['activeFlights'])" label="Активных рейсов" />
    </div>
    <div class="col-6 col-lg-3">
        <x-stat-card icon="🎫" :value="number_format($stats['soldTickets'])" label="Продано билетов" color="green" />
    </div>
    <div class="col-6 col-lg-3">
        <x-stat-card icon="💰" :value="number_format($stats['revenue'], 0, '.', ' ') . ' ₽'" label="Выручка"
            color="orange" />
    </div>
    <div class="col-6 col-lg-3">
        <x-stat-card icon="⚠️" :value="number_format($stats['delayedFlights'])" label="Задержанных рейсов"
            color="red" />
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">Продажи билетов за неделю</div>
            <div class="card-body">
                <div class="d-flex align-items-end gap-2" style="height: 220px;">
                    @foreach ($weekChart as $i => $val)
                        @php
                            $max = max(1, max($weekChart));
                            $pct = ($val / $max) * 100;
                            // Высота столбца в пикселях (оставляем место для подписей)
                            $px = $pct == 0 ? 4 : (int) round($pct * 0.65);
                        @endphp
                        <div class="flex-grow-1 text-center h-100 d-flex flex-column justify-content-end">
                            <div class="fs-6 fw-bold">{{ $val }}</div>
                            <div class="chart-bar mx-auto" style="width: 60%; height: {{ $px }}px;"></div>
                            <small class="text-muted mt-1">{{ $days[$i] }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">Статусы билетов</div>
            <div class="card-body">
                @php
                    $statusCols = [
                        'confirmed' => 'bg-success',
                        'checked_in' => 'bg-info',
                        'boarded' => 'bg-primary',
                        'no_show' => 'bg-danger',
                        'cancelled' => 'bg-dark',
                    ];
                    $statusLabels = [
                        'confirmed' => 'Подтверждено',
                        'checked_in' => 'Зарегистрировано',
                        'boarded' => 'На борту',
                        'no_show' => 'Не явились',
                        'cancelled' => 'Отменено',
                    ];
                    $statusMax = max(1, max($statusCounts));
                @endphp
                @foreach ($statusLabels as $key => $label)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>{{ $label }}</small>
                            <strong>{{ number_format($statusCounts[$key] ?? 0) }}</strong>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar {{ $statusCols[$key] }}"
                                style="width: {{ (($statusCounts[$key] ?? 0) / $statusMax) * 100 }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Популярные направления --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-bold">Популярные направления <small class="text-muted fw-normal">Топ-5
            маршрутов</small></div>
    <div class="card-body">
        @php
            $totalRoutes = max(1, $popularRoutes->sum());
        @endphp
        @forelse ($popularRoutes as $route => $count)
            @php
                // Доля маршрута от общего количества проданных билетов
                $pct = ($count / $totalRoutes) * 100;
                $w1 = $count % 10;
                $w2 = $count % 100;
                $word = ($w1 == 1 && $w2 != 11) ? 'билет' : (($w1 >= 2 && $w1 <= 4 && ($w2 < 10 || $w2 >= 20)) ? 'билета' : 'билетов');
            @endphp
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <strong>{{ $route }}</strong>
                    <span class="text-muted">{{ number_format($count) }} {{ $word }}</span>
                </div>
                <div class="progress" style="height:8px;">
                    <div class="progress-bar" style="width: {{ $pct }}%;"></div>
                </div>
            </div>
        @empty
            <p class="text-muted text-center py-3 mb-0">Пока нет проданных билетов</p>
        @endforelse
    </div>
</div>