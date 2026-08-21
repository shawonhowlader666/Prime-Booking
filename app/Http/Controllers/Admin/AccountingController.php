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

        return view('admin.accounts.index', compact('kpis', 'chartData', 'recentLedgers', 'startDate', 'endDate', 'year'));
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

        $ledgers = $this->accountingService->getGeneralLedger($filters, 25);

        return view('admin.accounts.ledger', compact('ledgers', 'filters'));
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
     * High-Speed CSV Stream Export for General Ledger
     */
    public function exportLedger(Request $request): StreamedResponse
    {
        $filters = [
            'type'           => $request->query('type', 'all'),
            'payment_method' => $request->query('payment_method', 'all'),
            'start_date'     => $request->query('start_date'),
            'end_date'       => $request->query('end_date'),
        ];

        $ledgers = $this->accountingService->getGeneralLedger($filters, 5000);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="prime_booking_general_ledger_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($ledgers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['TXN Ref', 'Type', 'Category', 'Gross (BDT)', 'Commission (BDT)', 'Gateway Fee', 'Net (BDT)', 'Method', 'Status', 'Date', 'Description']);

            foreach ($ledgers as $l) {
                fputcsv($handle, [
                    $l->txn_reference,
                    strtoupper($l->type),
                    $l->category,
                    $l->gross_amount,
                    $l->commission_amount,
                    $l->gateway_fee,
                    $l->net_amount,
                    strtoupper($l->payment_method ?? 'N/A'),
                    ucfirst($l->status),
                    $l->created_at ? $l->created_at->format('Y-m-d H:i:s') : '',
                    $l->description,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
