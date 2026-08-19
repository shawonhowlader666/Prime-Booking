@extends('layouts.vendor')
@section('title', 'Guest Reviews & Ratings | Vendor Partner Portal')

@section('content')
<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h1 class="page-title m-0">
            <i class="fa-solid fa-star text-warning me-2"></i> Guest Reviews &amp; Property Ratings
        </h1>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Guest Reviews</strong>
    </div>
</div>

<div class="page-content-area">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:4px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex flex-column gap-3">
        @forelse($reviews as $rev)
        <div class="card p-3.5 bg-white" style="border: 1px solid #e2e8f0; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-2.5 gap-2">
                <div class="d-flex align-items-center gap-2.5">
                    <div style="width: 38px; height: 38px; background: #e0edff; color: #1890ff; border-radius: 4px; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                        {{ strtoupper(substr($rev->user->name ?? $rev->guest_name ?? 'G', 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 13.5px;">{{ $rev->user->name ?? $rev->guest_name ?? 'Verified Guest' }}</h6>
                        <small class="text-secondary" style="font-size: 11.5px;">{{ $rev->property->name ?? 'Property' }} ({{ $rev->property->city ?? 'BD' }}) &bull; {{ \Carbon\Carbon::parse($rev->created_at)->format('d M Y') }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge text-white fw-bold px-2.5 py-1" style="font-size: 12px; background-color: #1890ff; border-radius: 4px;">
                        ★ {{ number_format((float)($rev->rating ?? $rev->rating_score ?? 5.0), 1) }} / 5.0
                    </span>
                </div>
            </div>

            <p class="text-dark mb-2.5" style="font-size: 13px; line-height: 1.5;">
                "{{ $rev->comment ?? $rev->review_text ?? 'Great stay and hospitality!' }}"
            </p>

            {{-- Official Vendor Response Box --}}
            @if($rev->vendor_reply)
            <div class="p-2.5 bg-light border-start border-4 border-primary mt-1" style="border-radius: 0 4px 4px 0;">
                <small class="fw-bold text-primary d-block mb-1" style="font-size: 11.5px;"><i class="fa-solid fa-reply me-1"></i> Official Response from Property Management:</small>
                <p class="text-secondary mb-0" style="font-size: 12px;">{{ $rev->vendor_reply }}</p>
            </div>
            @else
            <form action="{{ route('vendor.reviews.reply', $rev->id) }}" method="POST" class="mt-1">
                @csrf
                <div class="input-group">
                    <input type="text" name="vendor_reply" class="form-control form-control-sm" placeholder="Write an official vendor reply to this guest..." required style="border-radius: 4px 0 0 4px; font-size: 12.5px;">
                    <button type="submit" class="btn btn-sm text-white fw-bold px-3" style="background-color: #1890ff; border-radius: 0 4px 4px 0; font-size: 12px;">
                        <i class="fa-solid fa-paper-plane me-1"></i> Post Reply
                    </button>
                </div>
            </form>
            @endif
        </div>
        @empty
        <div class="card p-5 text-center bg-white" style="border: 1px solid #e2e8f0; border-radius: 4px;">
            <i class="fa-solid fa-star-half-stroke text-secondary fs-1 mb-2"></i>
            <h6 class="fw-bold text-dark" style="font-size: 14px;">No reviews yet</h6>
            <p class="text-muted small m-0">When guests complete their stay and review your properties, they will appear here.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
