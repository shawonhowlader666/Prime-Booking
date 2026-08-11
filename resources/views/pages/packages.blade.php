@extends('layouts.main', ['activePage' => 'packages'])

@section('title', 'Exclusive Tour & Holiday Packages | Prime Booking')

@section('content')
{{-- Hero Subheader --}}
{{-- Hero Subheader with Rich Gradient & Right 3D Travel Graphic --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 20px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <!-- Decorative Floating Glowing Pattern Circles -->
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 id="bdPkgHeaderTitle" class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-plane-departure text-warning me-2" style="font-size: 20px;"></i> {{ __('Tour & Holiday Packages') }}
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500; opacity: 0.95;">
                {{ __('Discover handpicked international & domestic vacation packages with flights, luxury hotels & guided tours') }}
            </p>
        </div>

        <!-- Right Side Attractive Travel Graphic/Illustration -->
        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 28px;">✈️</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">Best Deals</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">Up to 35% OFF</div>
                </div>
            </div>
            <div style="font-size: 42px; transform: rotate(12deg); filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));">
                🧳
            </div>
        </div>
    </div>
</div>

<div class="py-4" style="max-width: 1240px; margin: 0 auto; padding-left: 15px; padding-right: 15px;">

    {{-- Filter Category Pills (Agoda style) --}}
    <div class="d-flex align-items-center gap-2 mb-4 overflow-auto pb-2" style="white-space: nowrap;">
        <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" style="background-color: #2067e1;">All Packages</button>
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold bg-white text-dark border">Popular Trips</button>
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold bg-white text-dark border">Honeymoon</button>
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold bg-white text-dark border">Umrah &amp; Hajj</button>
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold bg-white text-dark border">Domestic BD</button>
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold bg-white text-dark border">Visa &amp; Flights</button>
    </div>

    {{-- Package Cards Grid with Staggered Animations & Rich Pattern Design --}}
    <div class="row g-4">
        @forelse($packages as $index => $pkg)
            <div class="col-md-6 col-lg-4 scroll-reveal reveal-rotate-up delay-{{ ($index % 5) + 1 }}">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden card-hover position-relative bg-white" 
                     style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important;">
                    
                    {{-- Top Gradient Line Decorator --}}
                    <div style="height: 3px; background: linear-gradient(90deg, #2067e1 0%, #8b5cf6 50%, #ec4899 100%);"></div>

                    <div class="position-relative" style="height: 175px;">
                        @if($pkg->image_url)
                        <img src="{{ $pkg->image_url }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $pkg->title }}">
                        @else
                        <div class="w-100 h-100 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center fs-1">✈️</div>
                        @endif
                        @if($pkg->badge)
                        <span class="badge text-white position-absolute top-0 start-0 m-2.5 px-2.5 py-1 fw-bold shadow-xs" 
                              style="font-size: 10px; border-radius: 12px; background-color: #ef4444; letter-spacing: 0.5px;">
                            {{ $pkg->badge }}
                        </span>
                        @endif
                        <span class="badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 end-0 m-2.5 px-2 py-1" style="font-size: 10px; border-radius: 6px;">
                            <i class="fa-solid fa-clock me-1 text-warning"></i> {{ $pkg->days }}
                        </span>
                    </div>

                    <div class="card-body p-3 d-flex flex-column justify-content-between position-relative">
                        <!-- Subtle Background Pattern Overlay -->
                        <div style="position: absolute; right: 8px; bottom: 8px; opacity: 0.035; font-size: 90px; pointer-events: none; color: #000;">
                            <i class="fa-solid fa-compass"></i>
                        </div>

                        <div class="position-relative" style="z-index: 2;">
                            <h6 class="fw-bold mb-2" style="font-size: 15px; line-height: 1.3; color: #0f172a !important;">
                                {{ $pkg->title }}
                            </h6>

                            @if(!empty($pkg->includes) && is_array($pkg->includes))
                            <div class="mb-2">
                                <small class="text-uppercase fw-bold text-muted d-block mb-1.5" style="font-size: 10px; letter-spacing: 0.5px;">
                                    Inclusions:
                                </small>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($pkg->includes as $inc)
                                        <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 10px; font-weight: 500;">
                                            <i class="fa-solid fa-check text-success me-1"></i> {{ $inc }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="pt-2 mt-2 border-top d-flex align-items-center justify-content-between position-relative" style="z-index: 2;">
                            <div>
                                <div class="fw-bold text-primary" style="font-size: 17px; color: #2067e1 !important; line-height: 1.1;">
                                    BDT {{ number_format($pkg->price) }}
                                </div>
                                <small class="text-muted" style="font-size: 10px;">per person</small>
                            </div>

                            <a href="{{ route('contact') }}?package={{ urlencode($pkg->title) }}#inquiry" class="btn text-white fw-bold px-3 py-1.5 shadow-2xs" style="background: linear-gradient(135deg, #2067e1 0%, #1d4ed8 100%); border-radius: 8px; font-size: 11px;">
                                BOOK NOW <i class="fa-solid fa-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No packages currently available. Please check back soon!</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
