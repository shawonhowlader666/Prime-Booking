@extends('layouts.main', ['activePage' => 'vendor'])

@section('title', 'Guest Reviews & Feedback Replies | Vendor Partner')

@section('content')
<div class="py-4" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size: 24px;">
                    <i class="fa-solid fa-star text-warning me-2"></i> {{ __('Guest Reviews & Property Ratings') }}
                </h3>
                <p class="text-secondary small mb-0">Read feedback from guests who stayed at your properties and post official vendor responses.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex flex-column gap-3">
            @forelse($reviews as $rev)
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; background: #e0edff; color: #2067e1; border-radius: 50%; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                            {{ strtoupper(substr($rev->user->name ?? $rev->guest_name ?? 'G', 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $rev->user->name ?? $rev->guest_name ?? 'Verified Guest' }}</h6>
                            <small class="text-secondary">{{ $rev->property->name ?? 'Property' }} ({{ $rev->property->city ?? 'BD' }}) &bull; {{ \Carbon\Carbon::parse($rev->created_at)->format('d M Y') }}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary text-white fw-bold px-2.5 py-1 rounded-3" style="font-size: 13px; background-color: #2067e1 !important;">
                            {{ number_format((float)($rev->rating ?? $rev->rating_score ?? 9.0), 1) }} / 10
                        </div>
                    </div>
                </div>

                <p class="text-dark mb-3" style="font-size: 14.5px; line-height: 1.5;">
                    "{{ $rev->comment ?? $rev->review_text ?? 'Great stay and hospitality!' }}"
                </p>

                {{-- Official Vendor Response Box --}}
                @if($rev->vendor_reply)
                <div class="p-3 rounded-3 bg-light border-start border-4 border-primary mt-2">
                    <small class="fw-bold text-primary d-block mb-1"><i class="fa-solid fa-reply me-1"></i> Official Response from Property Management:</small>
                    <p class="small text-secondary mb-0">{{ $rev->vendor_reply }}</p>
                </div>
                @else
                <form action="{{ route('vendor.reviews.reply', $rev->id) }}" method="POST" class="mt-2">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="vendor_reply" class="form-control form-control-sm rounded-start-3" placeholder="Write an official vendor reply to this guest..." required>
                        <button type="submit" class="btn btn-sm text-white fw-bold px-3 rounded-end-3" style="background-color: #2067e1;">
                            POST REPLY
                        </button>
                    </div>
                </form>
                @endif
            </div>
            @empty
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                <i class="fa-regular fa-comment-dots display-3 text-secondary opacity-25 d-block mb-3"></i>
                <h5 class="fw-bold text-dark">No Guest Reviews Received Yet</h5>
                <p class="text-secondary small mb-0">Guest reviews for your properties will appear here after guests complete their stays.</p>
            </div>
            @endforelse
        </div>

        @if($reviews->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $reviews->links() }}
        </div>
        @endif

    </div>
</div>
@endsection
