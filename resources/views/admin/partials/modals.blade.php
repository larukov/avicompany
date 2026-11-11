{{-- Модальное окно добавления рейса --}}
<div class="modal fade" id="flightModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.flights.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Добавить рейс</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><x-ui.input name="flight_number" label="Номер рейса" required />
                        </div>
                        <div class="col-md-6"><x-ui.input name="airline" label="Авиакомпания" value="SkyAir" required />
                        </div>
                        <div class="col-md-6">
                            <x-ui.select name="company_id" label="Компания (логотип на билете)"
                                :options="$companies->pluck('name', 'id')->all()" />
                        </div>
                        <div class="col-md-6"><x-ui.input name="aircraft" label="Самолёт" required /></div>
                        <div class="col-md-6">
                            <x-ui.select name="status" label="Статус" required :options="[
                                'scheduled' => 'Запланирован',
                                'boarding' => 'Посадка',
                                'departed' => 'Вылетел',
                                'arrived' => 'Прибыл',
                                'delayed' => 'Задержан',
                                'cancelled' => 'Отменён',
                            ]" :value="'scheduled'" />
                        </div>
                        <div class="col-md-6"><x-ui.input name="departure_city" label="Город вылета" required /></div>
                        <div class="col-md-6"><x-ui.input name="departure_airport" label="Код аэропорта" required />
                        </div>
                        <div class="col-md-6"><x-ui.input name="arrival_city" label="Город прилёта" required /></div>
                        <div class="col-md-6"><x-ui.input name="arrival_airport" label="Код аэропорта" required /></div>
                        <div class="col-md-6"><x-ui.input name="departure_time" type="datetime-local"
                                label="Дата и время вылета" required /></div>
                        <div class="col-md-6"><x-ui.input name="arrival_time" type="datetime-local"
                                label="Дата и время прилёта" required /></div>
                        <div class="col-md-4"><x-ui.input name="price_economy" type="number" label="Цена эконом"
                                required /></div>
                        <div class="col-md-4"><x-ui.input name="price_comfort" type="number" label="Цена комфорт" />
                        </div>
                        <div class="col-md-4"><x-ui.input name="price_business" type="number" label="Цена бизнес" />
                        </div>
                        <div class="col-md-4"><x-ui.input name="total_seats" type="number" label="Всего мест"
                                required /></div>
                        <div class="col-md-4"><x-ui.input name="available_seats_economy" type="number"
                                label="Мест эконом" required /></div>
                        <div class="col-md-4"><x-ui.input name="available_seats_comfort" type="number"
                                label="Мест комфорт" /></div>
                        <div class="col-md-4"><x-ui.input name="available_seats_business" type="number"
                                label="Мест бизнес" /></div>
                        <div class="col-md-4">
                            <div class="form-check mt-4"><input class="form-check-input" type="checkbox" id="is_direct"
                                    name="is_direct" value="1"><label class="form-check-label" for="is_direct">Прямой
                                    рейс</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="has_meal"
                                    name="has_meal" value="1"><label class="form-check-label"
                                    for="has_meal">Питание</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="has_wifi"
                                    name="has_wifi" value="1"><label class="form-check-label"
                                    for="has_wifi">Wi-Fi</label></div>
                        </div>
                        <div class="col-md-4"><x-ui.input name="baggage_allowance" type="number" label="Багаж, кг"
                                value="23" /></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">💾 Сохранить рейс</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Модальные окна редактирования рейсов --}}
