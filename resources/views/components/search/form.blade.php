<section class="container mt-5 mb-5 text-white" id="flight-search">
    <form class="row" method="GET" action="{{ route('tickets') }}" id="flightSearchForm" autocomplete="off">
        <div class="bg-primary rounded-3 p-4 text-center shadows">
            <h3 class="">Открой мир путешествий</h3>
            <p>Найди те лучшие предложения</p>
            <div class="bg-light rounded-3 p-4 text-center col-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
                <input type="radio" class="btn-check" name="trip_type" id="trip-round" value="round"
                    autocomplete="off" {{ request('trip_type', 'round') !== 'oneway' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="trip-round">Туда и обратно</label>
                <input type="radio" class="btn-check" name="trip_type" id="trip-oneway" value="oneway"
                    autocomplete="off" {{ request('trip_type') === 'oneway' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="trip-oneway">в одну сторону</label>
                <div class="row text-dark g-3 mt-1">
                    <div class="d-flex flex-column col-12 col-lg-3">
                        <span>откуда</span>
                        <div class="position-relative">
                            <input type="text" name="from" class="form-control border-dark"
                                value="{{ request('from') }}" placeholder="Москва" autocomplete="off"
                                data-city-autocomplete data-url="{{ route('cities.suggest') }}">
                            <div class="city-suggest list-group position-absolute w-100" data-city-list hidden></div>
                        </div>
                    </div>
                    <div class="d-flex flex-column col-12 col-lg-3">
                        <span>куда</span>
                        <div class="position-relative">
                            <input type="text" name="to" class="form-control border-dark"
                                value="{{ request('to') }}" placeholder="Сочи" autocomplete="off"
                                data-city-autocomplete data-url="{{ route('cities.suggest') }}">
                            <div class="city-suggest list-group position-absolute w-100" data-city-list hidden></div>
                        </div>
                    </div>
                    <div class="d-flex flex-column col-12 col-lg-3">
                        <span>дата вылета</span>
                        <input type="date" name="date" class="form-control border-dark"
                            value="{{ request('date') }}">
                    </div>
                    <div class="d-flex flex-column col-12 col-lg-3 js-return-date {{ request('trip_type') === 'oneway' ? 'd-none' : '' }}">
                        <span>дата возврата</span>
                        <input type="date" name="return_date" class="form-control border-dark"
                            value="{{ request('return_date') }}" {{ request('trip_type') === 'oneway' ? 'disabled' : '' }}>
                    </div>
                    <div class="d-flex flex-column col-12 col-lg-3">
                        <span>пассажиры</span>
                        <select name="passengers" class="form-select border-dark">
                            @for ($n = 1; $n <= 9; $n++)
                                <option value="{{ $n }}" {{ (int) request('passengers', 1) === $n ? 'selected' : '' }}>
                                    {{ $n }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="text-white btn btn-primary col-12 col-lg-2 mt-3 fw-bold">
                            найти
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
