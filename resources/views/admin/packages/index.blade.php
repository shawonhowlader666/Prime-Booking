@extends('layouts.main', ['activePage' => 'admin'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'Admin Tour Packages Control | Prime Booking')

@section('content')
<div class="py-4" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size: 22px;">
                    <i class="fa-solid fa-shield-halved text-primary me-2"></i> {{ __('Admin Tour Packages Control Center') }}
                </h3>
                <p class="text-secondary small mb-0">Review, approve, and manage all platform tour packages submitted by vendors and admins.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">Total Packages: {{ $totalCount }}</span>
                <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">Active Live: {{ $activeCount }}</span>
            </div>
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
                            <th class="ps-4">Package</th>
                            <th>Partner / Vendor</th>
                            <th>Destination</th>
                            <th>Duration</th>
                            <th>Price / Person</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Admin Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $pkg)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $pkg->featured_image }}" alt="" style="width: 48px; height: 38px; object-fit: cover; border-radius: 6px;">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14px;">{{ $pkg->title }}</h6>
                                        <small class="text-muted">{{ $pkg->created_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $pkg->vendor?->name ?? 'System Admin' }}</span>
                                <small class="text-muted d-block" style="font-size: 11px;">{{ $pkg->vendor?->email ?? 'admin@primebooking.com' }}</small>
                            </td>
                            <td><span class="badge bg-primary-subtle text-primary fw-bold">{{ $pkg->destination }}</span></td>
                            <td>{{ $pkg->duration_days }}D / {{ $pkg->duration_nights }}N</td>
                            <td class="fw-bold text-dark">{{ CurrencyService::format($pkg->price_per_person) }}</td>
                            <td>
                                <span class="badge bg-{{ $pkg->status === 'active' ? 'success' : 'warning' }} text-white fw-bold">
                                    {{ ucfirst($pkg->status) }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('admin.packages.toggle', $pkg->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $pkg->status === 'active' ? 'outline-warning' : 'outline-success' }} rounded-pill me-1 fw-bold" style="font-size: 12px;">
                                        {{ $pkg->status === 'active' ? 'Deactivate' : 'Approve Live' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.packages.destroy', $pkg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this package permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                No tour packages registered in system.
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
</div>
@endsection
