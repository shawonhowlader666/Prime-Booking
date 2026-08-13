{{-- Shared form partial for Create & Edit --}}

@if($errors->any())
<div class="admin-alert error mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="row g-3">

    {{-- LEFT: Main content --}}
    <div class="col-lg-8">

        {{-- Content card --}}
        <div class="stockifly-card mb-3">
            <div class="card-header-stockifly">
                <i class="fa-solid fa-pen-to-square me-1"></i> Promotion Content
            </div>

            <div class="row g-2 mt-1">
                <div class="col-12">
                    <label class="form-label-sm">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror"
                        value="{{ old('title', $promotion?->title) }}" placeholder="e.g. Last Minute Hotel Deals" required maxlength="100">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-sm">Subtitle</label>
                    <input type="text" name="subtitle" class="form-control form-control-sm"
                        value="{{ old('subtitle', $promotion?->subtitle) }}" placeholder="e.g. Book now & save" maxlength="150">
                </div>

                <div class="col-md-6">
                    <label class="form-label-sm">Badge Text</label>
                    <input type="text" name="badge_text" class="form-control form-control-sm"
                        value="{{ old('badge_text', $promotion?->badge_text) }}" placeholder="e.g. LIMITED TIME" maxlength="50">
                </div>

                <div class="col-md-6">
                    <label class="form-label-sm">Call-to-Action Text</label>
                    <input type="text" name="cta_text" class="form-control form-control-sm"
                        value="{{ old('cta_text', $promotion?->cta_text) }}" placeholder="e.g. Up to 40% OFF" maxlength="60">
                </div>

                <div class="col-md-6">
                    <label class="form-label-sm">CTA Link (URL or /path)</label>
                    <input type="text" name="cta_link" class="form-control form-control-sm"
                        value="{{ old('cta_link', $promotion?->cta_link) }}" placeholder="/search?type=hotel or https://..." maxlength="300">
                </div>

                <div class="col-md-6">
                    <label class="form-label-sm">Icon / Emoji</label>
                    <input type="text" name="icon" class="form-control form-control-sm"
                        value="{{ old('icon', $promotion?->icon) }}" placeholder="🏨 ✈️ 🎯" maxlength="10">
                </div>

                <div class="col-md-6">
                    <label class="form-label-sm">Image URL (optional background)</label>
                    <input type="url" name="image_url" class="form-control form-control-sm"
                        value="{{ old('image_url', $promotion?->image_url) }}" placeholder="https://...jpg">
                </div>
            </div>
        </div>

        {{-- Appearance card --}}
        <div class="stockifly-card mb-3">
            <div class="card-header-stockifly">
                <i class="fa-solid fa-palette me-1"></i> Appearance & Colors
            </div>

            <div class="row g-2 mt-1">
                <div class="col-md-4">
                    <label class="form-label-sm">Background Color <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="color" name="bg_color" class="form-control form-control-color"
                            value="{{ old('bg_color', $promotion?->bg_color ?? '#1890ff') }}" style="height:32px;width:50px;">
                        <input type="text" id="bg_color_hex" class="form-control form-control-sm"
                            value="{{ old('bg_color', $promotion?->bg_color ?? '#1890ff') }}" oninput="document.querySelector('[name=bg_color]').value=this.value">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label-sm">Gradient End Color</label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="color" name="bg_color_end" class="form-control form-control-color"
                            value="{{ old('bg_color_end', $promotion?->bg_color_end ?? '#096dd9') }}" style="height:32px;width:50px;">
                        <input type="text" class="form-control form-control-sm"
                            value="{{ old('bg_color_end', $promotion?->bg_color_end ?? '#096dd9') }}" oninput="document.querySelector('[name=bg_color_end]').value=this.value">
                    </div>
                    <small class="text-muted" style="font-size:10px;">Leave empty for solid color</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label-sm">Text Color</label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="color" name="text_color" class="form-control form-control-color"
                            value="{{ old('text_color', $promotion?->text_color ?? '#ffffff') }}" style="height:32px;width:50px;">
                        <input type="text" class="form-control form-control-sm"
                            value="{{ old('text_color', $promotion?->text_color ?? '#ffffff') }}" oninput="document.querySelector('[name=text_color]').value=this.value">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label-sm">Badge Background</label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="color" name="badge_bg" class="form-control form-control-color"
                            value="{{ old('badge_bg', $promotion?->badge_bg ?? '#f5c518') }}" style="height:32px;width:50px;">
                        <input type="text" class="form-control form-control-sm"
                            value="{{ old('badge_bg', $promotion?->badge_bg ?? '#f5c518') }}" oninput="document.querySelector('[name=badge_bg]').value=this.value">
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <label class="form-label-sm">Live Preview</label>
                    <div id="promoPreview" style="
                        border-radius: 12px; padding: 16px 20px; display:flex; flex-direction:column;
                        align-items:flex-start; min-height:90px; max-width:300px; transition:all 0.3s;
                        background: linear-gradient(135deg, #1890ff, #096dd9); color:#fff;
                    ">
                        <span id="prev_badge" style="font-size:10px;background:#f5c518;color:#000;border-radius:4px;padding:2px 6px;margin-bottom:6px;font-weight:700;">BADGE</span>
                        <div id="prev_title" style="font-size:16px;font-weight:800;">Title Here</div>
                        <div id="prev_subtitle" style="font-size:12px;opacity:0.85;">Subtitle</div>
                        <div id="prev_cta" style="margin-top:8px;background:rgba(255,255,255,0.2);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">CTA Button</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scheduling --}}
        <div class="stockifly-card">
            <div class="card-header-stockifly"><i class="fa-solid fa-calendar me-1"></i> Schedule (Optional)</div>
            <div class="row g-2 mt-1">
                <div class="col-md-6">
                    <label class="form-label-sm">Start Date</label>
                    <input type="datetime-local" name="starts_at" class="form-control form-control-sm"
                        value="{{ old('starts_at', $promotion?->starts_at?->format('Y-m-d\TH:i')) }}">
                    <small class="text-muted" style="font-size:10px;">Leave empty = always active</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">End Date</label>
                    <input type="datetime-local" name="ends_at" class="form-control form-control-sm"
                        value="{{ old('ends_at', $promotion?->ends_at?->format('Y-m-d\TH:i')) }}">
                    <small class="text-muted" style="font-size:10px;">Leave empty = no expiry</small>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Settings --}}
    <div class="col-lg-4">
        <div class="stockifly-card mb-3">
            <div class="card-header-stockifly"><i class="fa-solid fa-sliders me-1"></i> Settings</div>

            <div class="mt-2">
                <label class="form-label-sm">Promotion Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select form-select-sm" required>
                    <option value="accommodation" @selected(old('type', $promotion?->type) == 'accommodation')>🏨 Accommodation</option>
                    <option value="flights"       @selected(old('type', $promotion?->type) == 'flights')>✈️ Flights</option>
                    <option value="activities"    @selected(old('type', $promotion?->type) == 'activities')>🎯 Activities</option>
                    <option value="destination"   @selected(old('type', $promotion?->type) == 'destination')>🗺️ Destination</option>
                    <option value="general"       @selected(old('type', $promotion?->type) == 'general')>📢 General</option>
                </select>
            </div>

            <div class="mt-2">
                <label class="form-label-sm">Target Property Type</label>
                <input type="text" name="target_type" class="form-control form-control-sm"
                    value="{{ old('target_type', $promotion?->target_type) }}" placeholder="hotel, resort, houseboat…">
            </div>

            <div class="mt-2">
                <label class="form-label-sm">Target City</label>
                <input type="text" name="target_city" class="form-control form-control-sm"
                    value="{{ old('target_city', $promotion?->target_city) }}" placeholder="Cox's Bazar, Dhaka…">
            </div>

            <div class="mt-2">
                <label class="form-label-sm">Sort Order</label>
                <input type="number" name="sort_order" class="form-control form-control-sm"
                    value="{{ old('sort_order', $promotion?->sort_order ?? 0) }}" min="0" max="999">
                <small class="text-muted" style="font-size:10px;">Lower = appears first</small>
            </div>

            <div class="mt-3 d-flex flex-column gap-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                        {{ old('is_active', $promotion?->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active" style="font-size:13px;">
                        <i class="fa-solid fa-toggle-on me-1 text-success"></i> Active (visible on site)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured"
                        {{ old('is_featured', $promotion?->is_featured ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured" style="font-size:13px;">
                        <i class="fa-solid fa-star me-1" style="color:#f5c518;"></i> Featured (pinned to top)
                    </label>
                </div>
            </div>
        </div>

        {{-- Quick color presets --}}
        <div class="stockifly-card">
            <div class="card-header-stockifly"><i class="fa-solid fa-swatchbook me-1"></i> Color Presets</div>
            <div class="d-flex flex-wrap gap-2 mt-2">
                @php
                    $presets = [
                        ['name'=>'Blue',    'start'=>'#0ea5e9','end'=>'#0284c7'],
                        ['name'=>'Purple',  'start'=>'#7c3aed','end'=>'#4f46e5'],
                        ['name'=>'Green',   'start'=>'#059669','end'=>'#047857'],
                        ['name'=>'Orange',  'start'=>'#f97316','end'=>'#ea580c'],
                        ['name'=>'Red',     'start'=>'#dc2626','end'=>'#b91c1c'],
                        ['name'=>'Pink',    'start'=>'#db2777','end'=>'#be185d'],
                        ['name'=>'Teal',    'start'=>'#0d9488','end'=>'#0f766e'],
                        ['name'=>'Indigo',  'start'=>'#4f46e5','end'=>'#3730a3'],
                    ];
                @endphp
                @foreach($presets as $p)
                <button type="button"
                    onclick="applyPreset('{{ $p['start'] }}','{{ $p['end'] }}')"
                    title="{{ $p['name'] }}"
                    style="width:36px;height:36px;border-radius:6px;border:2px solid rgba(0,0,0,0.1);cursor:pointer;
                           background:linear-gradient(135deg,{{ $p['start'] }},{{ $p['end'] }});">
                </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Live preview updater
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
    const bg = inputs.bg_color?.value || '#1890ff';
    const bgEnd = inputs.bg_color_end?.value;
    const tc = inputs.text_color?.value || '#fff';
    const bb = inputs.badge_bg?.value || '#f5c518';

    preview.style.background = bgEnd
        ? `linear-gradient(135deg, ${bg}, ${bgEnd})`
        : bg;
    preview.style.color = tc;

    document.getElementById('prev_badge').style.background  = bb;
    document.getElementById('prev_badge').textContent  = inputs.badge_text?.value || 'BADGE';
    document.getElementById('prev_title').textContent  = inputs.title?.value      || 'Title';
    document.getElementById('prev_subtitle').textContent = inputs.subtitle?.value || 'Subtitle';
    document.getElementById('prev_cta').textContent   = inputs.cta_text?.value   || 'Button';
}

Object.values(inputs).forEach(i => i?.addEventListener('input', updatePreview));
updatePreview();

function applyPreset(start, end) {
    inputs.bg_color.value = start;
    inputs.bg_color_end.value = end;
    // Sync text hex inputs
    document.querySelectorAll('[type=text]').forEach((el, i) => {
        if (i === 0) el.value = start;
        if (i === 1) el.value = end;
    });
    updatePreview();
}
</script>
@endpush
