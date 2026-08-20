<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->name }} — Official Property Brochure | PRIME BOOKING</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            font-size: 13.5px;
            padding: 30px 15px;
        }
        .brochure-card {
            max-width: 880px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        @media print {
            .no-print, header, footer { display: none !important; }
            body { background: #fff !important; padding: 0 !important; }
            .brochure-card { box-shadow: none !important; border: 1px solid #ddd !important; border-radius: 0 !important; }
        }
    </style>
</head>
<body>

    <div class="no-print d-flex align-items-center justify-content-between mb-4 mx-auto" style="max-width: 880px;">
        <a href="{{ route('hotels.show', $property->id) }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-bold">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Hotel Page
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4 rounded-pill fw-bold">
            <i class="fa-solid fa-print me-1"></i> Print / Save PDF Brochure
        </button>
    </div>

    <div class="brochure-card">
        
        {{-- Header Cover Image & Property Title --}}
        <div class="position-relative bg-dark" style="height: 280px;">
            <img src="{{ $property->primary_image }}" alt="{{ $property->name }}" class="w-100 h-100 object-fit-cover opacity-85">
            <div class="position-absolute bottom-0 start-0 w-100 p-4 p-md-5 text-white" style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%);">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary text-white fw-bold px-2 py-0.5 rounded" style="font-size: 11px;">PRIME PREFERRED</span>
                    <span class="text-warning">
                        @for($s = 0; $s < ($property->star_rating ?? 4); $s++) ★ @endfor
                    </span>
                </div>
                <h2 class="fw-bold mb-1 text-white" style="letter-spacing: -0.5px;">{{ $property->name }}</h2>
                <p class="mb-0 text-white-50 small">
                    <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $property->address ?: ($property->city . ', Bangladesh') }}
                </p>
            </div>
        </div>

        {{-- Overview and Key Metrics Bar --}}
        <div class="p-4 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="text-white fw-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: #2067e1; border-radius: 10px; font-size: 17px;">
                    {{ number_format((float)($property->rating_score ?? 8.8), 1) }}
                </div>
                <div>
                    <strong class="d-block text-dark" style="font-size: 14px;">Excellent Guest Rating</strong>
                    <small class="text-secondary">{{ number_format($property->total_reviews ?? 120) }} verified guest reviews</small>
                </div>
            </div>
            <div class="text-md-end">
                <small class="text-secondary d-block" style="font-size: 11px;">Nightly rates starting from</small>
                <h4 class="fw-bold text-primary mb-0 font-monospace">{{ \App\Services\CurrencyService::format($property->price_per_night) }}</h4>
            </div>
        </div>

        {{-- Property Description & Amenities --}}
        <div class="p-4 p-md-5 border-bottom">
            <h5 class="fw-bold text-dark mb-2">About the Property</h5>
            <p class="text-secondary" style="line-height: 1.6;">
                {{ $property->description ?: 'Located in prime destination, this property offers world-class hospitality, deluxe furnished rooms, high-speed WiFi, complimentary breakfast, and seamless booking experience via PRIME BOOKING.' }}
            </p>

            <h6 class="fw-bold text-dark mt-4 mb-3 small text-uppercase">Top Facilities &amp; Amenities:</h6>
            <div class="row g-2 text-secondary small">
                @php
                    $amenities = is_array($property->amenities) ? $property->amenities : ['Free High-Speed Wi-Fi', 'Swimming Pool', 'Complimentary Breakfast', 'Airport Shuttle', '24/7 Front Desk', 'Daily Housekeeping', 'Air Conditioning', 'Flat-screen TV'];
                @endphp
                @foreach($amenities as $amenity)
                <div class="col-md-4 col-6">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-2 bg-light border">
                        <i class="fa-solid fa-check text-primary"></i>
                        <span class="text-dark fw-semibold text-truncate">{{ $amenity }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Available Room Types --}}
        @if($property->rooms && $property->rooms->count() > 0)
        <div class="p-4 p-md-5 border-bottom">
            <h5 class="fw-bold text-dark mb-3">Room Types &amp; Accommodations</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="bg-light text-secondary small">
                        <tr>
                            <th>Room Name</th>
                            <th>Bed Setup</th>
                            <th>Max Guests</th>
                            <th class="text-end">Nightly Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($property->rooms as $rm)
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $rm->name }}</strong>
                                <small class="text-secondary d-block">{{ $rm->formatted_size }} • {{ $rm->view_type ?: 'Scenic view' }}</small>
                            </td>
                            <td class="small">{{ $rm->bed_type ?: '1 King Bed' }}</td>
                            <td class="small"><i class="fa-solid fa-user me-1 text-secondary"></i> Up to {{ $rm->max_adults }} Adults</td>
                            <td class="text-end font-monospace fw-bold text-primary">{{ \App\Services\CurrencyService::format($rm->price_per_night ?: $property->price_per_night) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Policies & Reservation Info --}}
        <div class="p-4 p-md-5 bg-light d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <strong class="d-block text-dark mb-1">Check-in: {{ $property->checkin_time ?: '14:00' }} • Check-out: {{ $property->checkout_time ?: '12:00' }}</strong>
                <small class="text-secondary">Official travel agency catalog generated by <strong>PRIME BOOKING</strong> • primebooking.com.bd</small>
            </div>
            <div>
                <a href="{{ route('booking.form', $property->id) }}" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
                    Book Online Now →
                </a>
            </div>
        </div>

    </div>

</body>
</html>
