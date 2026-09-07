<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hotel_id' => $this->hotel_id,
            'folio_number' => $this->folio_number,
            'total_charges' => (float) $this->total_charges,
            'total_payments' => (float) $this->total_payments,
            'balance' => (float) $this->balance,
            'status' => $this->status,
            'items' => $this->items->map(fn($item) => [
                'id' => $item->id,
                'item_type' => $item->item_type,
                'description' => $item->description,
                'unit_price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'total_amount' => (float) $item->total_amount,
                'posted_at' => $item->posted_at?->toIso8601String(),
            ]),
            'payments' => $this->payments->map(fn($payment) => [
                'id' => $payment->id,
                'payment_method' => $payment->payment_method,
                'amount' => (float) $payment->amount,
                'transaction_reference' => $payment->transaction_reference,
                'status' => $payment->status,
                'created_at' => $payment->created_at?->toIso8601String(),
            ]),
            'closed_at' => $this->closed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
