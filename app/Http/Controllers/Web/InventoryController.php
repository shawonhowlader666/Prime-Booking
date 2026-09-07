<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\InventoryItem;
use App\Models\ItemCategory;
use App\Models\ItemSupplier;
use App\Models\ItemUnit;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    private function getActiveHotel(Request $request): Hotel
    {
        $user = $request->user();
        if ($user && $user->hotel_id) {
            return Hotel::find($user->hotel_id) ?? Hotel::first() ?? new Hotel(['name' => 'Grand Palace Hotel', 'id' => 1]);
        }
        return Hotel::first() ?? new Hotel(['name' => 'Grand Palace Hotel', 'id' => 1]);
    }

    // 1. Items Directory
    public function items(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $categories = ItemCategory::where('hotel_id', $hotel->id)->where('is_active', true)->get();
        $units = ItemUnit::where('hotel_id', $hotel->id)->where('is_active', true)->get();
        $suppliers = ItemSupplier::where('hotel_id', $hotel->id)->where('is_active', true)->get();

        $metrics = $this->inventoryService->getValuationMetrics($hotel->id);

        $items = InventoryItem::with(['category', 'unit', 'supplier'])
            ->where('hotel_id', $hotel->id)
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn($sub) => $sub->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(15);

        return view('pages.items-list', compact('hotel', 'items', 'categories', 'units', 'suppliers', 'metrics'));
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'sku' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:item_categories,id',
            'unit_id' => 'nullable|exists:item_units,id',
            'supplier_id' => 'nullable|exists:item_suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'current_stock' => 'nullable|numeric|min:0',
            'min_alert_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $sku = $validated['sku'] ?? 'SKU-' . strtoupper(substr($validated['name'], 0, 3)) . '-' . rand(100, 999);

        $item = InventoryItem::create([
            'hotel_id' => $hotel->id,
            'category_id' => $validated['category_id'] ?? null,
            'unit_id' => $validated['unit_id'] ?? null,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'name' => $validated['name'],
            'sku' => $sku,
            'purchase_price' => $validated['purchase_price'],
            'unit_price' => $validated['unit_price'] ?? $validated['purchase_price'],
            'current_stock' => $validated['current_stock'] ?? 0.00,
            'min_alert_stock' => $validated['min_alert_stock'] ?? 5.00,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        // If initial stock was provided, log purchase movement
        if (!empty($validated['current_stock']) && $validated['current_stock'] > 0) {
            StockMovement::create([
                'hotel_id' => $hotel->id,
                'item_id' => $item->id,
                'user_id' => $request->user()?->id,
                'type' => 'PURCHASE_IN',
                'quantity' => $validated['current_stock'],
                'unit_cost' => $validated['purchase_price'],
                'total_cost' => $validated['current_stock'] * $validated['purchase_price'],
                'reference_no' => 'INIT-STOCK',
                'notes' => 'Initial stock registered on item creation',
            ]);
        }

        return redirect()->back()->with('success', "Item '{$item->name}' added to inventory successfully!");
    }

    public function purchaseStock(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.1',
            'unit_cost' => 'required|numeric|min:0',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $movement = $this->inventoryService->recordPurchaseIn(
            $hotel->id,
            $validated['item_id'],
            (float) $validated['quantity'],
            (float) $validated['unit_cost'],
            $validated['reference_no'] ?? null,
            $request->user()?->id,
            $validated['notes'] ?? null
        );

        return redirect()->back()->with('success', "Stock purchase received! Added {$movement->quantity} units to inventory.");
    }

    public function recordDamage(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.1',
            'reason' => 'required|string|max:255',
        ]);

        $this->inventoryService->recordDamageWriteOff(
            $hotel->id,
            $validated['item_id'],
            (float) $validated['quantity'],
            $request->user()?->id,
            $validated['reason']
        );

        return redirect()->back()->with('success', "Damaged stock write-off logged successfully!");
    }

    // 2. Categories
    public function categories(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $categories = ItemCategory::withCount('items')
            ->where('hotel_id', $hotel->id)
            ->latest()
            ->get();

        return view('pages.items-category-list', compact('hotel', 'categories'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        ItemCategory::create([
            'hotel_id' => $hotel->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', "Category '{$validated['name']}' created successfully!");
    }

    // 3. Units
    public function units(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $units = ItemUnit::withCount('items')
            ->where('hotel_id', $hotel->id)
            ->latest()
            ->get();

        return view('pages.items-unit-list', compact('hotel', 'units'));
    }

    public function storeUnit(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'short_code' => 'required|string|max:20',
        ]);

        ItemUnit::create([
            'hotel_id' => $hotel->id,
            'name' => $validated['name'],
            'short_code' => $validated['short_code'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', "Unit '{$validated['name']}' ({$validated['short_code']}) added successfully!");
    }

    // 4. Suppliers
    public function suppliers(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $suppliers = ItemSupplier::withCount('items')
            ->where('hotel_id', $hotel->id)
            ->latest()
            ->paginate(15);

        return view('pages.items-suppliers-list', compact('hotel', 'suppliers'));
    }

    public function storeSupplier(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
        ]);

        ItemSupplier::create([
            'hotel_id' => $hotel->id,
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'tax_number' => $validated['tax_number'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', "Supplier '{$validated['name']}' registered successfully!");
    }

    // 5. Damaged & Destroyed Goods
    public function destroyedList(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $items = InventoryItem::where('hotel_id', $hotel->id)->where('is_active', true)->get();

        $damages = StockMovement::with(['item.category', 'user'])
            ->where('hotel_id', $hotel->id)
            ->where('type', 'DAMAGED')
            ->latest()
            ->paginate(15);

        return view('pages.items-destroyed-list', compact('hotel', 'damages', 'items'));
    }

    // 6. Reports
    public function stockReport(Request $request)
    {
        $hotel = $this->getActiveHotel($request);

        if ($request->get('export') === 'csv') {
            $items = InventoryItem::with(['category', 'unit', 'supplier'])
                ->where('hotel_id', $hotel->id)
                ->get();
            $filename = 'stock-inventory-report-' . date('Y-m-d') . '.csv';
            return response()->streamDownload(function () use ($items) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['SKU', 'Item Name', 'Category', 'Current Stock', 'Unit', 'Unit Cost', 'Total Valuation', 'Status']);
                foreach ($items as $item) {
                    $status = $item->current_stock <= $item->min_alert_stock ? 'LOW_STOCK' : 'HEALTHY';
                    fputcsv($handle, [
                        $item->sku ?? 'N/A',
                        $item->name,
                        $item->category?->name ?? 'Uncategorized',
                        $item->current_stock,
                        $item->unit?->name ?? 'pcs',
                        $item->unit_price,
                        $item->current_stock * $item->unit_price,
                        $status,
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        $metrics = $this->inventoryService->getValuationMetrics($hotel->id);

        $items = InventoryItem::with(['category', 'unit', 'supplier'])
            ->where('hotel_id', $hotel->id)
            ->latest()
            ->paginate(20);

        return view('pages.stock-report', compact('hotel', 'items', 'metrics'));
    }

    public function purchaseReport(Request $request)
    {
        $hotel = $this->getActiveHotel($request);

        if ($request->get('export') === 'csv') {
            $purchases = StockMovement::with(['item.supplier', 'user'])
                ->where('hotel_id', $hotel->id)
                ->where('type', 'PURCHASE_IN')
                ->get();
            $filename = 'purchase-report-' . date('Y-m-d') . '.csv';
            return response()->streamDownload(function () use ($purchases) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Date', 'Reference No', 'Item Name', 'Supplier', 'Quantity', 'Unit Cost', 'Total Cost', 'Received By']);
                foreach ($purchases as $p) {
                    fputcsv($handle, [
                        $p->created_at?->format('Y-m-d H:i'),
                        $p->reference_no ?? 'N/A',
                        $p->item?->name ?? 'N/A',
                        $p->item?->supplier?->name ?? 'Direct',
                        $p->quantity,
                        $p->unit_cost,
                        $p->total_cost,
                        $p->user?->name ?? 'Staff',
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        $purchases = StockMovement::with(['item.supplier', 'user'])
            ->where('hotel_id', $hotel->id)
            ->where('type', 'PURCHASE_IN')
            ->latest()
            ->paginate(20);

        $totalPurchasesValuation = StockMovement::where('hotel_id', $hotel->id)
            ->where('type', 'PURCHASE_IN')
            ->sum('total_cost');

        return view('pages.purchase-report', compact('hotel', 'purchases', 'totalPurchasesValuation'));
    }
}
