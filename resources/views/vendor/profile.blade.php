@extends('layouts.vendor')
@section('title', 'My Profile | Vendor Portal')
@section('content')
<div class="page-header-card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-title m-0">My Profile &amp; Account Settings</h1>
            <div class="page-breadcrumb mt-1"><a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a><span class="sep">-</span><strong>My Profile</strong></div>
        </div>
        <button type="submit" form="vendorProfileForm" class="btn-add-primary" style="display:inline-flex;align-items:center;gap:7px;font-size:13px;padding:0 22px;height:38px;box-shadow:0 2px 8px rgba(24,144,255,0.3);"><i class="fa-solid fa-check"></i> Save Changes</button>
    </div>
</div>
<div class="page-content-area">
    @if(session('success'))
        <div class="admin-alert success mb-3"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="admin-alert error mb-3"><ul class="mb-0 ps-3">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="stockifly-card p-3 text-center">
                <div style="width:105px;height:105px;border-radius:50%;overflow:hidden;border:3px solid var(--primary);margin:0 auto 12px;background:#f1f5f9;position:relative;">
                    <img id="vendorAvatarPreviewImg" src="{{ $user->avatar ?: ('https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=fa8c16&color=fff&size=105') }}" class="w-100 h-100" style="object-fit:cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=fa8c16&color=fff&size=105'">
                </div>
                <h5 class="fw-bold mb-1" style="font-size:14px;">{{ $user->name }}</h5>
                <p style="font-size:12px;color:#64748b;" class="mb-2">{{ $user->email }}</p>
                <div class="mb-3"><span class="badge bg-success" style="font-size:11px;padding:4px 10px;border-radius:10px;"><i class="fa-solid fa-shield-check me-1"></i> VERIFIED HOTEL VENDOR</span></div>
                <div class="row g-2 text-center">
                    <div class="col-6"><div style="background:#f8fafc;border-radius:4px;padding:8px;"><div class="fw-bold" style="font-size:16px;color:#1890ff;">{{ $stats['total_properties'] ?? 0 }}</div><div style="font-size:11px;color:#64748b;">Properties</div></div></div>
                    <div class="col-6"><div style="background:#f8fafc;border-radius:4px;padding:8px;"><div class="fw-bold" style="font-size:16px;color:#28c76f;">{{ $stats['total_bookings'] ?? 0 }}</div><div style="font-size:11px;color:#64748b;">Bookings</div></div></div>
                    <div class="col-12"><div style="background:#f8fafc;border-radius:4px;padding:8px;"><div class="fw-bold" style="font-size:16px;color:#7367f0;">৳{{ number_format($stats['total_revenue'] ?? 0) }}</div><div style="font-size:11px;color:#64748b;">Total Revenue</div></div></div>
                </div>
                <p style="font-size:11px;color:#94a3b8;" class="mt-3 mb-0">Member since {{ $stats['member_since'] ?? '2026' }}</p>
            </div>
        </div>
        <div class="col-lg-8">
            <form id="vendorProfileForm" action="{{ route('vendor.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="stockifly-card p-3 mb-3">
                    <div class="saas-section-title mb-3 pb-2 border-bottom"><i class="fa-solid fa-user me-1"></i> Personal &amp; Business Information</div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="saas-label">Full Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control saas-input" value="{{ old('name', $user->name) }}" required></div>
                        <div class="col-md-6"><label class="saas-label">Email Address <span class="text-danger">*</span></label><input type="email" name="email" class="form-control saas-input" value="{{ old('email', $user->email) }}" required></div>
                        <div class="col-md-6"><label class="saas-label">Contact Phone</label><input type="text" name="phone" class="form-control saas-input" value="{{ old('phone', $user->phone ?? '') }}"></div>
                        <div class="col-md-6">
                            <label class="saas-label">Profile Photo (Device Upload)</label>
                            <input type="file" id="vendorAvatarInput" name="avatar" class="form-control form-control-sm" accept="image/*" style="height:32px;font-size:12px;border-radius:4px;" onchange="previewAvatarImage(this)">
                            <small style="font-size:10.5px;color:#64748b;">JPG, PNG, WebP up to 10MB.</small>
                        </div>
                        <div class="col-md-6"><label class="saas-label">Trade License / NID No.</label><input type="text" name="trade_license" class="form-control saas-input" placeholder="e.g. TRAD/DNCC/102948" value="{{ old('trade_license', 'TRAD/BD/98042') }}"></div>
                        <div class="col-md-6"><label class="saas-label">Preferred Payout Method</label>
                            <select name="payout_method" class="form-select saas-input">
                                <option value="bKash">bKash Merchant / Personal</option>
                                <option value="Nagad">Nagad Personal</option>
                                <option value="Bank">Bank Wire Transfer</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="stockifly-card p-3 mb-3">
                    <div class="saas-section-title mb-3 pb-2 border-bottom"><i class="fa-solid fa-building-columns me-1"></i> Payout Bank / Mobile Banking Details</div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="saas-label">Account / Mobile No.</label><input type="text" name="account_no" class="form-control saas-input" placeholder="e.g. 01700000000 or Account No." value="{{ old('account_no', '01711223344') }}"></div>
                        <div class="col-md-6"><label class="saas-label">Account Holder Name</label><input type="text" name="account_holder" class="form-control saas-input" placeholder="e.g. Grand Ocean Resort" value="{{ old('account_holder', $user->name) }}"></div>
                    </div>
                </div>
                <div class="stockifly-card p-3 mb-3">
                    <div class="saas-section-title mb-3 pb-2 border-bottom"><i class="fa-solid fa-lock me-1"></i> Change Password</div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="saas-label">New Password</label><input type="password" name="new_password" class="form-control saas-input" placeholder="Leave blank to keep current"></div>
                        <div class="col-md-6"><label class="saas-label">Confirm New Password</label><input type="password" name="new_password_confirmation" class="form-control saas-input" placeholder="Repeat new password"></div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn-add-primary px-4 py-2" style="font-size:13px;"><i class="fa-solid fa-check me-1"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
.saas-label{font-size:11px!important;font-weight:600!important;color:#475569!important;text-transform:uppercase!important;letter-spacing:.4px!important;margin-bottom:4px!important;display:block}
.saas-input{font-size:12px!important;height:32px!important;padding:3px 10px!important;border-radius:4px!important;border:1px solid #cbd5e1!important;color:#1e293b!important}
.saas-section-title{font-size:12.5px!important;font-weight:700!important;color:var(--primary)!important;text-transform:uppercase!important;letter-spacing:.5px!important}
</style>
<script>
function previewAvatarImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('vendorAvatarPreviewImg');
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

