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
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <div>
            <h1 class="page-title">
                <i class="fa-solid fa-cloud-arrow-down" style="color:var(--primary); margin-right:8px;"></i>
                Global OTA Inventory Automation &amp; Data Synchronization Engine
            </h1>
            <p class="text-secondary mb-0" style="font-size:12.5px; margin-top:2px;">
                Automated OTA property data synchronization and multi-channel inventory management.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ asset('downloads/prime-booking-importer.zip') }}" download class="btn btn-sm btn-success text-white fw-bold d-inline-flex align-items-center shadow-sm" style="font-size:12.5px; padding:7px 14px; border-radius:4px; text-decoration:none;">
                <i class="fa-solid fa-puzzle-piece me-1.5"></i> Download Chrome Extension (v1.0)
            </a>
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

    {{-- Active OTA Cookie Channels & Live Feeds Hub --}}
    <div class="form-card mb-4" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:16px 20px;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 flex-wrap gap-2">
            <div>
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size:14px;">
                    <i class="fa-solid fa-cookie-bite text-warning"></i>
                    Active OTA Cookie Channels &amp; Universal Importer Engine
                </h6>
                <small class="text-secondary" style="font-size:11.5px;">Multi-channel OTA API integration and real-time cookie data streams.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-2 py-1" onclick="openAddOptionModal('ota_channel')" style="font-size:11px; border-radius:4px;">
                    <i class="fa-solid fa-plus me-1"></i> Add Custom OTA Channel
                </button>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:11px; font-weight:600;">
                    <i class="fa-solid fa-signal me-1"></i> Universal Parser Active
                </span>
            </div>
        </div>
        <div class="row g-3" id="otaChannelsContainer">
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded d-flex align-items-center justify-content-between" style="background:#f8fafc; border-color:#e2e8f0!important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="pulse-dot" style="background:#52c41a; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(82,196,26,0.2);"></span>
                        <div>
                            <strong style="font-size:12px; display:block; color:#1e293b;">Agoda Global API</strong>
                            <small style="font-size:10.5px; color:#64748b;">Cookie Header Active</small>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary" style="font-size:10px;">OTA-01</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded d-flex align-items-center justify-content-between" style="background:#f8fafc; border-color:#e2e8f0!important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="pulse-dot" style="background:#52c41a; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(82,196,26,0.2);"></span>
                        <div>
                            <strong style="font-size:12px; display:block; color:#1e293b;">Booking.com Engine</strong>
                            <small style="font-size:10.5px; color:#64748b;">JSON Parser Active</small>
                        </div>
                    </div>
                    <span class="badge bg-success-subtle text-success" style="font-size:10px;">OTA-02</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded d-flex align-items-center justify-content-between" style="background:#f8fafc; border-color:#e2e8f0!important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="pulse-dot" style="background:#1890ff; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(24,144,255,0.2);"></span>
                        <div>
                            <strong style="font-size:12px; display:block; color:#1e293b;">Expedia / Hotels.com</strong>
                            <small style="font-size:10.5px; color:#64748b;">Payload Bridge Ready</small>
                        </div>
                    </div>
                    <span class="badge bg-warning-subtle text-warning" style="font-size:10px;">OTA-03</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded d-flex align-items-center justify-content-between" style="background:#f8fafc; border-color:#e2e8f0!important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="pulse-dot" style="background:#fa8c16; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(250,140,22,0.2);"></span>
                        <div>
                            <strong style="font-size:12px; display:block; color:#1e293b;">Trip.com / Airbnb</strong>
                            <small style="font-size:10.5px; color:#64748b;">Data Import Active</small>
                        </div>
                    </div>
                    <span class="badge bg-info-subtle text-info" style="font-size:10px;">OTA-04</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Real MySQL Live Database Metrics Bar --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="form-card py-2 px-3 d-flex align-items-center justify-content-between" style="border-radius:4px; background:#fff; border:1px solid #e2e8f0;">
                <div>
                    <span class="text-secondary d-block" style="font-size:11px; font-weight:600; text-transform:uppercase;">Total DB Inventory</span>
                    <strong style="font-size:15px; color:#0f172a;">{{ number_format($stats['total_properties'] ?? 0) }} Properties</strong>
                </div>
                <div class="p-2 rounded-circle" style="background:#e6f7ff; color:#1890ff;"><i class="fa-solid fa-hotel"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="form-card py-2 px-3 d-flex align-items-center justify-content-between" style="border-radius:4px; background:#fff; border:1px solid #e2e8f0;">
                <div>
                    <span class="text-secondary d-block" style="font-size:11px; font-weight:600; text-transform:uppercase;">Active Published</span>
                    <strong style="font-size:15px; color:#52c41a;">{{ number_format($stats['active_published'] ?? 0) }} Live</strong>
                </div>
                <div class="p-2 rounded-circle" style="background:#f6ffed; color:#52c41a;"><i class="fa-solid fa-circle-check"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="form-card py-2 px-3 d-flex align-items-center justify-content-between" style="border-radius:4px; background:#fff; border:1px solid #e2e8f0;">
                <div>
                    <span class="text-secondary d-block" style="font-size:11px; font-weight:600; text-transform:uppercase;">Covered Cities</span>
                    <strong style="font-size:15px; color:#1890ff;">{{ number_format($stats['total_cities'] ?? 0) }} Regions</strong>
                </div>
                <div class="p-2 rounded-circle" style="background:#e6f7ff; color:#1890ff;"><i class="fa-solid fa-location-dot"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="form-card py-2 px-3 d-flex align-items-center justify-content-between" style="border-radius:4px; background:#fff; border:1px solid #e2e8f0;">
                <div>
                    <span class="text-secondary d-block" style="font-size:11px; font-weight:600; text-transform:uppercase;">Room Categories</span>
                    <strong style="font-size:15px; color:#fa8c16;">{{ number_format($stats['total_rooms'] ?? 0) }} Rooms</strong>
                </div>
                <div class="p-2 rounded-circle" style="background:#fff7e6; color:#fa8c16;"><i class="fa-solid fa-bed"></i></div>
            </div>
        </div>
    </div>

