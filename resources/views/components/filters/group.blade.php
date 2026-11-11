@props(['title'])

<div class="mb-4">
    <h3 class="fs-5 fw-bold mb-3">{{ $title }}</h3>
    {{ $slot }}
</div>