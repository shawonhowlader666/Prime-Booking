@extends('layouts.admin')
@section('title', 'OTA Hotel Data Importer | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Inventory</span>
        <span class="sep">-</span><strong style="color:#333;">OTA Data Importer</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <h1 class="page-title m-0">
            <i class="fa-solid fa-cloud-arrow-down" style="color:var(--primary); margin-right:8px;"></i>
            OTA Data Importer
        </h1>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-primary text-white fw-bold d-inline-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#syncOtaCookieModal" style="font-size:12.5px; padding:7px 16px; border-radius:4px; background:var(--primary); border:none;">
                <i class="fa-solid fa-cloud-arrow-down me-1.5"></i> + Sync New OTA Hotels
            </button>
            <a href="{{ route('admin.properties.index') }}" class="btn-table-action" style="font-size:13px; padding:6px 14px; border-radius:4px;">
                <i class="fa-solid fa-hotel me-1"></i> View Property Inventory
            </a>
        </div>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
    <div class="admin-alert success d-flex align-items-center justify-content-between mb-4" style="border-radius:4px;">
        <div><i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="admin-alert error d-flex align-items-center justify-content-between mb-4" style="border-radius:4px;">
        <div><i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Real MySQL Live Database Metrics Bar --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="form-card py-2.5 px-3 d-flex align-items-center justify-content-between" style="border-radius:4px; background:#fff; border:1px solid #e2e8f0;">
                <div>
                    <span class="text-secondary d-block" style="font-size:11px; font-weight:600; text-transform:uppercase;">Total DB Inventory</span>
                    <strong style="font-size:16px; color:#0f172a;">{{ number_format($stats['total_properties'] ?? 0) }} Properties</strong>
                </div>
                <div class="p-2 rounded-circle" style="background:#e6f7ff; color:#1890ff;"><i class="fa-solid fa-hotel"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="form-card py-2.5 px-3 d-flex align-items-center justify-content-between" style="border-radius:4px; background:#fff; border:1px solid #e2e8f0;">
                <div>
                    <span class="text-secondary d-block" style="font-size:11px; font-weight:600; text-transform:uppercase;">Active Published</span>
                    <strong style="font-size:16px; color:#52c41a;">{{ number_format($stats['active_published'] ?? 0) }} Live</strong>
                </div>
                <div class="p-2 rounded-circle" style="background:#f6ffed; color:#52c41a;"><i class="fa-solid fa-circle-check"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="form-card py-2.5 px-3 d-flex align-items-center justify-content-between" style="border-radius:4px; background:#fff; border:1px solid #e2e8f0;">
                <div>
                    <span class="text-secondary d-block" style="font-size:11px; font-weight:600; text-transform:uppercase;">Covered Cities</span>
                    <strong style="font-size:16px; color:#1890ff;">{{ number_format($stats['total_cities'] ?? 0) }} Regions</strong>
                </div>
                <div class="p-2 rounded-circle" style="background:#e6f7ff; color:#1890ff;"><i class="fa-solid fa-location-dot"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="form-card py-2.5 px-3 d-flex align-items-center justify-content-between" style="border-radius:4px; background:#fff; border:1px solid #e2e8f0;">
                <div>
                    <span class="text-secondary d-block" style="font-size:11px; font-weight:600; text-transform:uppercase;">Room Categories</span>
                    <strong style="font-size:16px; color:#fa8c16;">{{ number_format($stats['total_rooms'] ?? 0) }} Rooms</strong>
                </div>
                <div class="p-2 rounded-circle" style="background:#fff7e6; color:#fa8c16;"><i class="fa-solid fa-bed"></i></div>
            </div>
        </div>
    </div>

    {{-- Active OTA Channels Grid --}}
    <div class="form-card mb-4" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:14px 18px;">
        <div class="d-flex align-items-center justify-content-between mb-2.5 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-dark d-flex align-items-center gap-1.5" style="font-size:13px;">
                    <i class="fa-solid fa-cookie-bite text-warning"></i> Active OTA Channels:
                </span>
            </div>
        </div>
        <div class="row g-2.5" id="otaChannelsContainer">
            {{-- Agoda --}}
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded d-flex align-items-center justify-content-between" style="background:#f8fafc; border-color:#e2e8f0!important;">
                    <div class="d-flex align-items-center gap-2">
                        @if($cookieStatus['agoda'] ?? false)
                            <span class="pulse-dot" style="background:#52c41a; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(82,196,26,0.2);" title="Active Vault Cookie"></span>
                            <div>
                                <strong style="font-size:12px; display:block; color:#1e293b;">Agoda Global API</strong>
                                <small style="font-size:10px; color:#52c41a; font-weight:600;">Cookie Active</small>
                            </div>
                        @else
                            <span class="pulse-dot" style="background:#ff4d4f; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(255,77,79,0.2);" title="Cookie Expired or Not Set"></span>
                            <div>
                                <strong style="font-size:12px; display:block; color:#1e293b;">Agoda Global API</strong>
                                <small style="font-size:10px; color:#ff4d4f; font-weight:600;">Cookie Expired</small>
                            </div>
                        @endif
                    </div>
                    @if($cookieStatus['agoda'] ?? false)
                        <span class="badge bg-success-subtle text-success" style="font-size:9.5px;">ACTIVE</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger" style="font-size:9.5px;">EXPIRED</span>
                    @endif
                </div>
            </div>

            {{-- Booking.com --}}
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded d-flex align-items-center justify-content-between" style="background:#f8fafc; border-color:#e2e8f0!important;">
                    <div class="d-flex align-items-center gap-2">
                        @if($cookieStatus['booking'] ?? false)
                            <span class="pulse-dot" style="background:#52c41a; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(82,196,26,0.2);" title="Active Vault Cookie"></span>
                            <div>
                                <strong style="font-size:12px; display:block; color:#1e293b;">Booking.com Engine</strong>
                                <small style="font-size:10px; color:#52c41a; font-weight:600;">Cookie Active</small>
                            </div>
                        @else
                            <span class="pulse-dot" style="background:#ff4d4f; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(255,77,79,0.2);" title="Cookie Expired or Not Set"></span>
                            <div>
                                <strong style="font-size:12px; display:block; color:#1e293b;">Booking.com Engine</strong>
                                <small style="font-size:10px; color:#ff4d4f; font-weight:600;">Cookie Expired</small>
                            </div>
                        @endif
                    </div>
                    @if($cookieStatus['booking'] ?? false)
                        <span class="badge bg-success-subtle text-success" style="font-size:9.5px;">ACTIVE</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger" style="font-size:9.5px;">EXPIRED</span>
                    @endif
                </div>
            </div>

            {{-- Expedia / Hotels.com --}}
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded d-flex align-items-center justify-content-between" style="background:#f8fafc; border-color:#e2e8f0!important;">
                    <div class="d-flex align-items-center gap-2">
                        @if($cookieStatus['expedia'] ?? false)
                            <span class="pulse-dot" style="background:#52c41a; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(82,196,26,0.2);" title="Active Vault Cookie"></span>
                            <div>
                                <strong style="font-size:12px; display:block; color:#1e293b;">Expedia / Hotels.com</strong>
                                <small style="font-size:10px; color:#52c41a; font-weight:600;">Cookie Active</small>
                            </div>
                        @else
                            <span class="pulse-dot" style="background:#ff4d4f; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(255,77,79,0.2);" title="Cookie Expired or Not Set"></span>
                            <div>
                                <strong style="font-size:12px; display:block; color:#1e293b;">Expedia / Hotels.com</strong>
                                <small style="font-size:10px; color:#ff4d4f; font-weight:600;">Cookie Expired</small>
                            </div>
                        @endif
                    </div>
                    @if($cookieStatus['expedia'] ?? false)
                        <span class="badge bg-success-subtle text-success" style="font-size:9.5px;">ACTIVE</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger" style="font-size:9.5px;">EXPIRED</span>
                    @endif
                </div>
            </div>

            {{-- Trip.com / Airbnb --}}
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded d-flex align-items-center justify-content-between" style="background:#f8fafc; border-color:#e2e8f0!important;">
                    <div class="d-flex align-items-center gap-2">
                        @if($cookieStatus['airbnb'] ?? false)
                            <span class="pulse-dot" style="background:#52c41a; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(82,196,26,0.2);" title="Active Vault Cookie"></span>
                            <div>
                                <strong style="font-size:12px; display:block; color:#1e293b;">Trip.com / Airbnb</strong>
                                <small style="font-size:10px; color:#52c41a; font-weight:600;">Cookie Active</small>
                            </div>
                        @else
                            <span class="pulse-dot" style="background:#ff4d4f; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(255,77,79,0.2);" title="Cookie Expired or Not Set"></span>
                            <div>
                                <strong style="font-size:12px; display:block; color:#1e293b;">Trip.com / Airbnb</strong>
                                <small style="font-size:10px; color:#ff4d4f; font-weight:600;">Cookie Expired</small>
                            </div>
                        @endif
                    </div>
                    @if($cookieStatus['airbnb'] ?? false)
                        <span class="badge bg-success-subtle text-success" style="font-size:9.5px;">ACTIVE</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger" style="font-size:9.5px;">EXPIRED</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Live Imported Property Inventory Table --}}
    <div class="form-card" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:20px;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 flex-wrap gap-2">
            <div>
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size:14.5px;">
                    <i class="fa-solid fa-list-check text-primary"></i>
                    Recent Live Imported Properties Audit Trail
                </h6>
                <small class="text-secondary" style="font-size:11.5px;">Real-time inventory synchronized from Agoda &amp; Booking.com into MySQL database.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.properties.index') }}" class="btn btn-sm btn-outline-secondary fw-bold px-3 py-1.5" style="font-size:12px; border-radius:4px;">
                    <i class="fa-solid fa-external-link me-1"></i> View All Properties
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
                <thead class="table-light">
                    <tr>
                        <th style="width:45px;">#</th>
                        <th>Property Name</th>
                        <th>City / Region</th>
                        <th>Category</th>
                        <th>Rating / Reviews</th>
                        <th>Base Price</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentImports as $index => $prop)
                    <tr>
                        <td class="text-secondary fw-bold">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                @if(!empty($prop->primary_image))
                                    <img src="{{ $prop->primary_image }}" alt="" style="width:36px; height:36px; object-fit:cover; border-radius:4px; border:1px solid #cbd5e1;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light text-secondary rounded" style="width:36px; height:36px; border:1px solid #e2e8f0;">
                                        <i class="fa-solid fa-hotel"></i>
                                    </div>
                                @endif
                                <div>
                                    <strong style="font-size:13px; display:block; color:#0f172a;">{{ Str::limit($prop->name, 35) }}</strong>
                                    <small class="text-secondary" style="font-size:11px;">ID: #PROP-{{ $prop->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:11px;">
                                <i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $prop->city ?? 'Cox\'s Bazar' }}
                            </span>
                        </td>
                        <td><span class="text-capitalize text-secondary">{{ $prop->property_type ?? 'Hotel' }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-1 text-warning" style="font-size:11px;">
                                <i class="fa-solid fa-star"></i>
                                <strong class="text-dark">{{ number_format($prop->rating_score ?? 4.8, 1) }}</strong>
                                <small class="text-secondary">({{ $prop->total_reviews ?? 120 }})</small>
                            </div>
                        </td>
                        <td>
                            <strong style="color:#059669;">৳ {{ number_format($prop->base_price ?? $prop->starting_price ?? 2500) }}</strong>
                        </td>
                        <td>
                            @if(($prop->status ?? 'active') === 'active')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:10px;">🟢 Live</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" style="font-size:10px;">🟡 Pending</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.properties.edit', $prop->id) }}" class="btn btn-sm btn-light border p-1 px-2" title="Edit Property" style="font-size:11px;">
                                <i class="fa-solid fa-pen text-secondary"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-secondary">
                            <i class="fa-solid fa-box-open fa-2x mb-2 d-block text-muted"></i>
                            No imported properties found yet. Click <strong>"+ Sync New OTA Hotels"</strong> to run real-time sync!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
