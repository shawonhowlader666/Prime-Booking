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

        if ($gateways->isEmpty()) {
            // Created without credentials and switched off. An administrator enters the real
            // keys from the payment provider and then activates the gateway.
            $defaults = [
                ['gateway_code' => 'sslcommerz', 'name' => 'SSLCommerz (Visa, Mastercard, Amex, bKash, Nagad, Rocket)', 'is_active' => false, 'is_sandbox' => true],
                ['gateway_code' => 'bkash', 'name' => 'bKash Tokenized Checkout', 'is_active' => false, 'is_sandbox' => true],
                ['gateway_code' => 'pay_at_hotel', 'name' => 'Pay at Property / Cash on Check-in', 'is_active' => true, 'is_sandbox' => false],
            ];
            foreach ($defaults as $item) {
                PaymentGateway::create($item);
            }
            $gateways = PaymentGateway::all();
        }

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
            'merchant_id' => $validated['merchant_id'] ?? null,
            // Secret fields are not echoed back into the form; a blank value means "unchanged".
            'api_key'     => filled($validated['api_key'] ?? null) ? $validated['api_key'] : $gateway->api_key,
            'api_secret'  => filled($validated['api_secret'] ?? null) ? $validated['api_secret'] : $gateway->api_secret,
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
