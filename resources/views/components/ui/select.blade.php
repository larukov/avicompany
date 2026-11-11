@props([
    'name',
    'label' => null,
    'options' => [],
    'required' => false,
    'placeholder' => '— Выберите —',
    'value' => null,
])

@php
    $inputId = 'select-' . $name . '-' . uniqid();
    $oldValue = old($name, $value ?? '');
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    <select name="{{ $name }}" id="{{ $inputId }}" class="form-select @error($name) is-invalid @enderror"
        @if ($required) required @endif {{ $attributes }}>
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" {{ $oldValue == $optValue ? 'selected' : '' }}>
                {{ $optLabel }}
            </option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>