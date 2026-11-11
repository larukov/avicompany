@props(['title', 'items', 'active', 'stats' => null, 'type' => 'profile'])

<div class="card shadow-sm border-0 overflow-hidden {{ $type }}-sidebar" data-sidebar="{{ $type }}">
    <div class="bg-primary text-white fw-bold py-3 px-3">
        {{ $title }}
    </div>

    <ul class="list-group list-group-flush">
        @foreach ($items as $item)
            <li class="list-group-item {{ ($active ?? '') === $item['target'] ? 'active' : '' }} d-flex justify-content-between align-items-center"
                data-bs-target="#{{ $item['target'] }}" role="tab" data-bs-toggle="tab"
                aria-controls="{{ $item['target'] }}">
                <span>
                    <i
                        class="{{ $item['icon'] }} me-2 {{ ($active ?? '') === $item['target'] ? '' : $item['icon_color'] ?? '' }}"></i>
                    {{ $item['label'] }}
                </span>
                @if (isset($item['badge']))
                    <span
                        class="badge {{ ($active ?? '') === $item['target'] ? 'bg-white text-primary' : 'bg-primary text-white' }} rounded-pill">
                        {{ $item['badge'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ul>

    @if ($stats)
        <div class="bg-primary text-white text-center py-3">
            <small class="opacity-75 d-block">{{ $stats['label'] }}</small>
            <div class="fw-bold fs-4">{{ $stats['value'] }} <small class="fs-6 fw-normal">{{ $stats['sublabel'] }}</small>
            </div>
        </div>
    @endif
</div>