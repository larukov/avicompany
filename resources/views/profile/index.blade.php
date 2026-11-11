@extends('layouts.app')

@section('title')
    Личный кабинет
@endsection

@section('css')
    <link rel="stylesheet" href="@vasset('css/profile.css')">
@endsection

@section('content')
    <div class="container my-5">
        <div class="row g-4">
            <div class="col-lg-3">
                <x-sidebar type="profile" title="👤 Мой профиль" :active="'tab-profile'" :items="[
                    ['target' => 'tab-profile', 'icon' => 'bi bi-person', 'label' => 'Личные данные'],
                    [
                        'target' => 'tab-bookings',
                        'icon' => 'bi bi-ticket-perforated',
                        'icon_color' => 'text-primary',
                        'label' => 'Мои бронирования',
                        'badge' => $bookings->count(),
                    ],
                    [
                        'target' => 'tab-loyalty',
                        'icon' => 'bi bi-star',
                        'icon_color' => 'text-warning',
                        'label' => 'Программа лояльности',
                    ],
                    ['target' => 'tab-settings', 'icon' => 'bi bi-gear', 'label' => 'Настройки'],
                ]" :stats="[
                    'label' => 'Ваш баланс миль',
                    'value' => number_format($stats['miles'], 0, '.', ' '),
                    'sublabel' => 'миль на счету',
                ]" />
            </div>

            <div class="col-lg-9">
                <div class="tab-content" id="profileTabContent">
                    <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
                        @include('profile.partials.user-card')
                        @include('profile.partials.stats')
                        @include('profile.partials.personal-form')
                        @include('profile.partials.documents')
                    </div>

                    <div class="tab-pane fade" id="tab-bookings" role="tabpanel">
                        @include('profile.tabs.bookings')
                    </div>

                    <div class="tab-pane fade" id="tab-loyalty" role="tabpanel">
                        @include('profile.tabs.loyalty')
                    </div>

                    <div class="tab-pane fade" id="tab-settings" role="tabpanel">
                        @include('profile.tabs.settings')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="@vasset('js/profile.js')"></script>
@endsection

@include('profile.partials.document-modals')
