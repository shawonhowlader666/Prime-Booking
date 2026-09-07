@extends('layouts.app')

@section('title', 'Guest Portfolio & CRM')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Guest Portfolio & CRM</h4>
            <p class="text-muted mb-0">Guest directory, contact profiles, ID records, and booking history.</p>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2">
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by name, phone, email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Guests Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Guest Name</th>
                        <th>Phone & Email</th>
                        <th>ID Document</th>
                        <th>Address</th>
                        <th>Total Stays</th>
                        <th>VIP Status</th>
                        <th>Last Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $g)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                        {{ substr($g->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $g->full_name }}</div>
                                        <small class="text-muted">ID #{{ $g->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="fa-solid fa-phone me-1 text-muted"></i>{{ $g->phone ?? 'N/A' }}</div>
                                <small class="text-muted"><i class="fa-solid fa-envelope me-1 text-muted"></i>{{ $g->email ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $g->id_type }}</span>
                            </td>
                            <td><small class="text-muted">{{ $g->address ?? 'N/A' }}</small></td>
                            <td><span class="badge bg-primary bg-opacity-15 text-primary fw-bold">{{ $g->reservations_count }} Stays</span></td>
                            <td>
                                @if($g->is_vip)
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-crown me-1"></i>VIP Guest</span>
                                @else
                                    <span class="badge bg-light text-muted border">Standard</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $g->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No guest profiles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($guests->hasPages())
            <div class="card-footer bg-transparent py-3">
                {{ $guests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
