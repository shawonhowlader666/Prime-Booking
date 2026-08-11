@extends('layouts.admin')
@section('title', 'Deals & Offers — Admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 style="font-size:19px;font-weight:700;margin:0;">Deals & Special Offers</h1>
        <p style="font-size:12px;color:#8c8c8c;margin:0;">Manage time-limited promotions & discounts</p>
    </div>
    <a href="{{ route('admin.deals.create') }}" class="btn-stockifly-primary">
        <i class="fa-solid fa-plus me-1"></i> Add Deal
    </a>
</div>

@if(session('success'))
<div class="admin-alert success">{{ session('success') }}</div>
@endif

<div class="stockifly-card p-0">
    <div class="table-responsive">
        <table class="table table-stockifly mb-0">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Discount</th>
                    <th>Prices</th>
                    <th>Valid Until</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deals as $deal)
                <tr>
                    <td><strong>{{ $deal->sort_order }}</strong></td>
                    <td>
                        @if($deal->image_url)
                        <img src="{{ $deal->image_url }}" alt="{{ $deal->title }}" style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                        @else
                        <div style="width:60px;height:40px;background:#f0f2f5;border-radius:6px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-tag" style="color:#ccc;"></i></div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $deal->title }}</div>
                        <small style="color:#8c8c8c;">{{ $deal->subtitle }}</small>
                    </td>
                    <td><span class="badge bg-info text-dark">{{ ucfirst($deal->type) }}</span></td>
                    <td><span class="badge bg-warning text-dark">{{ $deal->badge_text ?: $deal->discount_pct.'% OFF' }}</span></td>
                    <td>
                        @if($deal->sale_price)
                        <strong class="text-success">৳{{ number_format($deal->sale_price) }}</strong>
                        @if($deal->original_price)
                        <del style="font-size:11px;color:#8c8c8c;">৳{{ number_format($deal->original_price) }}</del>
                        @endif
                        @else
                        —
                        @endif
                    </td>
                    <td>
                        @if($deal->valid_until)
                        <small>{{ $deal->valid_until->format('d M Y, H:i') }}</small>
                        @else
                        <span style="font-size:11px;color:#8c8c8c;">No Expiry</span>
                        @endif
                    </td>
                    <td>
                        @if($deal->is_active)
                        <span class="badge-stockifly success">Active</span>
                        @else
                        <span class="badge-stockifly secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.deals.edit', $deal) }}" class="btn btn-sm btn-outline-primary" style="padding:2px 8px;font-size:11px;">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.deals.destroy', $deal) }}" method="POST" onsubmit="return confirm('Delete this deal?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:2px 8px;font-size:11px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No deals found. Create one!</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deals->hasPages())
    <div class="px-3 py-2">{{ $deals->links() }}</div>
    @endif
</div>
@endsection
