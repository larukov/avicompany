<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <x-stat-card icon="✈️" :value="$stats['flights']" label="Совершено перелётов" />
    </div>
    <div class="col-6 col-md-3">
        <x-stat-card icon="🌍" :value="$stats['countries']" label="Стран посещено" />
    </div>
    <div class="col-6 col-md-3">
        <x-stat-card icon="⭐" :value="number_format($stats['miles'], 0, '.', ' ')" label="Миль накоплено" />
    </div>
    <div class="col-6 col-md-3">
        <x-stat-card icon="🎫" :value="$stats['active']" label="Активных бронирований" />
    </div>
</div>