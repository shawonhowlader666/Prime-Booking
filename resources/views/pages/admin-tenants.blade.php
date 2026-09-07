@extends('layouts.app')

@section('title', 'SaaS Master Tenant Control Center | Prime PMS')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
    <ol class="breadcrumb mb-0 bg-transparent">
        <li class="breadcrumb-item"><a href="{{ url('/dashboard.html') }}" title="home">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/tuning.html') }}">Tuning</a></li>
        <li class="breadcrumb-item active" aria-current="page">SaaS Master Control</li>
    </ol>
</div>

<div class="px-xl-5 px-lg-4 px-3 py-3 page-body">
    <!-- SaaS Top Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1">Total Hotels</h6>
                            <h2 class="fw-bold mb-0 text-primary">{{ number_format($metrics['total_hotels']) }}</h2>
                        </div>
                        <div class="avatar lg rounded-4 bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-hotel fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1">Active Licenses</h6>
                            <h2 class="fw-bold mb-0 text-success">{{ number_format($metrics['active_subscriptions']) }}</h2>
                        </div>
                        <div class="avatar lg rounded-4 bg-success-subtle text-success d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-circle-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1">Free Trials</h6>
                            <h2 class="fw-bold mb-0 text-warning">{{ number_format($metrics['free_trials']) }}</h2>
                        </div>
                        <div class="avatar lg rounded-4 bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-hourglass-half fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small text-uppercase mb-1">Monthly SaaS MRR</h6>
                            <h2 class="fw-bold mb-0 text-info">৳{{ number_format($metrics['monthly_mrr'], 2) }}</h2>
                        </div>
                        <div class="avatar lg rounded-4 bg-info-subtle text-info d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-sack-dollar fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenant Management Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold mb-0">Master Tenant Control Center</h4>
                <p class="text-muted small mb-0">Manage subscriptions, lifecycle, and remote provisioning across 10,000+ properties</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary px-4 py-2 rounded-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddTenant">
                    <i class="fa-solid fa-plus me-1"></i> Onboard New Hotel
                </button>
            </div>
        </div>

        <div class="card-body px-4 pt-0 pb-4">
            <!-- Search & Filter Bar -->
            <form action="{{ route('admin.tenants.index') }}" method="GET" class="row g-2 mb-4">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Search by hotel name, code, email, or city..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">All Subscription Statuses</option>
                        <option value="ACTIVE" {{ ($filters['status'] ?? '') === 'ACTIVE' ? 'selected' : '' }}>Active License</option>
                        <option value="TRIAL" {{ ($filters['status'] ?? '') === 'TRIAL' ? 'selected' : '' }}>Free Trial</option>
                        <option value="EXPIRED" {{ ($filters['status'] ?? '') === 'EXPIRED' ? 'selected' : '' }}>Expired</option>
                        <option value="SUSPENDED" {{ ($filters['status'] ?? '') === 'SUSPENDED' ? 'selected' : '' }}>Suspended</option>
                        <option value="LOCKED" {{ ($filters['status'] ?? '') === 'LOCKED' ? 'selected' : '' }}>Locked Only</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="plan" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">All Plans</option>
                        <option value="STANDARD" {{ ($filters['plan'] ?? '') === 'STANDARD' ? 'selected' : '' }}>Standard (৳1,000/mo)</option>
                        <option value="PREMIUM" {{ ($filters['plan'] ?? '') === 'PREMIUM' ? 'selected' : '' }}>Premium</option>
                        <option value="ENTERPRISE" {{ ($filters['plan'] ?? '') === 'ENTERPRISE' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-light w-100"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Hotel / Property</th>
                            <th>Owner Contact</th>
                            <th>Units / Bookings</th>
                            <th>Plan & Fee</th>
                            <th>Status</th>
                            <th>Validity</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $t)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar rounded-3 bg-primary text-white d-flex align-items-center justify-content-center me-3 fw-bold">
                                            {{ strtoupper(substr($t->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $t->name }}</h6>
                                            <span class="text-muted small">Code: <code class="text-primary">{{ $t->code }}</code> | {{ $t->city }}, {{ $t->country }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $t->users->first()?->name ?? 'N/A' }}</span><br>
                                    <span class="text-muted small">{{ $t->email ?? $t->users->first()?->email }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark"><i class="fa-solid fa-door-open me-1"></i> {{ $t->rooms_count }} Rooms</span><br>
                                    <span class="badge bg-light text-dark mt-1"><i class="fa-solid fa-calendar-check me-1"></i> {{ $t->reservations_count }} Bookings</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary">{{ $t->subscription_plan }}</span><br>
                                    <strong class="text-success small">৳{{ number_format($t->monthly_fee, 2) }}/mo</strong>
                                </td>
                                <td>
                                    @if($t->is_subscription_locked)
                                        <span class="badge bg-danger"><i class="fa-solid fa-lock me-1"></i> LOCKED</span>
                                    @elseif($t->subscription_status === 'ACTIVE')
                                        <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> ACTIVE</span>
                                    @elseif($t->subscription_status === 'TRIAL')
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-hourglass-half me-1"></i> TRIAL</span>
                                    @elseif($t->subscription_status === 'SUSPENDED')
                                        <span class="badge bg-danger"><i class="fa-solid fa-ban me-1"></i> SUSPENDED</span>
                                    @else
                                        <span class="badge bg-secondary">EXPIRED</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $t->subscription_expires_at?->format('d M, Y') ?? 'N/A' }}</span><br>
                                    <span class="text-muted small">({{ $t->daysUntilExpiry() }} days remaining)</span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Manage
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.tenants.impersonate', $t->id) }}">
                                                    <i class="fa-solid fa-arrow-right-to-bracket text-primary me-2"></i> Login As Hotel
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditTenant{{ $t->id }}">
                                                    <i class="fa-solid fa-pen-to-square text-info me-2"></i> Edit Details & Plan
                                                </button>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.tenants.extend', $t->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="days" value="30">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa-solid fa-clock-rotate-left text-success me-2"></i> Extend +30 Days
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                @if($t->is_subscription_locked || $t->subscription_status === 'SUSPENDED')
                                                    <form action="{{ route('admin.tenants.toggle', $t->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="ACTIVATE">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fa-solid fa-unlock me-2"></i> Activate & Unlock
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.tenants.toggle', $t->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="SUSPEND">
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa-solid fa-ban me-2"></i> Suspend & Lock
                                                        </button>
                                                    </form>
                                                @endif
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.tenants.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $t->name }}? All room and booking records will be deleted.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa-solid fa-trash me-2"></i> Delete Hotel
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Edit Modal for Tenant -->
                                    <div class="modal fade" id="modalEditTenant{{ $t->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow rounded-4 text-start">
                                                <form action="{{ route('admin.tenants.update', $t->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Edit Hotel: {{ $t->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label text-muted small fw-bold">Hotel Name</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $t->name }}" required>
                                                        </div>
                                                        <div class="row g-2 mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label text-muted small fw-bold">Email</label>
                                                                <input type="email" name="email" class="form-control" value="{{ $t->email }}">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label text-muted small fw-bold">Phone</label>
                                                                <input type="text" name="phone" class="form-control" value="{{ $t->phone }}">
                                                            </div>
                                                        </div>
                                                        <div class="row g-2 mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label text-muted small fw-bold">Subscription Plan</label>
                                                                <select name="subscription_plan" class="form-select">
                                                                    <option value="STANDARD" {{ $t->subscription_plan === 'STANDARD' ? 'selected' : '' }}>Standard (৳1,000/mo)</option>
                                                                    <option value="PREMIUM" {{ $t->subscription_plan === 'PREMIUM' ? 'selected' : '' }}>Premium</option>
                                                                    <option value="ENTERPRISE" {{ $t->subscription_plan === 'ENTERPRISE' ? 'selected' : '' }}>Enterprise</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label text-muted small fw-bold">Subscription Status</label>
                                                                <select name="subscription_status" class="form-select">
                                                                    <option value="ACTIVE" {{ $t->subscription_status === 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                                                                    <option value="TRIAL" {{ $t->subscription_status === 'TRIAL' ? 'selected' : '' }}>TRIAL</option>
                                                                    <option value="SUSPENDED" {{ $t->subscription_status === 'SUSPENDED' ? 'selected' : '' }}>SUSPENDED</option>
                                                                    <option value="EXPIRED" {{ $t->subscription_status === 'EXPIRED' ? 'selected' : '' }}>EXPIRED</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row g-2 mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label text-muted small fw-bold">Monthly Fee (৳)</label>
                                                                <input type="number" step="0.01" name="monthly_fee" class="form-control" value="{{ $t->monthly_fee }}">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label text-muted small fw-bold">Expires At</label>
                                                                <input type="date" name="subscription_expires_at" class="form-control" value="{{ $t->subscription_expires_at?->format('Y-m-d') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary px-4 fw-bold">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fa-solid fa-hotel fs-1 mb-3 text-muted opacity-50"></i>
                                    <h5>No Hotels Found</h5>
                                    <p class="mb-0">Try changing your search filters or onboard a new hotel.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $tenants->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal: Onboard New Hotel Tenant -->
<div class="modal fade" id="modalAddTenant" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.tenants.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h4 class="modal-title fw-bold">Onboard New Hotel Property</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-primary mb-3">1. Hotel Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label text-muted small fw-bold">Hotel Name *</label>
                            <input type="text" name="name" class="form-control form-control-lg" placeholder="e.g. Radisson Blu Water Garden" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Hotel Code</label>
                            <input type="text" name="code" class="form-control form-control-lg" placeholder="e.g. RB-DAC">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">City</label>
                            <input type="text" name="city" class="form-control" value="Dhaka">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Country</label>
                            <input type="text" name="country" class="form-control" value="Bangladesh">
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3">2. Hotel Owner & Login Account</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Owner Full Name</label>
                            <input type="text" name="owner_name" class="form-control" placeholder="e.g. Tanvir Ahmed">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Owner Email (Login ID) *</label>
                            <input type="email" name="owner_email" class="form-control" placeholder="owner@hotel.com" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Initial Password *</label>
                            <input type="text" name="owner_password" class="form-control" value="password123" required>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3">3. SaaS Plan & Licensing</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Subscription Plan *</label>
                            <select name="subscription_plan" class="form-select">
                                <option value="STANDARD" selected>Standard (৳1,000/mo)</option>
                                <option value="PREMIUM">Premium (৳2,500/mo)</option>
                                <option value="ENTERPRISE">Enterprise (৳5,000/mo)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Monthly Fee (৳)</label>
                            <input type="number" name="monthly_fee" class="form-control" value="1000.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Trial / Validity Option</label>
                            <select name="trial_days" class="form-select">
                                <option value="14" selected>14-Day Free Trial</option>
                                <option value="30">30-Day Free Trial</option>
                                <option value="365">1-Year Paid Activation</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">
                        <i class="fa-solid fa-check me-2"></i> Create Hotel & Provision Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
