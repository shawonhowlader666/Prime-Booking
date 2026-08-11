@extends('layouts.admin')
@section('title', 'Homepage Hero Slider Control | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>CMS Content</span>
        <span class="sep">-</span><strong style="color:#333;">Hero Banner Slider</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Homepage Hero Slider &amp; Banner Manager</h1>
        <button class="btn-add-primary" onclick="document.getElementById('heroForm').submit()">
            <i class="fa-solid fa-check"></i> Save Slider Settings
        </button>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    <div style="max-width:860px; margin:0 auto;">
        <form id="heroForm" action="{{ route('admin.content.hero.update') }}" method="POST">
            @csrf

            {{-- Main Hero Text --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-heading me-1"></i> Main Hero Tagline &amp; Subtitle
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Hero Title / Main Heading <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="hero_title" class="form-control" value="Discover Bangladesh — Hotels, Resorts &amp; Luxury Cruises" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Hero Subtitle / Description</label>
                        <textarea name="hero_subtitle" class="form-control" rows="2">Book top-rated hotels in Cox's Bazar, Sajek, Sylhet and Sundarban luxury ship cruises at guaranteed lowest rates with instant bKash/Nagad confirmation.</textarea>
                    </div>
                </div>
            </div>

            {{-- Slider Banners --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-images me-1"></i> Active Slider Banners (3 Slides)
                </div>

                @foreach([
                    ['Slide 1: Cox\'s Bazar Sea Beach Resort', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200', 'Up to 25% Off Luxury Hotels in Cox\'s Bazar'],
                    ['Slide 2: Sundarban Houseboat Cruise', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200', 'Explore Sundarbans Mangrove — MV Zabin Ship'],
                    ['Slide 3: Sajek Valley Cloud Cottage', 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200', 'Experience Clouds in Sajek Valley Heights'],
                ] as $idx => $s)
                <div style="padding:14px; background:#fafafa; border:1px solid #f0f0f0; border-radius:6px; margin-bottom:12px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                        <strong style="font-size:13px; color:#1e293b;">{{ $s[0] }}</strong>
                        <span class="badge-status active">Active Slide</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-7">
                            <label class="form-label">Banner Image URL</label>
                            <input type="text" name="slide_image[]" class="form-control" value="{{ $s[1] }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Banner Badge Text</label>
                            <input type="text" name="slide_badge[]" class="form-control" value="{{ $s[2] }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="btn-add-primary" style="padding:8px 28px;">
                    Save Hero Settings <i class="fa-solid fa-check ms-1"></i>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

