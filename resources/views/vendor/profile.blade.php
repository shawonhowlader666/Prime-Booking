@extends('layouts.vendor')
@section('title', 'My Profile | Vendor Portal')
@section('content')
<div class="page-header-card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <h1 class="page-title m-0">My Profile & Account Settings</h1>
        <button type="button" onclick="document.getElementById('vendorProfileForm').submit()" class="btn-add-primary" style="display:inline-flex;align-items:center;gap:7px;font-size:13px;padding:0 18px;height:36px;"><i class="fa-solid fa-check"></i> Save Changes</button>
    </div>
    <div class="page-breadcrumb mt-2"><a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a><span class="sep">-</span><strong>My Profile</strong></div>
</div>
<div class="page-content-area">
    @if(session('success'))
        <div class="admin-alert success mb-3"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</div>
    @endif
    @if(->any())
        <div class="admin-alert error mb-3"><ul class="mb-0 ps-3">@foreach(->all() as )<li>{{  }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="stockifly-card p-3 text-center">
                <div style="width:90px;height:90px;border-radius:50%;overflow:hidden;border:3px solid var(--primary);margin:0 auto 12px;">
                    <img src="{{ ->avatar ?: ('https://ui-avatars.com/api/?name='.urlencode(->name).'&background=fa8c16&color=fff&size=90') }}" class="w-100 h-100" style="object-fit:cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(->name) }}&background=fa8c16&color=fff&size=90'">
                </div>
                <h5 class="fw-bold mb-1" style="font-size:14px;">{{ ->name }}</h5>
                <p style="font-size:12px;color:#64748b;" class="mb-3">{{ ->email }}</p>
                <div class="row g-2 text-center">
                    <div class="col-6"><div style="background:#f8fafc;border-radius:4px;padding:8px;"><div class="fw-bold" style="font-size:16px;color:#1890ff;">{{ ['total_properties'] }}</div><div style="font-size:11px;color:#64748b;">Properties</div></div></div>
                    <div class="col-6"><div style="background:#f8fafc;border-radius:4px;padding:8px;"><div class="fw-bold" style="font-size:16px;color:#28c76f;">{{ ['total_bookings'] }}</div><div style="font-size:11px;color:#64748b;">Bookings</div></div></div>
                    <div class="col-12"><div style="background:#f8fafc;border-radius:4px;padding:8px;"><div class="fw-bold" style="font-size:16px;color:#7367f0;">৳{{ number_format(['total_revenue']) }}</div><div style="font-size:11px;color:#64748b;">Total Revenue</div></div></div>
                </div>
                <p style="font-size:11px;color:#94a3b8;" class="mt-3 mb-0">Member since {{ ['member_since'] }}</p>
            </div>
        </div>
        <div class="col-lg-8">
            <form id="vendorProfileForm" action="{{ route('vendor.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="stockifly-card p-3 mb-3">
                    <div class="saas-section-title mb-3 pb-2 border-bottom"><i class="fa-solid fa-user me-1"></i> Personal Information</div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="saas-label">Full Name</label><input type="text" name="name" class="form-control saas-input" value="{{ old('name', ->name) }}" required></div>
                        <div class="col-md-6"><label class="saas-label">Email Address</label><input type="email" name="email" class="form-control saas-input" value="{{ old('email', ->email) }}" required></div>
                        <div class="col-md-6"><label class="saas-label">Contact Phone</label><input type="text" name="phone" class="form-control saas-input" value="{{ old('phone', ->phone ?? '') }}"></div>
                        <div class="col-md-6"><label class="saas-label">Profile Photo</label><input type="file" name="avatar" class="form-control form-control-sm" accept="image/*" style="height:32px;font-size:12px;border-radius:4px;"></div>
                    </div>
                </div>
                <div class="stockifly-card p-3">
                    <div class="saas-section-title mb-3 pb-2 border-bottom"><i class="fa-solid fa-lock me-1"></i> Change Password</div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="saas-label">New Password</label><input type="password" name="new_password" class="form-control saas-input" placeholder="Leave blank to keep current"></div>
                        <div class="col-md-6"><label class="saas-label">Confirm New Password</label><input type="password" name="new_password_confirmation" class="form-control saas-input" placeholder="Repeat new password"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<style>.saas-label{font-size:11px!important;font-weight:600!important;color:#475569!important;text-transform:uppercase!important;letter-spacing:.4px!important;margin-bottom:4px!important;display:block}.saas-input{font-size:12px!important;height:32px!important;padding:3px 10px!important;border-radius:4px!important;border:1px solid #cbd5e1!important;color:#1e293b!important}.saas-section-title{font-size:12.5px!important;font-weight:700!important;color:var(--primary)!important;text-transform:uppercase!important;letter-spacing:.5px!important}</style>
@endsection
