@extends('layouts.vendor')
@section('title', 'Financial Reports & Analytics | Vendor Portal')

@section('content')
<div class="page-header-card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px; padding:20px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1 class="page-title m-0 fw-bold fs-5 text-dark">
                <i class="fa-solid fa-chart-line me-2 text-primary"></i> Financial Reports &amp; Analytics
            </h1>
            <p class="text-muted mb-0 small mt-1">Review your properties' revenue performance, booking velocity, and occupancy metrics.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('vendor.reports') }}" method="GET" class="d-flex align-items-center gap-2 m-0" id="filterForm">
                @if(isset($vendorProperties) && $vendorProperties->count() > 0)
                <select name="property_id" class="form-select form-select-sm" style="border-radius:4px; font-size:12.5px; height:34px; max-width:200px;" onchange="this.form.submit()">
                    <option value="">All Properties ({{ $vendorProperties->count() }})</option>
                    @foreach($vendorProperties as $p)
                        <option value="{{ $p->id }}" {{ ($selectedPropertyId ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                @endif
                <select name="year" class="form-select form-select-sm" style="border-radius:4px; font-size:12.5px; height:34px; width:100px;" onchange="this.form.submit()">
                    @for($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" {{ $y == ($selectedYear ?? now()->year) ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
            <a href="{{ route('vendor.reports', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" style="border-radius:4px; height:34px;">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
        </div>
    </div>
</div>

<div class="page-content-area">
    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card p-3 shadow-sm border-0 position-relative overflow-hidden" style="background:#fff; border-radius:4px; border-left:4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold" style="letter-spacing:0.5px;">Gross Revenue ({{ $selectedYear }})</span>
                    <div style="width:34px; height:34px; border-radius:4px; background:rgba(16,185,129,0.12); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:15px;">
                        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-dark">৳{{ number_format($grossRevenue ?? 0, 2) }}</div>
                <small class="text-muted" style="font-size:11.5px;">Excludes cancelled bookings</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card p-3 shadow-sm border-0 position-relative overflow-hidden" style="background:#fff; border-radius:4px; border-left:4px solid #1890ff !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold" style="letter-spacing:0.5px;">Total Bookings</span>
                    <div style="width:34px; height:34px; border-radius:4px; background:rgba(24,144,255,0.12); color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:15px;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($totalBookings ?? 0) }}</div>
                <small class="text-muted" style="font-size:11.5px;">Processed in {{ $selectedYear }}</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card p-3 shadow-sm border-0 position-relative overflow-hidden" style="background:#fff; border-radius:4px; border-left:4px solid #8b5cf6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold" style="letter-spacing:0.5px;">Avg Booking Value</span>
                    <div style="width:34px; height:34px; border-radius:4px; background:rgba(139,92,246,0.12); color:#8b5cf6; display:flex; align-items:center; justify-content:center; font-size:15px;">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-dark">৳{{ number_format($avgValue ?? 0, 2) }}</div>
                <small class="text-muted" style="font-size:11.5px;">Per completed booking</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card p-3 shadow-sm border-0 position-relative overflow-hidden" style="background:#fff; border-radius:4px; border-left:4px solid #ef4444 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold" style="letter-spacing:0.5px;">Cancellations</span>
                    <div style="width:34px; height:34px; border-radius:4px; background:rgba(239,68,68,0.12); color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:15px;">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($cancellations ?? 0) }}</div>
                <small class="text-danger" style="font-size:11.5px;">
                    {{ $totalBookings > 0 ? round(($cancellations / $totalBookings) * 100, 1) : 0 }}% cancellation rate
                </small>
            </div>
        </div>
    </div>

    {{-- Main Chart & Top Properties Row --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card p-3 shadow-sm border-0" style="background:#fff; border-radius:4px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark m-0" style="font-size:14px;">
                        <i class="fa-solid fa-chart-column me-2 text-primary"></i> Monthly Revenue &amp; Bookings Trend — {{ $selectedYear }}
                    </h6>
                    <div class="d-flex align-items-center gap-3 font-size-base">
                        <span class="d-flex align-items-center gap-1" style="font-size:12px; color:#1890ff;">
                            <span style="display:inline-block; width:12px; height:12px; background:#1890ff; border-radius:2px;"></span> Revenue (BDT)
                        </span>
                        <span class="d-flex align-items-center gap-1" style="font-size:12px; color:#10b981;">
                            <span style="display:inline-block; width:12px; height:3px; background:#10b981; border-radius:2px;"></span> Bookings Count
                        </span>
                    </div>
                </div>
                <div style="position:relative; height:300px; width:100%;">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100" style="background:#fff; border-radius:4px;">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark m-0" style="font-size:14px;">
                        <i class="fa-solid fa-trophy me-2 text-warning"></i> Top Properties ({{ $selectedYear }})
                    </h6>
                    <span class="badge bg-light text-muted border" style="font-size:11px;">Ranked by Volume</span>
                </div>
                <div class="card-body p-0">
                    @forelse(($topProperties ?? []) as $index => $prop)
                    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary-subtle text-secondary fw-bold" style="width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; font-size:11px;">
                                #{{ $index + 1 }}
                            </span>
                            <div>
                                <div class="fw-bold text-dark" style="font-size:13px;">{{ $prop->name }}</div>
                                <small class="text-muted" style="font-size:11px;">{{ $prop->city ?? 'Bangladesh' }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="border-radius:4px; font-size:12px;">
                                {{ $prop->bookings_count ?? 0 }} bookings
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted" style="font-size:13px;">
                        <i class="fa-solid fa-hotel d-block mb-2 text-muted" style="font-size:24px;"></i>
                        No property data recorded yet.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Bookings Breakdown Table --}}
    <div class="card shadow-sm border-0" style="background:#fff; border-radius:4px;">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="fw-bold text-dark m-0" style="font-size:14px;">
                <i class="fa-solid fa-list-check me-2 text-primary"></i> Recent Transactions / Bookings
            </h6>
            <a href="{{ route('vendor.bookings.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:4px; font-size:12px;">
                View All Bookings <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                <thead style="background:#f8fafc; color:#64748b; font-size:11.5px; text-transform:uppercase;">
                    <tr>
                        <th class="ps-3 py-3">Booking Ref</th>
                        <th>Guest Details</th>
                        <th>Property &amp; Room</th>
                        <th>Dates</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($recentBookings ?? []) as $b)
                    <tr>
                        <td class="ps-3">
                            <span class="fw-bold text-primary font-monospace">{{ $b->booking_reference ?? $b->booking_code ?? '#'.$b->id }}</span>
                            <div class="text-muted" style="font-size:11px;">{{ $b->created_at->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $b->guest_name }}</div>
                            <small class="text-muted">{{ $b->guest_phone ?? $b->guest_email ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $b->property->name ?? 'Property' }}</div>
                            <small class="text-muted">{{ $b->room->name ?? 'Standard Room' }}</small>
                        </td>
                        <td>
                            <div>{{ $b->check_in ? $b->check_in->format('d M') : '-' }} → {{ $b->check_out ? $b->check_out->format('d M Y') : '-' }}</div>
                            <small class="text-muted">{{ $b->nights ?? ($b->check_in && $b->check_out ? $b->check_in->diffInDays($b->check_out) : 1) }} Night(s)</small>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">৳{{ number_format($b->total_price ?? $b->total_amount ?? 0) }}</span>
                        </td>
                        <td>
                            @switch($b->status)
                                @case('confirmed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="border-radius:4px;">Confirmed</span>
                                    @break
                                @case('pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" style="border-radius:4px;">Pending</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="border-radius:4px;">Cancelled</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="border-radius:4px;">{{ ucfirst($b->status ?? 'pending') }}</span>
                            @endswitch
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('vendor.bookings.show', $b->booking_reference ?: $b->id) }}" class="btn btn-sm btn-light border" style="border-radius:4px; font-size:12px; padding:3px 9px;" title="View Details">
                                <i class="fa-solid fa-eye text-muted"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No transactions found for the selected period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    
    const labels = {!! json_encode(array_column($monthlyData ?? [], 'month')) !!};
    const revenueData = {!! json_encode(array_column($monthlyData ?? [], 'revenue')) !!};
    const bookingsData = {!! json_encode(array_column($monthlyData ?? [], 'bookings')) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Revenue (BDT)',
                    data: revenueData,
                    backgroundColor: 'rgba(24, 144, 255, 0.25)',
                    borderColor: '#1890ff',
                    borderWidth: 2,
                    borderRadius: 4,
                    yAxisID: 'yRevenue',
                    order: 2
                },
                {
                    type: 'line',
                    label: 'Bookings Count',
                    data: bookingsData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2.5,
                    tension: 0.3,
                    fill: false,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4,
                    yAxisID: 'yBookings',
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.yAxisID === 'yRevenue') {
                                return 'Revenue: ৳' + context.parsed.y.toLocaleString();
                            }
                            return 'Bookings: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                yRevenue: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(v) {
                            return '৳' + (v >= 1000 ? (v / 1000).toFixed(0) + 'K' : v);
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                yBookings: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
