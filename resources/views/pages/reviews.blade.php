@extends('layouts.main', ['activePage' => 'reviews'])

@section('title', 'Guest Reviews & Feedback | Prime Booking')
@section('meta_description', 'View and manage your hotel and trip reviews on Prime Booking.')

@section('content')
<div class="py-4" style="background-color: #f7f9fa; min-height: 88vh; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 16px;">
        <div class="row g-4">
            
            {{-- Left User Account Sidebar Navigation --}}
            <div class="col-lg-3 col-md-4" style="max-width: 270px;">
                <x-user-sidebar activePage="reviews" />
            </div>

            {{-- Right Column: Agoda 1:1 Reviews State --}}
            <div class="col-lg-9 col-md-8">
                
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(isset($userReviews) && count($userReviews) > 0)
                    {{-- ── 1. ACTIVE REVIEWS LIST ── --}}
                    <div class="bg-white border shadow-sm p-4 mb-4" style="border-color: #cbd5e1 !important; border-radius: 20px !important;">
                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                            <div>
                                <h4 class="fw-bold text-dark mb-1" style="font-size: 20px;">{{ __('Your Guest Reviews') }}</h4>
                                <p class="text-muted mb-0" style="font-size: 13.5px;">Manage and view feedback you shared for previous stays.</p>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fw-bold" style="font-size: 13px; border-radius: 8px;">
                                {{ $userReviews->total() }} {{ __('Reviews Written') }}
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @foreach($userReviews as $rev)
                            <div class="p-3 border rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="border-color: #e2e8f0 !important; background: #fafbfc;">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $rev->property?->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80' }}"
                                         alt="{{ $rev->property?->name ?? 'Hotel' }}"
                                         style="width: 70px; height: 55px; object-fit: cover; border-radius: 8px;" class="flex-shrink-0">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 15px;">{{ $rev->property?->name ?? 'Hotel Stay' }}</div>
                                        <div class="text-muted" style="font-size: 12px;"><i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ $rev->property?->city ?? 'Bangladesh' }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge" style="background-color: #1e2430; color: #ffffff; font-size: 11px; padding: 3px 6px;">
                                                <i class="fa-solid fa-star text-warning me-1"></i>{{ number_format($rev->rating ?? 5.0, 1) }} / 5.0
                                            </span>
                                            <span class="text-muted" style="font-size: 11.5px;">Reviewed on {{ $rev->created_at ? $rev->created_at->format('d M Y') : 'Recent' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-md-end w-100 w-md-auto">
                                    @if($rev->property)
                                    <a href="{{ route('property.show', $rev->property->slug ?? $rev->property->id) }}" class="btn btn-outline-primary btn-sm fw-bold px-3 py-1.5" style="border-radius: 8px; font-size: 12.5px;">
                                        View Hotel
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @if($rev->comment || $rev->review_text)
                            <div class="p-3 bg-light rounded-3 text-secondary" style="font-size: 13.5px; line-height: 1.5; margin-top: -8px; border-left: 3px solid #2067e1;">
                                "{{ $rev->comment ?? $rev->review_text }}"
                            </div>
                            @endif
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $userReviews->links() }}
                        </div>
                    </div>

                    {{-- Pending Completed Stays Ready for Review --}}
                    @if(isset($pendingBookings) && count($pendingBookings) > 0)
                    <div class="bg-white border shadow-sm p-4 mb-4" style="border-color: #cbd5e1 !important; border-radius: 20px !important;">
                        <h5 class="fw-bold text-dark mb-3" style="font-size: 17px;"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Rate Your Recent Stays</h5>
                        <div class="d-flex flex-column gap-3">
                            @foreach($pendingBookings as $pb)
                            <div class="p-3 border rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 bg-white" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $pb->property?->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80' }}"
                                         style="width: 65px; height: 50px; object-fit: cover; border-radius: 8px;" class="flex-shrink-0">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 14.5px;">{{ $pb->property?->name ?? 'Hotel Stay' }}</div>
                                        <div class="text-muted" style="font-size: 12px;">Stayed {{ \Carbon\Carbon::parse($pb->check_in)->format('d M') }} - {{ \Carbon\Carbon::parse($pb->check_out)->format('d M Y') }}</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm fw-bold px-3 py-1.5 rounded-pill" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $pb->property_id }}" style="font-size: 12.5px;">
                                    <i class="fa-solid fa-star me-1 text-warning"></i> Write Review
                                </button>
                            </div>

                            {{-- Review Modal --}}
                            <div class="modal fade" id="reviewModal{{ $pb->property_id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <form action="{{ route('hotels.review.store', $pb->property_id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-dark">Rate {{ $pb->property?->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-4">
                                                <div class="mb-3 text-center">
                                                    <label class="form-label fw-bold text-muted small text-uppercase">Overall Rating</label>
                                                    <div class="d-flex justify-content-center gap-2 fs-3 text-warning">
                                                        <select name="rating" class="form-select text-center fw-bold mx-auto" style="max-width: 150px;" required>
                                                            <option value="5">⭐⭐⭐⭐⭐ (5.0 Excellent)</option>
                                                            <option value="4">⭐⭐⭐⭐ (4.0 Very Good)</option>
                                                            <option value="3">⭐⭐⭐ (3.0 Good)</option>
                                                            <option value="2">⭐⭐ (2.0 Fair)</option>
                                                            <option value="1">⭐ (1.0 Poor)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold text-dark small">Your Experience Feedback</label>
                                                    <textarea name="comment" class="form-control rounded-3" rows="4" placeholder="How was the room, cleanliness, staff, and location?" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4">Submit Review</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                @else
                    {{-- ── 2. AGODA 1:1 AUTHENTIC EMPTY STATE (Exact Parity with Screenshot) ── --}}
                    <div class="bg-white border shadow-sm text-center" style="border-color: #cbd5e1 !important; border-radius: 20px !important; padding: 75px 24px 85px 24px;">
                        
                        {{-- 100% Exact Agoda Hotel Building & 3 Floating Stars Vector Illustration --}}
                        <div class="mb-4 d-inline-block">
                            <svg viewBox="0 0 160 140" style="width: 140px; height: 120px; overflow: visible;">
                                {{-- 3 Blue Floating Stars on Top (★ ★ ★) --}}
                                <g fill="#1a56db">
                                    {{-- Left Star --}}
                                    <path d="M 40 18 L 43 26 L 51 27 L 45 33 L 47 41 L 40 37 L 33 41 L 35 33 L 29 27 L 37 26 Z" />
                                    {{-- Middle Star (Slightly Higher) --}}
                                    <path d="M 80 8 L 83.5 18 L 94 19 L 86 26 L 88.5 36 L 80 31 L 71.5 36 L 74 26 L 66 19 L 76.5 18 Z" />
                                    {{-- Right Star --}}
                                    <path d="M 120 18 L 123 26 L 131 27 L 125 33 L 127 41 L 120 37 L 113 41 L 115 33 L 109 27 L 117 26 Z" />
                                </g>

                                {{-- Main Hotel Building Base (Royal Blue Block) --}}
                                <rect x="20" y="48" width="120" height="85" fill="#3b82f6" />
                                
                                {{-- Central Tower / Facade with Pitched Roof --}}
                                <path d="M 52 48 L 80 28 L 108 48 Z" fill="#1d4ed8" />
                                <rect x="52" y="48" width="56" height="85" fill="#1d4ed8" />

                                {{-- Windows on Left Wing (2 Square White Windows) --}}
                                <rect x="28" y="70" width="16" height="16" fill="#ffffff" />

                                {{-- Windows on Right Wing (2 Square White Windows) --}}
                                <rect x="116" y="70" width="16" height="16" fill="#ffffff" />

                                {{-- Center Upper Window (Arched 4-pane window) --}}
                                <path d="M 70 54 Q 80 44 90 54 L 90 70 L 70 70 Z" fill="#ffffff" />
                                <line x1="80" y1="48" x2="80" y2="70" stroke="#1d4ed8" stroke-width="2" />
                                <line x1="70" y1="60" x2="90" y2="60" stroke="#1d4ed8" stroke-width="2" />

                                {{-- Center Entrance Doorway (Arched White Doorway) --}}
                                <path d="M 70 102 Q 80 90 90 102 L 90 133 L 70 133 Z" fill="#ffffff" />
                            </svg>
                        </div>

                        {{-- Main Exact Heading --}}
                        <h3 class="fw-bold mb-2" style="font-size: 21px; color: #2d2d2d; letter-spacing: -0.2px;">
                            Nothing to review yet. Let’s change that!
                        </h3>

                        {{-- Exact Subheading --}}
                        <p class="text-secondary mb-4" style="font-size: 15px; color: #737373 !important; max-width: 480px; margin-left: auto; margin-right: auto; line-height: 1.5;">
                            The world awaits. Book a trip now.
                        </p>

                        {{-- Agoda Authentic Pill Button --}}
                        <div>
                            <a href="{{ route('search.index') }}" class="btn text-white fw-bold shadow-sm"
                               style="background-color: #2067e1; border-radius: 28px; padding: 11px 40px; font-size: 15px; border: none; transition: transform 0.15s ease, background-color 0.15s ease;"
                               onmouseover="this.style.backgroundColor='#1a56db'; this.style.transform='translateY(-1px)';"
                               onmouseout="this.style.backgroundColor='#2067e1'; this.style.transform='translateY(0)';">
                                Get Started
                            </a>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
</div>
@endsection
