<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Hotel;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NightAuditService
{
    /**
     * Execute the Night Audit process for a specific hotel and business date.
     */
    public function runAudit(Hotel $hotel, ?string $businessDate = null): array
    {
        $auditDate = $businessDate ? Carbon::parse($businessDate)->toDateString() : Carbon::yesterday()->toDateString();

        Log::info("Starting Night Audit for Hotel: {$hotel->name} (Code: {$hotel->code}) on Date: {$auditDate}");

        return DB::transaction(function () use ($hotel, $auditDate) {
            $postedRoomCharges = 0;
            $totalTaxPosted = 0.00;
            $totalServiceChargePosted = 0.00;
            $overdueReservations = 0;

            // 1. Fetch all active in-house reservations
            $inHouseReservations = Reservation::where('hotel_id', $hotel->id)
                ->where('status', 'CHECKED_IN')
                ->where('check_in_date', '<=', $auditDate)
                ->where('check_out_date', '>', $auditDate)
                ->with(['folio', 'roomType', 'room'])
                ->get();

            foreach ($inHouseReservations as $res) {
                if (!$res->folio) {
                    continue;
                }

                $folio = $res->folio;

                // Check if room charge for this specific night is already posted to prevent duplicate charges
                $alreadyPosted = FolioItem::where('folio_id', $folio->id)
                    ->where('item_type', 'ROOM_CHARGE')
                    ->where('description', 'like', "%Night of {$auditDate}%")
                    ->exists();

                if (!$alreadyPosted) {
                    $nightlyRate = $res->roomType?->base_price ?? 0;
                    if ($nightlyRate <= 0) {
                        $nightsCount = max(1, Carbon::parse($res->check_in_date)->diffInDays(Carbon::parse($res->check_out_date)));
                        $nightlyRate = $res->total_amount / $nightsCount;
                    }

                    // 1. Post Room Night Charge
                    FolioItem::create([
                        'hotel_id' => $hotel->id,
                        'folio_id' => $folio->id,
                        'item_type' => 'ROOM_CHARGE',
                        'description' => "Room Night Tariff ({$res->room?->room_number}) - Night of {$auditDate}",
                        'unit_price' => $nightlyRate,
                        'quantity' => 1,
                        'total_amount' => $nightlyRate,
                        'posted_at' => now(),
                    ]);
                    $postedRoomCharges++;

                    // 2. Post Service Charge if configured
                    if ($hotel->service_charge_percentage > 0) {
                        $serviceAmount = round(($nightlyRate * $hotel->service_charge_percentage) / 100, 2);
                        FolioItem::create([
                            'hotel_id' => $hotel->id,
                            'folio_id' => $folio->id,
                            'item_type' => 'SERVICE_CHARGE',
                            'description' => "Service Charge ({$hotel->service_charge_percentage}%) for {$auditDate}",
                            'unit_price' => $serviceAmount,
                            'quantity' => 1,
                            'total_amount' => $serviceAmount,
                            'posted_at' => now(),
                        ]);
                        $totalServiceChargePosted += $serviceAmount;
                    }

                    // 3. Post VAT/Tax if configured
                    if ($hotel->tax_percentage > 0) {
                        $taxAmount = round(($nightlyRate * $hotel->tax_percentage) / 100, 2);
                        FolioItem::create([
                            'hotel_id' => $hotel->id,
                            'folio_id' => $folio->id,
                            'item_type' => 'TAX',
                            'description' => "Tax/VAT ({$hotel->tax_percentage}%) for {$auditDate}",
                            'unit_price' => $taxAmount,
                            'quantity' => 1,
                            'total_amount' => $taxAmount,
                            'posted_at' => now(),
                        ]);
                        $totalTaxPosted += $taxAmount;
                    }

                    $folio->recalculateBalance();

                    $res->total_amount = $folio->total_charges;
                    $res->balance_amount = $folio->balance;
                    $res->save();
                }
            }

            // 2. Check for overdue reservations (expected checkout before audit date, but still status CHECKED_IN)
            $overdueList = Reservation::where('hotel_id', $hotel->id)
                ->where('status', 'CHECKED_IN')
                ->where('check_out_date', '<=', $auditDate)
                ->get();
            $overdueReservations = $overdueList->count();

            // 3. Create High-Level Audit Log
            $summary = [
                'audit_date' => $auditDate,
                'in_house_count' => $inHouseReservations->count(),
                'room_charges_posted' => $postedRoomCharges,
                'total_tax_posted' => $totalTaxPosted,
                'total_service_charge_posted' => $totalServiceChargePosted,
                'overdue_reservations' => $overdueReservations,
                'completed_at' => now()->toIso8601String(),
            ];

            AuditLog::create([
                'hotel_id' => $hotel->id,
                'action' => 'NIGHT_AUDIT_EXECUTED',
                'entity_type' => Hotel::class,
                'entity_id' => $hotel->id,
                'new_values' => $summary,
                'ip_address' => '127.0.0.1 (CLI/Scheduler)',
                'user_agent' => 'Prime-PMS Night Audit Daemon',
            ]);

            return $summary;
        });
    }
}
