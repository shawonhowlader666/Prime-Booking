<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice #{{ $booking->booking_reference }} | PRIME BOOKING</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            font-size: 13.5px;
            padding: 30px 15px;
        }
        .invoice-card {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            .no-print, header, footer { display: none !important; }
            body { background: #fff !important; padding: 0 !important; }
            .invoice-card { box-shadow: none !important; border: 1px solid #ddd !important; border-radius: 0 !important; }
        }
    </style>
</head>
<body>

    <div class="no-print d-flex align-items-center justify-content-between mb-4 mx-auto" style="max-width: 820px;">
        <a href="{{ route('booking.history') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-bold">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Reservations
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('booking.voucher.download', $booking->booking_reference) }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-bold">
                <i class="fa-solid fa-ticket me-1"></i> Hotel Check-in Voucher
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm px-4 rounded-pill fw-bold">
                <i class="fa-solid fa-print me-1"></i> Print / Save PDF Invoice
            </button>
        </div>
    </div>

    <div class="invoice-card">
        
        {{-- Invoice Header --}}
        <div class="p-4 p-md-5 border-bottom bg-light d-flex align-items-center justify-content-between flex-wrap gap-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="fs-4 fw-bold text-primary" style="letter-spacing: -0.5px;">PRIME BOOKING</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-0.5 rounded" style="font-size: 11px;">OFFICIAL INVOICE</span>
                </div>
                <p class="text-secondary small mb-0">Prime Aviation &amp; Hospitality Technologies Ltd.<br>Tax Reg. / BIN: <strong>004829104-0101</strong> • Dhaka, Bangladesh</p>
            </div>
            <div class="text-md-end">
                <h4 class="fw-bold text-dark mb-1">TAX INVOICE</h4>
                <div class="mono small text-secondary">
                    Invoice No: <strong class="text-dark">INV-{{ $booking->booking_reference }}</strong><br>
                    Date Issued: <span class="text-dark">{{ $booking->created_at ? $booking->created_at->format('d M Y') : now()->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Billed To and Stay Details --}}
        <div class="p-4 p-md-5 border-bottom">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted small text-uppercase mb-2">Billed To (Lead Guest)</h6>
                    <h5 class="fw-bold text-dark mb-1">{{ $booking->guest_name }}</h5>
                    <p class="text-secondary small mb-0">
                        Email: <strong>{{ $booking->guest_email }}</strong><br>
                        Phone: <strong>{{ $booking->guest_phone }}</strong><br>
                        Payment Method: <strong class="text-uppercase">{{ $booking->payment_method ?? 'bKash Online' }}</strong>
                    </p>
                </div>
                <div class="col-md-6 border-start-md ps-md-4">
                    <h6 class="fw-bold text-muted small text-uppercase mb-2">Reservation Details</h6>
                    <h5 class="fw-bold text-dark mb-1">{{ $booking->property->name ?? 'Hotel Stay' }}</h5>
                    <p class="text-secondary small mb-0">
                        Location: <strong>{{ $booking->property->address ?? $booking->property->city ?? 'Bangladesh' }}</strong><br>
                        Room Type: <strong>{{ $booking->room->name ?? 'Standard Room' }}</strong><br>
                        Stay Period: <strong>{{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }} → {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</strong> ({{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }} Nights)
                    </p>
                </div>
            </div>
        </div>

        {{-- Items & Charges Table --}}
        <div class="p-4 p-md-5 border-bottom">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead class="border-bottom text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-0">Description</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Nights / Qty</th>
                            <th class="text-end pe-0">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="border-bottom">
                        @php
                            $nights = max(1, \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)));
                            $roomRate = $booking->room?->price_per_night ?? $booking->property?->price_per_night ?? ($booking->total_amount / $nights);
                            $roomSubtotal = $roomRate * $nights;
                        @endphp
                        <tr>
                            <td class="ps-0 py-3">
                                <strong class="text-dark d-block">{{ $booking->property->name ?? 'Hotel Room Stay' }}</strong>
                                <small class="text-secondary">{{ $booking->room->name ?? 'Standard Suite' }} (Max {{ $booking->guests ?? 2 }} Guests)</small>
                            </td>
                            <td class="text-center">{{ \App\Services\CurrencyService::format($roomRate) }}</td>
                            <td class="text-center">{{ $nights }}</td>
                            <td class="text-end pe-0 font-monospace fw-bold text-dark">{{ \App\Services\CurrencyService::format($roomSubtotal) }}</td>
                        </tr>

                        {{-- Add-ons if any --}}
                        @if(isset($addons) && count($addons) > 0)
                            @foreach($addons as $addon)
                            <tr>
                                <td class="ps-0 py-2">
                                    <strong class="text-dark d-block"><i class="fa-solid fa-plus-circle text-primary me-1"></i> {{ $addon->name }}</strong>
                                </td>
                                <td class="text-center">{{ \App\Services\CurrencyService::format($addon->price) }}</td>
                                <td class="text-center">1</td>
                                <td class="text-end pe-0 font-monospace fw-bold text-dark">{{ \App\Services\CurrencyService::format($addon->price) }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Summary Breakdown --}}
            <div class="row justify-content-end mt-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between py-1 text-secondary small">
                        <span>Subtotal:</span>
                        <span class="mono fw-bold text-dark">{{ \App\Services\CurrencyService::format($booking->total_amount * 0.925) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 text-secondary small">
                        <span>VAT &amp; Government Taxes (7.5%):</span>
                        <span class="mono fw-bold text-dark">{{ \App\Services\CurrencyService::format($booking->total_amount * 0.075) }}</span>
                    </div>
                    @if($booking->discount_amount > 0)
                    <div class="d-flex justify-content-between py-1 text-success small">
                        <span>Promotional Discount:</span>
                        <span class="mono fw-bold">-{{ \App\Services\CurrencyService::format($booking->discount_amount) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between py-2 border-top border-2 mt-2 align-items-center">
                        <strong class="text-dark fs-6">Grand Total Paid:</strong>
                        <strong class="text-primary fs-5 mono">{{ \App\Services\CurrencyService::format($booking->total_amount) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Verification Stamp & Footer --}}
        <div class="p-4 p-md-5 bg-light d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 text-success fw-bold small mb-1">
                    <i class="fa-solid fa-circle-check fs-5"></i> PAYMENT STATUS: PAID &amp; SETTLED
                </div>
                <small class="text-secondary d-block" style="font-size: 11px;">This is a computer-generated tax receipt and requires no physical signature.</small>
            </div>
            <div class="text-md-end mono text-muted small" style="font-size: 11px;">
                TRx Reference: <strong>{{ $booking->booking_reference }}</strong><br>
                Security Stamp: <strong>PRM-VERIFIED-AUTH</strong>
            </div>
        </div>

    </div>

</body>
</html>
