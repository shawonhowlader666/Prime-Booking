@extends('layouts.main', ['activePage' => 'hotels'])

@section('title', 'Compare Hotels & Stays | PRIME BOOKING')
@section('meta_description', 'Compare prices, star ratings, amenities, and guest reviews for top hotels in Bangladesh side-by-side.')

@section('content')
<div class="py-4" style="background: #f8fafc; min-height: 80vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 16px;">

        {{-- Header Breadcrumb & Title --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 12.5px;">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('search.index') }}" class="text-decoration-none text-muted">Hotels</a></li>
                        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Compare Stays</li>
                    </ol>
                </nav>
                <h4 class="fw-bold text-dark mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <i class="fa-solid fa-code-compare text-primary me-2"></i>Side-by-Side Property Comparison
                </h4>
                <small class="text-muted">Compare prices, amenities, and policies to choose the perfect stay for your trip.</small>
            </div>
            <a href="{{ route('search.index') }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3">
                <i class="fa-solid fa-magnifying-glass me-1"></i> Search More Hotels
            </a>
        </div>

        {{-- Comparison Table Matrix --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle text-center" style="min-width: 800px; border-color: #e2e8f0;">
                    
                    {{-- 1. Property Card Headers --}}
                    <thead>
                        <tr class="bg-light">
                            <th class="p-3 text-start align-top" style="width: 220px; min-width: 200px; background: #f1f5f9;">
                                <div class="fw-bold text-secondary text-uppercase" style="font-size: 11.5px; letter-spacing: 0.5px;">Properties ({{ $properties->count() }})</div>
                                <small class="text-muted d-block mt-1">Comparing Key Differences</small>
                            </th>
                            @foreach($properties as $prop)
                            <th class="p-3 align-top" style="width: calc((100% - 220px) / {{ max(1, $properties->count()) }});">
                                <div class="card border-0 shadow-none text-start p-0">
                                    <div class="position-relative rounded-3 overflow-hidden mb-2" style="height: 140px; background: #0f172a;">
                                        <img src="{{ $prop->primary_image ?: ($prop->images[0] ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=600&q=80') }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $prop->name }}">
                                        <span class="badge bg-primary position-absolute top-0 start-0 m-2 fw-bold" style="font-size: 10px;">{{ ucfirst($prop->type) }}</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $prop->name }}" style="font-size: 14px;">{{ $prop->name }}</h6>
                                    <small class="text-muted d-block mb-2 text-truncate" style="font-size: 11.5px;">
                                        <i class="fa-solid fa-location-dot text-primary me-1"></i>{{ $prop->city }}
                                    </small>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-primary fw-bold" style="font-size: 11px;">{{ number_format((float)($prop->rating_score ?? 8.5), 1) }}</span>
                                            <small class="text-muted" style="font-size: 11px;">({{ $prop->total_reviews ?: 12 }} reviews)</small>
                                        </div>
                                        <div class="fw-bold text-danger fs-6">৳{{ number_format((float)$prop->price_per_night) }}<small class="text-muted fw-normal" style="font-size: 10px;">/nt</small></div>
                                    </div>
                                    <div class="d-grid gap-1 mt-2">
                                        <a href="{{ route('booking.form', $prop->id) }}" class="btn btn-primary btn-sm fw-bold rounded-pill" style="font-size: 12px;">
                                            Book Stay
                                        </a>
                                        <a href="{{ route('hotels.show', $prop->id) }}" class="btn btn-outline-secondary btn-sm fw-semibold rounded-pill" style="font-size: 11.5px;">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        {{-- Price per Night --}}
                        <tr>
                            <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Price per Night</td>
                            @foreach($properties as $prop)
                            <td class="fw-bold text-dark fs-6">
                                ৳{{ number_format((float)$prop->price_per_night) }}
                                @if($prop->original_price && $prop->original_price > $prop->price_per_night)
                                <small class="text-muted text-decoration-line-through d-block fw-normal" style="font-size: 11px;">৳{{ number_format((float)$prop->original_price) }}</small>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- Star Rating --}}
                        <tr>
                            <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Star Category</td>
                            @foreach($properties as $prop)
                            <td>
                                @for($i = 1; $i <= ($prop->star_rating ?? 3); $i++)
                                <i class="fa-solid fa-star text-warning" style="font-size: 12px;"></i>
                                @endfor
                                <span class="d-block small text-muted mt-0.5" style="font-size: 11px;">{{ $prop->star_rating ?? 3 }}-Star Property</span>
                            </td>
                            @endforeach
                        </tr>

                        {{-- Cancellation Policy --}}
                        <tr>
                            <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Cancellation Policy</td>
                            @foreach($properties as $prop)
                            <td>
                                @if($prop->free_cancellation)
                                <span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-1" style="font-size: 11px;">
                                    <i class="fa-solid fa-check me-1"></i>Free Cancellation
                                </span>
                                @else
                                <span class="text-secondary small" style="font-size: 11.5px;">Standard Hotel Policy</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- Payment Policy --}}
                        <tr>
                            <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Pay at Hotel</td>
                            @foreach($properties as $prop)
                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis fw-bold px-2 py-1" style="font-size: 11px;">
                                    <i class="fa-solid fa-money-bill-wave me-1"></i>Available
                                </span>
                            </td>
                            @endforeach
                        </tr>

                        {{-- Room Types Count --}}
                        <tr>
                            <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Room Types</td>
                            @foreach($properties as $prop)
                            <td class="text-dark fw-semibold" style="font-size: 12.5px;">
                                {{ $prop->rooms ? $prop->rooms->count() : 1 }} Available Options
                            </td>
                            @endforeach
                        </tr>

                        {{-- Nearest Landmark / Address --}}
                        <tr>
                            <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Address & Area</td>
                            @foreach($properties as $prop)
                            <td class="text-secondary small px-3 text-start" style="font-size: 12px;">
                                {{ $prop->address ?: ($prop->city . ', Bangladesh') }}
                                @if($prop->nearest_landmark)
                                <div class="text-primary mt-1 fw-medium" style="font-size: 11px;"><i class="fa-solid fa-location-pin me-1"></i>{{ $prop->nearest_landmark }}</div>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- Amenities Checklist Rows --}}
                        <tr class="table-secondary">
                            <td colspan="{{ $properties->count() + 1 }}" class="text-start fw-bold text-dark py-2 px-3 text-uppercase" style="font-size: 11.5px; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-list-check me-1.5 text-primary"></i> Popular Facilities & Amenities
                            </td>
                        </tr>

                        @foreach($standardAmenities as $amenityName)
                        <tr>
                            <td class="text-start text-dark fw-medium bg-light px-3" style="font-size: 12.5px;">
                                {{ $amenityName }}
                            </td>
                            @foreach($properties as $prop)
                            @php
                                $propAmenities = is_array($prop->amenities) ? $prop->amenities : (json_decode((string)$prop->amenities, true) ?: []);
                                $hasAmenity = false;
                                foreach($propAmenities as $am) {
                                    if (stripos($am, $amenityName) !== false || stripos($amenityName, $am) !== false) {
                                        $hasAmenity = true;
                                        break;
                                    }
                                }
                                // Fallback: default common amenities on active properties
                                if (!$hasAmenity && in_array($amenityName, ['Free Wi-Fi', 'Air conditioning', 'Free parking'])) {
                                    $hasAmenity = true;
                                }
                            @endphp
                            <td>
                                @if($hasAmenity)
                                <i class="fa-solid fa-circle-check text-success fs-6" title="Included"></i>
                                @else
                                <i class="fa-solid fa-circle-xmark text-muted fs-6" style="opacity: 0.4;" title="Not listed"></i>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach

                        {{-- Bottom Booking Action Row --}}
                        <tr class="bg-light">
                            <td class="text-start fw-bold text-dark px-3" style="font-size: 13px;">Action</td>
                            @foreach($properties as $prop)
                            <td class="p-3">
                                <a href="{{ route('booking.form', $prop->id) }}" class="btn btn-primary btn-sm fw-bold w-100 rounded-pill py-2 shadow-xs" style="font-size: 13px;">
                                    Book Now — ৳{{ number_format((float)$prop->price_per_night) }}
                                </a>
                            </td>
                            @endforeach
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
