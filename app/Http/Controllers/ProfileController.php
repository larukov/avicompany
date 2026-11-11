<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Document;
use App\Models\MilesHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user()->load('documents');

        $bookings = Booking::with(['ticket', 'passengers', 'services'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Статистика из реальных бронирований
        $completedFlights = $bookings->whereIn('status', ['completed', 'boarded'])->count();
        $completedBookings = $bookings->whereIn('status', ['completed', 'boarded']);
        $countriesVisited = $completedBookings
            ->pluck('ticket.arrival_city')
            ->unique()
            ->filter()
            ->count();
        $activeBookings = $bookings->whereIn('status', ['pending', 'confirmed', 'checked_in'])->count();
        $milesBalance = $user->miles_balance ?? 0;

        $milesHistory = MilesHistory::where('user_id', Auth::id())
            ->with('booking')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'flights' => $completedFlights,
            'countries' => $countriesVisited,
            'miles' => $milesBalance,
            'active' => $activeBookings,
        ];

        return view('profile.index', compact('user', 'bookings', 'stats', 'milesHistory'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'lastname'     => 'required|string|max:255',
            'patronomic'   => 'nullable|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'tel'        => 'required|unique:users,tel,' . $user->id,
            'phone'        => 'nullable|string|max:20',
            'birth_date'   => 'nullable|date',
            'gender'       => 'nullable|in:male,female',
            'citizenship'  => 'nullable|string|max:100',
            'loyalty_card' => 'nullable|string|max:50',
            'address'      => 'nullable|string|max:500',
        ]);

        // Держим phone синхронным с tel (для форм покупки и совместимости)
        $validated['phone'] = $validated['phone'] ?? $validated['tel'];

        $user->update($validated);
        return back()->with('success', 'Данные успешно обновлены!');
    }

    // CRUD для документов
    public function storeDocument(Request $request)
    {
        $validated = $request->validate([
            'type'        => 'required|in:passport_rf,passport_international,driver_license,other',
            'series'      => 'nullable|string|max:10',
            'number'      => 'required|string|max:20',
            'issued_by'   => 'nullable|string|max:255',
            'issued_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date|after:issued_at',
        ]);

        Auth::user()->documents()->create($validated);
        return back()->with('success', 'Документ успешно добавлен!');
    }

    public function updateDocument(Request $request, Document $document)
    {
        // Проверка, что документ принадлежит пользователю
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type'        => 'required|in:passport_rf,passport_international,driver_license,other',
            'series'      => 'nullable|string|max:10',
            'number'      => 'required|string|max:20',
            'issued_by'   => 'nullable|string|max:255',
            'issued_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date|after:issued_at',
        ]);

        $document->update($validated);
        return back()->with('success', 'Документ успешно обновлен!');
    }

    public function destroyDocument(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        $document->delete();
        return back()->with('success', 'Документ удален!');
    }
}
