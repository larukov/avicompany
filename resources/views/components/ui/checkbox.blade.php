@props([
    'name',
    'value',
    'label',
    'id' => null,
    'checked' => false,
])

@php
    $inputId = $id ?? 'checkbox-' . $name . '-' . $value . '-' . uniqid();
@endphp

<div class="form-check">
    <input class="form-check-input" type="checkbox" name="{{ $name }}" value="{{ $value }}" id="{{ $inputId }}"
        @if($checked) checked @endif {{ $attributes->merge(['class' => '']) }}>
    <label class="form-check-label" for="{{ $inputId }}">
        {{ $label ?? $slot }}
    </label>
</div>