@extends('layouts.vendor')
@section('title', 'Vendor Tour Packages | Vendor Partner Portal')

@php use App\Services\CurrencyService; @endphp

@section('content')
<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h1 class="page-title m-0">
            <i class="fa-solid fa-compass me-2 text-primary"></i> Tour Packages &amp; Experiences
        </h1>
        <a href="{{ route('vendor.packages.create') }}" class="btn btn-primary text-white fw-bold d-inline-flex align-items-center gap-1.5" style="background-color: var(--primary); border-radius: 4px; font-size: 13px; height: 36px; padding: 0 16px; border: none;">
            <i class="fa-solid fa-plus"></i> Create New Package
        </a>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Tour Packages</strong>
    </div>
</div>

<div class="page-content-area">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:4px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Packages Table --}}
    <div class="card p-0 bg-white" style="border: 1px solid #e2e8f0; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3 py-2.5">Package Title</th>
                        <th class="py-2.5">Destination</th>
                        <th class="py-2.5">Duration</th>
                        <th class="py-2.5">Price / Person</th>
                        <th class="py-2.5">Seats Available</th>
                        <th class="py-2.5">Status</th>
                        <th class="pe-3 text-end py-2.5">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $pkg)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2.5">
                                <img src="{{ $pkg->featured_image }}" alt="" style="width: 44px; height: 36px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1;">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 13px;">{{ $pkg->title }}</h6>
                                    <small class="text-muted" style="font-family: monospace; font-size: 11px;">{{ $pkg->slug }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-primary border fw-bold" style="border-radius: 3px; font-size: 11px;">{{ $pkg->destination }}</span></td>
                        <td>{{ $pkg->duration_days }}D / {{ $pkg->duration_nights }}N</td>
                        <td class="fw-bold text-dark">{{ CurrencyService::format($pkg->price_per_person) }}</td>
                        <td><span class="badge bg-light text-dark border" style="font-size: 11px; border-radius: 3px;">{{ $pkg->available_seats }} / {{ $pkg->max_seats }}</span></td>
                        <td>
                            <span class="badge bg-success text-white fw-bold" style="font-size: 11px; border-radius: 3px;">{{ ucfirst($pkg->status) }}</span>
                        </td>
                        <td class="pe-3 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1.5">
                                <a class="btn btn-outline-primary btn-sm p-1 px-2" href="{{ route('packages.show', $pkg->slug) }}" target="_blank" style="font-size: 11.5px; border-radius: 4px;" title="View Live">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                <form action="{{ route('vendor.packages.destroy', $pkg->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to delete this package?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm p-1 px-2" style="font-size: 11.5px; border-radius: 4px;" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-secondary">
                            <i class="fa-solid fa-compass fs-1 opacity-25 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">You haven't listed any tour packages yet</h6>
                            <p class="small text-muted mb-3" style="font-size: 12px;">Add your first tour package to start receiving bookings from travelers across Bangladesh.</p>
                            <a href="{{ route('vendor.packages.create') }}" class="btn btn-sm btn-primary text-white fw-bold px-3" style="background-color: var(--primary); border-radius: 4px;">
                                Create Package Now
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($packages->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $packages->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
