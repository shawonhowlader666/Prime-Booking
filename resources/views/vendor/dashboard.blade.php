@extends('layouts.vendor')
@section('title', 'Vendor Partner Dashboard | Prime Aviation')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Vendor Overview</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Vendor Partner Dashboard Overview</h1>
        <div style="display:flex; align-items:center; gap:8px;">
            <a href="{{ route('vendor.property.create') }}" class="btn-add-primary">
                <i class="fa-solid fa-plus"></i> Add Property
            </a>
            <button class="btn-export-csv" onclick="alert('Exporting...')">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </button>
        </div>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#7367f0;"><i class="fa-solid fa-bangladeshi-taka-sign"></i></div>
                    <div>
                        <p class="kpi-value">BDT {{ number_format($vendorStats['total_earnings'] ?? 0) }}</p>
                        <p class="kpi-label">Total Revenue Earned</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-check-double"></i> Paid to Bank/bKash</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#ff9f43;"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <div>
                        <p class="kpi-value">BDT {{ number_format($vendorStats['pending_payout'] ?? 0) }}</p>
                        <p class="kpi-label">Pending Payout</p>
                        <p class="kpi-growth-down"><i class="fa-solid fa-clock"></i> Next Payout Cycle</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#28c76f;"><i class="fa-solid fa-bed"></i></div>
                    <div>
                        <p class="kpi-value">{{ $vendorStats['active_bookings'] ?? 0 }} Stays</p>
                        <p class="kpi-label">Active Guest Bookings</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-user-check"></i> Confirmed Stays</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#00cfe8;"><i class="fa-solid fa-building"></i></div>
                    <div>
                        <p class="kpi-value">{{ $vendorStats['total_properties'] ?? 0 }} Properties</p>
                        <p class="kpi-label">Listed Properties</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-city"></i> Hotels, Ships, Cottages</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
    </div>

    {{-- Listed Properties Table View --}}
    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>Your Listed Properties &amp; Inventory</h6>
            <a href="{{ route('vendor.property.create') }}" class="btn-add-primary">
                <i class="fa-solid fa-plus"></i> Add Property
            </a>
        </div>

        {{-- Properties Grid --}}
        @if(isset($properties) && count($properties) > 0)
            <div style="padding:16px;">
                <div class="row g-3">
                    @foreach($properties as $p)
                    <div class="col-md-4 col-sm-6">
                        <div class="property-grid-card">
                            <img src="{{ $p->primary_image ?? 'https://placehold.co/400x150/1890ff/white?text=Property' }}" alt="{{ $p->name }}">
                            <div class="property-grid-card-body">
                                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:6px;">
                                    <strong style="font-size:13px; color:#1e293b; line-height:1.3;">{{ $p->name }}</strong>
                                    <span class="badge-status active" style="flex-shrink:0;">Active</span>
                                </div>
                                <p style="font-size:12px; color:#8c8c8c; margin:0 0 8px;">
                                    <i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> {{ $p->city ?? 'Bangladesh' }}
                                </p>
                                <div style="display:flex; align-items:center; justify-content:space-between; padding-top:8px; border-top:1px solid #f0f0f0;">
                                    <div>
                                        <span style="font-size:10px; color:#8c8c8c; display:block;">Price / Night</span>
                                        <strong style="font-size:14px; color:var(--primary);">BDT {{ number_format($p->price_per_night ?? 0) }}</strong>
                                    </div>
                                    <a href="{{ route('hotels.show', $p->id) }}" target="_blank" class="btn-table-action">
                                        View Live <i class="fa-solid fa-external-link"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div style="padding:48px; text-align:center;">
                <div style="width:64px; height:64px; background:rgba(24,144,255,0.08); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:16px;">
                    <i class="fa-solid fa-hotel" style="font-size:28px; color:var(--primary);"></i>
                </div>
                <h6 style="font-size:15px; font-weight:700; color:#1e293b; margin-bottom:6px;">No Properties Listed Yet</h6>
                <p style="font-size:12.5px; color:#8c8c8c; margin-bottom:16px;">Start by adding your first hotel, ship, or cottage to the platform.</p>
                <a href="{{ route('vendor.property.create') }}" class="btn-add-primary" style="padding:8px 24px;">
                    <i class="fa-solid fa-plus"></i> Add Your First Property
                </a>
            </div>
        @endif

        <div style="padding:10px 16px; border-top:1px solid #f0f0f0; font-size:12px; color:#8c8c8c;">
            {{ isset($properties) ? count($properties) : 0 }} properties listed on this account
        </div>
    </div>

    {{-- Recent Bookings Table --}}
    <div class="data-table-card mt-3">
        <div class="data-table-card-header">
            <h6>Recent Guest Bookings</h6>
            <span class="live-feed-badge">Real-Time Feed</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
                <thead>
                    <tr>
                        <th>Booking Ref</th>
                        <th>Guest Name</th>
                        <th>Property</th>
                        <th>Check-in / Check-out</th>
                        <th>Amount (BDT)</th>
                        <th>Status</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentBookings ?? [] as $b)
                    <tr>
                        <td>
                            <a href="{{ route('vendor.bookings.show', $b->booking_reference) }}" style="color:var(--primary); font-weight:700; text-decoration:none;">{{ $b->booking_reference }}</a><br>
                            <span style="font-size:11px; color:#8c8c8c;">{{ $b->created_at->format('M d, Y') }}</span>
                        </td>
                        <td>
                            <strong style="font-size:13px; color:#1e293b;">{{ $b->guest_name }}</strong><br>
                            <span style="font-size:11px; color:#8c8c8c;">{{ $b->guest_phone }}</span>
                        </td>
                        <td style="font-size:12.5px; color:#334155;">{{ $b->property?->name ?? 'Hotel Stay' }}</td>
                        <td style="font-size:12px; color:#595959;">{{ \Carbon\Carbon::parse($b->check_in)->format('M d') }} → {{ \Carbon\Carbon::parse($b->check_out)->format('M d, Y') }}</td>
                        <td><strong style="color:var(--primary);">BDT {{ number_format($b->total_price ?: $b->total_amount, 0) }}</strong></td>
                        <td><span class="badge-status {{ $b->effective_status }}">{{ ucfirst($b->effective_status) }}</span></td>
                        <td style="text-align:right;">
                            <a href="{{ route('vendor.bookings.show', $b->booking_reference) }}" class="btn-table-action primary">
                                Details <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-secondary">No recent bookings found for your properties.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:10px 16px; border-top:1px solid #f0f0f0; font-size:12px; color:#8c8c8c;">
            Showing latest bookings for your listed properties
        </div>
    </div>

</div>
@endsection
