<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Folio;
use App\Models\Hotel;
use App\Services\FinancialLedgerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialBillingController extends Controller
{
    public function __construct(
        protected FinancialLedgerService $ledgerService
    ) {}

    private function getHotelId(): int
    {
        return auth()->user()?->hotel_id ?? Hotel::value('id') ?? 1;
    }

    public function transactions(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $transactions = $this->ledgerService->getTransactions($hotelId, $request->get('search'), 20);
        $kpis = $this->ledgerService->getTransactionKpis($hotelId);

        return view('pages.transaction', compact('transactions', 'kpis'));
    }

    public function paymentSettings(): View
    {
        $hotelId = $this->getHotelId();
        $gateways = $this->ledgerService->getGatewaySettings($hotelId);

        return view('pages.payment-set', compact('gateways'));
    }

    public function saveGateway(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $gateway = $request->input('gateway', 'STRIPE');

        $validated = $request->validate([
            'gateway' => 'required|string',
            'is_enabled' => 'nullable',
            'environment' => 'nullable|string',
            'currency_code' => 'nullable|string',
            'endpoint_url' => 'nullable|string',
            'publishable_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
        ]);

        $validated['is_enabled'] = $request->has('is_enabled');

        $this->ledgerService->saveGateway($hotelId, $gateway, $validated);

        return redirect()->back()->with('success', strtoupper($gateway) . ' gateway configuration saved successfully!');
    }

    public function bookingReport(Request $request)
    {
        $hotelId = $this->getHotelId();

        if ($request->get('export') === 'csv') {
            $reports = $this->ledgerService->getBookingReports($hotelId, $request->get('from'), $request->get('to'), $request->get('booking_status'), $request->get('payment_status'), 5000);
            $filename = 'booking-report-' . date('Y-m-d') . '.csv';
            return response()->streamDownload(function () use ($reports) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Reservation Code', 'Guest Name', 'Room', 'Check In', 'Check Out', 'Total Amount', 'Paid Amount', 'Balance', 'Status']);
                foreach ($reports as $r) {
                    fputcsv($handle, [
                        $r->reservation_code,
                        $r->guest?->full_name ?? 'N/A',
                        $r->room?->room_number ?? 'N/A',
                        $r->check_in_date?->format('Y-m-d'),
                        $r->check_out_date?->format('Y-m-d'),
                        $r->total_amount,
                        $r->paid_amount,
                        $r->balance_amount,
                        $r->status,
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        $reports = $this->ledgerService->getBookingReports(
            $hotelId,
            $request->get('from'),
            $request->get('to'),
            $request->get('booking_status'),
            $request->get('payment_status'),
            20
        );

        return view('pages.booking-report', compact('reports'));
    }

    public function folioInvoice(int $id): View
    {
        $hotelId = $this->getHotelId();
        $folio = Folio::with(['reservation.guest', 'reservation.room', 'reservation.roomType', 'items', 'payments'])
            ->where('hotel_id', $hotelId)
            ->findOrFail($id);

        $hotel = \App\Models\Hotel::find($hotelId);

        return view('pages.folio-invoice', compact('folio', 'hotel'));
    }

    public function accountsSetting(): View
    {
        $user = auth()->user();
        return view('pages.accounts-setting', compact('user'));
    }
}
