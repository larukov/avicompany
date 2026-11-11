@extends('layouts.app')
@section('title', 'Билеты')
@section('css')
    <link rel="stylesheet" href="@vasset('css/tickets.css')">
@endsection
@section('content')
    <x-search.form />
    <section class="container-fluid">
        <div class="row justify-content-center mt-2 mb-2">
            <div class="col-12 col-lg-6">
                <button class="btn btn-dark w-100 d-lg-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
                    🔎 Фильтры
                </button>
            </div>
        </div>
        <div class="row">
            <div id="filtersCollapse" class="col-12 col-lg-2 offset-lg-1 collapse">
                <x-filters.sidebar :collapsed="false" />
            </div>
            <div class="col-lg-8">
                @php
                    $totalFound = $tickets->count() + $returnTickets->count();
                    $outboundDate = request('date');
                    $returnDate = request('return_date');
                @endphp

                <div class="border bg-white rounded-3 shadows p-3 mb-4 number_flights">
                    <div class="row align-items-center g-3">
                        <h3 class="col-12 col-lg-5 mb-0">Найдено {{ $totalFound }} рейсов</h3>
                        <div class="col-12 col-lg-4 offset-lg-2 col-xl-4">
                            <x-ui.select name="sort" :options="[
                                'price_asc' => 'Сортировать по цене ↑',
                                'price_desc' => 'Сортировать по цене ↓',
                                'time' => 'По времени вылета',
                                'duration' => 'По длительности',
                            ]" :value="request('sort')" />
                        </div>
                    </div>
                    @if ($isRoundTrip)
                        <small class="text-muted d-block mt-2">
                            Туда: {{ \Illuminate\Support\Carbon::parse($outboundDate)->format('d.m.Y') }} •
                            Обратно: {{ \Illuminate\Support\Carbon::parse($returnDate)->format('d.m.Y') }}
                        </small>
                    @endif
                </div>

                @if ($isRoundTrip)
                    <h5 class="fw-bold mb-3">
                        ✈️ Туда
                        <small class="text-muted fw-normal">{{ request('from') }} → {{ request('to') }},
                            {{ \Illuminate\Support\Carbon::parse($outboundDate)->format('d.m.Y') }}</small>
                    </h5>
                @endif

                @forelse ($tickets as $ticket)
                    <x-ticket-card :ticket="$ticket" :selected-class="$selectedClass" />
                @empty
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle fs-1 d-block mb-3"></i>
                        <h5>Рейсы не найдены</h5>
                        <p>Попробуйте изменить параметры поиска</p>
                    </div>
                @endforelse

                @if ($isRoundTrip)
                    <h5 class="fw-bold mb-3 mt-4">
                        ↩️ Обратно
                        <small class="text-muted fw-normal">{{ request('to') }} → {{ request('from') }},
                            {{ \Illuminate\Support\Carbon::parse($returnDate)->format('d.m.Y') }}</small>
                    </h5>

                    @forelse ($returnTickets as $ticket)
                        <x-ticket-card :ticket="$ticket" :selected-class="$selectedClass" />
                    @empty
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-info-circle fs-1 d-block mb-3"></i>
                            <h5>Обратные рейсы не найдены</h5>
                            <p>Попробуйте выбрать другую дату возврата</p>
                        </div>
                    @endforelse
                @endif
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script src="@vasset('js/tickets.js')"></script>
@endsection
