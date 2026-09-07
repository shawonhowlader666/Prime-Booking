<?php

namespace App\Services;

use App\Models\FloorPlan;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoomOperationsService
{
    /**
     * Get paginated rooms stream with room type, bed type, and size.
     */
    public function getRoomList(int $hotelId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return Room::withoutGlobalScopes()
            ->with(['roomType'])
            ->where('hotel_id', $hotelId)
            ->when($search, function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhereHas('roomType', fn($rt) => $rt->where('name', 'like', "%{$search}%"));
            })
            ->orderBy('room_number')
            ->paginate($perPage);
    }

    /**
     * Store new room and optionally link to room type.
     */
    public function createRoom(int $hotelId, array $data): Room
    {
        return DB::transaction(function () use ($hotelId, $data) {
            $roomTypeId = $data['room_type_id'] ?? null;
            if (!$roomTypeId && !empty($data['room_type_name'])) {
                $rt = RoomType::firstOrCreate(
                    ['hotel_id' => $hotelId, 'name' => $data['room_type_name']],
                    [
                        'code' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $data['room_type_name']), 0, 6)),
                        'base_price' => $data['room_price'] ?? 5000.00,
                        'capacity' => $data['capacity'] ?? 2,
                    ]
                );
                $roomTypeId = $rt->id;
            }

            if (!$roomTypeId) {
                $roomTypeId = RoomType::where('hotel_id', $hotelId)->value('id') ?? 1;
            }

            return Room::create([
                'hotel_id' => $hotelId,
                'room_type_id' => $roomTypeId,
                'room_number' => $data['room_number'] ?? ('R-' . rand(100, 999)),
                'floor' => $data['floor'] ?? 1,
                'status' => 'AVAILABLE',
                'cleanliness' => 'CLEAN',
            ]);
        });
    }

    /**
     * Get paginated room bookings with guest, room, and folio financial details.
     */
    public function getRoomBookingList(int $hotelId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return Reservation::withoutGlobalScopes()
            ->with(['guest', 'room.roomType', 'folio'])
            ->where('hotel_id', $hotelId)
            ->when($search, function ($q) use ($search) {
                $q->where('reservation_code', 'like', "%{$search}%")
                  ->orWhereHas('guest', fn($g) => $g->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get room statuses stream for all rooms in property.
     */
    public function getRoomStatuses(int $hotelId): Collection
    {
        return Room::withoutGlobalScopes()
            ->with(['roomType', 'currentReservation.guest'])
            ->where('hotel_id', $hotelId)
            ->orderBy('room_number')
            ->get();
    }

    /**
     * Get floor plans for hotel.
     */
    public function getFloorPlans(int $hotelId): Collection
    {
        return FloorPlan::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->orderBy('floor_number')
            ->get();
    }

    /**
     * Create floor plan.
     */
    public function createFloorPlan(int $hotelId, array $data): FloorPlan
    {
        return FloorPlan::create([
            'hotel_id' => $hotelId,
            'floor_name' => $data['floor_name'],
            'floor_number' => $data['floor_number'] ?? 1,
            'number_of_rooms' => $data['number_of_rooms'] ?? 0,
            'start_room_number' => $data['start_room_number'] ?? null,
        ]);
    }

    /**
     * Assign floor with rooms.
     */
    public function assignFloor(int $hotelId, array $data): FloorPlan
    {
        return FloorPlan::updateOrCreate(
            ['hotel_id' => $hotelId, 'floor_name' => $data['floor_name']],
            [
                'number_of_rooms' => $data['number_of_rooms'] ?? 5,
                'start_room_number' => $data['start_room_number'] ?? '100',
            ]
        );
    }

    /**
     * Get room images gallery.
     */
    public function getRoomImages(int $hotelId): Collection
    {
        return RoomImage::withoutGlobalScopes()
            ->with(['roomType', 'room'])
            ->where('hotel_id', $hotelId)
            ->latest()
            ->get();
    }

    /**
     * Create room image.
     */
    public function createRoomImage(int $hotelId, array $data): RoomImage
    {
        return RoomImage::create([
            'hotel_id' => $hotelId,
            'room_type_id' => $data['room_type_id'] ?? null,
            'room_id' => $data['room_id'] ?? null,
            'image_url' => $data['image_url'] ?? 'assets/images/gallery/20.jpg',
            'caption' => $data['caption'] ?? null,
            'is_featured' => !empty($data['is_featured']),
        ]);
    }

    /**
     * Get active check-in/out customer and billing details.
     */
    public function getCheckinOutData(int $hotelId, ?int $roomId = null): array
    {
        $rooms = Room::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->orderBy('room_number')
            ->get();

        $selectedRoom = null;
        if ($roomId) {
            $selectedRoom = Room::withoutGlobalScopes()->where('hotel_id', $hotelId)->find($roomId);
        } else {
            $selectedRoom = $rooms->first();
        }

        $activeReservation = null;
        if ($selectedRoom) {
            $activeReservation = Reservation::withoutGlobalScopes()
                ->with(['guest', 'room.roomType', 'folio.items', 'folio.payments'])
                ->where('hotel_id', $hotelId)
                ->where('room_id', $selectedRoom->id)
                ->whereIn('status', ['CHECKED_IN', 'CONFIRMED'])
                ->latest()
                ->first();
        }

        return [
            'rooms' => $rooms,
            'selectedRoom' => $selectedRoom,
            'reservation' => $activeReservation,
        ];
    }
}
