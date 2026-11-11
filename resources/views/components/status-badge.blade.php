@props(['status', 'type' => 'default'])

@php
    $colors = [
        'success' => 'bg-success',
        'warning' => 'bg-warning text-dark',
        'danger' => 'bg-danger',
        'info' => 'bg-info text-dark',
        'secondary' => 'bg-secondary',
        'default' => 'bg-primary',
    ];
    $color = $colors[$type] ?? $colors['default'];
@endphp

<span class="badge {{ $color }}">{{ $status }}</span>
