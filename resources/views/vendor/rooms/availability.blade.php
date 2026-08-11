@extends('layouts.main', ['activePage' => 'vendor'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'Room Availability & Seasonal Rates Calendar | Vendor Partner')

@section('content')
<div class="py-4" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom gap-3">
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size: 24px;">
                    <i class="fa-solid fa-calendar-days text-primary me-2"></i> {{ __('Room Availability & Rates Calendar') }}
                </h3>
                <p class="text-secondary small mb-0">Set custom weekend/peak-season nightly rates and block dates for maintenance or full bookings.</p>
            </div>
            
            {{-- Property & Room Selector Dropdown --}}
            <form action="{{ route('vendor.availability.index') }}" method="GET" class="d-flex gap-2">
                <select name="room_id" class="form-select fw-bold rounded-pill shadow-xs" onchange="this.form.submit()" style="font-size: 13.5px; border-color: #2067e1;">
                    @foreach($properties as $p)
                        <optgroup label="{{ $p->name }} ({{ $p->city }})">
                            @foreach($p->rooms as $r)
                                <option value="{{ $r->id }}" {{ $selectedRoom && $selectedRoom->id === $r->id ? 'selected' : '' }}>
                                    {{ $r->name }} — Base: {{ CurrencyService::format($r->price_per_night) }}/night
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($selectedRoom)
        <div class="row g-4">
            
            {{-- Left Column (4 cols): Date Range Rate & Block Controller --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-sliders text-primary me-2"></i> Update Rates &amp; Dates
                    </h5>

                    <form action="{{ route('vendor.availability.update-range') }}" method="POST">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Start Date</label>
                            <input type="date" name="start_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">End Date</label>
                            <input type="date" name="end_date" class="form-control rounded-3" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Custom Nightly Rate (BDT ৳)</label>
                            <input type="number" name="price" class="form-control rounded-3" placeholder="Base rate: {{ (int)$selectedRoom->price_per_night }}" step="0.01">
                            <small class="text-muted d-block mt-1">Leave empty to keep standard base price ({{ CurrencyService::format($selectedRoom->price_per_night) }}).</small>
                        </div>

                        <div class="mb-4 form-check form-switch pt-2">
                            <input class="form-check-input" type="checkbox" name="is_blocked" value="1" id="blockRoomCheck">
                            <label class="form-check-input-label fw-bold text-danger ms-2" for="blockRoomCheck">
                                <i class="fa-solid fa-ban me-1"></i> Block Room / Mark as Sold Out
                            </label>
                        </div>

                        <button type="submit" class="btn text-white fw-bold w-100 py-2.5 rounded-pill shadow-xs" style="background-color: #2067e1;">
                            <i class="fa-solid fa-save me-1"></i> SAVE CALENDAR RATES
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right Column (8 cols): 30-Day Interactive Calendar Grid --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">30-Day Rates Grid</h5>
                            <small class="text-secondary">Selected Room: <strong>{{ $selectedRoom->name }}</strong></small>
                        </div>
                        <div class="d-flex align-items-center gap-3 font-mono small">
                            <span class="d-flex align-items-center gap-1"><span style="width: 12px; height: 12px; background: #e0edff; border-radius: 3px; display: inline-block;"></span> Available</span>
                            <span class="d-flex align-items-center gap-1"><span style="width: 12px; height: 12px; background: #fee2e2; border-radius: 3px; display: inline-block;"></span> Sold Out/Blocked</span>
                        </div>
                    </div>

                    {{-- Calendar Grid --}}
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-5 g-2">
                        @for($d = 0; $d < 30; $d++)
                        @php
                            $currentDateObj = $startDate->copy()->addDays($d);
                            $dateStr = $currentDateObj->format('Y-m-d');
                            $record = $availabilities->get($dateStr);
                            $isBlocked = $record ? $record->is_blocked : false;
                            $nightPrice = $record && $record->price ? $record->price : $selectedRoom->price_per_night;
                        @endphp
                        <div class="col">
                            <div class="p-3 border rounded-3 text-center transition position-relative" style="background-color: {{ $isBlocked ? '#fef2f2' : '#f8fafc' }}; border-color: {{ $isBlocked ? '#fca5a5' : '#cbd5e1' }} !important;">
                                <span class="d-block text-secondary small fw-semibold" style="font-size: 11px;">
                                    {{ $currentDateObj->format('D, d M') }}
                                </span>
                                <span class="fw-bold d-block my-1 {{ $isBlocked ? 'text-danger' : 'text-primary' }}" style="font-size: 14px;">
                                    @if($isBlocked)
                                        <i class="fa-solid fa-ban text-danger me-1"></i> SOLD OUT
                                    @else
                                        {{ CurrencyService::format($nightPrice) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

        </div>
        @else
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <i class="fa-solid fa-bed display-3 text-secondary opacity-25 d-block mb-3"></i>
            <h5 class="fw-bold text-dark">No Rooms Registered Yet</h5>
            <p class="text-secondary small mb-4">You need to register at least one room under your property to manage availability calendars.</p>
            <div>
                <a href="{{ route('vendor.properties.create') }}" class="btn text-white fw-bold px-4 py-2 rounded-pill" style="background-color: #2067e1;">
                    Add Property &amp; Rooms Now →
                </a>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
