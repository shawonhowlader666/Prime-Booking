@extends('layouts.app')

@section('title', 'Folio Invoice ' . $folio->folio_number)

@push('styles')
<style>
    @media print {
        #pms-sidebar, .pms-header, .no-print { display: none !important; }
        #pms-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .invoice-card { border: none !important; box-shadow: none !important; }
    }
    .invoice-card {
        max-width: 800px;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('reservations') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Back to Reservations</a>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print me-2"></i>Print Folio Invoice</button>
        </div>
    </div>

    <div class="card invoice-card shadow-sm p-4 p-md-5">
        <!-- Invoice Header -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-primary text-white p-2 rounded-3">
                        <i class="fa-solid fa-hotel fs-5"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $hotel->name ?? 'Grand Palace Hotel & Resort' }}</h4>
                </div>
                <div class="text-muted small">{{ $hotel->address ?? 'Marine Drive, Coxs Bazar' }}</div>
                <div class="text-muted small"><i class="fa-solid fa-phone me-1"></i>{{ $hotel->phone ?? '+880 1711-000111' }} &bull; <i class="fa-solid fa-envelope me-1"></i>{{ $hotel->email ?? 'info@grandpalace.com' }}</div>
            </div>
            <div class="text-end">
                <span class="badge bg-primary fs-6 mb-2">GUEST FOLIO</span>
                <div class="fw-bold font-monospace text-primary fs-5">{{ $folio->folio_number }}</div>
                <small class="text-muted">Date: {{ date('M d, Y') }}</small>
            </div>
        </div>

        <!-- Guest & Booking Info Grid -->
        <div class="row g-3 mb-4 pb-3 border-bottom">
            <div class="col-sm-6">
                <h6 class="text-muted text-uppercase small fw-bold">Billed To (Guest)</h6>
                <div class="fw-bold fs-6">{{ $folio->reservation->guest->full_name ?? 'Guest' }}</div>
                <div class="text-muted small"><i class="fa-solid fa-phone me-1"></i>{{ $folio->reservation->guest->phone ?? 'N/A' }}</div>
                <div class="text-muted small"><i class="fa-solid fa-id-card me-1"></i>ID: {{ $folio->reservation->guest->id_type ?? 'NID' }}</div>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="text-muted text-uppercase small fw-bold">Reservation Details</h6>
                <div class="fw-semibold">Booking Code: <span class="text-primary font-monospace">{{ $folio->reservation->reservation_code }}</span></div>
                <div class="small">Room: <span class="badge bg-light text-dark border">Room {{ $folio->reservation->room->room_number ?? 'N/A' }}</span> ({{ $folio->reservation->roomType->name ?? 'Standard' }})</div>
                <div class="small text-muted">Stay: {{ $folio->reservation->check_in_date->format('M d, Y') }} &rarr; {{ $folio->reservation->check_out_date->format('M d, Y') }}</div>
            </div>
        </div>

        <!-- Folio Items Table -->
        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Charge Description</th>
                        <th>Type</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($folio->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="fw-semibold">{{ $item->description }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->item_type }}</span></td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end fw-bold">৳{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary & Balance -->
        <div class="row justify-content-end mb-4">
            <div class="col-sm-6">
                <div class="p-3 bg-light rounded-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Charges:</span>
                        <span class="fw-bold">৳{{ number_format($folio->total_charges, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Total Paid / Received:</span>
                        <span class="fw-bold">- ৳{{ number_format($folio->total_payments, 2) }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fs-5 fw-bold {{ $folio->balance > 0 ? 'text-danger' : 'text-success' }}">
                        <span>Balance Due:</span>
                        <span>৳{{ number_format($folio->balance, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Breakdown -->
        @if($folio->payments->count() > 0)
            <div class="border-top pt-3 mb-4">
                <h6 class="text-uppercase small fw-bold text-muted mb-2">Payment Receipts</h6>
                <div class="row g-2">
                    @foreach($folio->payments as $p)
                        <div class="col-sm-6">
                            <div class="p-2 border rounded small d-flex justify-content-between">
                                <span><i class="fa-solid fa-credit-card me-1 text-primary"></i>{{ $p->payment_method }} (Ref: {{ $p->transaction_reference ?? 'N/A' }})</span>
                                <span class="fw-bold text-success">৳{{ number_format($p->amount, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Footer Notice -->
        <div class="text-center text-muted small border-top pt-3 mt-auto">
            <div>Thank you for choosing {{ $hotel->name ?? 'Grand Palace Hotel' }}! We look forward to welcoming you again.</div>
            <div style="font-size: 0.7rem;">Generated by Prime Cloud Hotel Management System &bull; All Taxes & Service Charges Included.</div>
        </div>
    </div>
</div>
@endsection
