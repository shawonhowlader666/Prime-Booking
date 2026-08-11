@extends('layouts.main', ['activePage' => 'vendor'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'Vendor Promotions & Coupons | Prime Booking Partner')

@section('content')
<div class="py-4" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom gap-3">
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size: 24px;">
                    <i class="fa-solid fa-tags me-2 text-danger"></i> {{ __('Vendor Promo Codes & Discount Offers') }}
                </h3>
                <p class="text-secondary small mb-0">Create promotional discount codes for your hotels and resort stays in Bangladesh.</p>
            </div>
            <a href="{{ route('vendor.promotions.create') }}" class="btn text-white fw-bold rounded-pill px-4 py-2" style="background-color: #2067e1;">
                <i class="fa-solid fa-plus me-1"></i> {{ __('Create Promo Code') }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4">Promo Code</th>
                            <th>Discount Value</th>
                            <th>Min Spend</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $c)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-danger-subtle text-danger fw-bold border border-danger-subtle px-3 py-2" style="font-family: monospace; font-size: 14px; letter-spacing: 1px;">
                                    {{ $c->code }}
                                </span>
                            </td>
                            <td class="fw-bold text-dark">
                                @if($c->type === 'percent')
                                    {{ (int)$c->value }}% OFF
                                @else
                                    {{ CurrencyService::format($c->value) }} OFF
                                @endif
                            </td>
                            <td>{{ CurrencyService::format($c->min_spend ?? 0) }}</td>
                            <td><i class="fa-regular fa-calendar-xmark text-secondary me-1"></i> {{ \Carbon\Carbon::parse($c->expires_at)->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $c->is_active ? 'success' : 'secondary' }} text-white fw-bold">
                                    {{ $c->is_active ? 'Active' : 'Expired' }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('vendor.promotions.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this promo code?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Delete Code">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-secondary">
                                <i class="fa-solid fa-ticket display-4 opacity-25 d-block mb-3"></i>
                                <h6 class="fw-bold text-dark">No active promo codes created</h6>
                                <p class="small mb-3">Create coupon codes like EID2026 or SUMMER15 to boost your resort bookings.</p>
                                <a href="{{ route('vendor.promotions.create') }}" class="btn btn-sm text-white fw-bold rounded-pill px-4" style="background-color: #2067e1;">
                                    Create Promo Code Now
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
</div>
@endsection
