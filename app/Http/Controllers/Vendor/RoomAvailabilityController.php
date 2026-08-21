<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RoomAvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $vendorId = auth()->id();
        abort_unless($vendorId, 403, 'Unauthorized vendor access.');

        // Fetch vendor properties with minimal column selection for high performance
        $properties = Property::where('vendor_id', $vendorId)
            ->select(['id', 'vendor_id', 'name', 'city', 'type'])
            ->with(['rooms:id,property_id,name,price_per_night,total_rooms'])
            ->get();

        $selectedRoomId = $request->query('room_id');
        $daysCount      = max(7, min(90, (int)($request->query('days', 30))));
        $selectedRoom   = null;
        $availabilities = collect();
        $startDate      = Carbon::now()->startOfDay();
        $endDate        = Carbon::now()->addDays($daysCount - 1)->endOfDay();

        if ($selectedRoomId) {
            $selectedRoom = Room::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
                ->where('id', $selectedRoomId)
                ->first();
        } elseif ($properties->isNotEmpty() && $properties->first()->rooms->isNotEmpty()) {
            $selectedRoom = $properties->first()->rooms->first();
        }

        $stats = [
            'total_days'        => $daysCount,
            'available_days'    => 0,
            'sold_out_days'     => 0,
            'custom_price_days' => 0,
            'avg_price'         => 0,
        ];

        if ($selectedRoom) {
            $cacheKey = "vendor:{$vendorId}:room:{$selectedRoom->id}:" . $startDate->format('Ymd') . ":{$daysCount}";

            $availabilities = Cache::remember($cacheKey, 300, function () use ($selectedRoom, $startDate, $endDate) {
                return RoomAvailability::where('room_id', $selectedRoom->id)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->select(['room_id', 'date', 'price', 'is_blocked'])
                    ->get()
                    ->keyBy(fn($item) => is_string($item->date) ? $item->date : $item->date->format('Y-m-d'));
            });

            // Calculate KPI stats
            $soldOut = 0;
            $custom  = 0;
            $totalPriceSum = 0;

            for ($d = 0; $d < $daysCount; $d++) {
                $dtStr  = $startDate->copy()->addDays($d)->format('Y-m-d');
                $record = $availabilities->get($dtStr);

                if ($record && $record->is_blocked) {
                    $soldOut++;
                }
                if ($record && $record->price && (float)$record->price !== (float)$selectedRoom->price_per_night) {
                    $custom++;
                    $totalPriceSum += (float)$record->price;
                } else {
                    $totalPriceSum += (float)$selectedRoom->price_per_night;
                }
            }

            $stats['sold_out_days']     = $soldOut;
            $stats['available_days']    = $daysCount - $soldOut;
            $stats['custom_price_days'] = $custom;
            $stats['avg_price']         = $daysCount > 0 ? round($totalPriceSum / $daysCount) : (float)$selectedRoom->price_per_night;
        }

        return view('vendor.rooms.availability', compact(
            'properties', 'selectedRoom', 'availabilities',
            'startDate', 'endDate', 'daysCount', 'stats'
        ));
    }

    public function updateRange(Request $request)
    {
        $vendorId = auth()->id();
        abort_unless($vendorId, 403, 'Unauthorized vendor access.');

        $validated = $request->validate([
            'room_id'    => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'price'      => 'nullable|numeric|min:0',
            'is_blocked' => 'nullable|boolean',
        ]);

        $room = Room::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
            ->where('id', $validated['room_id'])
            ->firstOrFail();

        $start     = Carbon::parse($validated['start_date']);
        $end       = Carbon::parse($validated['end_date']);
        $isBlocked = $request->boolean('is_blocked');
        $price     = $request->filled('price') ? (float)$validated['price'] : null;

        // Atomic Bulk Upsert: 1 single high-performance SQL query regardless of range length
        $records = [];
        $now = now();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $records[] = [
                'room_id'       => $room->id,
                'date'          => $date->format('Y-m-d'),
                'price'         => $price,
                'is_blocked'    => $isBlocked,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        if (!empty($records)) {
            RoomAvailability::upsert(
                $records,
                ['room_id', 'date'],
                ['price', 'is_blocked', 'updated_at']
            );
        }

        // Invalidate Redis/application caches for this room across all date scopes
        for ($d = 0; $d <= 30; $d++) {
            $dayKey = Carbon::now()->subDays($d)->format('Ymd');
            Cache::forget("vendor:{$vendorId}:room:{$room->id}:{$dayKey}:14");
            Cache::forget("vendor:{$vendorId}:room:{$room->id}:{$dayKey}:30");
            Cache::forget("vendor:{$vendorId}:room:{$room->id}:{$dayKey}:60");
            Cache::forget("vendor:{$vendorId}:room:{$room->id}:{$dayKey}:90");
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'     => 'success',
                'message'    => 'Rates & availability updated instantly!',
                'records'    => $records,
                'room_id'    => $room->id,
                'base_price' => (float)$room->price_per_night,
            ]);
        }

        return back()->with('success', '✅ Room rates & availability updated successfully for selected date range!');
    }

    /**
     * Apply Dynamic Weekend Surge Pricing (Fri & Sat +X%)
     */
    public function applyWeekendSurge(Request $request)
    {
        $vendorId = auth()->id();
        abort_unless($vendorId, 403, 'Unauthorized vendor access.');

        $validated = $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'surge_percentage' => 'required|numeric|min:1|max:100',
        ]);

        $room = Room::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
            ->where('id', $validated['room_id'])
            ->firstOrFail();

        $start      = Carbon::parse($validated['start_date']);
        $end        = Carbon::parse($validated['end_date']);
        $basePrice  = (float) $room->price_per_night;
        $surgePct   = (float) $validated['surge_percentage'];
        $surgePrice = round($basePrice * (1 + ($surgePct / 100)));

        $records = [];
        $now = now();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            // In Bangladesh / International Hospitality, Friday (5) & Saturday (6) are peak weekends
            if ($date->isFriday() || $date->isSaturday()) {
                $records[] = [
                    'room_id'       => $room->id,
                    'date'          => $date->format('Y-m-d'),
                    'price'         => $surgePrice,
                    'is_blocked'    => false,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
        }

        if (!empty($records)) {
            RoomAvailability::upsert(
                $records,
                ['room_id', 'date'],
                ['price', 'is_blocked', 'updated_at']
            );
        }

        // Invalidate cache
        for ($d = 0; $d <= 30; $d++) {
            $dayKey = Carbon::now()->subDays($d)->format('Ymd');
            Cache::forget("vendor:{$vendorId}:room:{$room->id}:{$dayKey}:30");
        }

        return back()->with('success', "⚡ Applied +{$surgePct}% Weekend Surge Rate (৳" . number_format($surgePrice) . "/night) on " . count($records) . " weekend days!");
    }
}
