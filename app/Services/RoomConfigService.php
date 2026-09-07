<?php

namespace App\Services;

use App\Models\BedType;
use App\Models\BookingType;
use App\Models\Commission;
use App\Models\ComplementaryService;
use App\Models\Promocode;
use App\Models\RoomFacility;
use App\Models\RoomSize;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RoomConfigService
{
    // 1. Promocodes
    public function getPromocodes(int $hotelId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return Promocode::with('roomType')
            ->where('hotel_id', $hotelId)
            ->when($search, fn($q) => $q->where('code', 'like', "%{$search}%"))
            ->latest()
            ->paginate($perPage);
    }

    public function createPromocode(int $hotelId, array $data): Promocode
    {
        $data['hotel_id'] = $hotelId;
        $promocode = Promocode::create($data);
        AuditService::log($hotelId, 'PROMOCODE_CREATED', $promocode);
        return $promocode;
    }

    public function updatePromocode(int $hotelId, int $id, array $data): Promocode
    {
        $promocode = Promocode::where('hotel_id', $hotelId)->findOrFail($id);
        $promocode->update($data);
        AuditService::log($hotelId, 'PROMOCODE_UPDATED', $promocode);
        return $promocode;
    }

    public function deletePromocode(int $hotelId, int $id): bool
    {
        $promocode = Promocode::where('hotel_id', $hotelId)->findOrFail($id);
        $promocode->delete();
        return true;
    }

    // 2. Facilities
    public function getFacilities(int $hotelId): Collection
    {
        return RoomFacility::where('hotel_id', $hotelId)->latest()->get();
    }

    public function createFacility(int $hotelId, array $data): RoomFacility
    {
        $data['hotel_id'] = $hotelId;
        return RoomFacility::create($data);
    }

    public function deleteFacility(int $hotelId, int $id): bool
    {
        RoomFacility::where('hotel_id', $hotelId)->findOrFail($id)->delete();
        return true;
    }

    // 3. Bed Types
    public function getBeds(int $hotelId): Collection
    {
        return BedType::where('hotel_id', $hotelId)->latest()->get();
    }

    public function createBed(int $hotelId, array $data): BedType
    {
        $data['hotel_id'] = $hotelId;
        return BedType::create($data);
    }

    public function deleteBed(int $hotelId, int $id): bool
    {
        BedType::where('hotel_id', $hotelId)->findOrFail($id)->delete();
        return true;
    }

    // 4. Room Sizes
    public function getSizes(int $hotelId): Collection
    {
        return RoomSize::where('hotel_id', $hotelId)->latest()->get();
    }

    public function createSize(int $hotelId, array $data): RoomSize
    {
        $data['hotel_id'] = $hotelId;
        return RoomSize::create($data);
    }

    public function deleteSize(int $hotelId, int $id): bool
    {
        RoomSize::where('hotel_id', $hotelId)->findOrFail($id)->delete();
        return true;
    }

    // 5. Booking Types
    public function getBookingTypes(int $hotelId): Collection
    {
        return BookingType::where('hotel_id', $hotelId)->latest()->get();
    }

    public function createBookingType(int $hotelId, array $data): BookingType
    {
        $data['hotel_id'] = $hotelId;
        return BookingType::create($data);
    }

    public function deleteBookingType(int $hotelId, int $id): bool
    {
        BookingType::where('hotel_id', $hotelId)->findOrFail($id)->delete();
        return true;
    }

    // 6. Commissions
    public function getCommissions(int $hotelId): Collection
    {
        return Commission::where('hotel_id', $hotelId)->latest()->get();
    }

    public function createCommission(int $hotelId, array $data): Commission
    {
        $data['hotel_id'] = $hotelId;
        return Commission::create($data);
    }

    public function deleteCommission(int $hotelId, int $id): bool
    {
        Commission::where('hotel_id', $hotelId)->findOrFail($id)->delete();
        return true;
    }

    // 7. Complementary
    public function getComplementary(int $hotelId): Collection
    {
        return ComplementaryService::where('hotel_id', $hotelId)->latest()->get();
    }

    public function createComplementary(int $hotelId, array $data): ComplementaryService
    {
        $data['hotel_id'] = $hotelId;
        return ComplementaryService::create($data);
    }

    public function deleteComplementary(int $hotelId, int $id): bool
    {
        ComplementaryService::where('hotel_id', $hotelId)->findOrFail($id)->delete();
        return true;
    }
}
