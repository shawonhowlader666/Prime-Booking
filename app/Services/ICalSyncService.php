<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ICalSyncService
 * ───────────────
 * Handles RFC 5545 iCalendar (.ics) exports & imports for Room Inventory
 * Synchronizes availability with Airbnb, Booking.com, Agoda, and Expedia.
 */
class ICalSyncService
{
    /**
     * Generate RFC 5545 compliant iCalendar (.ics) string for a Room.
     */
    public function generateIcsForRoom(Room $room, $bookings = null, $blockedDates = null): string
    {
        if ($bookings === null) {
            $bookings = Booking::where('room_id', $room->id)
                ->where(function ($q) {
                    $q->whereIn('status', ['confirmed', 'paid', 'pending'])
                      ->orWhereIn('booking_status', ['confirmed', 'paid']);
                })
                ->whereDate('check_out', '>=', now()->subMonths(1))
                ->get();
        }

        if ($blockedDates === null) {
            $blockedDates = RoomAvailability::where('room_id', $room->id)
                ->where(function ($q) {
                    $q->where('is_blocked', 1)
                      ->orWhere('available_qty', '<=', 0);
                })
                ->whereDate('date', '>=', now()->subMonths(1))
                ->get();
        }

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Prime Booking Enterprise OTA//Channel Manager iCal 2.0//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:' . addcslashes($room->property->name . ' - ' . $room->name, "\n\r;,"),
            'X-WR-TIMEZONE:UTC',
        ];

        // Export Bookings as VEVENT
        foreach ($bookings as $b) {
            $checkIn  = Carbon::parse($b->check_in)->format('Ymd');
            $checkOut = Carbon::parse($b->check_out)->format('Ymd');
            $uid      = 'booking-' . $b->id . '-' . md5($b->booking_reference) . '@primebooking.com';
            $dtstamp  = $b->created_at ? $b->created_at->format('Ymd\THis\Z') : now()->format('Ymd\THis\Z');

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = "UID:{$uid}";
            $lines[] = "DTSTAMP:{$dtstamp}";
            $lines[] = "DTSTART;VALUE=DATE:{$checkIn}";
            $lines[] = "DTEND;VALUE=DATE:{$checkOut}";
            $lines[] = "SUMMARY:Reserved - Prime Booking #{$b->booking_reference}";
            $lines[] = "DESCRIPTION:Guest: {$b->guest_name} | Ref: {$b->booking_reference}";
            $lines[] = 'STATUS:CONFIRMED';
            $lines[] = 'END:VEVENT';
        }

        // Export Manual Blocked Dates
        foreach ($blockedDates as $blk) {
            $dateStart = Carbon::parse($blk->date)->format('Ymd');
            $dateEnd   = Carbon::parse($blk->date)->addDay()->format('Ymd');
            $uid       = 'block-' . $blk->id . '@primebooking.com';

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = "UID:{$uid}";
            $lines[] = "DTSTAMP:" . now()->format('Ymd\THis\Z');
            $lines[] = "DTSTART;VALUE=DATE:{$dateStart}";
            $lines[] = "DTEND;VALUE=DATE:{$dateEnd}";
            $lines[] = "SUMMARY:Blocked (Owner/Manual)";
            $lines[] = 'STATUS:CONFIRMED';
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines);
    }

    /**
     * Import external iCal feed (from Airbnb/Booking.com/Agoda) and block dates.
     */
    public function importExternalIcs(Room $room, string $icsUrl): array
    {
        try {
            $response = Http::timeout(10)->get($icsUrl);
            if (!$response->successful()) {
                return ['success' => false, 'message' => 'Failed to fetch calendar feed from URL.'];
            }

            $body = $response->body();
            $events = $this->parseIcsEvents($body);

            $blockedCount = 0;
            foreach ($events as $ev) {
                $start = Carbon::parse($ev['start'])->startOfDay();
                $end   = Carbon::parse($ev['end'])->startOfDay();

                $period = \Carbon\CarbonPeriod::create($start, $end->copy()->subDay());
                foreach ($period as $day) {
                    $dateStr = $day->format('Y-m-d');
                    RoomAvailability::updateOrCreate(
                        [
                            'room_id' => $room->id,
                            'date'    => $dateStr,
                        ],
                        [
                            'is_blocked'    => 1,
                            'status'        => 'blocked',
                            'available_qty' => 0,
                            'note'          => 'Synced from external iCal (' . ($ev['summary'] ?? 'OTA') . ')',
                        ]
                    );
                    $blockedCount++;
                }
            }

            return [
                'success'       => true,
                'message'       => "Calendar synced successfully. {$blockedCount} date slots updated.",
                'events_parsed' => count($events),
            ];
        } catch (\Throwable $e) {
            Log::error("iCal sync error for room #{$room->id}: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error parsing iCal: ' . $e->getMessage()];
        }
    }

    /**
     * Simple parser for VEVENT DTSTART and DTEND from iCalendar data.
     */
    private function parseIcsEvents(string $icsContent): array
    {
        $events = [];
        $rawEvents = explode('BEGIN:VEVENT', $icsContent);
        array_shift($rawEvents); // Remove header

        foreach ($rawEvents as $raw) {
            $eventBlock = explode('END:VEVENT', $raw)[0];
            $dtStart = null;
            $dtEnd = null;
            $summary = 'External Reservation';

            if (preg_match('/DTSTART(?:;VALUE=DATE)?:(\d{8}(?:T\d{6}Z?)?)/i', $eventBlock, $mStart)) {
                $dtStart = $mStart[1];
            }
            if (preg_match('/DTEND(?:;VALUE=DATE)?:(\d{8}(?:T\d{6}Z?)?)/i', $eventBlock, $mEnd)) {
                $dtEnd = $mEnd[1];
            }
            if (preg_match('/SUMMARY:(.*)/i', $eventBlock, $mSum)) {
                $summary = trim($mSum[1]);
            }

            if ($dtStart && $dtEnd) {
                $events[] = [
                    'start'   => $dtStart,
                    'end'     => $dtEnd,
                    'summary' => $summary,
                ];
            }
        }

        return $events;
    }
}
