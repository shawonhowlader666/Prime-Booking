@extends('layouts.main', ['activePage' => 'services'])

@section('title', 'Search Hotels & Stays in ' . ($destination ?: 'Bangladesh') . ' | Prime Aviation')

@section('content')
{{-- 1. Agoda Official "Just a moment!" Loading Modal Overlay (Image 1 Parity) --}}
<div id="agodaSearchLoadingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 999999; align-items: center; justify-content: center;">
    <div class="card border-0 shadow-lg rounded-4 text-center p-4 p-md-5 bg-white" style="width: 440px; max-width: 90vw; border-radius: 18px !important;">
        {{-- Animated Mascot Icon --}}
        <div class="mx-auto mb-3 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
            <i class="fa-solid fa-parachute-box text-primary" style="font-size: 36px; color: #2067e1 !important;"></i>
        </div>
        <h4 class="fw-bold mb-2" style="color: #6366f1; font-size: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">Just a moment!</h4>
        <p class="text-secondary small mb-0" style="font-size: 13.5px; line-height: 1.5;">We're finding great stays for your dates and destination in Bangladesh.</p>
        <div class="spinner-border text-primary spinner-border-sm mt-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

{{-- 2. Agoda Compact Subheader Search Bar (Image 2 Parity) --}}
<div style="background-color: #1d2b45; padding: 12px 0; border-bottom: 1px solid #334155;">
    <div style="max-width: 1320px; margin: 0 auto; padding: 0 12px;">
        <form action="{{ route('search.index') }}" method="GET" class="row g-2 align-items-center" id="searchHeaderForm" onsubmit="showLoadingModal();">
            <input type="hidden" name="search_type" value="{{ $searchType ?? 'hotel' }}">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 text-secondary"><i class="fa-solid fa-magnifying-glass" style="color: #2067e1;"></i></span>
                    <input type="text" name="destination" class="form-control border-0 rounded-end-3" value="{{ $destination }}" placeholder="Enter destination or property" style="height: 42px; font-weight: 600; font-size: 14px;">
                </div>
            </div>
            <div class="col-md-2">
                <input type="date" name="check_in" class="form-control rounded-3" value="{{ $checkIn }}" style="height: 42px; font-weight: 500; font-size: 14px;">
            </div>
            <div class="col-md-2">
                <input type="date" name="check_out" class="form-control rounded-3" value="{{ $checkOut }}" style="height: 42px; font-weight: 500; font-size: 14px;">
            </div>
            <div class="col-md-3">
                <select name="guests" class="form-select rounded-3" style="height: 42px; font-weight: 500; font-size: 14px;">
                    <option value="1" {{ $guests == 1 ? 'selected' : '' }}>1 Adult, 0 Children (1 room)</option>
                    <option value="2" {{ $guests == 2 ? 'selected' : '' }}>2 Adults, 0 Children (1 room)</option>
                    <option value="4" {{ $guests == 4 ? 'selected' : '' }}>3 Adults, 1 Child (2 rooms)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn text-white w-100 fw-bold rounded-3 shadow-xs" style="background-color: #2067e1; height: 42px; font-size: 14px; letter-spacing: 0.5px;">
                    SEARCH
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 3. Agoda Coupon Deals Strip Banner (Image 2 Parity) --}}
<div style="background: #fff0f3; border-bottom: 1px solid #fecdd3; padding: 10px 0;">
    <div class="d-flex align-items-center justify-content-between" style="max-width: 1320px; margin: 0 auto; padding: 0 12px; font-size: 13px;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger rounded-pill px-2 py-1"><i class="fa-solid fa-tag"></i></span>
            <strong class="text-dark">Looking for instant coupons?</strong>
            <span class="text-secondary d-none d-md-inline">Check out our Coupons &amp; Deals page for today's BDT discounts</span>
        </div>
        <a href="{{ route('deals') }}" class="btn btn-sm btn-outline-danger bg-white rounded-pill px-3 py-1 fw-bold" style="font-size: 12px;">See all coupons</a>
    </div>
</div>

{{-- 4. Main Search Results Layout --}}
<div style="max-width: 1320px; margin: 0 auto; padding: 24px 12px;">
    <div class="row g-4">
        
        {{-- Left Filter Sidebar --}}
        <div class="col-lg-3">
            @include('components.search.filter-sidebar')
        </div>

        {{-- Right Results Feed --}}
        <div class="col-lg-9">
            
            {{-- Results Header & Sort Dropdown (Image 2 Parity) --}}
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold mb-0 text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">
                        {{ $searchResults['total_count'] }} properties in {{ $destination ?: 'Bangladesh' }}
                    </h4>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted fw-semibold mb-0 d-none d-sm-inline">Sort by:</label>
                    <select class="form-select form-select-sm rounded-3 fw-semibold" style="width: 180px; font-size: 13px; border-color: #cbd5e1;">
                        <option selected>Best match</option>
                        <option>Lowest price first</option>
                        <option>Highest guest rating</option>
                        <option>Distance from city center</option>
                    </select>
                </div>
            </div>

            {{-- Property Cards Grid --}}
            <div class="d-flex flex-column gap-3">
                @forelse($searchResults['merged_results'] as $item)
                    @include('components.search.property-card', ['item' => $item])
                @empty
                    <div class="card border-0 shadow-xs p-5 text-center bg-white rounded-4">
                        <i class="fa-solid fa-hotel text-muted mb-3" style="font-size: 42px;"></i>
                        <h5 class="fw-bold text-dark">No properties found matching your search.</h5>
                        <p class="text-secondary small">Try adjusting your budget range or search for "Dhaka", "Cox's Bazar", "Sundarban" or "Sajek".</p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>
</div>

<script>
function showLoadingModal() {
    const modal = document.getElementById('agodaSearchLoadingModal');
    if (modal) modal.style.display = 'flex';
}
</script>
@endsection
