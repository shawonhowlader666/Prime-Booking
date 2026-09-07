<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Folio;
use App\Services\FolioBillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FolioController extends Controller
{
    public function __construct(
        protected FolioBillingService $billingService
    ) {}

    public function show(int $id, Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $folio = Folio::with(['reservation.guest', 'reservation.room', 'items.postedBy', 'payments.processedBy'])
            ->where('hotel_id', $hotelId)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $folio,
        ]);
    }

    public function addItem(int $id, Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;
        $folio = Folio::where('hotel_id', $hotelId)->where('id', $id)->firstOrFail();

        $data = $request->validate([
            'item_type' => 'required|string|in:ROOM_CHARGE,FOOD_BEVERAGE,LAUNDRY,MINIBAR,SPA,EXTRA_BED,SERVICE_CHARGE,TAX,DISCOUNT,OTHER',
            'description' => 'required|string|max:255',
            'unit_price' => 'required|numeric',
            'quantity' => 'integer|min:1',
        ]);

        $item = $this->billingService->addItem($folio, $data, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Charge added to folio successfully',
            'data' => $item,
            'folio' => $folio->fresh(),
        ]);
    }

    public function recordPayment(int $id, Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;
        $folio = Folio::where('hotel_id', $hotelId)->where('id', $id)->firstOrFail();

        $data = $request->validate([
            'payment_method' => 'required|string|in:CASH,CARD_POS,BKASH,NAGAD,BANK_TRANSFER,ONLINE',
            'amount' => 'required|numeric|min:0.01',
            'transaction_reference' => 'nullable|string|max:100',
            'idempotency_key' => 'nullable|string|max:64',
        ]);

        $payment = $this->billingService->recordPayment($folio, $data, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'data' => $payment,
            'folio' => $folio->fresh(),
        ]);
    }
}
