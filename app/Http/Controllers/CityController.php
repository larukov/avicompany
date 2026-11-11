<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function suggest(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('q', ''));

        $cities = Ticket::query()
            ->distinct()
            ->pluck('departure_city')
            ->merge(Ticket::query()->distinct()->pluck('arrival_city'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if ($query !== '') {
            $cities = $cities
                ->filter(fn ($city) => mb_stripos((string) $city, $query) !== false)
                ->values();
        }

        return response()->json($cities->take(10)->values()->all());
    }
}
