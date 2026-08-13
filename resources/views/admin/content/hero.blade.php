@extends('layouts.admin')
@section('title', 'Homepage Hero Banner Slider | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>CMS Pages</span>
        <span class="sep">-</span><strong style="color:#333;">Hero Banner Slider</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <div>
            <h1 class="page-title m-0">Homepage Hero Slider &amp; Banner Manager</h1>
            <span style="font-size:12.5px; color:#64748b;">Upload banner images from computer/gallery, manage offer badges, and add unlimited slides</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <button type="button" class="btn btn-light border btn-sm fw-bold text-secondary" onclick="addNewSlideCard()" style="height:36px; border-radius:4px; font-size:12.5px;">
                <i class="fa-solid fa-plus text-primary me-1"></i> Add New Slide
            </button>
            <button type="button" class="btn-add-primary" onclick="document.getElementById('heroForm').submit()" style="font-size:13px; height:36px; padding:0 18px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-check"></i> <span>Save Hero Settings</span>
            </button>
        </div>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div style="max-width:960px; margin:0 auto;">
        <form id="heroForm" action="{{ route('admin.content.hero.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Main Hero Text Card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
                <div class="card-header bg-white" style="padding:16px 20px; border-bottom:1px solid #e2e8f0;">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size:14.5px;">
                        <i class="fa-solid fa-heading text-primary me-2"></i> Main Hero Tagline &amp; Search Subtitle
                    </h6>
                </div>
                <div class="card-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Hero Title / Main Heading <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="hero_title" class="form-control" value="{{ $heroSettings['hero_title'] ?? 'Discover Bangladesh — Hotels, Resorts & Luxury Cruises' }}" required style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="mb-0">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Hero Subtitle / Description</label>
                        <textarea name="hero_subtitle" class="form-control" rows="3" style="font-size:13px; border-radius:4px;">{{ $heroSettings['hero_subtitle'] ?? "Book top-rated hotels in Cox's Bazar, Sajek, Sylhet and Sundarban luxury ship cruises at guaranteed lowest rates with instant bKash/Nagad confirmation." }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Active Slider Banners Card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
                <div class="card-header bg-white d-flex align-items-center justify-content-between" style="padding:16px 20px; border-bottom:1px solid #e2e8f0;">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size:14.5px;">
                        <i class="fa-solid fa-images text-primary me-2"></i> Active Slider Banners (Unlimited Upload Support)
                    </h6>
                    <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="addNewSlideCard()" style="font-size:12px; border-radius:4px;">
                        <i class="fa-solid fa-plus me-1"></i> Add Another Slide
                    </button>
                </div>
                <div class="card-body" style="padding:20px;" id="slidesContainer">
                    @php $slidesList = $heroSettings['slides'] ?? []; @endphp
                    @forelse($slidesList as $idx => $s)
                    <div class="slide-card p-3 mb-3 rounded" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; position:relative;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <strong style="font-size:13.5px; color:#0f172a;"><i class="fa-solid fa-sliders text-primary me-1.5"></i> Banner Slide #<span class="slide-num">{{ $idx + 1 }}</span></strong>
                            <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1 px-2" onclick="removeSlideCard(this)" title="Remove Slide" style="font-size:12px;">
                                <i class="fa-solid fa-trash me-1"></i> Remove
                            </button>
                        </div>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-2 text-center">
                                <img src="{{ $s['image'] ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200' }}" alt="Slide Image" class="img-fluid rounded border shadow-sm slide-preview-img" style="height:64px; width:100%; object-fit:cover; border-radius:4px;">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label" style="font-size:11.5px; font-weight:600; color:#475569; margin-bottom:4px;">Upload File from Gallery / Computer</label>
                                <input type="file" name="slide_file[]" class="form-control form-control-sm mb-1" accept="image/*" style="font-size:11.5px; border-radius:4px;" onchange="previewLocalImage(this)">
                                <span style="font-size:10.5px; color:#94a3b8;">OR keep image URL below:</span>
                                <input type="text" name="slide_image[]" class="form-control form-control-sm slide-url-input" value="{{ $s['image'] ?? '' }}" placeholder="https://..." style="font-size:11.5px; height:32px; border-radius:4px;">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label" style="font-size:11.5px; font-weight:600; color:#475569; margin-bottom:4px;">Banner Offer Badge Text</label>
                                <input type="text" name="slide_badge[]" class="form-control" value="{{ $s['badge'] ?? '' }}" placeholder="e.g. Up to 25% Off Luxury Hotels" style="font-size:12.5px; height:36px; border-radius:4px;">
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="slide-card p-3 mb-3 rounded" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <strong style="font-size:13.5px; color:#0f172a;"><i class="fa-solid fa-sliders text-primary me-1.5"></i> Banner Slide #1</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1 px-2" onclick="removeSlideCard(this)" title="Remove Slide" style="font-size:12px;">
                                <i class="fa-solid fa-trash me-1"></i> Remove
                            </button>
                        </div>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-2 text-center">
                                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200" alt="Slide Image" class="img-fluid rounded border shadow-sm slide-preview-img" style="height:64px; width:100%; object-fit:cover; border-radius:4px;">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label" style="font-size:11.5px; font-weight:600; color:#475569; margin-bottom:4px;">Upload File from Gallery / Computer</label>
                                <input type="file" name="slide_file[]" class="form-control form-control-sm mb-1" accept="image/*" style="font-size:11.5px; border-radius:4px;" onchange="previewLocalImage(this)">
                                <span style="font-size:10.5px; color:#94a3b8;">OR keep image URL below:</span>
                                <input type="text" name="slide_image[]" class="form-control form-control-sm slide-url-input" value="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200" placeholder="https://..." style="font-size:11.5px; height:32px; border-radius:4px;">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label" style="font-size:11.5px; font-weight:600; color:#475569; margin-bottom:4px;">Banner Offer Badge Text</label>
                                <input type="text" name="slide_badge[]" class="form-control" value="Up to 25% Off Luxury Hotels in Cox's Bazar" placeholder="e.g. Up to 25% Off Luxury Hotels" style="font-size:12.5px; height:36px; border-radius:4px;">
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <button type="button" class="btn btn-light border fw-bold text-secondary" onclick="addNewSlideCard()" style="font-size:13px; height:38px; border-radius:4px;">
                    <i class="fa-solid fa-plus text-primary me-1"></i> Add Another Slide
                </button>
                <button type="submit" class="btn-add-primary" style="font-size:13px; height:38px; padding:0 24px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                    Save Hero Settings <i class="fa-solid fa-check ms-1"></i>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function addNewSlideCard() {
    const container = document.getElementById('slidesContainer');
    const count = container.querySelectorAll('.slide-card').length + 1;
    
    const div = document.createElement('div');
    div.className = 'slide-card p-3 mb-3 rounded';
    div.style.cssText = 'background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;';
    div.innerHTML = `
        <div class="d-flex align-items-center justify-content-between mb-2">
            <strong style="font-size:13.5px; color:#0f172a;"><i class="fa-solid fa-sliders text-primary me-1.5"></i> Banner Slide #<span class="slide-num">${count}</span></strong>
            <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1 px-2" onclick="removeSlideCard(this)" title="Remove Slide" style="font-size:12px;">
                <i class="fa-solid fa-trash me-1"></i> Remove
            </button>
        </div>
        <div class="row g-3 align-items-center">
            <div class="col-md-2 text-center">
                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200" alt="Slide Image" class="img-fluid rounded border shadow-sm slide-preview-img" style="height:64px; width:100%; object-fit:cover; border-radius:4px;">
            </div>
            <div class="col-md-5">
                <label class="form-label" style="font-size:11.5px; font-weight:600; color:#475569; margin-bottom:4px;">Upload File from Gallery / Computer</label>
                <input type="file" name="slide_file[]" class="form-control form-control-sm mb-1" accept="image/*" style="font-size:11.5px; border-radius:4px;" onchange="previewLocalImage(this)">
                <span style="font-size:10.5px; color:#94a3b8;">OR keep image URL below:</span>
                <input type="text" name="slide_image[]" class="form-control form-control-sm slide-url-input" value="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200" placeholder="https://..." style="font-size:11.5px; height:32px; border-radius:4px;">
            </div>
            <div class="col-md-5">
                <label class="form-label" style="font-size:11.5px; font-weight:600; color:#475569; margin-bottom:4px;">Banner Offer Badge Text</label>
                <input type="text" name="slide_badge[]" class="form-control" value="Exclusive Special Discount Offer" placeholder="e.g. Up to 25% Off Luxury Hotels" style="font-size:12.5px; height:36px; border-radius:4px;">
            </div>
        </div>
    `;
    container.appendChild(div);
}

function removeSlideCard(btn) {
    const card = btn.closest('.slide-card');
    const container = document.getElementById('slidesContainer');
    if (container.querySelectorAll('.slide-card').length > 1) {
        card.remove();
        // re-index slide numbers
        container.querySelectorAll('.slide-card').forEach((c, idx) => {
            const numEl = c.querySelector('.slide-num');
            if (numEl) numEl.textContent = idx + 1;
        });
    } else {
        alert('You must keep at least 1 hero banner slide!');
    }
}

function previewLocalImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const card = input.closest('.slide-card');
        const img = card.querySelector('.slide-preview-img');
        reader.onload = function(e) {
            if (img) img.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection
