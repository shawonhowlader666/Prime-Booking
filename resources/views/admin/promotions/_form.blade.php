{{-- Shared form partial for Create & Edit --}}

@if($errors->any())
<div class="admin-alert error mb-4" style="border-radius:4px; padding:12px 16px;">
    <i class="fa-solid fa-circle-xmark me-2"></i>
    <strong>Please review the input errors below:</strong>
    <ul class="mb-0 mt-1 ps-3" style="font-size:12.5px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="row g-4">

    {{-- LEFT: Content & Design (8 cols) --}}
    <div class="col-lg-8">

        {{-- Promotion Content Card --}}
        <div class="form-card mb-4" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:24px;">
            <div class="border-bottom pb-2.5 mb-3.5">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                    <i class="fa-solid fa-bullhorn text-primary" style="font-size:14px; width:18px;"></i>
                    <span>Promotion Content &amp; Copywriting</span>
                </h6>
            </div>

            <div class="row g-3.5">
                <div class="col-12">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Campaign Title <span style="color:#ff4d4f;">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $promotion?->title) }}" placeholder="e.g. Cox's Bazar Summer Special - 25% Off" required maxlength="100" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Subtitle / Tagline</label>
                    <input type="text" name="subtitle" class="form-control"
                        value="{{ old('subtitle', $promotion?->subtitle) }}" placeholder="e.g. Book top 5-star beach resorts at exclusive rates" maxlength="150" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Badge Text</label>
                    <input type="text" name="badge_text" class="form-control"
                        value="{{ old('badge_text', $promotion?->badge_text) }}" placeholder="e.g. HOT DEAL 25% OFF" maxlength="50" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Call-to-Action Text</label>
                    <input type="text" name="cta_text" class="form-control"
                        value="{{ old('cta_text', $promotion?->cta_text) }}" placeholder="e.g. Explore Beach Resorts" maxlength="60" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">CTA Link (Destination Path / URL)</label>
                    <input type="text" name="cta_link" class="form-control"
                        value="{{ old('cta_link', $promotion?->cta_link) }}" placeholder="/hotels?city=Coxs+Bazar" maxlength="300" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>

                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Icon / Emoji</label>
                    <input type="text" name="icon" class="form-control"
                        value="{{ old('icon', $promotion?->icon ?? '🏖️') }}" placeholder="🏖️ ✈️ 🎯 🗺️" maxlength="10" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>

                <div class="col-md-8">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Background Image CDN URL (Optional)</label>
                    <input type="url" name="image_url" class="form-control"
                        value="{{ old('image_url', $promotion?->image_url) }}" placeholder="https://images.unsplash.com/photo-..." style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>
            </div>
        </div>

        {{-- Appearance & Color Setup Card --}}
        <div class="form-card mb-4" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:24px;">
            <div class="border-bottom pb-2.5 mb-3.5">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                    <i class="fa-solid fa-palette text-purple" style="font-size:14px; width:18px; color:#7367f0;"></i>
                    <span>Banner Color Styling &amp; Live Preview</span>
                </h6>
            </div>

            <div class="row g-3.5">
                <div class="col-md-3 col-6">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Start Gradient <span style="color:#ff4d4f;">*</span></label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="color" name="bg_color" class="form-control form-control-color"
                            value="{{ old('bg_color', $promotion?->bg_color ?? '#1e3a8a') }}" style="height:38px; width:45px; border-radius:4px; cursor:pointer;">
                        <input type="text" id="bg_color_hex" class="form-control"
                            value="{{ old('bg_color', $promotion?->bg_color ?? '#1e3a8a') }}" oninput="document.querySelector('[name=bg_color]').value=this.value" style="font-size:12px; border-radius:4px; height:38px; padding:0 8px;">
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">End Gradient</label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="color" name="bg_color_end" class="form-control form-control-color"
                            value="{{ old('bg_color_end', $promotion?->bg_color_end ?? '#3b82f6') }}" style="height:38px; width:45px; border-radius:4px; cursor:pointer;">
                        <input type="text" class="form-control"
                            value="{{ old('bg_color_end', $promotion?->bg_color_end ?? '#3b82f6') }}" oninput="document.querySelector('[name=bg_color_end]').value=this.value" style="font-size:12px; border-radius:4px; height:38px; padding:0 8px;">
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Text Color</label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="color" name="text_color" class="form-control form-control-color"
                            value="{{ old('text_color', $promotion?->text_color ?? '#ffffff') }}" style="height:38px; width:45px; border-radius:4px; cursor:pointer;">
                        <input type="text" class="form-control"
                            value="{{ old('text_color', $promotion?->text_color ?? '#ffffff') }}" oninput="document.querySelector('[name=text_color]').value=this.value" style="font-size:12px; border-radius:4px; height:38px; padding:0 8px;">
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Badge Color</label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="color" name="badge_bg" class="form-control form-control-color"
                            value="{{ old('badge_bg', $promotion?->badge_bg ?? '#ff9f43') }}" style="height:38px; width:45px; border-radius:4px; cursor:pointer;">
                        <input type="text" class="form-control"
                            value="{{ old('badge_bg', $promotion?->badge_bg ?? '#ff9f43') }}" oninput="document.querySelector('[name=badge_bg]').value=this.value" style="font-size:12px; border-radius:4px; height:38px; padding:0 8px;">
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:8px;">Live Render Preview</label>
                    <div id="promoPreview" class="shadow-sm" style="
                        border-radius: 8px; padding: 20px 24px; display:flex; flex-direction:column;
                        align-items:flex-start; min-height:110px; max-width:420px; transition:all 0.3s;
                        background: linear-gradient(135deg, #1e3a8a, #3b82f6); color:#fff;
                    ">
                        <span id="prev_badge" style="font-size:10px; background:#ff9f43; color:#000; border-radius:4px; padding:2px 8px; margin-bottom:8px; font-weight:800; text-transform:uppercase;">HOT DEAL 25% OFF</span>
                        <div id="prev_title" style="font-size:17px; font-weight:800; line-height:1.3;">Cox's Bazar Summer Special - 25% Off</div>
                        <div id="prev_subtitle" style="font-size:12px; opacity:0.9; margin-top:4px;">Book top 5-star beach resorts at exclusive rates</div>
                        <div id="prev_cta" style="margin-top:12px; background:rgba(255,255,255,0.25); padding:4px 14px; border-radius:4px; font-size:12px; font-weight:700;">Explore Beach Resorts <i class="fa-solid fa-arrow-right ms-1"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Schedule Setup Card --}}
        <div class="form-card" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:24px;">
            <div class="border-bottom pb-2.5 mb-3.5">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                    <i class="fa-solid fa-calendar text-success" style="font-size:14px; width:18px;"></i>
                    <span>Campaign Schedule (Optional)</span>
                </h6>
            </div>
            <div class="row g-3.5">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Start Date &amp; Time</label>
                    <input type="datetime-local" name="starts_at" class="form-control"
                        value="{{ old('starts_at', $promotion?->starts_at?->format('Y-m-d\TH:i')) }}" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                    <small class="text-secondary" style="font-size:11px; margin-top:4px; display:block;">Leave blank to publish immediately.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">End Date &amp; Time</label>
                    <input type="datetime-local" name="ends_at" class="form-control"
                        value="{{ old('ends_at', $promotion?->ends_at?->format('Y-m-d\TH:i')) }}" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                    <small class="text-secondary" style="font-size:11px; margin-top:4px; display:block;">Leave blank for non-expiring campaign.</small>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Settings & Presets (4 cols) --}}
    <div class="col-lg-4">
        {{-- Settings Card --}}
        <div class="form-card mb-4" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:24px;">
            <div class="border-bottom pb-2.5 mb-3.5">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                    <i class="fa-solid fa-sliders text-warning" style="font-size:14px; width:18px;"></i>
                    <span>Campaign Settings</span>
                </h6>
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Promotion Type <span style="color:#ff4d4f;">*</span></label>
                <select name="type" class="form-select" required style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                    <option value="accommodation" @selected(old('type', $promotion?->type) == 'accommodation')>🏨 Accommodation</option>
                    <option value="flights"       @selected(old('type', $promotion?->type) == 'flights')>✈️ Flights</option>
                    <option value="activities"    @selected(old('type', $promotion?->type) == 'activities')>🎯 Activities</option>
                    <option value="destination"   @selected(old('type', $promotion?->type) == 'destination')>🗺️ Destination</option>
                    <option value="general"       @selected(old('type', $promotion?->type) == 'general')>📢 General</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Target Property Type</label>
                <input type="text" name="target_type" class="form-control"
                    value="{{ old('target_type', $promotion?->target_type) }}" placeholder="hotel, resort, houseboat…" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Target City</label>
                <input type="text" name="target_city" class="form-control"
                    value="{{ old('target_city', $promotion?->target_city) }}" placeholder="Cox's Bazar, Dhaka…" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Sort Priority Order</label>
                <input type="number" name="sort_order" class="form-control"
                    value="{{ old('sort_order', $promotion?->sort_order ?? 1) }}" min="0" max="999" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                <small class="text-secondary" style="font-size:11px; margin-top:4px; display:block;">Lower number = appears first.</small>
            </div>

            <div class="p-3 bg-light border d-flex flex-column gap-2.5 mt-4" style="border-radius:4px;">
                <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                        {{ old('is_active', $promotion?->is_active ?? true) ? 'checked' : '' }} style="cursor:pointer; margin-top:0;">
                    <label class="form-check-label fw-bold text-dark mb-0" for="is_active" style="font-size:12.5px; cursor:pointer;">
                        <i class="fa-solid fa-circle-check text-success me-1"></i> Active &amp; Visible Live
                    </label>
                </div>
                <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured"
                        {{ old('is_featured', $promotion?->is_featured ?? false) ? 'checked' : '' }} style="cursor:pointer; margin-top:0;">
                    <label class="form-check-label fw-bold text-warning mb-0" for="is_featured" style="font-size:12.5px; cursor:pointer;">
                        <i class="fa-solid fa-star me-1"></i> Pinned to Hero Carousel
                    </label>
                </div>
            </div>
        </div>

        {{-- Color Presets Card --}}
        <div class="form-card" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:24px;">
            <div class="border-bottom pb-2.5 mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                    <i class="fa-solid fa-swatchbook text-info" style="font-size:14px; width:18px;"></i>
                    <span>Quick Color Presets</span>
                </h6>
            </div>
            <div class="d-flex flex-wrap gap-2.5 mt-2">
                @php
                    $presets = [
                        ['name'=>'Ocean Blue', 'start'=>'#1e3a8a','end'=>'#3b82f6'],
                        ['name'=>'Purple',     'start'=>'#7c3aed','end'=>'#4f46e5'],
                        ['name'=>'Emerald',    'start'=>'#059669','end'=>'#10b981'],
                        ['name'=>'Sunset',     'start'=>'#ea580c','end'=>'#f97316'],
                        ['name'=>'Crimson',    'start'=>'#dc2626','end'=>'#ef4444'],
                        ['name'=>'Magenta',    'start'=>'#db2777','end'=>'#ec4899'],
                        ['name'=>'Teal',       'start'=>'#0d9488','end'=>'#14b8a6'],
                        ['name'=>'Midnight',   'start'=>'#1e293b','end'=>'#475569'],
                    ];
                @endphp
                @foreach($presets as $p)
                <button type="button"
                    onclick="applyPreset('{{ $p['start'] }}','{{ $p['end'] }}')"
                    title="{{ $p['name'] }}"
                    style="width:38px; height:38px; border-radius:4px; border:2px solid rgba(0,0,0,0.1); cursor:pointer;
                           background:linear-gradient(135deg, {{ $p['start'] }}, {{ $p['end'] }}); transition:transform 0.15s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
                </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const inputs = {
    title:     document.querySelector('[name=title]'),
    subtitle:  document.querySelector('[name=subtitle]'),
    badge_text:document.querySelector('[name=badge_text]'),
    cta_text:  document.querySelector('[name=cta_text]'),
    bg_color:  document.querySelector('[name=bg_color]'),
    bg_color_end: document.querySelector('[name=bg_color_end]'),
    text_color:document.querySelector('[name=text_color]'),
    badge_bg:  document.querySelector('[name=badge_bg]'),
};

