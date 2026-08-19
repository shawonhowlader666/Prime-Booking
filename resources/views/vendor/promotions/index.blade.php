@extends('layouts.vendor')
@section('title', 'Vendor Promotions & Coupons | Vendor Partner Portal')

@php use App\Services\CurrencyService; @endphp

@section('content')
<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1 class="page-title m-0">
                <i class="fa-solid fa-tags me-2 text-primary"></i> Promo Codes &amp; Discount Offers
            </h1>
        </div>
        <a href="{{ route('vendor.promotions.create') }}" class="btn btn-primary text-white fw-bold d-inline-flex align-items-center gap-1.5" style="background-color: var(--primary); border-radius: 4px; font-size: 13px; height: 36px; padding: 0 16px; border: none;">
            <i class="fa-solid fa-plus"></i> Create Promo Code
        </a>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Promotions &amp; Coupons</strong>
    </div>
</div>

<div class="page-content-area">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:4px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card p-0 bg-white" style="border: 1px solid #e2e8f0; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th class="ps-3 py-2.5">Promo Code</th>
                        <th class="py-2.5">Discount Value</th>
                        <th class="py-2.5">Min Spend</th>
                        <th class="py-2.5">Expiry Date</th>
                        <th class="py-2.5">Status</th>
                        <th class="pe-3 text-end py-2.5">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $c)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-light text-primary fw-bold border px-2.5 py-1.5" style="font-family: monospace; font-size: 13px; letter-spacing: 1px; border-radius: 4px;">
                                {{ $c->code }}
                            </span>
                        </td>
                        <td class="fw-bold text-dark">
                            @if(($c->type ?? '') === 'percent' || ($c->discount_type ?? '') === 'percentage')
                                {{ (int)($c->value ?? $c->amount) }}% OFF
                            @else
                                {{ CurrencyService::format($c->value ?? $c->amount) }} OFF
                            @endif
                        </td>
                        <td>{{ CurrencyService::format($c->min_spend ?? 0) }}</td>
                        <td><i class="fa-regular fa-calendar text-secondary me-1"></i> {{ \Carbon\Carbon::parse($c->expires_at)->format('d M Y') }}</td>
                        <td>
                            @php $isActive = $c->is_active ?? ($c->status === 'active'); @endphp
                            <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }} text-white fw-bold" style="border-radius: 3px; font-size: 11px;">
                                {{ $isActive ? 'Active' : 'Expired' }}
                            </span>
                        </td>
                        <td class="pe-3 text-end">
                            <form action="{{ route('vendor.promotions.destroy', $c->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Delete this promo code?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm p-1 px-2" style="font-size: 11.5px; border-radius: 4px;" title="Delete">
                                    <i class="fa-solid fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-secondary">
                            <i class="fa-solid fa-ticket fs-1 opacity-25 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">No active promo codes created</h6>
                            <p class="small text-muted mb-3" style="font-size: 12px;">Create coupon codes like EID2026 or SUMMER15 to boost your bookings.</p>
                            <a href="{{ route('vendor.promotions.create') }}" class="btn btn-sm btn-primary text-white fw-bold px-3" style="background-color: var(--primary); border-radius: 4px;">
                                Create Promo Code
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $coupons->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
