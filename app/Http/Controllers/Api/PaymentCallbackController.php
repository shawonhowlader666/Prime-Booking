<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\PaymentGateway;

class PaymentCallbackController extends Controller
{
    /**
     * IPN / Webhook callback handler for payment gateways (bKash, Nagad, SSLCommerz, Stripe)
     */
    public function handleCallback(Request $request, $gateway)
    {
        $gatewayModel = PaymentGateway::where('gateway_code', $gateway)->first();

        if (!$gatewayModel || !$gatewayModel->is_active) {
            return response()->json(['success' => false, 'message' => 'Gateway inactive or unsupported.'], 400);
        }

        $reference = $request->input('tran_id') ?: ($request->input('paymentID') ?: $request->input('reference'));
        $status    = strtolower($request->input('status', $request->input('transactionStatus', 'success')));

        if (!$reference) {
            return response()->json(['success' => false, 'message' => 'Missing booking reference in payload.'], 422);
        }

        $booking = Booking::where('booking_reference', $reference)->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => "Booking reference {$reference} not found."], 404);
        }

        if (in_array($status, ['success', 'completed', 'valid', 'paid'])) {
            $booking->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment IPN processed successfully. Booking status updated to PAID.',
                'data'    => [
                    'reference'      => $booking->booking_reference,
                    'payment_status' => $booking->payment_status,
                ],
            ]);
        }

        $booking->update(['payment_status' => 'failed']);

        return response()->json(['success' => false, 'message' => 'Payment transaction failed or cancelled.'], 400);
    }
}
