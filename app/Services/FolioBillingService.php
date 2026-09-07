<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FolioBillingService
{
    /**
     * Add item/charge to Folio (Food, Laundry, Room Service, etc.)
     */
    public function addItem(Folio $folio, array $data, ?User $actor = null): FolioItem
    {
        return DB::transaction(function () use ($folio, $data, $actor) {
            $unitPrice = $data['unit_price'];
            $quantity = $data['quantity'] ?? 1;
            $total = $unitPrice * $quantity;

            $item = FolioItem::create([
                'hotel_id' => $folio->hotel_id,
                'folio_id' => $folio->id,
                'item_type' => $data['item_type'],
                'description' => $data['description'],
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'total_amount' => $total,
                'posted_by_user_id' => $actor?->id,
                'posted_at' => now(),
            ]);

            $folio->recalculateBalance();

            AuditLog::create([
                'hotel_id' => $folio->hotel_id,
                'user_id' => $actor?->id,
                'action' => 'FOLIO_CHARGE_POSTED',
                'entity_type' => FolioItem::class,
                'entity_id' => $item->id,
                'new_values' => $item->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $item;
        });
    }

    /**
     * Record a Payment with Idempotency.
     */
    public function recordPayment(Folio $folio, array $data, ?User $actor = null): Payment
    {
        return DB::transaction(function () use ($folio, $data, $actor) {
            $idempotencyKey = $data['idempotency_key'] ?? null;

            if ($idempotencyKey) {
                $existing = Payment::where('idempotency_key', $idempotencyKey)->first();
                if ($existing) {
                    return $existing;
                }
            }

            $payment = Payment::create([
                'hotel_id' => $folio->hotel_id,
                'folio_id' => $folio->id,
                'reservation_id' => $folio->reservation_id,
                'payment_method' => $data['payment_method'],
                'amount' => $data['amount'],
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'idempotency_key' => $idempotencyKey,
                'status' => 'SUCCESS',
                'processed_by_user_id' => $actor?->id,
            ]);

            $folio->recalculateBalance();

            // Update reservation paid amount
            if ($folio->reservation) {
                $folio->reservation->paid_amount = $folio->total_payments;
                $folio->reservation->balance_amount = $folio->balance;
                $folio->reservation->save();
            }

            AuditLog::create([
                'hotel_id' => $folio->hotel_id,
                'user_id' => $actor?->id,
                'action' => 'PAYMENT_COLLECTED',
                'entity_type' => Payment::class,
                'entity_id' => $payment->id,
                'new_values' => $payment->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $payment;
        });
    }
}
