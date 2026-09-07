<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\ItemCategory;
use App\Models\ItemSupplier;
use App\Models\ItemUnit;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    private function getHotelId(Request $request): int
    {
        return $request->user()?->hotel_id ?? 1;
    }

    public function items(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);

        $items = InventoryItem::with(['category', 'unit', 'supplier'])
            ->where('hotel_id', $hotelId)
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn($sub) => $sub->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Inventory items fetched',
            'data' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'total' => $items->total(),
                'per_page' => $items->perPage(),
            ],
        ]);
    }

    public function recordPurchase(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);

        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $movement = $this->inventoryService->recordPurchaseIn(
            $hotelId,
            (int)$validated['item_id'],
            (float)$validated['quantity'],
            (float)$validated['unit_cost'],
            $validated['reference_no'] ?? null,
            $request->user()?->id,
            $validated['notes'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Stock purchase recorded and stock incremented atomically',
            'data' => $movement,
        ], 201);
    }

    public function recordDamage(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);

        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|string',
        ]);

        $movement = $this->inventoryService->recordDamageWriteOff(
            $hotelId,
            $validated['item_id'],
            (float)$validated['quantity'],
            $request->user()?->id,
            $validated['reason']
        );

        return response()->json([
            'success' => true,
            'message' => 'Damage write-off recorded and stock decremented atomically',
            'data' => $movement,
        ], 201);
    }

    public function valuation(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);
        $metrics = $this->inventoryService->getValuationMetrics($hotelId);

        return response()->json([
            'success' => true,
            'message' => 'Inventory valuation metrics calculated',
            'data' => $metrics,
        ]);
    }
}
