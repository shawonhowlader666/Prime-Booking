@extends('layouts.admin')
@section('title', 'Property Inventory | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Inventory</span>
        <span class="sep">-</span><strong style="color:#333;">Properties & Listings</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Property Inventory &amp; Hotel Listings</h1>
        <a href="{{ route('admin.properties.create') }}" class="btn-add-primary">
            <i class="fa-solid fa-plus me-1"></i> Add New Listing
        </a>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="page-filters-bar">
    <form method="GET" action="{{ route('admin.properties.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label">City / Region</label>
                <select name="city" class="form-select" onchange="this.form.submit()">
                    <option value="">All Cities</option>
                    <option>Cox's Bazar</option>
                    <option>Dhaka</option>
                    <option>Sylhet</option>
                    <option>Sajek</option>
                    <option>Sundarban</option>
                    <option>Kuakata</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label">Property Type</label>
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="all">All Types</option>
                    <option value="hotel">Hotel &amp; Resort</option>
                    <option value="houseboat">Ship &amp; Houseboat</option>
                    <option value="homestay">Eco Cottage</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label">Listing Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="all">All Properties</option>
                    <option value="active">Active / Listed</option>
                    <option value="inactive">Draft / Unlisted</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label">Search Listings</label>
                <div style="display:flex;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name…" style="border-radius:6px 0 0 6px; border-right:none;">
                    <button class="btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>All Hotel, Ship &amp; Cottage Inventory Items</h6>
            <span style="font-size:12px; color:#8c8c8c;">
                {{ isset($properties) ? ($properties->total() ?? count($properties)) : 0 }} Properties
            </span>
        </div>

        <x-table-toolbar tableId="inventoryTable" exportName="properties" searchPlaceholder="Search hotel, ship..." />

        <div style="overflow-x:auto;">
            <table class="table-stockifly" id="inventoryTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>Property Image &amp; Title</th>
                        <th>Category</th>
                        <th>City / Location</th>
                        <th>Base Price/Night</th>
                        <th>Rating</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($properties ?? [] as $p)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <img src="{{ $p->primary_image ?? 'https://placehold.co/50x38/1890ff/white?text=Hotel' }}"
                                     style="width:50px; height:38px; object-fit:cover; border-radius:5px; border:1px solid #e8e8e8;" alt="">
                                <div>
                                    <strong style="font-size:13px; color:#1e293b; display:block;">{{ $p->name }}</strong>
                                    <span style="font-size:10.5px; color:#8c8c8c;">ID: #PROP-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge-gateway">{{ strtoupper($p->type ?? 'HOTEL') }}</span></td>
                        <td><strong style="font-size:12.5px; color:#334155;"><i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> {{ $p->city ?? 'Bangladesh' }}</strong></td>
                        <td><strong style="color:var(--primary); font-size:13px;">BDT {{ number_format($p->price_per_night ?? 0) }}</strong></td>
                        <td><span style="color:#ff9f43; font-size:12px;">{{ str_repeat('★', $p->star_rating ?? 5) }}</span></td>
                        <td>
                            @if($p->is_featured ?? false)
                                <span class="badge-status confirmed">Featured</span>
                            @else
                                <span style="font-size:11px; color:#8c8c8c;">Normal</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status {{ ($p->status ?? 'active') == 'active' ? 'active' : 'pending' }}">
                                {{ ($p->status ?? 'active') == 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.properties.edit', $p->id) }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Listing
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.rooms.index', $p->id) }}">
                                            <i class="fa-solid fa-bed text-success me-2"></i> Manage Rooms
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.properties.toggle-status', $p->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-secondary">
                                                <i class="fa-solid {{ ($p->status ?? 'active') == 'active' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-secondary' }} me-2"></i> 
                                                {{ ($p->status ?? 'active') == 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('hotels.show', $p->id) }}" target="_blank">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-info me-2"></i> View Live Site
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.properties.destroy', $p->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this listing permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Property
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:40px; color:#8c8c8c;">
                            <i class="fa-solid fa-hotel" style="font-size:32px; color:#d9d9d9; display:block; margin-bottom:10px;"></i>
                            <strong style="display:block; font-size:14px; color:#1e293b; margin-bottom:6px;">No Properties Found</strong>
                            <span style="font-size:12.5px;">Click "Add New Listing" above to onboard your first hotel, resort, or ship.</span><br>
                            <a href="{{ route('admin.properties.create') }}" class="btn-add-primary" style="margin-top:12px; display:inline-flex;">
                                <i class="fa-solid fa-plus"></i> Add First Property
                            </a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :items="$properties" :perPage="15" />
    </div>

</div>
@endsection

