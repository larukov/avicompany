<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        // берем тип поездки, по умолчанию туда-обратно
        $tripType = $request->input('trip_type', 'round');

        // дата возврата, нужна если летим туда и обратно
        $returnDate = $request->input('return_date');

        // класс обслуживания который выбрал юзер
        $selectedClass = $request->input('class');

        // проверяем что класс реально валидный, если нет то ставим null
        if (!in_array($selectedClass, ['economy', 'comfort', 'business'], true)) {
            $selectedClass = null;
        }

        // ищем билеты туда
        $tickets = $this->searchTickets(
            $request,
            $request->input('from'),
            $request->input('to'),
            $request->input('date'),
            $selectedClass
        );

        // смотрим, это туда-обратно или нет
        $isRoundTrip = false;
        if ($tripType === 'round'
            && $request->filled('from')
            && $request->filled('to')
            && $request->filled('return_date')) {
            $isRoundTrip = true;
        }

        // обратные рейсы ищем уже в обратную сторону (поменяли откуда и куда местами)
        $returnTickets = collect();
        if ($isRoundTrip) {
            $returnTickets = $this->searchTickets(
                $request,
                $request->input('to'),
                $request->input('from'),
                $returnDate,
                $selectedClass
            );
        }

        // сколько пассажиров, не меньше 1 и не больше 9
        $passengers = (int) $request->input('passengers', 1);
        if ($passengers < 1) {
            $passengers = 1;
        }
        if ($passengers > 9) {
            $passengers = 9;
        }

        return view('tickets', compact(
            'tickets', 'returnTickets', 'selectedClass', 'tripType', 'isRoundTrip', 'passengers'
        ));
    }

    private function searchTickets(
        Request $request,
        $from,
        $to,
        $date,
        $selectedClass
    ) {
        // строится запрос к таблице билетов
        $query = Ticket::query();

        // тут мы убираем билеты неактивных компаний
        // если у билета нет компании, то показываем его, а если есть,
        // то она должна быть активной (is_active = true)
        $query->where(function ($q) {
            $q->whereNull('company_id')
                ->orWhereHas('company', function ($c) {
                    $c->where('is_active', true);
                });
        });

        // поиск по городам
        if ($from) {
            $query->where('departure_city', 'like', '%' . $from . '%');
        }
        if ($to) {
            $query->where('arrival_city', 'like', '%' . $to . '%');
        }

        // если есть дата, ищем рейсы на эту дату
        if ($date) {
            $query->whereDate('departure_time', $date);
        }

        // фильтр по классу: у билета должна быть цена на этот класс
        if ($selectedClass) {
            $query->whereNotNull('price_' . $selectedClass);
        }

        // фильтрация по пересадкам (галочки в форме)
        if ($request->has('stops')) {
            $stops = $request->input('stops');
            if (is_array($stops)) {
                $query->where(function ($q) use ($stops) {
                    foreach ($stops as $stop) {
                        if ($stop == 'direct') {
                            $q->orWhere('is_direct', true);
                        } elseif ($stop == '1') {
                            $q->orWhere('stops', 1);
                        } elseif ($stop == '2') {
                            $q->orWhere('stops', 2);
                        }
                    }
                });
            }
        }

        // фильтр по времени вылета
        // важно: в базе departure_time это дата + время,
        // поэтому сравниваем только по часам через whereTime
        if ($request->has('departure_time')) {
            $periods = $request->input('departure_time');
            $query->where(function ($q) use ($periods) {
                foreach ($periods as $period) {
                    if ($period == 'morning') {
                        $q->orWhere(function ($sub) {
                            $sub->whereTime('departure_time', '>=', '06:00:00')
                                ->whereTime('departure_time', '<', '12:00:00');
                        });
                    } elseif ($period == 'day') {
                        $q->orWhere(function ($sub) {
                            $sub->whereTime('departure_time', '>=', '12:00:00')
                                ->whereTime('departure_time', '<', '18:00:00');
                        });
                    } elseif ($period == 'evening') {
                        $q->orWhere(function ($sub) {
                            $sub->whereTime('departure_time', '>=', '18:00:00')
                                ->whereTime('departure_time', '<', '23:59:59');
                        });
                    } elseif ($period == 'night') {
                        $q->orWhere(function ($sub) {
                            $sub->whereTime('departure_time', '>=', '00:00:00')
                                ->whereTime('departure_time', '<', '06:00:00');
                        });
                    }
                }
            });
        }

        // фильтруем по названию авиакомпании (несколько можно выбрать)
        if ($request->has('airline')) {
            $query->whereIn('airline', $request->input('airline'));
        }

        // удобства на борту
        if ($request->has('baggage')) {
            $query->where('baggage_allowance', '>', 0);
        }
        if ($request->has('meal')) {
            $query->where('has_meal', true);
        }
        if ($request->has('wifi')) {
            $query->where('has_wifi', true);
        }

        // считаем цену по которой фильтруем и сортируем
        // если выбран класс, то берем его цену
        // а если нет, то берем минимальную из всех классов
        if ($selectedClass) {
            $priceColumn = 'price_' . $selectedClass;
        } else {
            $priceColumn = 'LEAST(price_economy, COALESCE(price_comfort, price_economy), COALESCE(price_business, price_economy))';
        }

        // фильтр по цене (от и до)
        $priceMin = $request->input('price_min');
        if ($priceMin !== null && $priceMin !== '') {
            $query->whereRaw($priceColumn . ' >= ?', [(float) $priceMin]);
        }
        $priceMax = $request->input('price_max');
        if ($priceMax !== null && $priceMax !== '') {
            $query->whereRaw($priceColumn . ' <= ?', [(float) $priceMax]);
        }

        // сортировка какая выбрана в форме
        $sortBy = $request->input('sort', 'price_asc');
        if ($sortBy == 'price_asc') {
            $query->orderByRaw($priceColumn . ' asc');
        } elseif ($sortBy == 'price_desc') {
            $query->orderByRaw($priceColumn . ' desc');
        } elseif ($sortBy == 'time') {
            $query->orderBy('departure_time', 'asc');
        } elseif ($sortBy == 'duration') {
            $query->orderBy('duration_minutes', 'asc');
        }

        // получаем результат
        return $query->get();
    }
}