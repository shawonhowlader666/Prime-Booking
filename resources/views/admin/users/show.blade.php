@extends('layouts.admin')
@section('title', 'User Profile: ' . $user->name . ' | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.users.index') }}">Users</a>
        <span class="sep">-</span><strong style="color:#333;">Account Profile</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">User Account: {{ $user->name }}</h1>
        <a href="{{ route('admin.users.index') }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959;">
            <i class="fa-solid fa-arrow-left"></i> Back to All Users
        </a>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">

            {{-- User Information --}}
            <div class="form-card mb-3" style="border-radius:6px; border:1px solid #e2e8f0; background:#fff; padding:20px;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-3 mb-3 border-bottom">
                    <div class="form-section-title mb-0" style="font-size:15px; font-weight:700; color:#1e293b;">
                        <i class="fa-solid fa-address-card me-1.5 text-primary"></i> Profile &amp; Omni-Channel Contact Hub
                    </div>
                    {{-- 1-Click Direct Omni-Channel Trigger Buttons --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if($user->phone)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $user->phone) }}" class="btn btn-sm btn-outline-success fw-bold px-3 py-1.5" style="border-radius:4px; font-size:12.5px;" title="Direct Call">
                                <i class="fa-solid fa-phone me-1"></i> Direct Call
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}?text=Hello%20{{ urlencode($user->name) }},%20greetings%20from%20Prime%20Booking!" target="_blank" class="btn btn-sm btn-success fw-bold px-3 py-1.5 text-white" style="border-radius:4px; font-size:12.5px; background:#25D366; border-color:#25D366;" title="WhatsApp Chat">
                                <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
                            </a>
                            <a href="sms:{{ preg_replace('/[^0-9+]/', '', $user->phone) }}?body=Hello%20{{ urlencode($user->name) }},%20Prime%20Booking%20Update:" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1.5" style="border-radius:4px; font-size:12.5px;" title="Send SIM SMS">
                                <i class="fa-solid fa-comment-sms me-1"></i> Send SMS
                            </a>
                        @endif
                        <a href="mailto:{{ $user->email }}?subject=Prime%20Booking%20Notification&body=Dear%20{{ urlencode($user->name) }}," class="btn btn-sm btn-outline-danger fw-bold px-3 py-1.5" style="border-radius:4px; font-size:12.5px;" title="Gmail / Email">
                            <i class="fa-solid fa-envelope me-1"></i> Gmail / Mail
                        </a>
                        @if($user->last_login_ip && !in_array($user->last_login_ip, ['127.0.0.1', '::1']))
                            <a href="https://ipinfo.io/{{ $user->last_login_ip }}" target="_blank" class="btn btn-sm btn-outline-secondary fw-bold px-2.5 py-1.5" style="border-radius:4px; font-size:12.5px;" title="IP Geolocation Lookup">
                                <i class="fa-solid fa-globe me-1"></i> IP Lookup
                            </a>
                        @endif
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:11.5px; color:#64748b; font-weight:600; text-transform:uppercase;">Full Name</label>
                        <p style="font-size:15px; font-weight:700; color:#1e293b; margin:0;">{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:11.5px; color:#64748b; font-weight:600; text-transform:uppercase;">Email Address</label>
                        <p style="font-size:14px; font-weight:600; color:#334155; margin:0;">{{ $user->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:11.5px; color:#64748b; font-weight:600; text-transform:uppercase;">Phone Number</label>
                        <p style="font-size:14px; font-weight:600; color:#334155; margin:0;">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:11.5px; color:#64748b; font-weight:600; text-transform:uppercase;">Registration Date</label>
                        <p style="font-size:14px; font-weight:600; color:#334155; margin:0;">{{ $user->created_at ? $user->created_at->format('F d, Y, h:i A') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:11.5px; color:#64748b; font-weight:600; text-transform:uppercase;">Last Login IP &amp; Activity</label>
                        <p style="font-size:13.5px; font-weight:600; color:#334155; margin:0;">
                            <code>{{ $user->last_login_ip ?? 'No recorded IP' }}</code>
                            @if($user->last_login_at)
                                <small class="text-muted ms-1">({{ $user->last_login_at->diffForHumans() }})</small>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:11.5px; color:#64748b; font-weight:600; text-transform:uppercase;">Loyalty &amp; Miles</label>
                        <p style="font-size:13.5px; font-weight:700; color:var(--primary); margin:0;">
                            {{ $user->prime_miles ?? 0 }} Prime Miles | {{ $user->rewards ? $user->rewards->points_balance : 0 }} Reward Pts
                        </p>
                    </div>
                </div>
            </div>

            {{-- Booking History --}}
            <div class="data-table-card">
                <div class="data-table-card-header">
                    <h6>Booking History ({{ count($bookings) }} Recent)</h6>
                </div>
                <div style="overflow-x:auto;">
                    <table class="table-stockifly" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Property</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($bookings as $b)
                            <tr>
                                <td><a href="{{ route('admin.bookings.show', $b->id) }}" style="color:var(--primary); font-weight:700;">{{ $b->booking_reference ?? 'PRM-'.$b->id }}</a></td>
                                <td>{{ Str::limit(optional($b->property)->name ?? 'Property', 25) }}</td>
                                <td style="font-size:12px;">{{ $b->check_in }} → {{ $b->check_out }}</td>
                                <td><strong style="color:var(--primary);">BDT {{ number_format($b->total_amount ?? $b->total_price ?? 0) }}</strong></td>
                                <td><span class="badge-status {{ strtolower($b->booking_status ?? $b->status ?? 'confirmed') }}">{{ ucfirst($b->booking_status ?? $b->status ?? 'Confirmed') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:24px; color:#8c8c8c;">No booking history available for this user.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Actions Column --}}
        <div class="col-lg-4">

            {{-- Role Change --}}
            <div class="data-table-card mb-3">
                <div class="data-table-card-header">
                    <h6>Role Permission Assignment</h6>
                </div>
                <div style="padding:16px;">
                    <form action="{{ route('admin.users.update-role', $user->id) }}" method="POST">
                        @csrf
                        <label class="form-label mb-1" style="font-size:11px; font-weight:600; color:#8c8c8c;">SELECT ROLE</label>
                        <select name="role" class="form-select mb-3" style="height:36px; font-size:13px;">
                            <option value="user" {{ ($user->role ?? 'user') == 'user' ? 'selected' : '' }}>Guest / Customer (user)</option>
                            <option value="vendor" {{ $user->role == 'vendor' ? 'selected' : '' }}>Hotel Partner (vendor)</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>System Admin (admin)</option>
                        </select>
                        <button type="submit" class="btn-add-primary w-100" style="justify-content:center; padding:8px;">
                            Update User Role <i class="fa-solid fa-shield ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Ban / Unban --}}
            <div class="data-table-card mb-3">
                <div class="data-table-card-header">
                    <h6>Account Status Control</h6>
                </div>
                <div style="padding:16px;">
                    <p style="font-size:12px; color:#595959; margin-bottom:12px;">
                        Current Account Status: <strong style="color:{{ $user->status == 'banned' ? '#ff4d4f' : '#52c41a' }};">{{ ucfirst($user->status ?? 'Active') }}</strong>
                    </p>
                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-table-action {{ $user->status == 'banned' ? 'success' : 'danger' }} w-100" style="justify-content:center; padding:8px;">
                            {{ $user->status == 'banned' ? 'Re-activate User Account' : 'Ban / Suspend Account' }} <i class="fa-solid fa-ban ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="data-table-card">
                <div class="data-table-card-header">
                    <h6>Danger Zone</h6>
                </div>
                <div style="padding:16px;">
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Permanently delete this user account?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-table-action danger w-100" style="justify-content:center; padding:8px;">
                            Delete Account Permanently <i class="fa-solid fa-trash ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

