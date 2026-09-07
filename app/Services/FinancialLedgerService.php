<?php

namespace App\Services;

use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\HotelPaymentGateway;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialLedgerService
{
    /**
     * Get paginated transactions / payments stream for hotel.
     */
    public function getTransactions(int $hotelId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return Payment::withoutGlobalScopes()
            ->with(['reservation.guest', 'reservation.room', 'folio'])
            ->where('hotel_id', $hotelId)
            ->when($search, function ($q) use ($search) {
                $q->where('transaction_reference', 'like', "%{$search}%")
                  ->orWhereHas('reservation.guest', fn($g) => $g->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get aggregate transaction statistics (Bills, Food, Utility, Shopping/Extras).
     */
    public function getTransactionKpis(int $hotelId): array
    {
        $totalPayments = Payment::withoutGlobalScopes()->where('hotel_id', $hotelId)->where('status', 'SUCCESS')->sum('amount');
        
        $foodCharges = FolioItem::withoutGlobalScopes()->whereHas('folio', fn($f) => $f->where('hotel_id', $hotelId))
            ->where('item_type', 'like', '%FOOD%')
            ->sum('total_price');

        $amenityCharges = FolioItem::withoutGlobalScopes()->whereHas('folio', fn($f) => $f->where('hotel_id', $hotelId))
            ->whereIn('item_type', ['AMENITY', 'CAB_SERVICE', 'LAUNDRY'])
            ->sum('total_price');

        return [
            'total_bill' => (float) $totalPayments,
            'food' => (float) $foodCharges,
            'utility' => (float) ($totalPayments * 0.05), // Estimated tax/utility share
            'shopping' => (float) $amenityCharges,
        ];
    }

    /**
     * Get all payment gateway records indexed by gateway code.
     */
    public function getGatewaySettings(int $hotelId): Collection
    {
        return HotelPaymentGateway::where('hotel_id', $hotelId)->get()->keyBy('gateway');
    }

    /**
     * Update or create a payment gateway configuration.
     */
    public function saveGateway(int $hotelId, string $gateway, array $data): HotelPaymentGateway
    {
        return HotelPaymentGateway::updateOrCreate(
            ['hotel_id' => $hotelId, 'gateway' => strtoupper($gateway)],
            [
                'is_enabled' => $data['is_enabled'] ?? false,
                'environment' => $data['environment'] ?? 'sandbox',
                'currency_code' => $data['currency_code'] ?? 'USD',
                'endpoint_url' => $data['endpoint_url'] ?? null,
                'publishable_key' => $data['publishable_key'] ?? null,
                'secret_key' => $data['secret_key'] ?? null,
                'webhook_secret' => $data['webhook_secret'] ?? null,
            ]
        );
    }

    /**
     * Get booking reports with filters on date range and statuses.
     */
    public function getBookingReports(
        int $hotelId,
        ?string $from = null,
        ?string $to = null,
        ?string $bookingStatus = null,
        ?string $paymentStatus = null,
        int $perPage = 20
    ): LengthAwarePaginator {
        return Reservation::with(['guest', 'room.roomType', 'folio.payments'])
            ->where('hotel_id', $hotelId)
            ->when($from, fn($q) => $q->whereDate('check_in_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('check_out_date', '<=', $to))
            ->when($bookingStatus, fn($q) => $q->where('status', $bookingStatus))
            ->latest()
            ->paginate($perPage);
    }
}
