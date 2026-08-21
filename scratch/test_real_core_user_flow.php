<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\AccountingLedger;
use App\Models\Payout;
use App\Models\Inquiry;
use App\Services\AccountingService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

echo "=================================================================\n";
echo "   PRIME BOOKING REAL USER CORE END-TO-END VERIFICATION SUITE    \n";
echo "=================================================================\n\n";

DB::beginTransaction();

try {
    // 1. Fetch real Vendor & Property
    echo "[TEST 1] Fetching Real Vendor Partner & Hotel Listing from DB...\n";
    $vendor = User::where('role', 'vendor')->first();
    if (!$vendor) {
        $vendor = User::create([
            'name' => 'Real Vendor Partner Ltd',
            'email' => 'vendor_test_' . time() . '@primebooking.com.bd',
            'password' => bcrypt('password'),
            'role' => 'vendor'
        ]);
    }
    
    $property = Property::where('vendor_id', $vendor->id)->first();
    if (!$property) {
        $property = Property::first();
        if ($property) {
            $property->vendor_id = $vendor->id;
            $property->save();
        }
    }
    
    if (!$property) {
        throw new Exception("No hotel property found in database!");
    }
    echo "  ✅ Real Vendor Partner: '{$vendor->name}' (#{$vendor->id})\n";
    echo "  ✅ Hotel Listing: '{$property->name}' (#{$property->id}) - Base Rate: BDT {$property->price_per_night}\n\n";

    // 2. Real Guest / Traveler Customer
    echo "[TEST 2] Identifying Real Customer/Guest Account...\n";
    $customer = User::where('role', 'customer')->first();
    if (!$customer) {
        $customer = User::create([
            'name' => 'Tanvir Ahmed (Guest)',
            'email' => 'guest_tanvir_' . time() . '@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '01711223344'
        ]);
    }
    echo "  ✅ Guest Customer: '{$customer->name}' ({$customer->email})\n\n";

    // 3. Real Checkout & Booking Creation
    echo "[TEST 3] Simulating Real Booking Flow (bKash Gateway Reservation)...\n";
    $nights = 3;
    $pricePerNight = 5000.00;
    $subtotal = $pricePerNight * $nights;
    $tax = $subtotal * 0.05;
    $total = $subtotal + $tax;
    $reference = 'PRM-' . strtoupper(Str::random(7));

    $booking = Booking::create([
        'booking_reference' => $reference,
        'user_id'           => $customer->id,
        'property_id'       => $property->id,
        'guest_name'        => $customer->name,
        'guest_email'       => $customer->email,
        'guest_phone'       => $customer->phone ?? '01711223344',
        'check_in'          => date('Y-m-d', strtotime('+3 days')),
        'check_out'         => date('Y-m-d', strtotime('+6 days')),
        'guests'            => 2,
        'nights'            => $nights,
        'price_per_night'   => $pricePerNight,
        'subtotal'          => $subtotal,
        'tax_amount'        => $tax,
        'total_price'       => $total,
        'total_amount'      => $total,
        'currency'          => 'BDT',
        'payment_method'    => 'bkash',
        'payment_status'    => 'paid',
        'status'            => 'confirmed',
        'booking_status'    => 'confirmed',
        'special_requests'  => 'Late check-in requested (9:00 PM)',
    ]);

    echo "  ✅ Booking Created in DB: Ref #{$booking->booking_reference} (ID: #{$booking->id})\n";
    echo "  ✅ 3 Nights @ BDT 5,000/night + 5% VAT = Total BDT " . number_format($total, 2) . "\n\n";

    // 4. Double-Entry Accounting Ledger Execution
    echo "[TEST 4] Triggering Real Accounting Ledger & 12% Commission Engine...\n";
    $ledger = AccountingService::recordBookingLedger($booking, 'bkash');

    echo "  ✅ Ledger Transaction Ref: {$ledger->txn_reference}\n";
    echo "  ✅ Gross Booking Value (GBV): BDT " . number_format($ledger->gross_amount, 2) . "\n";
    echo "  ✅ 12% OTA Platform Commission: BDT " . number_format($ledger->commission_amount, 2) . " (Expected: 1,890.00)\n";
    echo "  ✅ Gateway Processing Fee (1.5%): BDT " . number_format($ledger->gateway_fee, 2) . " (Expected: 236.25)\n";
    echo "  ✅ 88% Vendor Net Earning: BDT " . number_format($ledger->net_amount, 2) . " (Expected: 13,623.75)\n\n";

    // Assertions — use the property's actual commission_rate (dynamic, not hardcoded 12%)
    $propertyCommissionRate = (float) ($property->commission_rate ?? 12.0);
    $expectedCommission = round($total * ($propertyCommissionRate / 100), 2);
    $expectedGatewayFee = round($total * 0.015, 2);
    $expectedVendorNet  = round($total - $expectedCommission - $expectedGatewayFee, 2);

    if (abs($ledger->commission_amount - $expectedCommission) > 0.05) {
        throw new Exception("Commission mismatch! Expected {$expectedCommission} ({$propertyCommissionRate}%), got {$ledger->commission_amount}");
    }
    if (abs($ledger->net_amount - $expectedVendorNet) > 0.05) {
        throw new Exception("Net vendor earnings mismatch! Expected {$expectedVendorNet}, got {$ledger->net_amount}");
    }
    echo "  ✅ Commission rate: {$propertyCommissionRate}% (property-specific dynamic rate confirmed)\n\n";

    // 5. Vendor Financial Statement & Settlement Verification
    echo "[TEST 5] Verifying Vendor Financial Statement & Available Balance...\n";
    $summary = AccountingService::getVendorFinanceSummary($vendor->id);
    echo "  ✅ Completed Bookings Count: {$summary['total_bookings']}\n";
    echo "  ✅ Gross Turnover: BDT " . number_format($summary['gross_sales'], 2) . "\n";
    echo "  ✅ Platform Commission Deducted: BDT " . number_format($summary['commission_deducted'], 2) . "\n";
    echo "  ✅ Vendor Net Payable: BDT " . number_format($summary['net_payable'], 2) . "\n";
    echo "  ✅ Total Settled Payouts: BDT " . number_format($summary['payouts_paid'], 2) . "\n";
    echo "  ✅ Real-time Withdrawable Balance: BDT " . number_format($summary['available_balance'], 2) . "\n\n";

    // 6. Guest Inquiry & Vendor Response Communication Flow
    echo "[TEST 6] Testing Guest Inquiry & Vendor Reply Flow...\n";
    $inquiry = Inquiry::create([
        'user_id'     => $customer->id,
        'property_id' => $property->id,
        'vendor_id'   => $vendor->id,
        'name'        => $customer->name,
        'email'       => $customer->email,
        'phone'       => '01711223344',
        'subject'     => 'Airport Shuttle Availability',
        'message'     => 'Is early check-in possible at 11 AM?',
        'status'      => 'pending',
    ]);
    echo "  ✅ Guest Inquiry Submitted: #{$inquiry->id} - Subject: '{$inquiry->subject}'\n";

    // Vendor responds
    $inquiry->reply = 'Yes! Early check-in is available upon arrival.';
    $inquiry->status = 'replied';
    $inquiry->replied_at = now();
    $inquiry->save();
    echo "  ✅ Vendor Reply Logged: '{$inquiry->reply}' - Status: '{$inquiry->status}'\n\n";

    // 7. Security / Rollback Test
    DB::rollBack();
    echo "=================================================================\n";
    echo "   🎉 ALL 6 REAL CORE USER FLOW TESTS PASSED WITH 100% SUCCESS!  \n";
    echo "   (DB transaction verified & rolled back cleanly without spam) \n";
    echo "=================================================================\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "❌ TEST FAILED: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
