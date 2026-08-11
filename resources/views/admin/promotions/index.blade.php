@extends('layouts.admin')

@section('title', 'Promotions Manager — Admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 style="font-size:19px;font-weight:700;margin:0;">Promotions Manager</h1>
        <p style="font-size:12px;color:#8c8c8c;margin:2px 0 0;">Control all homepage banners — Accommodation, Flights, Activities</p>
    </div>
    <a href="{{ route('admin.promotions.create') }}" class="btn-stockifly-primary">
        <i class="fa-solid fa-plus me-1"></i> New Promotion
    </a>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="admin-alert success"><i class="fa-solid fa-circle-check me-1"></i>{{ session('success') }}</div>
@endif

{{-- KPI Stats --}}
<div class="row g-2 mb-3">
    <div class="col-6 col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Total</div>
            <div class="kpi-value">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-card">
            <div class="kpi-label" style="color:#28c76f;">Active</div>
            <div class="kpi-value" style="color:#28c76f;">{{ $stats['active'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Accommodation</div>
            <div class="kpi-value">{{ $stats['accommodation'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Flights</div>
            <div class="kpi-value">{{ $stats['flights'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Activities</div>
            <div class="kpi-value">{{ $stats['activities'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-card">
            <div class="kpi-label" style="color:#ff9f43;">Vendor</div>
            <div class="kpi-value" style="color:#ff9f43;">{{ $stats['vendor_promos'] }}</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="stockifly-card mb-3">
    <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
        <div>
            <label class="form-label-sm">Type</label>
            <select name="type" class="form-select form-select-sm" style="min-width:140px;">
                <option value="">All Types</option>
                <option value="accommodation" @selected(request('type')=='accommodation')>🏨 Accommodation</option>
                <option value="flights"       @selected(request('type')=='flights')>✈️ Flights</option>
                <option value="activities"    @selected(request('type')=='activities')>🎯 Activities</option>
                <option value="destination"   @selected(request('type')=='destination')>🗺️ Destination</option>
                <option value="general"       @selected(request('type')=='general')>📢 General</option>
            </select>
        </div>
        <div>
            <label class="form-label-sm">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="1" @selected(request('status')==='1')>Active</option>
                <option value="0" @selected(request('status')==='0')>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn-stockifly-primary btn-sm">Filter</button>
        <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </form>
</div>

{{-- Promotions Table --}}
<div class="stockifly-card p-0">
    <div class="table-responsive">
        <table class="table table-stockifly mb-0">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Preview</th>
                    <th>Title / Subtitle</th>
                    <th>Type</th>
                    <th>CTA</th>
                    <th>Status</th>
                    <th>Schedule</th>
                    <th>Sort</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promotions as $promo)
                <tr>
                    <td>{{ $promo->id }}</td>
                    <td>
                        {{-- Color preview card --}}
                        <div style="
                            background: {{ $promo->bg_color_end ? "linear-gradient(135deg, {$promo->bg_color}, {$promo->bg_color_end})" : $promo->bg_color }};
                            color: {{ $promo->text_color }};
                            padding: 6px 10px;
                            border-radius: 6px;
                            font-size: 11px;
                            font-weight: 700;
                            min-width: 90px;
                            text-align: center;
                        ">
                            @if($promo->badge_text)
                            <div style="font-size:9px;background:{{ $promo->badge_bg }};color:#000;border-radius:3px;padding:1px 4px;margin-bottom:3px;display:inline-block;">
                                {{ $promo->badge_text }}
                            </div><br>
                            @endif
                            {{ Str::limit($promo->title, 18) }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $promo->title }}</div>
                        @if($promo->subtitle)
                        <div style="font-size:11px;color:#8c8c8c;">{{ $promo->subtitle }}</div>
                        @endif
                        @if($promo->vendor_id)
                        <span class="badge-stockifly warning" style="font-size:10px;">Vendor: {{ $promo->vendor?->name }}</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $typeIcons = ['accommodation'=>'🏨','flights'=>'✈️','activities'=>'🎯','destination'=>'🗺️','general'=>'📢'];
                        @endphp
                        <span style="font-size:12px;">{{ $typeIcons[$promo->type] ?? '📢' }} {{ ucfirst($promo->type) }}</span>
                    </td>
                    <td>
                        @if($promo->cta_text)
                        <div style="font-size:12px;font-weight:600;">{{ $promo->cta_text }}</div>
                        @endif
                        @if($promo->cta_link)
                        <div style="font-size:11px;color:#1890ff;">{{ Str::limit($promo->cta_link, 30) }}</div>
                        @endif
                    </td>
                    <td>
                        @if($promo->is_live)
                            <span class="badge-stockifly success">🟢 Live</span>
                        @elseif($promo->status_label === 'Scheduled')
                            <span class="badge-stockifly warning">⏰ Scheduled</span>
                        @elseif($promo->status_label === 'Expired')
                            <span class="badge-stockifly danger">⌛ Expired</span>
                        @else
                            <span class="badge-stockifly secondary">⚪ Inactive</span>
                        @endif
                    </td>
                    <td style="font-size:11px;">
                        @if($promo->starts_at)
                        <div>From: {{ $promo->starts_at->format('d M Y') }}</div>
                        @endif
                        @if($promo->ends_at)
                        <div>To: {{ $promo->ends_at->format('d M Y') }}</div>
                        @endif
                        @if(!$promo->starts_at && !$promo->ends_at)
                        <span style="color:#8c8c8c;">Always on</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:12px;font-weight:600;">{{ $promo->sort_order }}</span>
                        @if($promo->is_featured)
                        <span title="Featured"><i class="fa-solid fa-star" style="color:#f5c518;font-size:11px;"></i></span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('admin.promotions.edit', $promo) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit" style="padding:2px 8px;font-size:11px;">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.promotions.toggle', $promo) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $promo->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                    title="{{ $promo->is_active ? 'Deactivate' : 'Activate' }}" style="padding:2px 8px;font-size:11px;">
                                    <i class="fa-solid {{ $promo->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.promotions.destroy', $promo) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Delete this promotion?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:2px 8px;font-size:11px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4" style="color:#8c8c8c;">No promotions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($promotions->hasPages())
    <div class="px-3 py-2">{{ $promotions->links() }}</div>
    @endif
</div>
@endsection
