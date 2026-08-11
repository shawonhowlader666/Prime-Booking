@extends('layouts.main', ['activePage' => 'packages'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'Tour Packages & Day Trips | Prime Booking')

@section('content')
{{-- Hero Section --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0284c7 100%); padding: 48px 0; color: #fff; position: relative; overflow: hidden;">
    <div style="position: absolute; top: -50px; right: 10%; width: 250px; height: 250px; background: rgba(56, 189, 248, 0.2); filter: blur(50px); border-radius: 50%;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2 rounded-pill" style="font-size: 12px; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-compass me-1"></i> EXCLUSIVE BANGLADESH TOURS
                </span>
                <h1 class="fw-bold mb-2 text-white" style="font-size: 34px; letter-spacing: -0.5px;">
                    Explore Bangladesh Tour Packages & Holiday Trips
                </h1>
                <p class="mb-4 text-white-50" style="font-size: 15px;">
                    Book guided day trips, beach holidays, tea garden tours, and mangrove safaris with instant e-ticket confirmation.
                </p>
            </div>
        </div>

        {{-- Search Filter Form --}}
        <div class="card border-0 shadow-lg rounded-4 p-3 bg-white text-dark mt-2" style="border-radius: 16px !important;">
            <form action="{{ route('packages.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-secondary"><i class="fa-solid fa-location-dot text-primary"></i></span>
                        <input type="text" name="destination" class="form-control border-0 bg-light rounded-end-3" value="{{ request('destination') }}" placeholder="Where do you want to go? (Cox's Bazar, Sylhet, Sundarbans...)" style="height: 44px; font-size: 14px; font-weight: 500;">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="sort" class="form-select border-0 bg-light rounded-3" style="height: 44px; font-size: 14px; font-weight: 500;">
                        <option value="featured" @selected(request('sort') == 'featured')>Sort by: Featured First</option>
                        <option value="price_low" @selected(request('sort') == 'price_low')>Price: Low to High</option>
                        <option value="price_high" @selected(request('sort') == 'price_high')>Price: High to Low</option>
                        <option value="duration" @selected(request('sort') == 'duration')>Duration: Longest First</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn text-white w-100 fw-bold rounded-3 shadow-sm" style="background-color: #2067e1; height: 44px; font-size: 14px;">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> SEARCH PACKAGES
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Main Packages Grid --}}
<div class="py-5" style="background-color: #f8fafc; min-height: 70vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="fw-bold mb-0 text-dark" style="font-size: 20px;">
                    Available Tour Packages ({{ $packages->total() }})
                </h4>
                <small class="text-secondary">All packages include verified hotel stays, transport, and guide assistance</small>
            </div>
            @if(request('destination'))
                <a href="{{ route('packages.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                    Clear Filter
                </a>
            @endif
        </div>

        <div class="row g-4">
            @forelse($packages as $pkg)
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 rounded-4 shadow-sm h-100 bg-white overflow-hidden hover-shadow transition">
                    <div class="position-relative" style="height: 210px;">
                        <img src="{{ $pkg->featured_image }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $pkg->title }}">
                        <span class="badge bg-dark bg-opacity-75 text-white position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill fw-semibold" style="font-size: 11px;">
                            <i class="fa-regular fa-clock me-1 text-warning"></i> {{ $pkg->duration_days }} Days / {{ $pkg->duration_nights }} Nights
                        </span>
                        <span class="badge bg-primary text-white position-absolute bottom-0 start-0 m-3 px-2 py-1 rounded-3 fw-bold" style="font-size: 11px;">
                            <i class="fa-solid fa-location-dot me-1"></i> {{ $pkg->destination }}
                        </span>
                    </div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold text-dark mb-2" style="font-size: 16.5px; line-height: 1.35;">
                                <a href="{{ route('packages.show', $pkg->slug) }}" class="text-decoration-none text-dark hover-primary">
                                    {{ $pkg->title }}
                                </a>
                            </h5>

                            {{-- Inclusions Chips --}}
                            @if(!empty($pkg->inclusions))
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                @foreach(array_slice($pkg->inclusions, 0, 3) as $inc)
                                <span class="badge bg-light text-secondary border fw-normal" style="font-size: 11px;">
                                    <i class="fa-solid fa-check text-success me-1"></i>{{ $inc }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-secondary d-block" style="font-size: 11px;">Per Person</small>
                                <span class="fw-bold text-primary fs-5" style="color: #2067e1 !important;">
                                    {{ CurrencyService::format($pkg->price_per_person) }}
                                </span>
                            </div>
                            <a href="{{ route('packages.show', $pkg->slug) }}" class="btn text-white fw-bold rounded-pill px-4" style="background-color: #2067e1; font-size: 13px;">
                                VIEW DETAILS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-xs rounded-4 p-5 bg-white">
                    <i class="fa-solid fa-compass display-3 text-muted opacity-25 mb-3"></i>
                    <h5 class="fw-bold text-dark">No packages found for this destination</h5>
                    <p class="text-secondary small mb-3">Try clearing search filters or search another destination like Cox's Bazar or Sylhet.</p>
                    <div>
                        <a href="{{ route('packages.index') }}" class="btn text-white fw-bold px-4 rounded-pill" style="background-color: #2067e1;">View All Packages</a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        @if($packages->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $packages->appends(request()->query())->links() }}
        </div>
        @endif

    </div>
</div>
@endsection
