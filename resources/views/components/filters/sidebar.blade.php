<div class="border bg-white rounded-3 shadows p-3 filters-sidebar">

    <form method="GET" action="{{ route('tickets') }}">
        {{-- Сохраняем контекст поиска при применении фильтров --}}
        <input type="hidden" name="trip_type" value="{{ request('trip_type', 'round') }}">
        <input type="hidden" name="from" value="{{ request('from') }}">
        <input type="hidden" name="to" value="{{ request('to') }}">
        <input type="hidden" name="date" value="{{ request('date') }}">
        <input type="hidden" name="return_date" value="{{ request('return_date') }}">
        <input type="hidden" name="passengers" value="{{ request('passengers', 1) }}">
        <input type="hidden" name="sort" value="{{ request('sort', 'price_asc') }}">

        {{-- Пересадки --}}
        <x-filters.group title="Пересадки">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="stops[]" value="direct" id="direct"
                    {{ in_array('direct', request('stops', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="direct">Прямой рейс</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="stops[]" value="1" id="one-stop"
                    {{ in_array('1', request('stops', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="one-stop">1 пересадка</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="stops[]" value="2" id="two-stops"
                    {{ in_array('2', request('stops', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="two-stops">2 пересадки</label>
            </div>
        </x-filters.group>

        {{-- Время вылета --}}
        <x-filters.group title="Время вылета">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="departure_time[]" value="morning" id="morning"
                    {{ in_array('morning', request('departure_time', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="morning">Утро (6:00-12:00)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="departure_time[]" value="day" id="day"
                    {{ in_array('day', request('departure_time', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="day">День (12:00-18:00)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="departure_time[]" value="evening" id="evening"
                    {{ in_array('evening', request('departure_time', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="evening">Вечер (18:00-00:00)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="departure_time[]" value="night" id="night"
                    {{ in_array('night', request('departure_time', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="night">Ночь (00:00-6:00)</label>
            </div>
        </x-filters.group>

        {{-- Цена --}}
        <x-filters.group title="Цена, ₽">
            <div class="row g-2">
                <div class="col-6">
                    <input type="number" name="price_min" class="form-control form-control-sm" placeholder="От"
                        value="{{ request('price_min') }}">
                </div>
                <div class="col-6">
                    <input type="number" name="price_max" class="form-control form-control-sm" placeholder="До"
                        value="{{ request('price_max') }}">
                </div>
            </div>
        </x-filters.group>

        {{-- Авиакомпании: активные компании из админки + рейсы без компании --}}
        <x-filters.group title="Авиакомпании">
            @php
                // Компании, добавленные в админке. Значение фильтра — имя компании (подставляется в airline рейса).
                $activeCompanies = \App\Models\Company::where('is_active', true)
                    ->orderBy('name')
                    ->pluck('name');

                // Рейсы, не привязанные к компании (старые записи) с уникальными названиями авиалиний,
                // которых нет среди активных компаний.
                $legacyAirlines = \App\Models\Ticket::whereNull('company_id')
                    ->whereNotNull('airline')
                    ->distinct()
                    ->orderBy('airline')
                    ->pluck('airline');

                $filterAirlines = $activeCompanies->concat($legacyAirlines)->unique()->values();
            @endphp
            @forelse ($filterAirlines as $airline)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="airline[]" value="{{ $airline }}"
                        id="airline-{{ $loop->index }}"
                        {{ in_array($airline, request('airline', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="airline-{{ $loop->index }}">{{ $airline }}</label>
                </div>
            @empty
                <small class="text-muted">Нет авиакомпаний</small>
            @endforelse
        </x-filters.group>

        {{-- Класс обслуживания --}}
        <x-filters.group title="Класс обслуживания">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="class" value="" id="class-all"
                    {{ !request('class') ? 'checked' : '' }}>
                <label class="form-check-label" for="class-all">Все классы</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="class" value="economy" id="economy"
                    {{ request('class') === 'economy' ? 'checked' : '' }}>
                <label class="form-check-label" for="economy">Эконом</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="class" value="comfort" id="comfort"
                    {{ request('class') === 'comfort' ? 'checked' : '' }}>
                <label class="form-check-label" for="comfort">Комфорт</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="class" value="business" id="business"
                    {{ request('class') === 'business' ? 'checked' : '' }}>
                <label class="form-check-label" for="business">Бизнес</label>
            </div>
        </x-filters.group>

        {{-- Удобства --}}
        <x-filters.group title="Удобства">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="baggage" value="1" id="baggage"
                    {{ request('baggage') ? 'checked' : '' }}>
                <label class="form-check-label" for="baggage">С багажом</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="meal" value="1" id="meal"
                    {{ request('meal') ? 'checked' : '' }}>
                <label class="form-check-label" for="meal">С питанием</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="wifi" value="1" id="wifi"
                    {{ request('wifi') ? 'checked' : '' }}>
                <label class="form-check-label" for="wifi">Wi-Fi на борту</label>
            </div>
        </x-filters.group>

        <button type="submit" class="btn btn-primary w-100 mb-2">Применить фильтры</button>
        <a href="{{ route('tickets') }}" class="btn btn-outline-secondary w-100">Сбросить</a>
    </form>

</div>
