<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\BookingService;
use App\Models\MilesHistory;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function create(Request $request, Ticket $ticket)
    {
        $class = $request->input('class', 'economy');
        $passengers = max(1, min(9, (int) $request->input('passengers', 1)));

        $user = Auth::user();

        return view('booking.create', compact('ticket', 'class', 'passengers', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'class' => 'required|in:economy,comfort,business',
            'passengers_count' => 'required|integer|min:1|max:9',
            'passengers' => 'required|array|min:1',
            'passengers.*.firstname' => 'required|string|max:255',
            'passengers.*.lastname' => 'required|string|max:255',
            'passengers.*.patronomic' => 'nullable|string|max:255',
            'passengers.*.birth_date' => 'required|date',
            'passengers.*.gender' => 'required|in:male,female',
            'passengers.*.document_type' => 'required|in:passport_rf,passport_international',
            'passengers.*.document_series' => 'nullable|string|max:10',
            'passengers.*.document_number' => 'required|string|max:20',
            'passengers.*.document_issued_by' => 'nullable|string|max:255',
            'passengers.*.document_issued_at' => 'nullable|date',
            'passengers.*.document_expires_at' => 'nullable|date',
            'passengers.*.phone' => 'nullable|string|max:20',
            'passengers.*.email' => 'nullable|email',
            'services' => 'nullable|array',
            'services.*' => 'in:extra_baggage,seat_selection,special_meal,insurance,priority_boarding,business_lounge',
            'payment_method' => 'required|in:card,sbp,bank',
            'miles_used' => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();

        return DB::transaction(function () use ($validated, $user) {
            $ticket = Ticket::findOrFail($validated['ticket_id']);
            $basePrice = $this->getClassPrice($ticket, $validated['class']);
            $servicesPrice = $this->calculateServicesPrice($validated['services'] ?? [], $validated['passengers_count']);
            $subtotal = ($basePrice * $validated['passengers_count']) + $servicesPrice;

            // Списание миль
            $milesUsed = (int) ($validated['miles_used'] ?? 0);
            $userMiles = (int) ($user->miles_balance ?? 0);
            $maxUsable = (int) min($subtotal, $userMiles);
            if ($milesUsed > $maxUsable) {
                $milesUsed = $maxUsable;
            }

            $totalPrice = max(0, $subtotal - $milesUsed);
            // Начисление миль: 10% от стоимости перелёта (после скидки милями)
            $milesEarned = (int) round($totalPrice * 0.10);

            // Проверяем наличие мест перед покупкой
            $seatsColumn = 'available_seats_' . $validated['class'];
            if ($ticket->{$seatsColumn} < $validated['passengers_count']) {
                return back()->withErrors(['seats' => 'Недостаточно свободных мест на выбранный класс.']);
            }

            $booking = Booking::create([
                'booking_code' => Booking::generateCode(),
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'class' => $validated['class'],
                'passengers_count' => $validated['passengers_count'],
                'base_price' => $basePrice,
                'services_price' => $servicesPrice,
                'total_price' => $totalPrice,
                'miles_used' => $milesUsed,
                'miles_earned' => $milesEarned,
                'status' => 'confirmed',
            ]);

            foreach ($validated['passengers'] as $passengerData) {
                BookingPassenger::create(array_merge($passengerData, ['booking_id' => $booking->id]));
            }

            foreach ($validated['services'] ?? [] as $serviceType) {
                BookingService::create([
                    'booking_id' => $booking->id,
                    'type' => $serviceType,
                    'price' => $this->getServicePrice($serviceType),
                    'quantity' => $validated['passengers_count'],
                ]);
            }

            Payment::create([
                'booking_id' => $booking->id,
                'method' => $validated['payment_method'],
                'amount' => $totalPrice,
                'status' => 'success',
                'transaction_id' => 'TXN-' . uniqid(),
            ]);

            // Уменьшаем число свободных мест ровно на число купленных билетов
            $ticket->decrement($seatsColumn, $validated['passengers_count']);

            // Обновляем баланс миль пользователя: списываем использованные, начисляем заработанные
            $newBalance = $userMiles - $milesUsed + $milesEarned;
            $user->miles_balance = $newBalance;
            $user->save();

            if ($milesUsed > 0) {
                MilesHistory::create([
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'type' => 'spent',
                    'amount' => $milesUsed,
                    'description' => 'Оплата билета милями',
                ]);
            }
            if ($milesEarned > 0) {
                MilesHistory::create([
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'type' => 'earned',
                    'amount' => $milesEarned,
                    'description' => 'Начислено за перелёт',
                ]);
            }

            return redirect()->route('booking.success', $booking->booking_code);
        });
    }

    public function success(string $code)
    {
        $booking = Booking::where('booking_code', $code)
            ->where('user_id', Auth::id())
            ->with(['ticket', 'passengers', 'services'])
            ->firstOrFail();

        return view('booking.success', compact('booking'));
    }

    private function getClassPrice(Ticket $ticket, string $class): float
    {
        return match($class) {
            'economy' => (float) $ticket->price_economy,
            'comfort' => (float) ($ticket->price_comfort ?? $ticket->price_economy),
            'business' => (float) ($ticket->price_business ?? $ticket->price_economy),
        };
    }

    private function getServicePrice(string $type): float
    {
        return match($type) {
            'extra_baggage' => 1500,
            'seat_selection' => 800,
            'special_meal' => 600,
            'insurance' => 500,
            'priority_boarding' => 1200,
            'business_lounge' => 2000,
        };
    }

    private function calculateServicesPrice(array $services, int $passengers): float
    {
        $total = 0;
        foreach ($services as $service) {
            $total += $this->getServicePrice($service) * $passengers;
        }
        return $total;
    }
}