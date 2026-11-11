<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 fw-bold">📝 Личные данные
            <small class="text-muted fs-6 fw-normal">Обновите информацию о себе</small>
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <x-ui.input name="lastname" label="Фамилия" required :value="$user->lastname" />
                </div>
                <div class="col-md-6">
                    <x-ui.input name="name" label="Имя" required :value="$user->name" />
                </div>
                <div class="col-md-6">
                    <x-ui.input name="patronomic" label="Отчество" :value="$user->patronomic" />
                </div>
                <div class="col-md-6">
                    <x-ui.input name="birth_date" type="date" label="Дата рождения" required :value="$user->birth_date?->format('Y-m-d')" />
                </div>
                <div class="col-md-6">
                    <x-ui.select name="gender" label="Пол" required :options="[
                        'male' => 'Мужской',
                        'female' => 'Женский',
                    ]" :value="$user->gender" />

                </div>
                <div class="col-md-6">
                    <x-ui.select name="citizenship" label="Гражданство" required :options="[
                        'Россия' => 'Россия',
                        'Беларусь' => 'Беларусь',
                        'Казахстан' => 'Казахстан',
                    ]" :value="$user->citizenship ?? 'Россия'" />
                </div>
                <div class="col-md-6">
                    <x-ui.input name="email" type="email" label="Email" required :value="$user->email" />
                </div>
                <div class="col-md-6">
                    <x-ui.input name="tel" type="tel" id="phone" label="Телефон" placeholder="+7 (999) 123-45-67" required :value="$user->tel" />
                </div>
                <div class="col-md-6">
                    <x-ui.input name="loyalty_card" label="Номер карты лояльности" :value="$user->loyalty_card" />
                </div>
                <div class="col-md-6">
                    <x-ui.input name="address" label="Адрес" :value="$user->address" />
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4">💾 Сохранить</button>
                </div>
            </div>
        </form>
    </div>
</div>