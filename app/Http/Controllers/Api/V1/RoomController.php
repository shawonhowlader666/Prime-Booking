<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $rooms = Room::with('roomType')
            ->where('hotel_id', $hotelId)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('room_type_id'), fn($q) => $q->where('room_type_id', $request->room_type_id))
            ->orderBy('room_number')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rooms,
        ]);
    }

    public function roomTypes(Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $types = RoomType::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->withCount(['rooms', 'rooms as available_rooms_count' => fn($q) => $q->where('status', 'AVAILABLE')])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }

    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $room = Room::where('hotel_id', $hotelId)->where('id', $id)->firstOrFail();

        $request->validate([
            'status' => 'required|string|in:AVAILABLE,OCCUPIED,DIRTY,CLEANING,MAINTENANCE,OUT_OF_ORDER',
        ]);

        $room->status = $request->status;
        $room->save();

        return response()->json([
            'success' => true,
            'message' => "Room {$room->room_number} status updated to {$room->status}",
            'data' => $room,
        ]);
    }
}
