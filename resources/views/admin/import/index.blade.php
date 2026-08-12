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

{{-- MODAL: POPUP OTA COOKIE SYNCHRONIZER CARD --}}
<div class="modal fade" id="syncOtaCookieModal" tabindex="-1" aria-labelledby="syncOtaCookieModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:6px; border:none; box-shadow:0 10px 30px rgba(0,0,0,0.15);">
            
            <div class="modal-header border-bottom px-4 py-3" style="background:#f8fafc; border-top-left-radius:6px; border-top-right-radius:6px;">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="syncOtaCookieModalLabel" style="font-size:15px;">
                        <i class="fa-solid fa-cookie-bite text-warning"></i>
                        Live OTA Cookie Data Synchronizer
                    </h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="javascript:(function(){navigator.clipboard.writeText(document.cookie);alert('✅ Agoda/Booking Active Cookie Copied to Clipboard!');})();" class="btn btn-sm btn-primary text-white fw-bold px-2.5 py-1 d-inline-flex align-items-center shadow-sm" style="font-size:11.5px; border-radius:4px; background:var(--primary); border:none; cursor:grab;" title="Drag this button to your browser Bookmark Bar or click to copy script!" onclick="copyCookieScript(event)">
                        <i class="fa-solid fa-puzzle-piece me-1.5" style="color:#fbbf24;"></i> Copy Cookie
                    </a>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form action="{{ route('admin.import-hotels.store') }}" method="POST" id="importHotelFormModal">
                @csrf
                <input type="hidden" name="mode" value="cookie_sync">
                <input type="hidden" name="target_city" value="Cox's Bazar">
                <input type="hidden" name="max_limit" value="50">
                <input type="hidden" name="override_status" value="active">

                <div class="modal-body p-4">
                    
                    {{-- Target OTA Source Channel Selection + [+] Button --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size:12.5px;">Target OTA Source Channel <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <select name="ota_channel" id="otaChannelSelectModal" class="form-select flex-grow-1" style="height:38px; border-radius:4px;" required>
                                <option value="agoda" selected>Agoda.com (Global API)</option>
                                <option value="booking">Booking.com Engine</option>
                                <option value="expedia">Expedia / Hotels.com</option>
                                <option value="airbnb">Trip.com / Airbnb</option>
                            </select>
                            <button type="button" class="btn text-white fw-bold px-3 d-inline-flex align-items-center justify-content-center shadow-none" onclick="openAddOptionModal('ota_channel')" title="Add Custom OTA Channel" style="background:var(--primary); border:none; border-radius:4px; height:38px; min-width:40px; flex-shrink:0;">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Cookie Header Input Box --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <label class="form-label fw-bold text-dark m-0" style="font-size:12.5px;">
                                Cookie Header Data <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                @if(!empty($savedCookie))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size:10.5px;">
                                        <i class="fa-solid fa-database me-1"></i> Vault Cookie Active
                                    </span>
                                @endif
                                <button type="button" class="btn btn-link text-decoration-none p-0 small fw-bold" onclick="fillSampleJson()" style="font-size:11.5px; color:var(--primary);">
                                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Fill Sample Cookie
                                </button>
                            </div>
                        </div>
                        <textarea name="cookie_header" id="jsonPayloadInputModal" class="form-control font-monospace" rows="4" placeholder="Paste browser Cookie header here... (e.g. agoda.sid=...; _ga=...; booking_session=...)" style="border-radius:4px; border-color:#cbd5e1; font-size:12px;" required>{{ old('cookie_header', $savedCookie ?? '') }}</textarea>
                        <small class="text-secondary d-block mt-1.5" style="font-size:11.5px;">
                            💡 Vaulted cookies persist automatically for background sync.
                        </small>
                    </div>

                    {{-- Live Execution Console Logs inside Modal --}}
                    @if(session('import_logs') && is_array(session('import_logs')))
                    <div class="mt-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size:12.5px;"><i class="fa-solid fa-terminal text-primary me-1"></i> Live Synchronization Audit Log</label>
                        <div id="importLogsConsole" style="background:#090d16; color:#38bdf8; font-family:'Courier New', Courier, monospace; font-size:11.5px; padding:12px 14px; border-radius:4px; height:180px; overflow-y:auto; line-height:1.6; border:1px solid #1e293b;">
                            @foreach(session('import_logs') as $log)
                                <div style="color:#52c41a; margin-bottom:3px;"><i class="fa-solid fa-angle-right me-1" style="color:#38bdf8;"></i> {{ $log }}</div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

                <div class="modal-footer border-top px-4 py-3" style="background:#f8fafc;">
                    <button type="button" class="btn btn-light fw-bold px-3 py-2" data-bs-dismiss="modal" style="border-radius:4px; font-size:13px;">Cancel</button>
                    <button type="submit" class="btn text-white fw-bold px-4 py-2 shadow-sm" style="background:var(--primary); border-radius:4px; font-size:13px; border:none;" id="btnSubmitImportModal">
                        <i class="fa-solid fa-bolt me-1.5"></i> Execute Cookie Data Synchronization
                    </button>
                </div>
            </form>

        </div>
</div>
</div>

{{-- Stockifly 1:1 Matching Interactive Modal for [+] Add Option --}}
<div class="modal fade" id="addOptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:4px; overflow:hidden;">
            <div class="modal-header text-white" style="background:#001529; border-bottom:1px solid rgba(255,255,255,0.1); border-radius:4px 4px 0 0;">
                <h6 class="modal-title fw-bold" id="addOptionModalTitle">
                    <i class="fa-solid fa-plus-circle me-1" style="color:var(--primary);"></i> Add New Option
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="addOptionCategory">
                <div class="mb-3">
                    <label class="form-label fw-semibold" id="addOptionLabel">Option Value</label>
                    <input type="text" id="addOptionInput" class="form-control" placeholder="Type new option..." style="height:42px; font-size:14px; border-radius:4px;">
                </div>
                <div id="addOptionHelpText" class="text-secondary small">
                    This option will be added to the dropdown list dynamically and selected.
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-radius:0 0 4px 4px;">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal" style="font-size:13px; border-radius:4px;">Cancel</button>
                <button type="button" class="btn text-white px-4 fw-bold" onclick="saveNewOption()" style="background:var(--primary); font-size:13px; border-radius:4px;">
                    <i class="fa-solid fa-check me-1"></i> Save &amp; Select Option
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var currentCategory = 'city';

function openAddOptionModal(category) {
    currentCategory = category;
    document.getElementById('addOptionCategory').value = category;
    var input = document.getElementById('addOptionInput');
    input.value = '';

    var title = document.getElementById('addOptionModalTitle');
    var label = document.getElementById('addOptionLabel');
    var help = document.getElementById('addOptionHelpText');

    if (category === 'city') {
        title.innerHTML = '<i class="fa-solid fa-location-dot text-primary me-1"></i> Add New Target City / Region';
        label.innerText = 'City / Region Name';
        input.placeholder = 'e.g. Saint Martin, Bandarban, Dubai, Male';
        help.innerText = 'Adding a new city will save it to the database destinations and select it for import.';
    } else if (category === 'limit') {
        title.innerHTML = '<i class="fa-solid fa-list-ol text-primary me-1"></i> Add Custom Processing Limit';
        label.innerText = 'Max Limit Count (Number)';
        input.placeholder = 'e.g. 150';
        help.innerText = 'Enter any numeric count between 1 and 1000.';
    } else if (category === 'type') {
        title.innerHTML = '<i class="fa-solid fa-hotel text-primary me-1"></i> Add New Property Type';
        label.innerText = 'Property Type Name';
        input.placeholder = 'e.g. Houseboat, Yacht, Treehouse, Capsule';
        help.innerText = 'This property category will be available in the importer.';
    } else if (category === 'status') {
        title.innerHTML = '<i class="fa-solid fa-toggle-on text-primary me-1"></i> Add Custom Listing Status';
        label.innerText = 'Status Name';
        input.placeholder = 'e.g. draft, archived';
        help.innerText = 'Custom listing status option for property batch import.';
    } else if (category === 'ota_channel') {
        title.innerHTML = '<i class="fa-solid fa-globe text-primary me-1"></i> Add Custom OTA Channel';
        label.innerText = 'OTA Website / Channel Name';
        input.placeholder = 'e.g. Traveloka, MakeMyTrip, Goibibo, Klook, ShareTrip';
        help.innerText = 'Add a new custom OTA website channel to the active feeds dashboard.';
    }

    var bsModal = new bootstrap.Modal(document.getElementById('addOptionModal'));
    bsModal.show();

    setTimeout(function() { input.focus(); }, 300);
}

function saveNewOption() {
    var val = document.getElementById('addOptionInput').value.trim();
    if (!val) {
        alert('Please enter a valid option value.');
        return;
    }

    if (currentCategory === 'city') {
        var select = document.getElementById('targetCitySelect');
        var opt = new Option('📍 ' + val, val, true, true);
        select.add(opt, select.options[0]);
        select.value = val;

        // Save to DB asynchronously
        fetch('{{ route("admin.featured-destinations.ajax-add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ city: val, country: 'Bangladesh' })
        }).catch(function(err) { console.log('City AJAX save note:', err); });

    } else if (currentCategory === 'limit') {
        var select = document.getElementById('maxLimitSelect');
        var opt = new Option('⚡ ' + val + ' Properties', val, true, true);
        select.add(opt, select.options[0]);
        select.value = val;

    } else if (currentCategory === 'type') {
        var select = document.getElementById('overrideTypeSelect');
        var opt = new Option('🏠 ' + val, val.toLowerCase(), true, true);
        select.add(opt, select.options[0]);
        select.value = val.toLowerCase();

    } else if (currentCategory === 'status') {
        var select = document.getElementById('overrideStatusSelect');
        var opt = new Option('🔵 ' + val, val.toLowerCase(), true, true);
        select.add(opt, select.options[0]);
        select.value = val.toLowerCase();

    } else if (currentCategory === 'ota_channel') {
        appendOtaChannelCard(val);
        saveOtaChannelToStorage(val);
    }

    var modalEl = document.getElementById('addOptionModal');
    var modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
}

function appendOtaChannelCard(name) {
    var otaContainer = document.getElementById('otaChannelsContainer');
    if (!otaContainer) return;
    var count = otaContainer.children.length + 1;
    var otaId = 'OTA-' + (count < 10 ? '0' + count : count);
    var col = document.createElement('div');
    col.className = 'col-md-3 col-6';
    col.innerHTML = '<div class="p-2 border rounded d-flex align-items-center justify-content-between" style="background:#f8fafc; border-color:#e2e8f0!important;">' +
        '<div class="d-flex align-items-center gap-2">' +
            '<span class="pulse-dot" style="background:#52c41a; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(82,196,26,0.2);"></span>' +
            '<div>' +
                '<strong style="font-size:12px; display:block; color:#1e293b;">' + name + '</strong>' +
                '<small style="font-size:10.5px; color:#64748b;">Custom Channel Active</small>' +
            '</div>' +
        '</div>' +
        '<span class="badge bg-primary-subtle text-primary" style="font-size:10px;">' + otaId + '</span>' +
    '</div>';
    otaContainer.appendChild(col);
}

function saveOtaChannelToStorage(name) {
    var saved = JSON.parse(localStorage.getItem('custom_ota_channels') || '[]');
    if (!saved.includes(name)) {
        saved.push(name);
        localStorage.setItem('custom_ota_channels', JSON.stringify(saved));
    }
}

function copyCookieScript(e) {
    if (e && e.preventDefault) e.preventDefault();
    var script = "javascript:(function(){navigator.clipboard.writeText(document.cookie);alert('✅ Active Cookie Copied to Clipboard! Now paste into Prime Booking Importer.');})();";
    if (navigator.clipboard) {
        navigator.clipboard.writeText(script).then(function() {
            alert("✅ Copy Cookie Script Copied to Clipboard!\n\n💡 Tip: You can also DRAG this button directly onto your browser's Bookmarks Bar to create a 1-click Cookie Extractor button!");
        }).catch(function() {
            prompt("Copy this 1-Click Cookie Extractor Bookmarklet script:", script);
        });
    } else {
        prompt("Copy this 1-Click Cookie Extractor Bookmarklet script:", script);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var saved = JSON.parse(localStorage.getItem('custom_ota_channels') || '[]');
    saved.forEach(function(channelName) {
        appendOtaChannelCard(channelName);
    });

    @if(session('import_logs'))
        var syncModal = new bootstrap.Modal(document.getElementById('syncOtaCookieModal'));
        syncModal.show();
    @endif
});

function switchImportTab(mode) {
    document.getElementById('importModeInput').value = mode;
    
    var tabPayload = document.getElementById('tabPayload');
    var tabApi = document.getElementById('tabApi');
    var sectionJson = document.getElementById('sectionJsonPayload');
    var sectionApi = document.getElementById('sectionApiFetch');

    if (mode === 'json_payload') {
        sectionJson.style.display = 'block';
        sectionApi.style.display = 'none';
        
        tabPayload.style.background = '#ffffff';
        tabPayload.style.color = 'var(--primary)';
        tabPayload.classList.add('shadow-sm');

        tabApi.style.background = 'transparent';
        tabApi.style.color = '#64748b';
        tabApi.classList.remove('shadow-sm');
    } else {
        sectionJson.style.display = 'none';
        sectionApi.style.display = 'block';

        tabApi.style.background = '#ffffff';
        tabApi.style.color = 'var(--primary)';
        tabApi.classList.add('shadow-sm');

        tabPayload.style.background = 'transparent';
        tabPayload.style.color = '#64748b';
        tabPayload.classList.remove('shadow-sm');
    }
}

function fillSampleJson() {
    var sample = [
        {
            "name": "MV Zabin Sundarban Luxury Ship Cruise",
            "city": "Sundarban",
            "starRating": 5,
            "ratingScore": 4.9,
            "totalReviews": 320,
            "address": "Mongla Port & Sundarbans Waterway, Khulna",
            "price": 18500,
            "primaryImage": "https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1000&q=80",
            "images": [
                "https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1000&q=80",
                "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1000&q=80"
            ],
            "facilities": ["Full Ocean View Deck", "Buffet Dining", "AC Cabins", "Guided Jungle Safari"]
        },
        {
            "name": "Royal Tulip Sea Pearl Beach Resort & Spa",
            "city": "Cox's Bazar",
            "starRating": 5,
            "ratingScore": 4.8,
            "totalReviews": 890,
            "address": "Jaliapalong, Inani, Cox's Bazar",
            "price": 12500,
            "primaryImage": "https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=80",
            "images": [
                "https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=80",
                "https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80"
            ],
            "facilities": ["Private Beach", "Infinity Pool", "Spa & Wellness", "Free WiFi", "Breakfast Included"]
        },
        {
            "name": "Sajek Valley Eco Cottage",
            "city": "Sajek",
            "starRating": 4,
            "ratingScore": 4.7,
            "totalReviews": 145,
            "address": "Ruilui Para, Sajek Valley, Rangamati",
            "price": 4500,
            "primaryImage": "https://images.unsplash.com/photo-1587061949409-02df41d5e562?auto=format&fit=crop&w=1000&q=80",
            "images": [
                "https://images.unsplash.com/photo-1587061949409-02df41d5e562?auto=format&fit=crop&w=1000&q=80"
            ],
            "facilities": ["Cloud View Balcony", "Traditional BBQ", "Helipad Access", "24/7 Security"]
        }
    ];

    document.getElementById('jsonPayloadInput').value = JSON.stringify(sample, null, 2);
}

document.getElementById('importHotelForm').addEventListener('submit', function() {
    var btn = document.getElementById('btnSubmitImport');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Importing Real Hotel Data... Please wait';
});
</script>
@endsection
