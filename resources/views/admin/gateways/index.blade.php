@extends('layouts.admin')
@section('title', 'Payment Gateways & Webhooks Vault — Admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 style="font-size:19px;font-weight:700;margin:0;">Payment Gateways &amp; API Vault</h1>
        <p style="font-size:12px;color:#8c8c8c;margin:0;">Configure credentials, sandbox/live modes, and webhook listeners for bKash, Nagad, SSLCommerz, Stripe</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success p-2 small mb-3">{{ session('success') }}</div>
@endif

<div class="row g-3">
    @foreach($gateways as $gw)
    <div class="col-md-6">
        <div class="card p-3 border-0 shadow-sm rounded-3 bg-white h-100 position-relative">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span>
                        @if($gw->gateway_code === 'bkash') <i class="fa-solid fa-wallet text-danger fs-4"></i>
                        @elseif($gw->gateway_code === 'nagad') <i class="fa-solid fa-mobile-screen-button text-warning fs-4"></i>
                        @elseif($gw->gateway_code === 'sslcommerz') <i class="fa-solid fa-credit-card text-primary fs-4"></i>
                        @else <i class="fa-solid fa-globe text-info fs-4"></i>
                        @endif
                    </span>
                    <div>
                        <h6 class="fw-bold mb-0">{{ $gw->name }}</h6>
                        <small class="font-monospace text-muted" style="font-size:11px;">Code: {{ $gw->gateway_code }}</small>
                    </div>
                </div>
                <form action="{{ route('admin.gateways.toggle', $gw->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $gw->is_active ? 'btn-success' : 'btn-secondary' }}">
                        {{ $gw->is_active ? 'Active' : 'Disabled' }}
                    </button>
                </form>
            </div>

            <form action="{{ route('admin.gateways.update', $gw->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-2">
                    <label class="form-label small fw-bold">Display Name</label>
                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $gw->name }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Merchant ID / Store ID</label>
                    <input type="text" name="merchant_id" class="form-control form-control-sm font-monospace" value="{{ $gw->merchant_id }}">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">API App Key / Public Key</label>
                    <input type="text" name="api_key" class="form-control form-control-sm font-monospace" value="{{ $gw->api_key }}">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">API Secret / Store Password</label>
                    <input type="password" name="api_secret" class="form-control form-control-sm font-monospace" value="{{ $gw->api_secret }}">
                </div>

                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_sandbox" value="1" id="sb_{{ $gw->id }}" @checked($gw->is_sandbox)>
                        <label class="form-check-label small fw-bold" for="sb_{{ $gw->id }}">Sandbox Mode (Testing)</label>
                    </div>

                    <div class="form-check form-switch ms-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="ac_{{ $gw->id }}" @checked($gw->is_active)>
                        <label class="form-check-label small fw-bold" for="ac_{{ $gw->id }}">Enabled</label>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-save me-1"></i> Save Gateway Credentials
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
