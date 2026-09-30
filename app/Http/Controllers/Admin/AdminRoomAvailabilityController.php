<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomAvailability;
use App\Models\Booking;
use App\Services\DynamicPricingService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;

/**
 * Enterprise Extranet Room Availability & Multi-Month Calendar Matrix for Admin.
 */
class AdminRoomAvailabilityController extends Controller
{
    public function __construct(
        protected DynamicPricingService $pricingService
    ) {}

    public function index(Request $request)
    {
        $propertyId = $request->query('property_id');
        $roomId     = $request->query('room_id');
        $daysCount  = max(7, min(90, (int)($request->query('days', 30))));
        $startDate  = Carbon::parse($request->query('start_date', Carbon::today()->format('Y-m-d')))->startOfDay();
        $endDate    = $startDate->copy()->addDays($daysCount - 1)->endOfDay();

        // 1. Fetch properties list
        $properties = Property::select(['id', 'name', 'city', 'type', 'vendor_id'])
            ->with(['rooms:id,property_id,name,price_per_night,total_rooms', 'vendor:id,name'])
            ->orderBy('name')
            ->get();

        $selectedProperty = null;
        $selectedRoom     = null;

        if ($propertyId) {
            $selectedProperty = $properties->firstWhere('id', (int)$propertyId);
        } elseif ($properties->isNotEmpty()) {
            $selectedProperty = $properties->first();
        }

        if ($selectedProperty && $selectedProperty->rooms->isNotEmpty()) {
            if ($roomId) {
                $selectedRoom = $selectedProperty->rooms->firstWhere('id', (int)$roomId);
            }
            if (!$selectedRoom) {
                $selectedRoom = $selectedProperty->rooms->first();
            }
        }

        $matrix = [];
        $stats = [
            'total_days'        => $daysCount,
            'available_days'    => 0,
            'blocked_days'      => 0,
            'custom_price_days' => 0,
            'avg_rate'          => 0.0,
            'total_bookings'    => 0,
        ];

        if ($selectedRoom) {
            $baseRate  = (float) $selectedRoom->price_per_night;
            $capacity  = (int) ($selectedRoom->total_rooms ?? 10);

            // Fetch custom availability records in one query
            $overrides = RoomAvailability::where('room_id', $selectedRoom->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get()
                ->keyBy(fn($r) => is_string($r->date) ? $r->date : $r->date->format('Y-m-d'));

            // Fetch all active bookings in date range in one query
            $bookings = Booking::where('room_id', $selectedRoom->id)
                ->where(function ($q) {
                    $q->where('status', 'confirmed')
                      ->orWhere('booking_status', 'confirmed')
                      ->orWhere('status', 'pending');
                })
                ->where('check_in', '<=', $endDate->format('Y-m-d'))
                ->where('check_out', '>=', $startDate->format('Y-m-d'))
                ->select(['id', 'booking_reference', 'guest_name', 'check_in', 'check_out', 'status'])
                ->get();

            $period = CarbonPeriod::create($startDate, $endDate);
            $totalRateSum = 0.0;
            $blockedCount = 0;
            $customCount  = 0;

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $isWeekend = in_array($date->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY], true);

                $override = $overrides->get($dateStr);
                $isBlocked = $override && ($override->is_blocked || $override->status === 'blocked');
                
                // Count bookings on this specific day
                $dayBookings = $bookings->filter(function ($b) use ($dateStr) {
                    return $b->check_in <= $dateStr && $b->check_out > $dateStr;
                });
                $bookedCount = $dayBookings->count();

                // Dynamic pricing breakdown
                $dayPrice = $baseRate;
                $pricingNote = 'Base Rate';

                if ($override && (float)$override->price > 0) {
                    $dayPrice = (float) $override->price;
                    $pricingNote = 'Custom Override';
                    $customCount++;
                } elseif ($isWeekend) {
                    $dayPrice = round($baseRate * 1.15, 2);
                    $pricingNote = 'Weekend Surge (+15%)';
                }

                $availableUnits = $isBlocked ? 0 : max(0, $capacity - $bookedCount);
                if ($isBlocked) $blockedCount++;

                $totalRateSum += $dayPrice;

                $matrix[] = [
                    'date'            => $dateStr,
                    'day_name'        => $date->format('D'),
                    'day_num'         => $date->format('j'),
                    'month_name'      => $date->format('M'),
                    'is_weekend'      => $isWeekend,
                    'is_today'        => $date->isToday(),
                    'is_blocked'      => $isBlocked,
                    'price'           => $dayPrice,
                    'pricing_note'    => $pricingNote,
                    'capacity'        => $capacity,
                    'booked_count'    => $bookedCount,
                    'available_units' => $availableUnits,
                    'bookings'        => $dayBookings->values()->toArray(),
                ];
            }

            $stats['blocked_days']      = $blockedCount;
            $stats['available_days']    = $daysCount - $blockedCount;
            $stats['custom_price_days'] = $customCount;
            $stats['avg_rate']          = $daysCount > 0 ? round($totalRateSum / $daysCount, 2) : $baseRate;
            $stats['total_bookings']    = $bookings->count();
        }

        return view('admin.rooms.availability', compact(
            'properties',
            'selectedProperty',
            'selectedRoom',
            'matrix',
            'stats',
            'startDate',
            'endDate',
            'daysCount'
        ));
    }

    /**
     * Bulk update availability, blocking, and custom rates for a date range.
     */
    public function updateRange(Request $request)
    {
        $validated = $request->validate([
            'room_id'    => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'action'     => 'required|in:block,unblock,set_price,reset_price',
            'price'      => 'nullable|numeric|min:0',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $start = Carbon::parse($validated['start_date']);
        $end   = Carbon::parse($validated['end_date']);
        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $dt) {
            $dateStr = $dt->format('Y-m-d');
            $record = RoomAvailability::firstOrNew([
                'room_id' => $room->id,
                'date'    => $dateStr,
            ]);

            switch ($validated['action']) {
                case 'block':
                    $record->is_blocked = 1;
                    $record->status = 'blocked';
                    break;
                case 'unblock':
                    $record->is_blocked = 0;
                    $record->status = 'available';
                    break;
                case 'set_price':
                    if (isset($validated['price']) && (float)$validated['price'] > 0) {
                        $record->price = (float) $validated['price'];
                    }
                    break;
                case 'reset_price':
                    $record->price = (float) $room->price_per_night;
                    break;
            }

            $record->save();

            // Clear real-time cached availability
            Cache::forget("room_avail_units_{$room->id}_{$dateStr}");
        }

        return back()->with('success', "Extranet room matrix updated successfully for {$period->count()} nights.");
    }
}
