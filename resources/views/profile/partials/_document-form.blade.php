@php
    $document = $document ?? null;
@endphp

<x-ui.select name="type" label="Тип документа" required :options="[
    'passport_rf' => 'Паспорт РФ',
    'passport_international' => 'Загранпаспорт',
    'driver_license' => 'Водительское удостоверение',
    'other' => 'Другой документ',
]" :value="$document->type ?? null" />

<div class="row g-3">
    <div class="col-4">
        <x-ui.input name="series" label="Серия" required placeholder="1234" data-mask="9999"
            :value="$document->series ?? null" />
    </div>
    <div class="col-8">
        <x-ui.input name="number" label="Номер" required data-mask="999999" :value="$document->number ?? null" />
    </div>
</div>

<x-ui.input name="issued_by" label="Кем выдан" required placeholder="ОВД района..."
    :value="$document->issued_by ?? null" />

<div class="row g-3">
    <div class="col-6">
        <x-ui.input name="issued_at" type="date" label="Дата выдачи" required
            :value="$document?->issued_at?->format('Y-m-d')" />
    </div>
    <div class="col-6">
        <x-ui.input name="expires_at" type="date" label="Действителен до" required
            :value="$document?->expires_at?->format('Y-m-d')" />
    </div>
</div>
