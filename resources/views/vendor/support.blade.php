@extends('layouts.vendor')
@section('title', 'Support & Help | Vendor Portal')
@section('content')
<div class="page-header-card">
    <div><h1 class="page-title m-0"><i class="fa-solid fa-headset me-2 text-primary"></i>Support & Help Center</h1></div>
    <div class="page-breadcrumb mt-2"><a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a><span class="sep">-</span><strong>Support</strong></div>
</div>
<div class="page-content-area">
    @if(session('success'))
        <div class="admin-alert success mb-3"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</div>
    @endif
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="stockifly-card p-3">
                <div class="fw-bold mb-3 pb-2 border-bottom" style="font-size:13px;color:var(--primary);text-transform:uppercase;letter-spacing:.5px;"><i class="fa-solid fa-paper-plane me-1"></i> Submit a Support Request</div>
                <form action="{{ route('vendor.support.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="saas-label">Subject</label>
                        <input type="text" name="subject" class="form-control saas-input" required placeholder="Brief description of your issue...">
                    </div>
                    <div class="mb-3">
                        <label class="saas-label">Category</label>
                        <select name="category" class="form-select saas-input">
                            <option>Booking Issue</option>
                            <option>Payout / Payment Problem</option>
                            <option>Property Listing</option>
                            <option>Account & Login</option>
                            <option>Technical Bug</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="saas-label">Detailed Message</label>
                        <textarea name="message" class="form-control" rows="5" required style="font-size:12.5px;border-radius:4px;border:1px solid #cbd5e1;padding:8px 10px;" placeholder="Describe your issue in detail..."></textarea>
                    </div>
                    <button type="submit" class="btn-add-primary w-100" style="height:38px;display:flex;align-items:center;justify-content:center;gap:8px;font-size:13px;"><i class="fa-solid fa-paper-plane"></i> Submit Support Request</button>
                </form>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="stockifly-card p-3 mb-3">
                <div class="fw-bold mb-3 pb-2 border-bottom" style="font-size:13px;color:var(--primary);text-transform:uppercase;letter-spacing:.5px;"><i class="fa-solid fa-circle-info me-1"></i> Quick Help &amp; FAQs</div>
                @php
                    $faqs = [
                        ['How do I add a new property?', 'Go to Properties → Add New Property. Fill in all details and submit for admin review. Typically approved within 24 hours.'],
                        ['Why is my payout pending?', 'Payouts are processed every Monday. Minimum payout threshold is ৳5,000. Check your bank details in your profile.'],
                        ['How to update room rates?', 'Navigate to Rates & Calendar. Select your property and update rates per date range.'],
                        ['My property is not showing on the website?', 'Ensure your property status is Active. If pending, it needs admin approval first.']
                    ];
                @endphp
                @foreach($faqs as $faq)
                <div class="mb-3 pb-3 border-bottom">
                    <div class="fw-bold" style="font-size:12.5px;color:#1e293b;">Q: {{ $faq[0] }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:4px;">{{ $faq[1] }}</div>
                </div>
                @endforeach
            </div>
            <div class="stockifly-card p-3">
                <div class="fw-bold mb-2 pb-2 border-bottom" style="font-size:13px;color:var(--primary);text-transform:uppercase;letter-spacing:.5px;"><i class="fa-solid fa-phone me-1"></i> Direct Contact</div>
                <div style="font-size:12.5px;" class="d-flex flex-column gap-2">
                    <div><i class="fa-solid fa-envelope me-2 text-primary"></i>support@primebooking.com.bd</div>
                    <div><i class="fa-brands fa-whatsapp me-2 text-success"></i>+880 1700-000000</div>
                    <div><i class="fa-solid fa-clock me-2 text-warning"></i>Mon–Sat: 9AM – 9PM BDT</div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>.saas-label{font-size:11px!important;font-weight:600!important;color:#475569!important;text-transform:uppercase!important;letter-spacing:.4px!important;margin-bottom:4px!important;display:block}.saas-input{font-size:12px!important;height:32px!important;padding:3px 10px!important;border-radius:4px!important;border:1px solid #cbd5e1!important}</style>
@endsection
