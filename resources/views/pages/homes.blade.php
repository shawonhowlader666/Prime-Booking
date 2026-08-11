@extends('layouts.main', ['activePage' => 'homes'])

@section('title', 'Vacation Homes & Luxury Apartments | Prime Aviation')

@section('content')
{{-- Hero Subheader --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 20px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-house-user text-warning me-2" style="font-size: 20px;"></i> {{ __('Vacation Homes & Serviced Apartments') }}
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500; opacity: 0.95;">
                {{ __('Book entire beach villas, luxury service apartments, and private holiday homes.') }}
            </p>
        </div>

        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 26px;">🏡</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">Private Homes</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">Entire Apartments</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-4" style="background-color: #f4f6fa; min-height: 80vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <!-- Search Redirect Banner -->
        <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white mb-4" style="border-radius: 18px !important;">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1.5 mb-2" style="font-size: 12px;">
                        <i class="fa-solid fa-key me-1"></i> Feel at Home Anywhere
                    </span>
                    <h3 class="fw-bold text-dark mb-2" style="font-size: 24px;">Looking for private space &amp; kitchen amenities?</h3>
                    <p class="text-secondary mb-0" style="font-size: 14px;">Browse verified vacation rentals, beachside bungalows in Cox's Bazar, and tea-estate cottages in Sreemangal.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('search.index', ['property_type' => 'apartment']) }}" class="btn text-white fw-bold px-4 py-2.5 rounded-pill shadow-sm" style="background-color: #2067e1; font-size: 14.5px;">
                        {{ __('Explore All Homes & Villas') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Dynamic Vacation Homes Grid --}}
        <h5 class="fw-bold text-dark mb-3">Featured Private Holiday Homes</h5>
        <div class="row g-3">
            @forelse($vacationHomes ?? [] as $home)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden h-100">
                        <img src="{{ $home->primary_image ?: 'https://images.unsplash.com/photo-1587061949409-02df41d5e562?auto=format&fit=crop&w=600&q=80' }}"
                             alt="{{ $home->name }}" style="height: 180px; object-fit: cover;">
                        <div class="p-3">
                            <span class="badge bg-warning text-dark fw-bold mb-1">{{ $home->type }}</span>
                            <h6 class="fw-bold text-dark mb-1">{{ $home->name }}</h6>
                            <p class="text-secondary small mb-2"><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $home->city }}</p>
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <span class="fw-bold text-success">BDT {{ number_format($home->price_per_night) }}/night</span>
                                <a href="{{ route('hotels.show', $home->id) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">View Stay</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 bg-white rounded-4 border">
                    <p class="text-secondary mb-0">No vacation homes found in database.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