function updatePreview() {
    const preview = document.getElementById('promoPreview');
    if (!preview) return;
    const bg = inputs.bg_color?.value || '#1e3a8a';
    const bgEnd = inputs.bg_color_end?.value;
    const tc = inputs.text_color?.value || '#fff';
    const bb = inputs.badge_bg?.value || '#ff9f43';

    preview.style.background = bgEnd
        ? `linear-gradient(135deg, ${bg}, ${bgEnd})`
        : bg;
    preview.style.color = tc;

    document.getElementById('prev_badge').style.background  = bb;
    document.getElementById('prev_badge').textContent  = inputs.badge_text?.value || 'HOT DEAL';
    document.getElementById('prev_title').textContent  = inputs.title?.value      || 'Campaign Title';
    document.getElementById('prev_subtitle').textContent = inputs.subtitle?.value || 'Subtitle description';
    document.getElementById('prev_cta').innerHTML   = (inputs.cta_text?.value || 'Explore Offer') + ' <i class="fa-solid fa-arrow-right ms-1"></i>';
}

Object.values(inputs).forEach(i => i?.addEventListener('input', updatePreview));
document.addEventListener('DOMContentLoaded', updatePreview);

function applyPreset(start, end) {
    if (inputs.bg_color) inputs.bg_color.value = start;
    if (inputs.bg_color_end) inputs.bg_color_end.value = end;
    updatePreview();
}
</script>
@endpush
