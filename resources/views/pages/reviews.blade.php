@extends('layouts.main', ['activePage' => 'reviews'])

@section('title', 'Guest Reviews & Ratings | Prime Aviation')

@section('content')
<div class="py-4" style="background-color: #f4f6fa; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        <div class="row g-4">
            
            <!-- Left White Sidebar Navigation -->
            <div class="col-lg-3 col-md-4" style="max-width: 260px;">
                <x-user-sidebar activePage="reviews" />
            </div>

            <!-- Right Column: Reviews & Feedbacks -->
            <div class="col-lg-9 col-md-8">
                <div class="card border shadow-xs p-4" style="border-color: #cbd5e1 !important; border-radius: 18px !important; background-color: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 18px;">{{ __('Your Guest Reviews') }}</h5>
                        <span class="badge bg-light text-dark border px-3 py-1.5 fw-bold" style="font-size: 12px;">
                            {{ isset($userReviews) ? $userReviews->total() : 0 }} Reviews Written
                        </span>
                    </div>

                    @if(isset($userReviews) && count($userReviews) > 0)
                        <div class="d-flex flex-column gap-3">
                            @foreach($userReviews as $review)
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <strong class="text-dark" style="font-size: 15px;">{{ $review->property?->name ?? 'Hotel Stay' }}</strong>
                                            <span class="badge bg-warning text-dark fw-bold" style="font-size: 11px;">
                                                <i class="fa-solid fa-star me-1"></i> {{ number_format($review->rating ?? 5.0, 1) }}
                                            </span>
                                        </div>
                                        <small class="text-secondary">{{ $review->created_at ? $review->created_at->format('d M Y') : '' }}</small>
                                    </div>
                                    <p class="text-secondary small mb-0">{{ $review->comment ?? $review->review_text }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $userReviews->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 28px;">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 16px;">Share your stay experiences</h6>
                            <p class="text-secondary small mb-4" style="max-width: 440px; margin: 0 auto;">After completing a hotel or transfer booking, submit your review to help fellow travelers!</p>
                            <a href="{{ route('booking.history') }}" class="btn text-white fw-bold rounded-pill px-4 py-2" style="background-color: #2067e1; font-size: 14px;">
                                {{ __('View Completed Bookings') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
