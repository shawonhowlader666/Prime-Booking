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
                                <div class="dropdown action-gear-dropdown d-inline-block">
                                    <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                        <i class="fa-solid fa-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                        <li>
                                            <form action="{{ route('admin.packages.toggle', $pkg->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-warning">
                                                    <i class="fa-solid {{ $pkg->status === 'active' ? 'fa-toggle-off' : 'fa-toggle-on' }} me-2"></i>
                                                    {{ $pkg->status === 'active' ? 'Deactivate Package' : 'Approve & Make Live' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="{{ route('admin.packages.destroy', $pkg->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to remove this package permanently?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                    <i class="fa-solid fa-trash me-2"></i> Delete Package
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
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
