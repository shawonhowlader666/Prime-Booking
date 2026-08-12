@extends('layouts.main', ['activePage' => 'vendor'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'Vendor Tour Packages | Prime Booking Partner')

@section('content')
<div class="py-4" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        {{-- Vendor Top Navigation Header --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom gap-3">
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size: 24px;">
                    <i class="fa-solid fa-compass me-2 text-primary"></i> {{ __('Vendor Tour Packages & Experiences') }}
                </h3>
                <p class="text-secondary small mb-0">Create, manage, and track your tour package listings and bookings.</p>
            </div>
            <a href="{{ route('vendor.packages.create') }}" class="btn text-white fw-bold rounded-pill px-4 py-2" style="background-color: #2067e1;">
                <i class="fa-solid fa-plus me-1"></i> {{ __('Create New Package') }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Packages Table --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4">Package Title</th>
                            <th>Destination</th>
                            <th>Duration</th>
                            <th>Price / Person</th>
                            <th>Seats Available</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $pkg)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $pkg->featured_image }}" alt="" style="width: 50px; height: 40px; object-fit: cover; border-radius: 8px;">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14px;">{{ $pkg->title }}</h6>
                                        <small class="text-muted" style="font-family: monospace;">{{ $pkg->slug }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-primary-subtle text-primary fw-bold">{{ $pkg->destination }}</span></td>
                            <td>{{ $pkg->duration_days }}D / {{ $pkg->duration_nights }}N</td>
                            <td class="fw-bold text-dark">{{ CurrencyService::format($pkg->price_per_person) }}</td>
                            <td><span class="badge bg-success-subtle text-success">{{ $pkg->available_seats }} / {{ $pkg->max_seats }}</span></td>
                            <td>
                                <span class="badge bg-success text-white fw-bold">{{ ucfirst($pkg->status) }}</span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="dropdown action-gear-dropdown d-inline-block">
                                    <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                        <i class="fa-solid fa-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                        <li>
                                            <a class="dropdown-item py-1.5 px-3" href="{{ route('packages.show', $pkg->slug) }}" target="_blank">
                                                <i class="fa-solid fa-eye text-primary me-2"></i> View Public Tour Page
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="{{ route('vendor.packages.destroy', $pkg->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this package?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                    <i class="fa-solid fa-trash me-2"></i> Delete Tour Package
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
                                <i class="fa-solid fa-compass display-4 opacity-25 d-block mb-3"></i>
                                <h6 class="fw-bold text-dark">You haven't listed any tour packages yet</h6>
                                <p class="small mb-3">Add your first tour package to start receiving bookings from travelers across Bangladesh.</p>
                                <a href="{{ route('vendor.packages.create') }}" class="btn btn-sm text-white fw-bold rounded-pill px-4" style="background-color: #2067e1;">
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
</div>
@endsection
