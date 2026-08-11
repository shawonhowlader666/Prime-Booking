@extends('layouts.admin')
@section('title', 'Featured Destinations Control | Prime Aviation Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>CMS Content</span>
        <span class="sep">-</span><strong style="color:#333;">Featured Destinations</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Featured Tourist Destinations Manager</h1>
        <button class="btn-add-primary" onclick="document.getElementById('destForm').submit()">
            <i class="fa-solid fa-check"></i> Save Destinations
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

    <form id="destForm" action="{{ route('admin.content.destinations.update') }}" method="POST">
        @csrf
        <div class="row g-3">
            @foreach([
                ['Cox\'s Bazar Sea Beach', '120+ Hotels & Resorts', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=500', 'World\'s longest natural sea beach'],
                ['Sajek Valley Clouds', '45+ Hill Cottages', 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=500', 'Valley of clouds in Rangamati hills'],
                ['Sylhet & Sreemangal', '80+ Tea Estate Resorts', 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=500', 'Land of two leaves and a bud'],
                ['Sundarban Mangrove', '15+ Luxury Houseboats', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=500', 'UNESCO World Heritage Forest & Cruise'],
                ['Kuakata Sunset Beach', '30+ Beachfront Hotels', 'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=500', 'Daughter of the Sea — Sunrise & Sunset'],
                ['Dhaka Capital City', '200+ Luxury Suites', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=500', '5-Star Hospitality & Business Hub'],
            ] as $dest)
            <div class="col-md-4">
                <div class="form-card h-100">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                        <img src="{{ $dest[2] }}" style="width:50px; height:50px; border-radius:8px; object-fit:cover; border:1px solid #e8e8e8;" alt="">
                        <div>
                            <strong style="font-size:14px; color:#1e293b; display:block;">{{ $dest[0] }}</strong>
                            <span style="font-size:11px; color:var(--primary); font-weight:600;">{{ $dest[1] }}</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Destination Title</label>
                        <input type="text" name="dest_title[]" class="form-control" value="{{ $dest[0] }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Image Thumbnail URL</label>
                        <input type="text" name="dest_image[]" class="form-control" value="{{ $dest[2] }}">
                    </div>
                    <div>
                        <label class="form-label">Short Description</label>
                        <input type="text" name="dest_desc[]" class="form-control" value="{{ $dest[3] }}">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div style="display:flex; justify-content:flex-end; margin-top:20px;">
            <button type="submit" class="btn-add-primary" style="padding:8px 28px;">
                Save All Destinations <i class="fa-solid fa-check ms-1"></i>
            </button>
        </div>
    </form>

</div>
@endsection