@foreach ($flights as $flight)
    <div class="modal fade" id="editFlightModal{{ $flight->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.flights.update', $flight) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Рейс {{ $flight->flight_number }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <x-ui.select name="status" label="Статус" required :options="[
                                'scheduled' => 'Запланирован',
                                'boarding' => 'Посадка',
                                'departed' => 'Вылетел',
                                'arrived' => 'Прибыл',
                                'delayed' => 'Задержан',
                                'cancelled' => 'Отменён',
                            ]" :value="$flight->status" />
                        <x-ui.select name="company_id" label="Компания (логотип на билете)"
                            :options="$companies->pluck('name', 'id')->all()" :value="$flight->company_id" />
                        <x-ui.input name="price_economy" type="number" label="Цена эконом" :value="$flight->price_economy"
                            required />
                        <x-ui.input name="price_comfort" type="number" label="Цена комфорт"
                            :value="$flight->price_comfort" />
                        <x-ui.input name="price_business" type="number" label="Цена бизнес"
                            :value="$flight->price_business" />
                        <x-ui.input name="total_seats" type="number" label="Всего мест"
                            :value="$flight->total_seats" required />
                        <x-ui.input name="available_seats_economy" type="number" label="Мест эконом"
                            :value="$flight->available_seats_economy" required />
                        <x-ui.input name="available_seats_comfort" type="number" label="Мест комфорт"
                            :value="$flight->available_seats_comfort" />
                        <x-ui.input name="available_seats_business" type="number" label="Мест бизнес"
                            :value="$flight->available_seats_business" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">💾 Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

{{-- Модальное окно добавления компании --}}
<div class="modal fade" id="companyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Добавить компанию</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <x-ui.input name="name" label="Название" required placeholder="SkyAir" />
                    <x-ui.input name="iata_code" label="Код IATA" maxlength="3" placeholder="SU" />
                    <x-ui.input name="logo_url" label="Ссылка на логотип" placeholder="https://.../logo.png" />
                    <div class="mb-3">
                        <label class="form-label" for="companyLogo">Или загрузите файл</label>
                        <input type="file" name="logo" id="companyLogo" class="form-control" accept="image/*">
                        <small class="text-muted">PNG, JPG или SVG до 2 МБ</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="companyActive" value="1"
                            checked>
                        <label class="form-check-label" for="companyActive">Активна</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">💾 Сохранить компанию</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Модальные окна редактирования компаний --}}
@foreach ($companies as $company)
    <div class="modal fade" id="editCompanyModal{{ $company->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.companies.update', $company) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Компания {{ $company->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <x-ui.input name="name" label="Название" required :value="$company->name" />
                        <x-ui.input name="iata_code" label="Код IATA" maxlength="3" :value="$company->iata_code" />
                        <x-ui.input name="logo_url" label="Ссылка на логотип" :value="$company->logo" />
                        <div class="mb-3">
                            <label class="form-label" for="companyLogo{{ $company->id }}">Заменить файл</label>
                            <input type="file" name="logo" id="companyLogo{{ $company->id }}"
                                class="form-control" accept="image/*">
                            @if ($company->logo_url)
                                <img src="{{ $company->logo_url }}" alt="{{ $company->name }}"
                                    class="mt-2" style="height: 36px; max-width: 110px; object-fit: contain;">
                            @endif
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active"
                                id="companyActive{{ $company->id }}" value="1"
                                {{ $company->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="companyActive{{ $company->id }}">Активна</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">💾 Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

{{-- Модальные окна изменения статуса билетов --}}
@foreach ($bookings as $booking)
    <div class="modal fade" id="editBookingModal{{ $booking->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Билет {{ $booking->booking_code }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">
                            Рейс {{ $booking->ticket?->flight_number }} • {{ $booking->ticket?->departure_city }} →
                            {{ $booking->ticket?->arrival_city }}
                            <br><small class="text-muted">Пассажиров: {{ $booking->passengers_count }}</small>
                        </p>
                        <x-ui.select name="status" label="Статус билета" required :options="[
                                'pending' => 'Ожидает',
                                'confirmed' => 'Подтверждён',
                                'checked_in' => 'Зарегистрирован',
                                'boarded' => 'На борту',
                                'no_show' => 'Не явился',
                                'cancelled' => 'Отменён',
                                'completed' => 'Завершён',
                            ]" :value="$booking->status" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">💾 Изменить статус</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach