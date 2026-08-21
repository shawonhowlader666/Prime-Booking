<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{
    public function __construct(
        protected AccountingService $accountingService
    ) {}

    /**
     * Master Accounts Hub & Executive P&L Overview
     */
    public function index(Request $request): View
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $year      = (int) $request->query('year', date('Y'));

        $kpis      = $this->accountingService->getOverviewKPIs($startDate, $endDate);
        $chartData = $this->accountingService->getMonthlyPnLChartData($year);
        $recentLedgers = $this->accountingService->getGeneralLedger([], 8);
        $vendorsList   = \App\Models\User::where('role', 'vendor')->select('id', 'name', 'email')->get();

        return view('admin.accounts.index', compact('kpis', 'chartData', 'recentLedgers', 'startDate', 'endDate', 'year', 'vendorsList'));
    }

    /**
     * Double-Entry General Ledger
     */
    public function ledger(Request $request): View
    {
        $filters = [
            'type'           => $request->query('type', 'all'),
            'payment_method' => $request->query('payment_method', 'all'),
            'start_date'     => $request->query('start_date'),
            'end_date'       => $request->query('end_date'),
            'search'         => $request->query('search'),
        ];

        $ledgers     = $this->accountingService->getGeneralLedger($filters, 25);
        $vendorsList = \App\Models\User::where('role', 'vendor')->select('id', 'name', 'email')->get();

        return view('admin.accounts.ledger', compact('ledgers', 'filters', 'vendorsList'));
    }

    /**
     * Store Manual Ledger Transaction / Commission Adjustment / Offline Payment
     */
    public function storeManualEntry(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'txn_reference'     => 'nullable|string|max:64|unique:accounting_ledgers,txn_reference',
            'type'              => 'required|in:credit,debit,commission,payout,refund,gateway_fee',
            'category'          => 'required|string|max:64',
            'vendor_id'         => 'nullable|exists:users,id',
            'gross_amount'      => 'required|numeric|min:0',
            'commission_amount' => 'nullable|numeric|min:0',
            'gateway_fee'       => 'nullable|numeric|min:0',
            'payment_method'    => 'required|string|max:32',
            'description'       => 'required|string|max:255',
            'notes'             => 'nullable|string|max:500',
            'created_at'        => 'nullable|date',
        ]);

        $this->accountingService->recordManualEntry($validated);

        return redirect()->back()->with('success', 'Manual transaction recorded successfully in Accounting General Ledger.');
    }

    /**
     * Vendor Financial Statements & Payout Balances
     */
    public function vendorStatements(Request $request): View
    {
        $vendors = $this->accountingService->getVendorStatements(null, 25);
        return view('admin.accounts.vendor-statements', compact('vendors'));
    }

    /**
     * Official Printable Statement with QR Code
     */
    public function printVendorStatement($vendorId): View
    {
        $vendor     = \App\Models\User::where('role', 'vendor')->findOrFail($vendorId);
        $properties = \App\Models\Property::where('vendor_id', $vendorId)->get();
        $finance    = $this->accountingService->getSingleVendorAccounting((int)$vendorId);
        $ledgers    = \App\Models\AccountingLedger::where('vendor_id', $vendorId)->latest('id')->get();

        return view('pages.vendor-statement-print', compact('vendor', 'properties', 'finance', 'ledgers'));
    }

    /**
     * High-Speed CSV Stream Export for General Ledger (Zero-Memory O(1) Cursor Streaming)
     */
    public function exportLedger(Request $request): StreamedResponse
    {
        $filters = [
            'type'           => $request->query('type', 'all'),
            'payment_method' => $request->query('payment_method', 'all'),
            'start_date'     => $request->query('start_date'),
            'end_date'       => $request->query('end_date'),
        ];

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="prime_booking_general_ledger_' . date('Y-m-d') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($filters) {
            $handle = fopen('php://output', 'w');
            $this->accountingService->streamLedgerCsv($filters, $handle);
            fclose($handle);
        }, 200, $headers);
    }
}
