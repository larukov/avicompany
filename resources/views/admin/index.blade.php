@extends('layouts.app')

@section('title', 'Админ-панель — SkyAir')

@section('css')
    <link rel="stylesheet" href="@vasset('css/admin.css')">
@endsection

@section('content')
    <div class="container my-5">
        <div class="row g-4">
            <div class="col-lg-3 admin-sidebar-col">
                <x-sidebar type="admin" title="🛠️ Админ-панель" :active="'tab-dashboard'" :items="[
            ['target' => 'tab-dashboard', 'icon' => 'bi bi-speedometer2', 'label' => 'Дашборд'],
            [
                'target' => 'tab-flights',
                'icon' => 'bi bi-airplane',
                'icon_color' => 'text-primary',
                'label' => 'Рейсы',
                'badge' => $flights->count(),
            ],
            [
                'target' => 'tab-tickets',
                'icon' => 'bi bi-ticket-perforated',
                'icon_color' => 'text-warning',
                'label' => 'Билеты',
                'badge' => $totalTickets,
            ],
            [
                'target' => 'tab-companies',
                'icon' => 'bi bi-building',
                'icon_color' => 'text-success',
                'label' => 'Компании',
                'badge' => $companies->count(),
            ],
        ]" :stats="[
            'label' => 'Продано билетов',
            'value' => number_format($stats['soldTickets']),
            'sublabel' => 'на ' . number_format($stats['revenue'], 0, '.', ' ') . ' ₽',
        ]" />
            </div>

            <div class="col-lg-9">
                {{-- Уведомления --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="tab-content" id="adminTabContent">
                    <div class="tab-pane fade show active" id="tab-dashboard" role="tabpanel">
                        @include('admin.tabs.dashboard')
                    </div>

                    <div class="tab-pane fade" id="tab-flights" role="tabpanel">
                        @include('admin.tabs.flights')
                    </div>

                    <div class="tab-pane fade" id="tab-tickets" role="tabpanel">
                        @include('admin.tabs.tickets')
                    </div>

                    <div class="tab-pane fade" id="tab-companies" role="tabpanel">
                        @include('admin.tabs.companies')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="@vasset('js/admin.js')"></script>
@endsection

@include('admin.partials.modals')