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
            <span style="font-size:12.5px; color:#64748b;">Manage main homepage title, tagline, banner slides, and background imagery</span>
        </div>
        <div>
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

    <div style="max-width:920px; margin:0 auto;">
        <form id="heroForm" action="{{ route('admin.content.hero.update') }}" method="POST">
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
                        <i class="fa-solid fa-images text-primary me-2"></i> Active Slider Banners (3 Banner Slides)
                    </h6>
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold" style="font-size:11px; padding:4px 8px; border-radius:4px;">
                        <i class="fa-solid fa-circle-dot me-1"></i> Live Homepage Carousel
                    </span>
                </div>
                <div class="card-body" style="padding:20px;">
                    @foreach([
                        ['Slide 1: Cox\'s Bazar Sea Beach Resort', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200', 'Up to 25% Off Luxury Hotels in Cox\'s Bazar'],
                        ['Slide 2: Sundarban Houseboat Cruise', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200', 'Explore Sundarbans Mangrove — MV Zabin Ship'],
                        ['Slide 3: Sajek Valley Cloud Cottage', 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200', 'Experience Clouds in Sajek Valley Heights'],
                    ] as $idx => $s)
                    <div class="p-3 mb-3 rounded" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <strong style="font-size:13.5px; color:#0f172a;"><i class="fa-solid fa-sliders text-primary me-1.5"></i> {{ $s[0] }}</strong>
                            <span class="badge bg-success text-white fw-bold" style="font-size:10.5px; padding:3px 8px; border-radius:4px;">Active Slide</span>
                        </div>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-2 text-center">
                                <img src="{{ $s[1] }}" alt="Slide Image" class="img-fluid rounded border shadow-sm" style="height:56px; width:100%; object-fit:cover; border-radius:4px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:11.5px; font-weight:600; color:#475569; margin-bottom:4px;">Banner Image URL</label>
                                <input type="text" name="slide_image[]" class="form-control" value="{{ $s[1] }}" style="font-size:12.5px; height:36px; border-radius:4px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size:11.5px; font-weight:600; color:#475569; margin-bottom:4px;">Banner Offer Badge Text</label>
                                <input type="text" name="slide_badge[]" class="form-control" value="{{ $s[2] }}" style="font-size:12.5px; height:36px; border-radius:4px;">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn-add-primary" style="font-size:13px; height:38px; padding:0 24px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                    Save Hero Settings <i class="fa-solid fa-check ms-1"></i>
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
