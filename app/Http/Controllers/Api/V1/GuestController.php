<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $query = Guest::where('hotel_id', $hotelId);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $guests = $query->withCount('reservations')->latest()->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $guests,
        ]);
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $guest = Guest::with(['reservations' => fn($q) => $q->latest()])
            ->where('hotel_id', $hotelId)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $guest,
        ]);
    }
}