<div class="row g-4">
    {{-- Left Import Form Column --}}
    <div class="col-lg-7">
        <div class="form-card" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:20px;">
            
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size:15px;">
                    <i class="fa-solid fa-cookie-bite text-warning"></i>
                    Live OTA Cookie Data Synchronizer
                </h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:11px; font-weight:600;">
                    <i class="fa-solid fa-signal me-1"></i> Cookie Direct Sync Active
                </span>
            </div>

            <form action="{{ route('admin.import-hotels.store') }}" method="POST" id="importHotelForm">
                @csrf
                <input type="hidden" name="mode" id="importModeInput" value="cookie_sync">

                {{-- Row 1: Target City Select + [+] Button & OTA Channel Select --}}
                <div class="row g-3 mb-3">
                    
                    {{-- Target City Select + [+] Button --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark" style="font-size:12.5px;">Target City / Region <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <select name="target_city" id="targetCitySelect" class="form-select flex-grow-1" style="height:38px; border-radius:4px;" required>
                                @foreach($cities as $c)
                                    <option value="{{ $c }}" {{ $c === "Cox's Bazar" ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn text-white fw-bold px-3 d-inline-flex align-items-center justify-content-center shadow-none" onclick="openAddOptionModal('city')" title="Add New City Option" style="background:var(--primary); border:none; border-radius:4px; height:38px; min-width:40px; flex-shrink:0;">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- OTA Source Channel Select + [+] Button --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark" style="font-size:12.5px;">OTA Source Channel <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <select name="ota_channel" id="otaChannelSelect" class="form-select flex-grow-1" style="height:38px; border-radius:4px;">
                                <option value="agoda" selected>Agoda.com (Global API)</option>
                                <option value="booking">Booking.com Engine</option>
                                <option value="expedia">Expedia / Hotels.com</option>
                                <option value="traveloka">Traveloka / MakeMyTrip</option>
                                <option value="airbnb">Airbnb / Homestay</option>
                            </select>
                            <button type="button" class="btn text-white fw-bold px-3 d-inline-flex align-items-center justify-content-center shadow-none" onclick="openAddOptionModal('ota_channel')" title="Add Custom OTA Channel" style="background:var(--primary); border:none; border-radius:4px; height:38px; min-width:40px; flex-shrink:0;">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Max Limit Select + [+] Button & Default Status Select + [+] Button --}}
                <div class="row g-3 mb-3">
                    
                    {{-- Max Limit Select + [+] Button --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark" style="font-size:12.5px;">Max Limit (Hotels to Sync)</label>
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <select name="max_limit" id="maxLimitSelect" class="form-select flex-grow-1" style="height:38px; border-radius:4px;">
                                <option value="10">10 Properties</option>
                                <option value="25">25 Properties</option>
                                <option value="50" selected>50 Properties (Recommended)</option>
                                <option value="100">100 Properties</option>
                                <option value="200">200 Properties</option>
                            </select>
                            <button type="button" class="btn text-white fw-bold px-3 d-inline-flex align-items-center justify-content-center shadow-none" onclick="openAddOptionModal('limit')" title="Add Custom Limit Option" style="background:var(--primary); border:none; border-radius:4px; height:38px; min-width:40px; flex-shrink:0;">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Default Status Select + [+] Button --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark" style="font-size:12.5px;">Default Listing Status</label>
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <select name="override_status" id="overrideStatusSelect" class="form-select flex-grow-1" style="height:38px; border-radius:4px;">
                                <option value="active" selected>🟢 Active &amp; Published</option>
                                <option value="pending">🟡 Pending Review</option>
                                <option value="inactive">🔴 Inactive</option>
                            </select>
                            <button type="button" class="btn text-white fw-bold px-3 d-inline-flex align-items-center justify-content-center shadow-none" onclick="openAddOptionModal('status')" title="Add Status Option" style="background:var(--primary); border:none; border-radius:4px; height:38px; min-width:40px; flex-shrink:0;">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Cookie Header Input Box --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="form-label fw-semibold text-dark m-0" style="font-size:12.5px;">
                            Cookie Data Header <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            @if(!empty($savedCookie))
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">
                                    <i class="fa-solid fa-database me-1"></i> Saved Vault Cookie Active
                                </span>
                            @endif
                            <button type="button" class="btn btn-link text-decoration-none p-0 small fw-bold" onclick="fillSampleJson()" style="font-size:11.5px; color:var(--primary);">
                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Fill Sample Cookie
                            </button>
                        </div>
                    </div>
                    <textarea name="cookie_header" id="jsonPayloadInput" class="form-control font-monospace" rows="7" placeholder="Paste your browser Cookie header here... (e.g. agoda.sid=...; _ga=...; booking_session=...)" style="border-radius:4px; border-color:#cbd5e1; font-size:12px;">{{ old('cookie_header', $savedCookie ?? '') }}</textarea>
                    <small class="text-secondary d-block mt-1.5" style="font-size:11.5px;">
                        💡 <strong>একবার কুকি দিলে তা সেভ হয়ে থাকবে</strong> — পরবর্তীতে আর পেস্ট করতে হবে না, অটোমেটিক সেভ করা কুকি দিয়ে সিঙ্ক হবে!
                    </small>
                </div>

                {{-- Submit Button --}}
                <div>
                    <button type="submit" class="btn text-white fw-bold px-4 py-2.5 w-100 shadow-sm" style="background:var(--primary); border-radius:4px; font-size:14px; border:none;" id="btnSubmitImport">
                        <i class="fa-solid fa-bolt me-2"></i> Execute Cookie Data Synchronization
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- Right Information & Execution Console Column --}}
    <div class="col-lg-5">
        
        {{-- How it works card --}}
        <div class="form-card mb-4" style="background:#f8fafc; border-radius:4px; border:1px solid #e2e8f0; padding:20px;">
            <h6 class="form-section-title d-flex align-items-center gap-2 text-dark fw-bold mb-3" style="font-size:14px;">
                <i class="fa-solid fa-cookie-bite text-warning"></i> How Cookie Import Works (3 Steps)
            </h6>
            <ol class="ps-3 mb-0" style="font-size:12.5px; color:#334155; line-height:1.7;">
                <li class="mb-2">
                    <strong>Open OTA Website:</strong> Open Agoda or Booking.com in Google Chrome.
                </li>
                <li class="mb-2">
                    <strong>Copy Cookie Header:</strong> Press <code>F12</code> &rarr; Go to <strong>Network</strong> tab &rarr; Copy the <code>Cookie:</code> line from Request Headers.
                </li>
                <li class="mb-2">
                    <strong>Paste &amp; Sync:</strong> Paste the Cookie into the box on the left and click <strong>"Execute Cookie Data Synchronization"</strong>!
                </li>
            </ol>
        </div>      </div>

        {{-- Execution Console Logs --}}
        <div class="form-card" style="border-radius:4px;">
            <h6 class="form-section-title d-flex align-items-center justify-content-between mb-3">
                <span class="d-flex align-items-center gap-2" style="font-size:13.5px; font-weight:600;">
                    <i class="fa-solid fa-terminal text-primary"></i> Live Importer Execution Console &amp; Audit Log
                </span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size:10px; font-weight:600;">
                    <i class="fa-solid fa-spinner fa-spin me-1"></i> Real-Time Audit
                </span>
            </h6>

            <div id="importLogsConsole" style="background:#090d16; color:#38bdf8; font-family:'Courier New', Courier, monospace; font-size:12px; padding:14px 16px; border-radius:4px; height:240px; overflow-y:auto; line-height:1.6; border:1px solid #1e293b;">
                @if(session('import_logs') && is_array(session('import_logs')))
                    @foreach(session('import_logs') as $log)
                        <div style="color:#52c41a; margin-bottom:3px;"><i class="fa-solid fa-angle-right me-1" style="color:#38bdf8;"></i> {{ $log }}</div>
                    @endforeach
                @else
                    <div style="color:#94a3b8; font-style:italic;"><i class="fa-solid fa-terminal me-2" style="color:#1890ff;"></i> Console ready. Click "Execute Data Synchronization" to run batch operation...</div>
                @endif
            </div>
        </div>

    </div>
</div>
</div> {{-- end page-content-area --}}

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

document.addEventListener('DOMContentLoaded', function() {
    var saved = JSON.parse(localStorage.getItem('custom_ota_channels') || '[]');
    saved.forEach(function(channelName) {
        appendOtaChannelCard(channelName);
    });
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
