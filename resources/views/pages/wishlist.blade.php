@extends('layouts.main', ['activePage' => 'wishlist'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'My Saved Wishlist & Favorites | Prime Booking')

@section('content')
<div class="py-5" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div>
                <h2 class="fw-bold mb-1 text-dark" style="font-size: 26px;">
                    <i class="fa-solid fa-heart text-danger me-2"></i> {{ __('My Saved Wishlist & Favorites') }}
                </h2>
                <p class="text-secondary small mb-0">Properties, resorts, and stays you saved for future travel plans.</p>
            </div>
            <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2 rounded-pill fw-bold border border-danger-subtle">
                {{ $wishlists->total() }} Saved Hotel{{ $wishlists->total() === 1 ? '' : 's' }}
            </span>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            @forelse($wishlists as $w)
            @php $item = $w->property; @endphp
            @if($item)
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100 position-relative hover-shadow transition">
                    {{-- Image --}}
                    <div style="height: 220px; position: relative;">
                        <img src="{{ $item->primary_image }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $item->name }}">
                        
                        {{-- Remove Wishlist Heart --}}
                        <form action="{{ route('wishlist.toggle') }}" method="POST" class="position-absolute top-0 end-0 m-3">
                            @csrf
                            <input type="hidden" name="property_id" value="{{ $item->id }}">
                            <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Remove from Wishlist">
                                <i class="fa-solid fa-heart text-danger fs-6"></i>
                            </button>
                        </form>

                        <span class="badge bg-primary text-white position-absolute bottom-0 start-0 m-3 px-3 py-1.5 rounded-pill fw-bold" style="font-size: 11px;">
                            <i class="fa-solid fa-location-dot me-1"></i> {{ $item->city }}
                        </span>
                    </div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 16px;">
                                <a href="{{ route('hotels.show', $item->id) }}" class="text-decoration-none text-dark hover-primary">
                                    {{ $item->name }}
                                </a>
                            </h5>
                            <p class="text-secondary small mb-3"><i class="fa-solid fa-location-arrow me-1"></i>{{ $item->address }}</p>
                        </div>

                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">Per Night</small>
                                <span class="fw-bold text-primary fs-5" style="color: #2067e1 !important;">
                                    {{ CurrencyService::format($item->price_per_night) }}
                                </span>
                            </div>
                            <a href="{{ route('hotels.show', $item->id) }}" class="btn text-white fw-bold rounded-pill px-4" style="background-color: #2067e1; font-size: 13px;">
                                BOOK NOW
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @empty
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-xs rounded-4 p-5 bg-white">
                    <i class="fa-regular fa-heart display-2 text-danger opacity-25 d-block mb-3"></i>
                    <h5 class="fw-bold text-dark">Your Wishlist is Empty</h5>
                    <p class="text-secondary small mb-4">Click the heart icon on any hotel or resort card to save it for quick access later.</p>
                    <div>
                        <a href="{{ route('search.index') }}" class="btn text-white fw-bold px-5 py-2.5 rounded-pill" style="background-color: #2067e1;">
                            Explore Hotels &amp; Resorts →
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        @if($wishlists->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $wishlists->links() }}
        </div>
        @endif

    </div>
</div>
@endsection
