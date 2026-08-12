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
                OTA Real Hotel Data Importer Tool
            </h1>
            <p class="text-secondary mb-0" style="font-size:12.5px; margin-top:2px;">
                Import real hotels, room categories, pricing, amenities &amp; photo galleries directly from Agoda, Booking.com, or API payloads.
            </p>
        </div>
        <a href="{{ route('admin.properties.index') }}" class="btn-table-action" style="font-size:13px; padding:6px 14px; border-radius:4px;">
            <i class="fa-solid fa-hotel me-1"></i> View Property Inventory
        </a>
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
                <small class="text-secondary" style="font-size:11.5px;">Supports Agoda, Booking.com, Traveloka, MakeMyTrip, Goibibo, Expedia, Airbnb &amp; Custom API Feeds.</small>
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
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded d-flex align-items-center justify-content-between" style="background:#f8fafc; border-color:#e2e8f0!important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="pulse-dot" style="background:#52c41a; width:8px; height:8px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px rgba(82,196,26,0.2);"></span>
                        <div>
                            <strong style="font-size:12px; display:block; color:#1e293b;">Agoda Global API</strong>
                            <small style="font-size:10.5px; color:#64748b;">Cookie Header Ready</small>
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

<div class="row g-4">
    {{-- Left Import Form Column --}}
    <div class="col-lg-7">
        <div class="form-card" style="border-radius:4px;">
            
            {{-- Mode Switcher Tabs --}}
            <div class="d-flex align-items-center gap-2 mb-4 p-1" style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:4px;">
                <button type="button" class="btn flex-fill fw-bold py-2 shadow-none border-0 active-import-tab" id="tabPayload" onclick="switchImportTab('json_payload')" style="font-size:13px; border-radius:4px; background:#ffffff; color:var(--primary); box-shadow:0 2px 6px rgba(0,0,0,0.06)!important;">
                    <i class="fa-solid fa-code me-1"></i> Paste Network JSON Payload
                </button>
                <button type="button" class="btn flex-fill fw-bold py-2 shadow-none border-0 text-secondary" id="tabApi" onclick="switchImportTab('api_fetch')" style="font-size:13px; border-radius:4px; background:transparent;">
                    <i class="fa-solid fa-link me-1"></i> Live API Fetch (Cookie / Token)
                </button>
            </div>

            <form action="{{ route('admin.import-hotels.store') }}" method="POST" id="importHotelForm">
                @csrf
                <input type="hidden" name="mode" id="importModeInput" value="json_payload">

                {{-- Row 1: Target City Select + [+] Button & Max Limit Select + [+] Button --}}
                <div class="row g-3 mb-3">
                    
                    {{-- Target City Select + [+] Button --}}
                    <div class="col-md-6">
                        <label class="form-label">Target City / Region <span class="text-danger">*</span></label>
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

                    {{-- Max Limit Select + [+] Button --}}
                    <div class="col-md-6">
                        <label class="form-label">Max Limit (Hotels to Process)</label>
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
                </div>

                {{-- Row 2: Property Type Select + [+] Button & Default Status Select + [+] Button --}}
                <div class="row g-3 mb-3">
                    
                    {{-- Property Type Override Select + [+] Button --}}
                    <div class="col-md-6">
                        <label class="form-label">Property Type Override</label>
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <select name="override_type" id="overrideTypeSelect" class="form-select flex-grow-1" style="height:38px; border-radius:4px;">
                                @foreach($propertyTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn text-white fw-bold px-3 d-inline-flex align-items-center justify-content-center shadow-none" onclick="openAddOptionModal('type')" title="Add New Property Type" style="background:var(--primary); border:none; border-radius:4px; height:38px; min-width:40px; flex-shrink:0;">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Default Status Select + [+] Button --}}
                    <div class="col-md-6">
                        <label class="form-label">Default Listing Status</label>
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <select name="override_status" id="overrideStatusSelect" class="form-select flex-grow-1" style="height:38px; border-radius:4px;">
                                <option value="active" selected>🟢 Active &amp; Published (Live immediately)</option>
                                <option value="pending">🟡 Pending Review (Draft mode)</option>
                                <option value="inactive">🔴 Inactive</option>
                            </select>
                            <button type="button" class="btn text-white fw-bold px-3 d-inline-flex align-items-center justify-content-center shadow-none" onclick="openAddOptionModal('status')" title="Add Status Option" style="background:var(--primary); border:none; border-radius:4px; height:38px; min-width:40px; flex-shrink:0;">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Mode 1: Raw JSON Payload --}}
                <div id="sectionJsonPayload">
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label m-0">Paste JSON Response <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-link text-decoration-none p-0 small fw-bold" onclick="fillSampleJson()" style="font-size:11.5px; color:var(--primary);">
                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Fill Sample Agoda/Booking Data
                            </button>
                        </div>
                        <textarea name="json_payload" id="jsonPayloadInput" class="form-control font-monospace" rows="10" placeholder='Paste your API response JSON here (e.g. [{"name": "Royal Tulip Beach Resort", "starRating": 5, "price": 12500, "images": [...]}, ...])' style="border-radius:4px;"></textarea>
                        <small class="text-muted d-block mt-1" style="font-size:11.5px;">
                            💡 Open F12 Network tab on Agoda/Booking.com, copy the JSON response array or object, and paste it directly here.
                        </small>
                    </div>
                </div>

                {{-- Mode 2: Live API Request --}}
                <div id="sectionApiFetch" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">API Endpoint URL <span class="text-danger">*</span></label>
                        <input type="url" name="endpoint_url" class="form-control" placeholder="https://www.agoda.com/api/cronos/search/getsearchhotelssync" style="border-radius:4px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cookie Header (Optional)</label>
                        <textarea name="cookie_header" class="form-control font-monospace" rows="3" placeholder="agoda.sid=...; _ga=...; access_token=..." style="border-radius:4px;"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Authorization Bearer Token (Optional)</label>
                        <input type="text" name="authorization_token" class="form-control font-monospace" placeholder="Bearer eyJhbGciOiJSUzI1NiIs..." style="border-radius:4px;">
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit" class="btn text-white fw-bold px-4 py-2 w-100" style="background:var(--primary); border-radius:4px; font-size:14px; border:none;" id="btnSubmitImport">
                        <i class="fa-solid fa-bolt me-2"></i> Start Importing Real Hotel Data
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- Right Information & Execution Console Column --}}
    <div class="col-lg-5">
        
        {{-- How it works card --}}
        <div class="form-card mb-4" style="background:#fafafa; border-radius:4px;">
            <h6 class="form-section-title d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-info"></i> Best Smart Logic Features
            </h6>
            <ul class="list-unstyled mb-0" style="font-size:12.5px; color:#475569; line-height:1.6;">
                <li class="mb-2 d-flex align-items-start gap-2">
                    <i class="fa-solid fa-plus text-primary mt-1"></i>
                    <div><strong>Interactive [+] Option Adders:</strong> Click <code>[+]</code> beside any dropdown to instantly add custom cities, limits, or property types.</div>
                </li>
                <li class="mb-2 d-flex align-items-start gap-2">
                    <i class="fa-solid fa-check text-success mt-1"></i>
                    <div><strong>Smart Auto-Normalization:</strong> Automatically parses hotel names, star ratings, ratings, amenities, and room pricing.</div>
                </li>
                <li class="mb-2 d-flex align-items-start gap-2">
                    <i class="fa-solid fa-check text-success mt-1"></i>
                    <div><strong>Photo Gallery Extractor:</strong> Extracts high-resolution image URLs and associates them with properties.</div>
                </li>
                <li class="mb-2 d-flex align-items-start gap-2">
                    <i class="fa-solid fa-check text-success mt-1"></i>
                    <div><strong>Duplicate Prevention:</strong> Uses <code>updateOrCreate</code> matching on name and city to prevent duplicate records.</div>
                </li>
                <li class="d-flex align-items-start gap-2">
                    <i class="fa-solid fa-check text-success mt-1"></i>
                    <div><strong>Auto Room Generation:</strong> Automatically builds Deluxe, Super Deluxe &amp; Family Suites so booking flow works instantly.</div>
                </li>
            </ul>
        </div>

        {{-- Execution Console Logs --}}
        <div class="form-card" style="border-radius:4px;">
            <h6 class="form-section-title d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-terminal me-1"></i> Import Execution Logs</span>
                <span class="badge bg-secondary" style="font-size:10px; border-radius:4px;">Real-Time Audit</span>
            </h6>

            <div id="importLogsConsole" style="background:#0f172a; color:#38bdf8; font-family:monospace; font-size:12px; padding:14px; border-radius:4px; height:240px; overflow-y:auto; line-height:1.5;">
                @if(session('import_logs') && is_array(session('import_logs')))
                    @foreach(session('import_logs') as $log)
                        <div>{{ $log }}</div>
                    @endforeach
                @else
                    <div class="text-muted">Console ready. Click "Start Importing" to execute batch operation...</div>
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
    }

    var modalEl = document.getElementById('addOptionModal');
    var modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
}

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
