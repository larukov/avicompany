<section class="container mt-5 text-dark">
    <h3 class="text-dark text-center mb-4 fw-bold">Популярные направления</h3>
    <div class="row g-4" id="direction">
        @php
            $destinations = [
                ['city' => 'Сочи', 'price' => '3 990', 'img' => 'images/sochi.svg'],
                ['city' => 'Санкт-Петербург', 'price' => '2 500', 'img' => 'images/piter.svg'],
                ['city' => 'Казань', 'price' => '2 800', 'img' => 'images/casan.svg'],
                ['city' => 'Калининград', 'price' => '3 200', 'img' => 'images/kalinin.svg'],
            ];
        @endphp

        @foreach ($destinations as $dest)
            <div class="col-lg-3">
                <div class="card h-100 p-0 border-secondary-subtle shadow">
                    <img src="{{ asset($dest['img']) }}" alt="{{ $dest['city'] }}" class="card-img-top">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold">{{ $dest['city'] }}</h5>
                        <p class="card-text fw-bold text-primary mt-auto">от {{ $dest['price'] }} ₽</p>
                        <button type="button" class="btn btn-primary fw-bold" data-destination="{{ $dest['city'] }}">
                            Выбрать даты
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>