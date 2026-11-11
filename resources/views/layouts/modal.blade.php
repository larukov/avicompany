<!-- Modal авторизации -->
<div class="modal fade" id="AuthorizationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Авторизация</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('login') }}" method="POST" class="d-flex flex-column">
                    @csrf

                    {{-- Вывод ошибок --}}
                    <x-validation-errors bag="login" />

                    <x-ui.input name="email" type="email" label="Email" placeholder="example@mail.ru" required />
                    <x-ui.input name="password" type="password" label="Пароль" required />

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                    </div>

                    <a href="#" class="text-center mb-2"
                        onclick="switchModal('AuthorizationModal','RegModal'); return false;">
                        Нет аккаунта? Зарегистрироваться
                    </a>

                    <button type="submit" class="btn btn-primary mt-2">Авторизоваться</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal регистрации -->
<div class="modal fade" id="RegModal" tabindex="-1" aria-labelledby="exampleModallabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModallabel">Регистрация</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('register') }}" method="POST" class="d-flex flex-column">
                    @csrf

                    <x-validation-errors />

                    <x-ui.input name="email" type="email" label="Email" placeholder="example@mail.ru" required />

                    <x-ui.input name="tel" type="tel" label="Номер телефона" id="phone" placeholder="+7 (999) 123-45-67"
                        required />

                    <x-ui.input name="name" type="text" label="Имя" placeholder="Иван" required />

                    <x-ui.input name="lastname" type="text" label="Фамилия" placeholder="Иванов" required />

                    <x-ui.input name="patronomic" type="text" label="Отчество" placeholder="Иванович" />

                    <x-ui.input name="password" type="password" label="Пароль" required />

                    <x-ui.input name="password_confirmation" type="password" label="Подтверждение пароля" required />

                    <a href="#" class="text-center"
                        onclick="switchModal('RegModal','AuthorizationModal'); return false;">
                        Есть аккаунт? Войти
                    </a>

                    <button type="submit" class="btn btn-primary mt-2">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>
</div>