@props([
    'icon' => null,
    'value',
    'label',
    'color' => 'primary',
])

<div class="card shadow-sm stat-card text-center p-3">
    @if ($icon)
        <div class="fs-2 mb-2">{{ $icon }}</div>
    @endif
    <h4 class="fw-bold mb-0">{{ $value }}</h4>
    <small class="text-muted">{{ $label }}</small>
</div>
