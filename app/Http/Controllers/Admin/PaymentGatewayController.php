<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Cache;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::all();
        return view('admin.gateways.index', compact('gateways'));
    }

    public function update(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'merchant_id' => 'nullable|string|max:255',
            'api_key'     => 'nullable|string|max:500',
            'api_secret'  => 'nullable|string|max:500',
            'is_sandbox'  => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
        ]);

        $gateway->update([
            'name'        => $validated['name'],
            'merchant_id' => $validated['merchant_id'],
            'api_key'     => $validated['api_key'],
            'api_secret'  => $validated['api_secret'],
            'is_sandbox'  => $request->has('is_sandbox'),
            'is_active'   => $request->has('is_active'),
        ]);

        Cache::forget('active_payment_gateways');

        return back()->with('success', "Payment gateway '{$gateway->name}' updated successfully!");
    }

    public function toggleStatus($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->is_active = !$gateway->is_active;
        $gateway->save();

        Cache::forget('active_payment_gateways');

        $statusStr = $gateway->is_active ? 'Enabled' : 'Disabled';
        return back()->with('success', "Payment gateway '{$gateway->name}' is now {$statusStr}.");
    }
}
