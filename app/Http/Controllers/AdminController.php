<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Company;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        // считаем активные рейсы
        $activeFlights = Ticket::whereIn('status', ['scheduled', 'boarding', 'departed'])->count();

        // сколько всего билетов продано (кроме отмененных)
        $soldTickets = Booking::where('status', '!=', 'cancelled')->sum('passengers_count');

        // выручка тоже без отмененных
        $revenue = Booking::where('status', '!=', 'cancelled')->sum('total_price');

        // сколько задержанных рейсов
        $delayedFlights = Ticket::where('status', 'delayed')->count();

        // статистика по статусам бронирований
        $statusCounts = [];
        $statusCounts['confirmed'] = Booking::where('status', 'confirmed')->sum('passengers_count');
        $statusCounts['checked_in'] = Booking::where('status', 'checked_in')->sum('passengers_count');
        $statusCounts['boarded'] = Booking::where('status', 'boarded')->sum('passengers_count');
        $statusCounts['no_show'] = Booking::where('status', 'no_show')->sum('passengers_count');
        $statusCounts['cancelled'] = Booking::where('status', 'cancelled')->sum('passengers_count');

        // общее число билетов = сумма всех статусов + те что в ожидании оплаты
        $totalTickets = 0;
        foreach ($statusCounts as $count) {
            $totalTickets += $count;
        }
        $totalTickets += Booking::where('status', 'pending')->sum('passengers_count');

        // популярные направления, группируем билеты по маршруту
        $bookingsForRoutes = Booking::with('ticket')
            ->where('status', '!=', 'cancelled')
            ->get();

        $routesMap = [];
        foreach ($bookingsForRoutes as $booking) {
            if ($booking->ticket) {
                $route = $booking->ticket->departure_city . ' → ' . $booking->ticket->arrival_city;
            } else {
                $route = '—';
            }

            if (!isset($routesMap[$route])) {
                $routesMap[$route] = 0;
            }
            $routesMap[$route] += $booking->passengers_count;
        }

        // сортируем по убыванию и берем первые 5
        arsort($routesMap);
        $popularRoutes = collect($routesMap)->take(5);

        // продажи за последние 7 дней
        $weekSales = Booking::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get(['created_at', 'passengers_count']);

        // группируем продажи по дате
        $salesByDate = [];
        foreach ($weekSales as $sale) {
            $key = $sale->created_at->format('Y-m-d');
            if (!isset($salesByDate[$key])) {
                $salesByDate[$key] = 0;
            }
            $salesByDate[$key] += $sale->passengers_count;
        }

        $dayNames = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
        $weekChart = []; // данные столбиков
        $days = [];      // подписи дней недели

        // идем от 6 дней назад до сегодня
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $key = $date->format('Y-m-d');

            // если на эту дату не было продаж, то 0
            if (isset($salesByDate[$key])) {
                $weekChart[] = $salesByDate[$key];
            } else {
                $weekChart[] = 0;
            }

            // определяем реальный день недели для этой даты
            // isoWeekDay возвращает 1 для понедельника и 7 для воскресенья
            $dayNumber = (int) $date->isoWeekDay();
            $days[] = $dayNames[$dayNumber - 1];
        }

        $flights = Ticket::orderBy('departure_time', 'desc')->get();
        $bookings = Booking::with(['ticket', 'user', 'passengers'])
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();
        $companies = Company::withCount('tickets')->orderBy('name')->get();

        $stats = [];
        $stats['activeFlights'] = $activeFlights;
        $stats['soldTickets'] = $soldTickets;
        $stats['revenue'] = $revenue;
        $stats['delayedFlights'] = $delayedFlights;

        return view('admin.index', compact(
            'stats',
            'statusCounts',
            'totalTickets',
            'weekChart',
            'days',
            'flights',
            'bookings',
            'popularRoutes',
            'companies'
        ));
    }

    public function storeFlight(Request $request)
    {

        $validated = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'flight_number' => 'required|string|max:20',
            'airline' => 'required|string|max:100',
            'company_id' => 'nullable|exists:companies,id',
            'aircraft' => 'required|string|max:100',
            'departure_city' => 'required|string|max:100',
            'departure_airport' => 'required|string|max:10',
            'departure_airport_name' => 'nullable|string|max:200',
            'departure_time' => 'required|date',
            'arrival_city' => 'required|string|max:100',
            'arrival_airport' => 'required|string|max:10',
            'arrival_airport_name' => 'nullable|string|max:200',
            'arrival_time' => 'required|date|after:departure_time',
            'price_economy' => 'required|numeric|min:0',
            'price_comfort' => 'nullable|numeric|min:0',
            'price_business' => 'nullable|numeric|min:0',
            'total_seats' => 'required|integer|min:1',
            'available_seats_economy' => 'required|integer|min:0',
            'available_seats_comfort' => 'nullable|integer|min:0',
            'available_seats_business' => 'nullable|integer|min:0',
            'status' => 'required|in:scheduled,boarding,departed,arrived,delayed,cancelled',
            'baggage_allowance' => 'nullable|integer|min:0',
            'is_direct' => 'nullable|boolean',
            'stops' => 'nullable|integer|min:0',
            'has_wifi' => 'nullable|boolean',
            'has_meal' => 'nullable|boolean',
        ])->after(function ($validator) use ($request) {
            // сумма мест по классам не должна превышать общее число мест в самолете
            $total = max(1, (int) $request->input('total_seats'));
            $sum = (int) $request->input('available_seats_economy', 0)
                + (int) $request->input('available_seats_comfort', 0)
                + (int) $request->input('available_seats_business', 0);
            if ($sum > $total) {
                $validator->errors()->add(
                    'available_seats_economy',
                    'Количество мест по классам (' . $sum . ') не может превышать общее число мест в самолете (' . $total . ').'
                );
            }
        })->validate();

        $departure = new \DateTime($validated['departure_time']);
        $arrival = new \DateTime($validated['arrival_time']);
        $diff = $departure->diff($arrival);
        $validated['duration_minutes'] = ($diff->h * 60) + $diff->i;

        $validated['is_direct'] = $request->boolean('is_direct');
        $validated['has_wifi'] = $request->boolean('has_wifi');
        $validated['has_meal'] = $request->boolean('has_meal');

        $validated['stops'] = $request->input('stops', 0);
        $validated['baggage_allowance'] = $request->input('baggage_allowance', 23);

        if (empty($validated['departure_airport_name'])) {
            $validated['departure_airport_name'] = $validated['departure_city'];
        }
        if (empty($validated['arrival_airport_name'])) {
            $validated['arrival_airport_name'] = $validated['arrival_city'];
        }

        // если выбрана компания, записываем ее название и логотип в рейс
        $this->applyCompanyToFlight($validated);

        Ticket::create($validated);

        return back()->with('success', 'Рейс успешно создан.');
    }

    public function updateFlight(Request $request, Ticket $flight)
    {

        $validated = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'status' => 'required|in:scheduled,boarding,departed,arrived,delayed,cancelled',
            'company_id' => 'nullable|exists:companies,id',
            'total_seats' => 'required|integer|min:1',
            'available_seats_economy' => 'required|integer|min:0',
            'available_seats_comfort' => 'nullable|integer|min:0',
            'available_seats_business' => 'nullable|integer|min:0',
            'price_economy' => 'required|numeric|min:0',
            'price_comfort' => 'nullable|numeric|min:0',
            'price_business' => 'nullable|numeric|min:0',
        ])->after(function ($validator) use ($request) {
            $total = max(1, (int) $request->input('total_seats'));
            $sum = (int) $request->input('available_seats_economy', 0)
                + (int) $request->input('available_seats_comfort', 0)
                + (int) $request->input('available_seats_business', 0);
            if ($sum > $total) {
                $validator->errors()->add(
                    'available_seats_economy',
                    'Количество мест по классам (' . $sum . ') не может превышать общее число мест в самолете (' . $total . ').'
                );
            }
        })->validate();

        $this->applyCompanyToFlight($validated);

        $flight->update($validated);

        return back()->with('success', 'Рейс обновлён.');
    }

    public function updateBookingStatus(Request $request, Booking $booking)
    {

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,boarded,no_show,cancelled,completed',
        ]);

        $booking->update($validated);

        return back()->with('success', 'Статус билета обновлён.');
    }

    public function destroyFlight(Ticket $flight)
    {

        $flight->delete();

        return back()->with('success', 'Рейс удалён.');
    }

    public function storeCompany(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'iata_code' => 'nullable|string|max:3',
            'logo_url' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $logoUrl = $validated['logo_url'] ?? null;
        $savedLogo = $this->saveCompanyLogo($request);

        if ($savedLogo) {
            $validated['logo'] = $savedLogo;
        } else {
            $validated['logo'] = $logoUrl ?: null;
        }
        unset($validated['logo_url']);
        // если галочка снята, поле не приходит, поэтому ставим false
        $validated['is_active'] = $request->boolean('is_active', false);

        Company::create($validated);

        return back()->with('success', 'Компания добавлена.');
    }

    public function updateCompany(Request $request, Company $company)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'iata_code' => 'nullable|string|max:3',
            'logo_url' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $logoUrl = $validated['logo_url'] ?? null;
        $savedLogo = $this->saveCompanyLogo($request);

        if ($savedLogo) {
            $validated['logo'] = $savedLogo;
        } elseif ($logoUrl) {
            $validated['logo'] = $logoUrl;
        } else {
            $validated['logo'] = $company->logo;
        }
        unset($validated['logo_url']);
        // если галочка снята, поле не приходит, поэтому ставим false
        $validated['is_active'] = $request->boolean('is_active', false);

        $company->update($validated);

        // когда изменили компанию, обновляем название и логотип у всех ее рейсов
        $company->tickets()->update([
            'airline' => $company->name,
            'airline_logo' => $company->logo,
        ]);

        return back()->with('success', 'Компания обновлена.');
    }

    public function destroyCompany(Company $company)
    {

        $company->delete();

        return back()->with('success', 'Компания удалена.');
    }

    private function applyCompanyToFlight(array &$validated)
    {
        // если компания не выбрана, ничего не делаем
        if (empty($validated['company_id'])) {
            return;
        }

        $company = Company::find($validated['company_id']);
        if (!$company) {
            return;
        }

        // подставляем данные компании в поля рейса
        $validated['airline'] = $company->name;
        $validated['airline_logo'] = $company->logo;
    }

    private function saveCompanyLogo(Request $request)
    {
        // если файл не загружен, возвращаем null
        if (!$request->hasFile('logo')) {
            return null;
        }

        $file = $request->file('logo');
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = Str::slug($baseName) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

        $dir = public_path('company-logos');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file->move($dir, $name);

        return 'company-logos/' . $name;
    }
}