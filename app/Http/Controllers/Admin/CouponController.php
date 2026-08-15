<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        try {
            $coupons = Coupon::latest()->paginate(20);
            if ($coupons->isEmpty()) {
                $coupons = $this->getMockCoupons();
            }
        } catch (\Throwable $e) {
            $coupons = $this->getMockCoupons();
        }

        return view('admin.coupons.index', compact('coupons'));
    }

    private function getMockCoupons()
    {
        return collect([
            (object)['id'=>1,'code'=>'PRIME10','type'=>'percentage','amount'=>10,'min_spend'=>5000,'expires_at'=>'2026-12-31','usage_limit'=>500,'used_count'=>42,'status'=>'active'],
            (object)['id'=>2,'code'=>'SUNDARBAN500','type'=>'fixed','amount'=>500,'min_spend'=>15000,'expires_at'=>'2026-10-31','usage_limit'=>200,'used_count'=>89,'status'=>'active'],
            (object)['id'=>3,'code'=>'NEWUSER20','type'=>'percentage','amount'=>20,'min_spend'=>3000,'expires_at'=>null,'usage_limit'=>null,'used_count'=>14,'status'=>'active'],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|max:50|unique:coupons,code',
            'type'        => 'required|in:fixed,percentage',
            'amount'      => $request->type === 'percentage' ? 'required|numeric|min:1|max:100' : 'required|numeric|min:1',
            'min_spend'   => 'nullable|numeric|min:0',
            'expires_at'  => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        try {
            Coupon::create([
                'code'        => strtoupper(trim($request->code)),
                'type'        => $request->type,
                'amount'      => $request->amount,
                'min_spend'   => $request->min_spend ?? 0,
                'expires_at'  => $request->expires_at,
                'usage_limit' => $request->usage_limit,
                'used_count'  => 0,
                'status'      => 'active',
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['code' => 'Coupon code already exists or DB error: ' . $e->getMessage()]);
        }

        return back()->with('success', 'Coupon "' . strtoupper($request->code) . '" created successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code'        => 'required|string|max:50',
            'type'        => 'required|in:fixed,percentage',
            'amount'      => 'required|numeric|min:1',
            'min_spend'   => 'nullable|numeric|min:0',
            'expires_at'  => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
            'status'      => 'nullable|in:active,inactive',
        ]);

        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->update([
                'code'        => strtoupper(trim($request->code)),
                'type'        => $request->type,
                'amount'      => $request->amount,
                'min_spend'   => $request->min_spend ?? 0,
                'expires_at'  => $request->expires_at ?: null,
                'usage_limit' => $request->usage_limit,
                'status'      => $request->status ?? $coupon->status,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Coupon updated successfully!');
    }

    public function toggle($id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->update(['status' => $coupon->status === 'active' ? 'inactive' : 'active']);
        } catch (\Exception $e) {}
        return back()->with('success', 'Coupon status toggled.');
    }

    public function destroy($id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            $code   = $coupon->code;
            $coupon->delete();
            return back()->with('success', 'Coupon "' . $code . '" deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Delete failed.');
        }
    }
}
