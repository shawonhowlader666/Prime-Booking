<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VendorPromotionController extends Controller
{
    /**
     * Show vendor's active promotion coupons.
     * GET /vendor/promotions
     */
    public function index(): View
    {
        $vendorId = auth()->id();
        $coupons = Coupon::where('vendor_id', $vendorId)->latest()->paginate(10);
        $properties = Property::where('vendor_id', $vendorId)->get();

        return view('vendor.promotions.index', compact('coupons', 'properties'));
    }

    /**
     * Show create promotion coupon form.
     * GET /vendor/promotions/create
     */
    public function create(): View
    {
        $properties = Property::where('vendor_id', auth()->id())->get();

        return view('vendor.promotions.create', compact('properties'));
    }

    /**
     * Store new vendor promotion coupon.
     * POST /vendor/promotions
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'           => 'required|string|max:50|unique:coupons,code',
            'type'           => 'required|in:percent,fixed',
            'value'          => 'required|numeric|min:1',
            'min_spend'      => 'nullable|numeric|min:0',
            'expires_at'     => 'required|date|after:today',
            'property_id'    => 'nullable|exists:properties,id',
        ]);

        $coupon = new Coupon();
        $coupon->vendor_id    = auth()->id();
        $coupon->code         = strtoupper(trim($validated['code']));
        $coupon->type         = $validated['type'];
        $coupon->value        = $validated['value'];
        $coupon->min_spend    = $validated['min_spend'] ?? 0;
        $coupon->expires_at   = $validated['expires_at'];
        $coupon->property_id  = $validated['property_id'] ?? null;
        $coupon->is_active    = true;
        $coupon->save();

        return redirect()->route('vendor.promotions.index')->with('success', 'Promo coupon created successfully!');
    }

    public function edit(int $id): View
    {
        $coupon = Coupon::where('vendor_id', auth()->id())->findOrFail($id);
        $properties = Property::where('vendor_id', auth()->id())->get();
        return view('vendor.promotions.edit', compact('coupon', 'properties'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $coupon = Coupon::where('vendor_id', auth()->id())->findOrFail($id);
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type'        => 'required|in:percent,percentage,fixed',
            'value'       => 'nullable|numeric|min:1',
            'amount'      => 'nullable|numeric|min:1',
            'min_spend'   => 'nullable|numeric|min:0',
            'expires_at'  => 'nullable|date',
            'property_id' => 'nullable|exists:properties,id',
        ]);

        $val = $validated['amount'] ?? $validated['value'] ?? $coupon->amount;
        $coupon->update([
            'code'        => strtoupper(trim($validated['code'])),
            'type'        => $validated['type'],
            'amount'      => $val,
            'min_spend'   => $validated['min_spend'] ?? 0,
            'expires_at'  => $validated['expires_at'] ?? $coupon->expires_at,
            'property_id' => $validated['property_id'] ?? null,
        ]);

        return redirect()->route('vendor.promotions.index')->with('success', 'Promotion coupon updated successfully.');
    }

    /**
     * Delete vendor promo coupon.
     * DELETE /vendor/promotions/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $coupon = Coupon::where('vendor_id', auth()->id())->findOrFail($id);
        $coupon->delete();

        return redirect()->route('vendor.promotions.index')->with('success', 'Promo coupon deleted.');
    }
}
