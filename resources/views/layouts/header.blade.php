<header>
    <nav class="navbar navbar-expand-lg navbar-dark border-bottom border-secondary py-3 shadow-sm rounded-bottom bg-primary">
        <div class="container">
            {{-- Логотип --}}
            <a class="navbar-brand fw-bold tracking-wider" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="SkyAir" class="site-logo">
            </a>

            {{-- Кнопка бургер --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Переключить навигацию">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Меню --}}
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('home') }}#direction">Направления</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('home') }}#about">О нас</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('home') }}#help">Помощь</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center flex-wrap gap-2 header-actions">
                    {{-- Купить билет --}}
                    <a href="{{ route('tickets') }}" class="btn btn-outline-light fw-bold">
                        купить билет
                    </a>

                    {{-- Админ-панель (только для админов) --}}
                    @auth
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light fw-bold">
                                Админ панель
                            </a>
                        @endif
                    @endauth

                    {{-- Гость --}}
                    @guest
                        <a href="#" class="btn btn-outline-light" data-bs-toggle="modal"
                            data-bs-target="#AuthorizationModal">
                            Авторизация
                        </a>
                    @endguest

                    {{-- Авторизованный пользователь --}}
                    @auth
                        <a href="{{ route('profile') }}" class="nav-link text-white px-3">
                            {{ auth()->user()->name }} {{ auth()->user()->lastname }}
                        </a>
                        <a href="{{ route('logout') }}" class="btn btn-danger fw-bold">
                            выйти
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>