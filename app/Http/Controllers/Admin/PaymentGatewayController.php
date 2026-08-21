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
            $defaults = [
                [
                    'gateway_code' => 'bkash',
                    'name'         => 'bKash Direct Checkout (Personal & Merchant)',
                    'is_active'    => true,
                    'is_sandbox'   => true,
                    'merchant_id'  => '01770887733',
                    'api_key'      => 'bkash_sandbox_api_key_prime',
                    'api_secret'   => 'bkash_sandbox_secret_prime',
                ],
                [
                    'gateway_code' => 'nagad',
                    'name'         => 'Nagad Online PGW Checkout',
                    'is_active'    => true,
                    'is_sandbox'   => true,
                    'merchant_id'  => 'NAGAD_MERCHANT_01',
                    'api_key'      => 'nagad_pub_key_sandbox',
                    'api_secret'   => 'nagad_sec_key_sandbox',
                ],
                [
                    'gateway_code' => 'sslcommerz',
                    'name'         => 'SSLCommerz (Visa, Mastercard, Amex, DBBL, City Touch)',
                    'is_active'    => true,
                    'is_sandbox'   => true,
                    'merchant_id'  => 'primebookinglive',
                    'api_key'      => 'primebookinglive_store_id',
                    'api_secret'   => 'primebookinglive_store_passwd',
                ],
                [
                    'gateway_code' => 'pay_at_hotel',
                    'name'         => 'Pay at Property / Cash on Check-in (Agoda Guarantee)',
                    'is_active'    => true,
                    'is_sandbox'   => false,
                    'merchant_id'  => 'CASH_DESK',
                    'api_key'      => null,
                    'api_secret'   => null,
                ],
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
