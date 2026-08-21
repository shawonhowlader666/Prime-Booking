<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\AccountingService;
use App\Models\AccountingLedger;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorAccountingController extends Controller
{
    public function __construct(
        protected AccountingService $accountingService
    ) {}

    /**
     * Vendor Financial Accounts & Balance Hub
     */
    public function index(Request $request): View
    {
        $vendorId = (int) auth()->id();
        $finance  = $this->accountingService->getSingleVendorAccounting($vendorId);
        
        $ledgers  = AccountingLedger::where('vendor_id', $vendorId)
            ->latest('id')
            ->paginate(15);

        $recentPayouts = Payout::where('vendor_id', $vendorId)
            ->latest('id')
            ->take(5)
            ->get();

        return view('vendor.accounts.index', compact('finance', 'ledgers', 'recentPayouts'));
    }

    /**
     * Official Printable Statement with QR Code for Vendor
     */
    public function printStatement(): View
    {
        $vendorId   = (int) auth()->id();
        $vendor     = auth()->user();
        $properties = \App\Models\Property::where('vendor_id', $vendorId)->get();
        $finance    = $this->accountingService->getSingleVendorAccounting($vendorId);
        $ledgers    = \App\Models\AccountingLedger::where('vendor_id', $vendorId)->latest('id')->get();

        return view('pages.vendor-statement-print', compact('vendor', 'properties', 'finance', 'ledgers'));
    }
}
