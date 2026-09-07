<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimelineMatrixController extends Controller
{
    /**
     * Generate an ultra-fast, multi-tenant 30-day room availability & occupancy timeline matrix.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $startDateStr = $request->input('start_date', now()->format('Y-m-d'));
        $daysCount = min(max((int) $request->input('days', 30), 1), 60);

        $startDate = Carbon::parse($startDateStr)->startOfDay();
        $endDate = (clone $startDate)->addDays($daysCount - 1)->endOfDay();
        $datePeriod = CarbonPeriod::create($startDate, '1 day', $endDate);

        $dates = [];
        foreach ($datePeriod as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        // Fetch rooms for current tenant
        $roomsQuery = Room::with('roomType')
            ->when($request->filled('room_type_id'), function ($q) use ($request) {
                $q->where('room_type_id', $request->input('room_type_id'));
            })
            ->orderBy('room_number');

        $rooms = $roomsQuery->get();

        // Fetch active reservations overlapping with the requested window
        $reservations = Reservation::with(['guest', 'roomType'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('check_in_date', '<=', $endDate->format('Y-m-d'))
                  ->where('check_out_date', '>=', $startDate->format('Y-m-d'));
            })
            ->whereIn('status', ['confirmed', 'checked_in', 'CONFIRMED', 'CHECKED_IN'])
            ->get();

        // Index reservations by room_id and date
        $roomBookings = [];
        foreach ($reservations as $res) {
            if (!$res->room_id) {
                continue;
            }

            $resStart = Carbon::parse($res->check_in_date);
            $resEnd = Carbon::parse($res->check_out_date);
            $period = CarbonPeriod::create($resStart, '1 day', $resEnd);

            foreach ($period as $day) {
                $dayKey = $day->format('Y-m-d');
                $roomBookings[$res->room_id][$dayKey] = [
                    'reservation_id' => $res->id,
                    'booking_reference' => $res->reservation_code ?? $res->booking_reference,
                    'guest_name' => $res->guest ? ($res->guest->first_name . ' ' . $res->guest->last_name) : 'Guest',
                    'status' => strtoupper($res->status),
                    'is_check_in' => $day->isSameDay($resStart),
                    'is_check_out' => $day->isSameDay($resEnd),
                    'rate' => (float) $res->total_amount,
                ];
            }
        }

        // Build matrix
        $matrix = [];
        $dailyOccupancyCounts = array_fill_keys($dates, 0);

        foreach ($rooms as $room) {
            $daysData = [];
            foreach ($dates as $dateKey) {
                if (isset($roomBookings[$room->id][$dateKey])) {
                    $slot = $roomBookings[$room->id][$dateKey];
                    $dailyOccupancyCounts[$dateKey]++;
                    $daysData[$dateKey] = [
                        'status' => in_array($slot['status'], ['CHECKED_IN', 'OCCUPIED']) ? 'OCCUPIED' : 'RESERVED',
                        'reservation' => $slot,
                    ];
                } elseif ($room->status === 'maintenance' || $room->status === 'out_of_order') {
                    $daysData[$dateKey] = [
                        'status' => 'MAINTENANCE',
                        'reservation' => null,
                    ];
                } elseif ($room->status === 'cleaning' || $room->status === 'dirty') {
                    $daysData[$dateKey] = [
                        'status' => 'HOUSEKEEPING',
                        'reservation' => null,
                    ];
                } else {
                    $daysData[$dateKey] = [
                        'status' => 'AVAILABLE',
                        'reservation' => null,
                    ];
                }
            }

            $matrix[] = [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'room_type' => $room->roomType?->name ?? 'Standard',
                'floor' => $room->floor ?? 'Floor 1',
                'current_operational_status' => $room->status,
                'schedule' => $daysData,
            ];
        }

        $totalRoomsCount = max($rooms->count(), 1);
        $dailyOccupancyRate = [];
        foreach ($dailyOccupancyCounts as $dateKey => $count) {
            $dailyOccupancyRate[$dateKey] = [
                'occupied_rooms' => $count,
                'available_rooms' => $totalRoomsCount - $count,
                'occupancy_percentage' => round(($count / $totalRoomsCount) * 100, 1),
            ];
        }

        return response()->json([
            'success' => true,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_days' => $daysCount,
            'total_rooms' => $rooms->count(),
            'dates' => $dates,
            'daily_summary' => $dailyOccupancyRate,
            'matrix' => $matrix,
        ]);
    }
}
