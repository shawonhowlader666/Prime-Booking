@extends('layouts.admin')

@section('title', 'Airport Taxi & Transfer Reservations — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0"><i class="fa-solid fa-taxi me-2 text-primary"></i>Airport Transfer &amp; Taxi Reservations</h1>
        <div style="display:flex; align-items:center; gap:8px;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('transfersTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('transfersTable', 'Transfer_Reservations')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('transfersTable', 'Transfer_Reservations')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-tbl-copy" onclick="printTable('transfersTable')"><i class="fa-solid fa-print"></i> Print</button>
            <a href="{{ route('admin.transfers.index') }}" class="btn-tbl-copy" style="text-decoration:none;"><i class="fa-solid fa-route"></i> Manage Routes</a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.transfers.index') }}">Airport Transfers</a>
        <span class="sep">-</span><strong style="color:#333;">Passenger Reservations</strong>
    </div>
</div>

<div class="page-content-area">
    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="admin-alert error mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- KPI Cards --}}
    @php
        $coll = method_exists($bookings, 'getCollection') ? $bookings->getCollection() : collect($bookings);
        $totalBookings = method_exists($bookings, 'total') ? $bookings->total() : $coll->count();
        $confirmedCount = $coll->where('status', 'confirmed')->count();
        $completedCount = $coll->where('status', 'completed')->count();
        $totalRevenue   = $coll->whereNotIn('status', ['cancelled'])->sum('total_amount');
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL RESERVATIONS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $totalBookings }} Rides</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">CONFIRMED UPCOMING</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $confirmedCount }} Confirmed</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">COMPLETED TRIPS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#7367f0; margin:0;">{{ $completedCount }} Completed</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">TOTAL REVENUE</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">৳ {{ number_format($totalRevenue) }}</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="admin-table-card" style="background:#fff; border-radius:8px; border:1px solid #e2e8f0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #edf2f7; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <h3 style="font-size:15px; font-weight:700; margin:0; color:#1e293b;">
                <i class="fa-solid fa-list-check me-2 text-primary"></i>All Airport Taxi Reservations ({{ $totalBookings }})
            </h3>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search rides..." onkeyup="filterTableSearch('transfersTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="transfersTable" style="font-size:13px;">
                <thead style="background:#f8fafc; color:#64748b; font-size:11.5px; text-transform:uppercase;">
                    <tr>
                        <th class="ps-3 py-2.5">Reference</th>
                        <th class="py-2.5">Passenger Details</th>
                        <th class="py-2.5">Route (Pickup → Drop-off)</th>
                        <th class="py-2.5">Pickup Time &amp; Flight</th>
                        <th class="py-2.5">Vehicle / Pax</th>
                        <th class="py-2.5">Total Fare</th>
                        <th class="py-2.5">Status</th>
                        <th class="pe-3 text-end py-2.5">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        <tr>
                            <td class="ps-3 fw-bold text-primary" style="font-family:monospace;">
                                #{{ $b->booking_reference }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $b->passenger_name }}</div>
                                <div class="small text-muted"><i class="fa-solid fa-phone me-1 text-secondary"></i>{{ $b->passenger_phone }}</div>
                                <div class="small text-muted"><i class="fa-solid fa-envelope me-1 text-secondary"></i>{{ $b->passenger_email }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><i class="fa-solid fa-plane-arrival text-info me-1"></i>{{ $b->pickup_location }}</div>
                                <div class="small text-secondary"><i class="fa-solid fa-arrow-down-long mx-1 text-muted"></i>{{ $b->dropoff_location }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 mb-1 d-inline-block">
                                    <i class="fa-regular fa-clock me-1 text-primary"></i>
                                    {{ $b->pickup_datetime ? $b->pickup_datetime->format('d M Y, h:i A') : 'N/A' }}
                                </span>
                                @if($b->flight_number)
                                    <div class="small text-muted"><i class="fa-solid fa-plane me-1"></i>Flight: <strong>{{ $b->flight_number }}</strong></div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $b->transfer?->vehicle_type ?? 'Sedan' }}</div>
                                <div class="small text-muted"><i class="fa-solid fa-user-group me-1"></i>{{ $b->passengers }} Pax</div>
                            </td>
                            <td>
                                <strong class="text-success" style="font-size:14px;">BDT {{ number_format((float)$b->total_amount) }}</strong>
                            </td>
                            <td>
                                @php
                                    $st = strtolower($b->status ?? 'confirmed');
                                    $badgeClass = match($st) {
                                        'completed' => 'bg-info bg-opacity-10 text-info border-info',
                                        'cancelled' => 'bg-danger bg-opacity-10 text-danger border-danger',
                                        default     => 'bg-success bg-opacity-10 text-success border-success',
                                    };
                                @endphp
                                <span class="badge border fw-bold px-2 py-1 {{ $badgeClass }}">
                                    {{ strtoupper($b->status) }}
                                </span>
                            </td>
                            <td class="pe-3 text-end" style="white-space:nowrap;">
                                <div class="dropdown action-gear-dropdown d-inline-block">
                                    <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                        <i class="fa-solid fa-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                        <li>
                                            <a class="dropdown-item py-1.5 px-3" href="{{ route('transfers.voucher', $b->booking_reference) }}" target="_blank">
                                                <i class="fa-solid fa-ticket text-primary me-2"></i> View E-Voucher
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="{{ route('admin.transfers.bookings.status', $b->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-success">
                                                    <i class="fa-solid fa-circle-check me-2"></i> Mark Confirmed
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.transfers.bookings.status', $b->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-info">
                                                    <i class="fa-solid fa-flag-checkered me-2"></i> Mark Completed
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.transfers.bookings.status', $b->id) }}" method="POST" class="m-0" onsubmit="return confirm('Cancel this taxi transfer #{{ $b->booking_reference }}?')">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                    <i class="fa-solid fa-ban me-2"></i> Mark Cancelled
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-taxi fa-2x mb-2 text-secondary"></i>
                                <p class="mb-0">No airport transfer reservations recorded yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div style="padding:16px 20px; border-top:1px solid #edf2f7;">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

