@extends('layouts.admin')
@section('title', 'Room Types: ' . $property->name . ' | Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.properties.index') }}">Inventory</a>
        <span class="sep">-</span><a href="{{ route('admin.properties.edit', $property->id) }}">{{ Str::limit($property->name, 28) }}</a>
        <span class="sep">-</span><strong style="color:#333;">Room Types</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Room Types — {{ $property->name }}</h1>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('admin.rooms.create', $property->id) }}" class="btn-add-primary">
                <i class="fa-solid fa-plus"></i> Add Room Type
            </a>
            <a href="{{ route('admin.properties.edit', $property->id) }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959;">
                <i class="fa-solid fa-arrow-left"></i> Back to Property
            </a>
        </div>
    </div>
</div>

{{-- CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Property quick info --}}
    <div class="data-table-card mb-3" style="padding:16px;">
        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
            <img src="{{ $property->primary_image ?? 'https://placehold.co/70x52/1890ff/white?text=Hotel' }}"
                 style="width:70px; height:52px; border-radius:6px; object-fit:cover; border:1px solid #e8e8e8;">
            <div>
                <strong style="font-size:14px; color:#1e293b;">{{ $property->name }}</strong>
                <span style="display:block; font-size:12px; color:#8c8c8c;">
                    {{ $property->city }} &bull; {{ $property->type }} &bull;
                    {{ str_repeat('★', $property->star_rating ?? 0) }} &bull;
                    BDT {{ number_format($property->price_per_night) }}/night base
                </span>
            </div>
            <div style="margin-left:auto; text-align:right;">
                <span style="font-size:22px; font-weight:700; color:var(--primary);">{{ $property->rooms->count() }}</span>
                <span style="display:block; font-size:11px; color:#8c8c8c;">Room Types</span>
            </div>
        </div>
    </div>

    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>All Room Types for this Property</h6>
            <span style="font-size:12px; color:#8c8c8c;">{{ $property->rooms->count() }} room types configured</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
                <thead>
                    <tr>
                        <th>Room Name</th>
                        <th>Bed Type</th>
                        <th>Size</th>
                        <th>Guests</th>
                        <th>Price/Night</th>
                        <th>Total Rooms</th>
                        <th>Breakfast</th>
                        <th>Cancellation</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($property->rooms as $room)
                    <tr>
                        <td>
                            <strong style="font-size:13px; color:#1e293b;">{{ $room->name }}</strong>
                            @if(!empty($room->facilities))
                                <span style="display:block; font-size:10.5px; color:#8c8c8c; margin-top:2px;">
                                    {{ count($room->facilities) }} facilities configured
                                </span>
                            @endif
                        </td>
                        <td style="font-size:12.5px;">{{ $room->bed_type ?? '1 King Bed' }}</td>
                        <td style="font-size:12.5px;">{{ $room->room_size_sqm ? $room->room_size_sqm . ' m²' : '—' }}</td>
                        <td style="font-size:12.5px;">
                            {{ $room->max_adults ?? 2 }} Adults
                            @if($room->max_children)+ {{ $room->max_children }} Child@endif
                        </td>
                        <td><strong style="color:var(--primary); font-size:13px;">BDT {{ number_format($room->price_per_night) }}</strong></td>
                        <td style="font-size:12.5px; font-weight:600;">{{ $room->total_rooms ?? 10 }} rooms</td>
                        <td>
                            @if($room->breakfast_included)
                                <span class="badge-status confirmed"><i class="fa-solid fa-mug-hot"></i> Included</span>
                            @else
                                <span style="font-size:11px; color:#8c8c8c;">Not included</span>
                            @endif
                        </td>
                        <td>
                            @if($room->free_cancellation)
                                <span class="badge-status active"><i class="fa-solid fa-check"></i> Free</span>
                            @else
                                <span class="badge-status pending">Non-refund</span>
                            @endif
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="{{ route('admin.rooms.edit', [$property->id, $room->id]) }}" class="btn-table-action primary">
                                Edit <i class="fa-solid fa-pen ms-1"></i>
                            </a>
                            <form action="{{ route('admin.rooms.destroy', [$property->id, $room->id]) }}" method="POST" style="display:inline;"
                                onsubmit="return confirm('Delete room type &quot;{{ $room->name }}&quot;?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-table-action danger" style="margin-left:4px;">
                                    Delete <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center; padding:40px; color:#8c8c8c;">
                            <i class="fa-solid fa-bed" style="font-size:32px; color:#d9d9d9; display:block; margin-bottom:10px;"></i>
                            <strong style="display:block; font-size:14px; color:#1e293b; margin-bottom:6px;">No Room Types Yet</strong>
                            <span style="font-size:12px;">Add room types to enable hotel-style booking with multiple room options.</span><br>
                            <a href="{{ route('admin.rooms.create', $property->id) }}" class="btn-add-primary" style="margin-top:12px; display:inline-flex;">
                                <i class="fa-solid fa-plus"></i> Add First Room Type
                            </a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
