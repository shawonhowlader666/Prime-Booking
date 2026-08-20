@extends('layouts.admin')
@section('title', 'VIP Member Roster | PRIME BOOKING Admin')

@section('content')
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.vip.settings') }}">VIP Loyalty Program</a>
        <span class="sep">-</span><span style="color:#666;">Member Roster</span>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title"><i class="fa-solid fa-users text-primary me-2"></i> VIP Member Roster &amp; Tier Status</h1>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('admin.vip.settings') }}" class="btn-table-action" style="padding:6px 14px;">
                <i class="fa-solid fa-sliders me-1"></i> Configure Tier Rules
            </a>
            <a href="{{ route('vip') }}" target="_blank" class="btn-table-action primary" style="padding:6px 14px;">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Live VIP Page
            </a>
        </div>
    </div>
</div>

<div class="page-content-area">
    <div style="max-width:1100px; margin:0 auto;">

        {{-- SEARCH BAR --}}
        <div class="card p-3 mb-3 border bg-white shadow-xs">
            <form action="{{ route('admin.vip.members') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search customer by name, email, or phone..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="tier" class="form-select form-select-sm">
                        <option value="">All VIP Tiers</option>
                        <option value="Bronze" {{ $tierFilter === 'Bronze' ? 'selected' : '' }}>Bronze Member</option>
                        <option value="Silver" {{ $tierFilter === 'Silver' ? 'selected' : '' }}>VIP Silver</option>
                        <option value="Gold" {{ $tierFilter === 'Gold' ? 'selected' : '' }}>VIP Gold</option>
                        <option value="Platinum" {{ $tierFilter === 'Platinum' ? 'selected' : '' }}>VIP Platinum</option>
                        <option value="Diamond" {{ $tierFilter === 'Diamond' ? 'selected' : '' }}>VIP Diamond</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn-add-primary w-100" style="padding:5px 12px; font-size:12px;">Filter Roster</button>
                    @if($search || $tierFilter)
                        <a href="{{ route('admin.vip.members') }}" class="btn btn-light btn-sm border" style="font-size:12px;">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="card border shadow-xs bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light text-secondary" style="font-size:11.5px; text-transform:uppercase;">
                        <tr>
                            <th style="padding-left:18px;">Customer</th>
                            <th>2-Year Bookings</th>
                            <th>2-Year Spend</th>
                            <th>Current VIP Tier</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            @php
                                $stats = $userStats->get($u->id);
                                $bCount = (int) ($stats->bookings_count ?? 0);
                                $spend = (float) ($stats->total_spend ?? 0);

                                if ($bCount >= 15 && $spend >= 1500) {
                                    $tierBadge = '<span class="badge" style="background:#9333ea; color:#fff;">★ VIP Diamond</span>';
                                } elseif ($bCount >= 10 || $spend >= 400) {
                                    $tierBadge = '<span class="badge bg-dark text-white">★ VIP Platinum</span>';
                                } elseif ($bCount >= 5 || $spend >= 200) {
                                    $tierBadge = '<span class="badge bg-warning text-dark">★ VIP Gold</span>';
                                } elseif ($bCount >= 2) {
                                    $tierBadge = '<span class="badge bg-secondary">★ VIP Silver</span>';
                                } else {
                                    $tierBadge = '<span class="badge" style="background:#ba6d4a; color:#fff;">★ VIP Bronze</span>';
                                }
                            @endphp
                            <tr>
                                <td style="padding-left:18px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center" style="width:32px; height:32px; background:#2563eb; font-size:13px;">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong class="text-dark d-block">{{ $u->name }}</strong>
                                            <small class="text-muted">{{ $u->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold">{{ $bCount }} Bookings</span>
                                </td>
                                <td>
                                    <strong style="color:#2067e1;">${{ number_format($spend) }}</strong>
                                </td>
                                <td>
                                    {!! $tierBadge !!}
                                </td>
                                <td>
                                    <span class="badge bg-success-light text-success border-success fw-semibold" style="font-size:10.5px;">Active Auto-Renew</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No customers found in roster.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
