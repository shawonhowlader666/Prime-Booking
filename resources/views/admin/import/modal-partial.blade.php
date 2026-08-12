{{-- GLOBAL MODAL: POPUP OTA COOKIE SYNCHRONIZER CARD --}}
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
                    <a href="javascript:(function(){navigator.clipboard.writeText(document.cookie);alert('Agoda/Booking Active Cookie Copied to Clipboard!');})();" class="btn btn-sm btn-primary text-white fw-bold px-2.5 py-1 d-inline-flex align-items-center shadow-sm" style="font-size:11.5px; border-radius:4px; background:var(--primary); border:none; cursor:grab;" title="Drag this button to your browser Bookmark Bar or click to copy script!" onclick="copyCookieScript(event)">
                        <i class="fa-solid fa-puzzle-piece me-1.5" style="color:#fbbf24;"></i> Copy Cookie
                    </a>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form action="{{ route('admin.import-hotels.store') }}" method="POST" id="importHotelFormModal">
                @csrf
                <input type="hidden" name="mode" value="cookie_sync">
                <input type="hidden" name="target_city" value="Bangladesh">
                <input type="hidden" name="max_limit" value="50">
                <input type="hidden" name="override_status" value="active">

                <div class="modal-body p-4">

                    {{-- Target Country Selection (Clean Professional Text) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size:12.5px;">Target Market / Country <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <select name="target_country" id="targetCountrySelectModal" class="form-select flex-grow-1" style="height:38px; border-radius:4px;" required>
                                <option value="BD" selected>Bangladesh (Primary Market)</option>
                                <option value="TH">Thailand</option>
                                <option value="UAE">UAE / Dubai</option>
                                <option value="MY">Malaysia</option>
                                <option value="IN">India</option>
                                <option value="GLOBAL">Global / All Markets</option>
                            </select>
                            <button type="button" class="btn text-white fw-bold px-3 d-inline-flex align-items-center justify-content-center shadow-none" onclick="openAddOptionModal('target_country')" title="Add Custom Target Country" style="background:var(--primary); border:none; border-radius:4px; height:38px; min-width:40px; flex-shrink:0;">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
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
                                @php
                                    $savedCookieGlobal = \App\Models\SiteSetting::get('ota_saved_cookie_agoda', '');
                                @endphp
                                @if(!empty($savedCookieGlobal))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size:10.5px;">
                                        <i class="fa-solid fa-database me-1"></i> Vault Cookie Active
                                    </span>
                                @endif
                                <button type="button" class="btn btn-link text-decoration-none p-0 small fw-bold" onclick="fillSampleJson()" style="font-size:11.5px; color:var(--primary);">
                                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Fill Sample Cookie
                                </button>
                            </div>
                        </div>
                        <textarea name="cookie_header" id="jsonPayloadInputModal" class="form-control font-monospace" rows="4" placeholder="Paste browser Cookie header or F12 JSON Network payload here... (e.g. agoda.sid=...; _ga=...; booking_session=...)" style="border-radius:4px; border-color:#cbd5e1; font-size:12px;" required>{{ old('cookie_header', $savedCookieGlobal ?? '') }}</textarea>
                        <small class="text-secondary d-block mt-1.5" style="font-size:11.5px;">
                            Vaulted cookies persist automatically for background sync.
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
var currentCategory = 'target_country';

function openAddOptionModal(category) {
    currentCategory = category;
    var categoryInput = document.getElementById('addOptionCategory');
    if (categoryInput) categoryInput.value = category;
    var input = document.getElementById('addOptionInput');
    if (input) input.value = '';

    var title = document.getElementById('addOptionModalTitle');
    var label = document.getElementById('addOptionLabel');
    var help = document.getElementById('addOptionHelpText');

    if (category === 'target_country') {
        if (title) title.innerHTML = '<i class="fa-solid fa-earth-americas text-primary me-1"></i> Add Custom Target Country';
        if (label) label.innerText = 'Country / Market Name';
        if (input) input.placeholder = 'e.g. Singapore, Malaysia, Saudi Arabia, UK';
        if (help) help.innerText = 'Add a custom target country destination market.';
    } else if (category === 'ota_channel') {
        if (title) title.innerHTML = '<i class="fa-solid fa-globe text-primary me-1"></i> Add Custom OTA Channel';
        if (label) label.innerText = 'OTA Website / Channel Name';
        if (input) input.placeholder = 'e.g. Traveloka, MakeMyTrip, Goibibo, Klook, ShareTrip';
        if (help) help.innerText = 'Add a new custom OTA website channel to the active feeds dashboard.';
    }

    var modalEl = document.getElementById('addOptionModal');
    if (modalEl) {
        var bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
        setTimeout(function() { if (input) input.focus(); }, 300);
    }
}

function saveNewOption() {
    var inputEl = document.getElementById('addOptionInput');
    if (!inputEl) return;
    var val = inputEl.value.trim();
    if (!val) {
        alert('Please enter a valid option value.');
        return;
    }

    if (currentCategory === 'target_country') {
        var countrySelectModal = document.getElementById('targetCountrySelectModal');
        if (countrySelectModal) {
            var opt = new Option(val, val, true, true);
            countrySelectModal.add(opt, countrySelectModal.options[0]);
            countrySelectModal.value = val;
        }
    } else if (currentCategory === 'ota_channel') {
        var otaSelectModal = document.getElementById('otaChannelSelectModal');
        if (otaSelectModal) {
            var opt = new Option(val + ' (Custom Channel)', val.toLowerCase(), true, true);
            otaSelectModal.add(opt, otaSelectModal.options[0]);
            otaSelectModal.value = val.toLowerCase();
        }
        if (typeof appendOtaChannelCard === 'function') {
            appendOtaChannelCard(val);
        }
        if (typeof saveOtaChannelToStorage === 'function') {
            saveOtaChannelToStorage(val);
        }
    }

    var modalEl = document.getElementById('addOptionModal');
    if (modalEl) {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
}

function copyCookieScript(e) {
    if (e && e.preventDefault) e.preventDefault();
    var script = "javascript:(function(){navigator.clipboard.writeText(document.cookie);alert('Active Cookie Copied to Clipboard! Now paste into Prime Booking Importer.');})();";
    if (navigator.clipboard) {
        navigator.clipboard.writeText(script).then(function() {
            alert("Copy Cookie Script Copied to Clipboard!\n\nTip: You can also DRAG this button directly onto your browser's Bookmarks Bar to create a 1-click Cookie Extractor button!");
        }).catch(function() {
            prompt("Copy this 1-Click Cookie Extractor Bookmarklet script:", script);
        });
    } else {
        prompt("Copy this 1-Click Cookie Extractor Bookmarklet script:", script);
    }
}

function fillSampleJson() {
    var txt = document.getElementById('jsonPayloadInputModal');
    if (txt) {
        txt.value = "agoda.sid=123456789; _ga=GA1.1.987654321.1600000000; booking_session=abcxyz987654321;";
    }
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('import_logs'))
        var syncModalEl = document.getElementById('syncOtaCookieModal');
        if (syncModalEl) {
            var syncModal = new bootstrap.Modal(syncModalEl);
            syncModal.show();
        }
    @endif
});
</script>
