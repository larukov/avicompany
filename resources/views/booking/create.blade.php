@extends('layouts.app')

@section('title', 'Оформление билета — SkyAir')

@section('css')
    <link rel="stylesheet" href="@vasset('css/create.css')">
@endsection

@section('content')
    <div class="container my-5">
        {{-- Stepper --}}
        <div class="step-indicator">
            <div class="step active" data-step="1">
                <div class="step-circle">1</div>
                <div class="step-label">Рейс</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-circle">2</div>
                <div class="step-label">Пассажиры</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-circle">3</div>
                <div class="step-label">Услуги</div>
            </div>
            <div class="step" data-step="4">
                <div class="step-circle">4</div>
                <div class="step-label">Оплата</div>
            </div>
        </div>

        <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" novalidate>
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
            <input type="hidden" name="passengers_count" id="passengersCount" value="{{ $passengers }}">

            <div class="row">
                <div class="col-lg-8">
                    {{-- Шаг 1: Рейс --}}
                    <div class="step-content active" id="step1">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold">✈️ Информация о рейсе</h5>
                                <small class="text-muted">Проверьте детали перед оформлением</small>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            @if ($ticket->airline_logo_url)
                                                <img src="{{ $ticket->airline_logo_url }}" alt="{{ $ticket->airline }}"
                                                    style="height: 32px; max-width: 70px; object-fit: contain;">
                                            @endif
                                            <h6 class="fw-bold mb-0">Рейс {{ $ticket->flight_number }}</h6>
                                        </div>
                                        <p class="text-muted mb-0">
                                            {{ $ticket->departure_time->format('d.m.Y') }} • Выполняет
                                            {{ $ticket->airline }}
                                        </p>
                                        @if ($ticket->is_direct)
                                            <span class="badge bg-success mt-2">Прямой рейс</span>
                                        @else
                                            <span class="badge bg-warning text-dark mt-2">{{ $ticket->stops }}
                                                пересадк{{ $ticket->stops == 1 ? 'а' : 'и' }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row align-items-center mb-4">
                                    <div class="col-5 text-center">
                                        <div class="fs-4 fw-bold">{{ $ticket->departure_time->format('H:i') }}</div>
                                        <div class="fw-bold">{{ $ticket->departure_airport }}</div>
                                        <small class="text-muted">{{ $ticket->departure_city }}</small>
                                    </div>
                                    <div class="col-2 text-center">
                                        <div class="text-muted fs-3">✈️</div>
                                        <small>{{ $ticket->duration_formatted }}</small>
                                    </div>
                                    <div class="col-5 text-center">
                                        <div class="fs-4 fw-bold">{{ $ticket->arrival_time->format('H:i') }}</div>
                                        <div class="fw-bold">{{ $ticket->arrival_airport }}</div>
                                        <small class="text-muted">{{ $ticket->arrival_city }}</small>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="border rounded p-3">
                                            <small class="text-muted d-block">Самолёт</small>
                                            <strong>{{ $ticket->aircraft }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border rounded p-3">
                                            <small class="text-muted d-block">Мест осталось</small>
                                            <strong>{{ $ticket->available_seats_economy }} мест</strong>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-3">Выберите класс обслуживания:</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="class-card {{ $class === 'economy' ? 'selected' : '' }}"
                                            onclick="selectClass('economy', {{ $ticket->price_economy }})">
                                            <h6 class="fw-bold">Эконом</h6>
                                            <div class="fs-4 fw-bold text-primary mb-2">
                                                {{ number_format($ticket->price_economy, 0, '.', ' ') }} ₽</div>
                                            <small class="text-muted">Стандартное место</small>
                                        </div>
                                    </div>
                                    @if ($ticket->price_comfort)
                                        <div class="col-md-4">
                                            <div class="class-card {{ $class === 'comfort' ? 'selected' : '' }}"
                                                onclick="selectClass('comfort', {{ $ticket->price_comfort }})">
                                                <h6 class="fw-bold">Комфорт</h6>
                                                <div class="fs-4 fw-bold text-primary mb-2">
                                                    {{ number_format($ticket->price_comfort, 0, '.', ' ') }} ₽</div>
                                                <small class="text-muted">Увеличенное место</small>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($ticket->price_business)
                                        <div class="col-md-4">
                                            <div class="class-card {{ $class === 'business' ? 'selected' : '' }}"
                                                onclick="selectClass('business', {{ $ticket->price_business }})">
                                                <h6 class="fw-bold">Бизнес</h6>
                                                <div class="fs-4 fw-bold text-primary mb-2">
                                                    {{ number_format($ticket->price_business, 0, '.', ' ') }} ₽</div>
                                                <small class="text-muted">Максимальный комфорт</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" name="class" id="selectedClass" value="{{ $class }}">
                                <input type="hidden" name="base_price" id="basePrice"
                                    value="{{ $class === 'economy' ? $ticket->price_economy : ($class === 'comfort' ? $ticket->price_comfort : $ticket->price_business) }}">

                                <hr>

                                <h6 class="fw-bold mb-3">Количество пассажиров:</h6>
                                <div class="row g-2 mb-3">
                                    @for ($n = 1; $n <= 9; $n++)
                                        <div class="col-4 col-md-3 col-lg-2">
                                            <div class="class-card passenger-count-card text-center py-3 {{ $passengers == $n ? 'selected' : '' }}"
                                                data-count="{{ $n }}">
                                                <div class="fs-4 fw-bold">{{ $n }}</div>
                                                <small class="text-muted">пасс.</small>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary px-4" onclick="nextStep(2)">Далее: Пассажиры
                                →</button>
                        </div>
                    </div>

                    {{-- Шаг 2: Пассажиры --}}
                    <div class="step-content" id="step2">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold">👥 Данные пассажиров</h5>
                                <small class="text-muted" id="passengerHeader">Пассажир 1 из {{ $passengers }}</small>
                                <button type="button" class="btn btn-sm btn-outline-primary float-end" id="fillMyData">
                                    👤 Заполнить моими данными
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="passengersContainer">
                                    @php
                                        $defaultDoc = $user->documents->first();
                                    @endphp
                                    @for ($i = 0; $i < 9; $i++)
                                        @php
                                            $showFirst = $i === 0;
                                        @endphp
                                        <div class="passenger-block mb-4 {{ $i < $passengers ? '' : 'd-none disabled-block' }}"
                                            data-passenger="{{ $i }}">
                                            <h6 class="fw-bold mb-3">Пассажир {{ $i + 1 }}</h6>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <x-ui.input name="passengers[{{ $i }}][lastname]"
                                                        label="Фамилия " required :value="$showFirst ? $user->lastname : ''" />
                                                </div>
                                                <div class="col-md-4">
                                                    <x-ui.input name="passengers[{{ $i }}][firstname]"
                                                        label="Имя " required :value="$showFirst ? $user->name : ''" />
                                                </div>
                                                <div class="col-md-4">
                                                    <x-ui.input name="passengers[{{ $i }}][patronomic]"
                                                        label="Отчество" :value="$showFirst && $user->patronomic
                                                            ? $user->patronomic
                                                            : ''" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-ui.input name="passengers[{{ $i }}][birth_date]"
                                                        type="date" label="Дата рождения " required
                                                        :value="$showFirst && $user->birth_date
                                                            ? $user->birth_date->format('Y-m-d')
                                                            : ''" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-ui.select name="passengers[{{ $i }}][gender]"
                                                        label="Пол " required :options="['male' => 'Мужской', 'female' => 'Женский']" :value="$showFirst ? $user->gender : ''" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-ui.select name="passengers[{{ $i }}][document_type]"
                                                        label="Тип документа " required :options="[
                                                            'passport_rf' => 'Паспорт РФ',
                                                            'passport_international' => 'Загранпаспорт',
                                                        ]"
                                                        :value="$showFirst && $defaultDoc ? $defaultDoc->type : ''" />
                                                </div>
                                                <div class="col-md-3">
                                                    <x-ui.input name="passengers[{{ $i }}][document_series]"
                                                        label="Серия" placeholder="1234" required data-mask="9999"
                                                        :value="$showFirst && $defaultDoc ? $defaultDoc->series : ''" />
                                                </div>
                                                <div class="col-md-3">
                                                    <x-ui.input name="passengers[{{ $i }}][document_number]"
                                                        label="Номер " required data-mask="999999"
                                                        :value="$showFirst && $defaultDoc ? $defaultDoc->number : ''" />
                                                </div>
                                                <div class="col-12">
                                                    <x-ui.input name="passengers[{{ $i }}][document_issued_by]"
                                                        label="Кем выдан" required :value="$showFirst && $defaultDoc
                                                            ? $defaultDoc->issued_by
                                                            : ''" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-ui.input name="passengers[{{ $i }}][document_issued_at]"
                                                        type="date" label="Дата выдачи" required :value="$showFirst && $defaultDoc && $defaultDoc->issued_at
                                                            ? $defaultDoc->issued_at->format('Y-m-d')
                                                            : ''" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-ui.input
                                                        name="passengers[{{ $i }}][document_expires_at]"
                                                        type="date" label="Действителен до" required
                                                        :value="$showFirst && $defaultDoc && $defaultDoc->expires_at
                                                            ? $defaultDoc->expires_at->format('Y-m-d')
                                                            : ''" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-ui.input name="passengers[{{ $i }}][phone]"
                                                        type="tel" label="Телефон" required
                                                        placeholder="+7 (999) 123-45-67" :value="$showFirst ? $user->phone ?? $user->tel : ''" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-ui.input name="passengers[{{ $i }}][email]"
                                                        type="email" label="Email" required :value="$showFirst ? $user->email : ''" />
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(1)">←
                                Назад</button>
                            <button type="button" class="btn btn-primary px-4" onclick="nextStep(3)">Далее: Услуги
                                →</button>
                        </div>
                    </div>

                    {{-- Шаг 3: Услуги --}}
                    <div class="step-content" id="step3">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold">🎁 Дополнительные услуги</h5>
                                <small class="text-muted">Сделайте поездку комфортнее</small>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="service-card">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                    value="extra_baggage" id="extra_baggage">
                                                <label class="form-check-label w-100" for="extra_baggage">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>🧳 Дополнительный багаж</strong>
                                                            <br><small class="text-muted">Ещё одно место багажа до 23
                                                                кг</small>
                                                        </div>
                                                        <strong class="text-primary">1 500 ₽</strong>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="service-card">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                    value="seat_selection" id="seat_selection">
                                                <label class="form-check-label w-100" for="seat_selection">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>💺 Выбор места</strong>
                                                            <br><small class="text-muted">Выберите удобное место в
                                                                салоне</small>
                                                        </div>
                                                        <strong class="text-primary">800 ₽</strong>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="service-card">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                    value="special_meal" id="special_meal">
                                                <label class="form-check-label w-100" for="special_meal">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>🍽️ Специальное питание</strong>
                                                            <br><small class="text-muted">Вегетарианское, детское или
                                                                диетическое</small>
                                                        </div>
                                                        <strong class="text-primary">600 ₽</strong>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="service-card">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                    value="insurance" id="insurance">
                                                <label class="form-check-label w-100" for="insurance">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>🛡️ Страховка</strong>
                                                            <br><small class="text-muted">Медицинская страховка на время
                                                                поездки</small>
                                                        </div>
                                                        <strong class="text-primary">500 ₽</strong>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="service-card">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                    value="priority_boarding" id="priority_boarding">
                                                <label class="form-check-label w-100" for="priority_boarding">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>⭐ Приоритетная посадка</strong>
                                                            <br><small class="text-muted">Посадка в самолёт в первой
                                                                очереди</small>
                                                        </div>
                                                        <strong class="text-primary">1 200 ₽</strong>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="service-card">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                    value="business_lounge" id="business_lounge">
                                                <label class="form-check-label w-100" for="business_lounge">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>🛋️ Бизнес-зал</strong>
                                                            <br><small class="text-muted">Доступ в бизнес-зал
                                                                аэропорта</small>
                                                        </div>
                                                        <strong class="text-primary">2 000 ₽</strong>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(2)">←
                                Назад</button>
                            <button type="button" class="btn btn-primary px-4" onclick="nextStep(4)">Далее: Оплата
                                →</button>
                        </div>
                    </div>

                    {{-- Шаг 4: Оплата --}}
                    <div class="step-content" id="step4">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold">💳 Оплата</h5>
                                <small class="text-muted">Выберите способ оплаты</small>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            value="card" id="pay_card" checked>
                                        <label class="form-check-label" for="pay_card">💳 Банковская карта</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            value="sbp" id="pay_sbp">
                                        <label class="form-check-label" for="pay_sbp">📱 СБП</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            value="bank" id="pay_bank">
                                        <label class="form-check-label" for="pay_bank">🏦 Интернет-банк</label>
                                    </div>
                                </div>

                                <div id="cardFields">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <x-ui.input name="card_number" label="Номер карты " required
                                                placeholder="0000 0000 0000 0000" data-mask="9999 9999 9999 9999" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-ui.input name="card_expiry" label="Срок действия " required
                                                placeholder="MM/YY" data-mask="99/99" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-ui.input name="card_cvv" label="CVV/CVC " required placeholder="123"
                                                data-mask="999" />
                                        </div>
                                        <div class="col-12">
                                            <x-ui.input name="card_holder" label="Имя держателя карты " required
                                                placeholder="IVAN IVANOV" />
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-4">
                                    <small>🔒 Оплата защищена SSL-шифрованием. Ваши данные в безопасности.</small>
                                </div>

                                <div class="border rounded p-3 mt-3">
                                    <h6 class="fw-bold mb-2">⭐ Оплата милями</h6>
                                    <p class="small text-muted mb-2">
                                        У вас на счету: <strong>{{ number_format($user->miles_balance ?? 0, 0, '.', ' ') }}
                                            миль</strong>
                                        (1 миля = 1 ₽ скидки)
                                    </p>
                                    @if (($user->miles_balance ?? 0) > 0)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="useMilesCheck">
                                            <label class="form-check-label" for="useMilesCheck">Использовать мили для
                                                оплаты</label>
                                        </div>
                                        <div id="milesAmountWrap" class="d-none">
                                            <label class="form-label small" for="miles_amount">Сколько миль
                                                списать</label>
                                            <input type="number" name="miles_used" id="miles_amount"
                                                class="form-control form-control-sm" min="0"
                                                max="{{ (int) $user->miles_balance }}" value="0">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(3)">←
                                Назад</button>
                            <button type="submit" class="btn btn-success px-4">✓ Оплатить и оформить билет</button>
                        </div>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="col-lg-4">
                    <div class="summary-box">
                        <h6 class="fw-bold mb-3">Ваш заказ</h6>
                        <div class="mb-3">
                            <small class="text-muted d-block">Рейс</small>
                            <strong>{{ $ticket->flight_number }}</strong>
                            <br><small>{{ $ticket->departure_city }} → {{ $ticket->arrival_city }}</small>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Класс</small>
                            <strong id="summaryClass">{{ ucfirst($class) }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Пассажиров</small>
                            <strong id="summaryPassengers">{{ $passengers }} чел.</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Базовый тариф:</span>
                            <strong
                                id="summaryBase">{{ number_format($class === 'economy' ? $ticket->price_economy * $passengers : ($class === 'comfort' ? $ticket->price_comfort * $passengers : $ticket->price_business * $passengers), 0, '.', ' ') }}
                                ₽</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Доп. услуги:</span>
                            <strong id="summaryServices">0 ₽</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2" id="summaryMilesRow" style="display:none;">
                            <span>Скидка милями:</span>
                            <strong id="summaryMilesDiscount">−0 ₽</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fs-5">
                            <strong>Итого:</strong>
                            <strong class="text-primary"
                                id="summaryTotal">{{ number_format($class === 'economy' ? $ticket->price_economy * $passengers : ($class === 'comfort' ? $ticket->price_comfort * $passengers : $ticket->price_business * $passengers), 0, '.', ' ') }}
                                ₽</strong>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        window.bookingConfig = (function() {
            @php
                $defaultDoc = $defaultDoc ?? ($user->documents->first() ?? null);
                $myPassengerData = [
                    'lastname' => $user->lastname,
                    'firstname' => $user->name,
                    'patronomic' => $user->patronomic,
                    'birth_date' => $user->birth_date ? $user->birth_date->format('Y-m-d') : '',
                    'gender' => $user->gender,
                    'document_type' => $defaultDoc?->type,
                    'document_series' => $defaultDoc?->series,
                    'document_number' => $defaultDoc?->number,
                    'document_issued_by' => $defaultDoc?->issued_by,
                    'document_issued_at' => $defaultDoc?->issued_at?->format('Y-m-d') ?? '',
                    'document_expires_at' => $defaultDoc?->expires_at?->format('Y-m-d') ?? '',
                    'phone' => $user->phone ?? $user->tel,
                    'email' => $user->email,
                ];
            @endphp
            return {
                basePrice: {{ $class === 'economy' ? $ticket->price_economy : ($class === 'comfort' ? $ticket->price_comfort : $ticket->price_business) }},
                passengersCount: {{ $passengers }},
                userMiles: {{ (int) ($user->miles_balance ?? 0) }},
                myPassengerData: @json($myPassengerData),
            };
        })();
    </script>
    <script src="@vasset('js/create.js')"></script>
@endsection