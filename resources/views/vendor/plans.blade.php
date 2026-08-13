@extends('layouts.vendor')
@section('title', 'SaaS Subscription Plans | Vendor Portal')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">SaaS Subscription Plans &amp; Billing</h1>
        
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">SaaS Plans &amp; Billing</strong>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- FILTER / INFO BAR --}}
    <div class="page-filters-bar">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <span style="font-size:12.5px; color:#595959; font-weight:600;">
                    <i class="fa-solid fa-circle-info" style="color:var(--primary);"></i>
                    Lower your commission rate and get priority placement on Prime Aviation OTA search results.
                </span>
            </div>
        </div>
    </div>

    {{-- Current Plan KPI --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#7367f0;"><i class="fa-solid fa-crown"></i></div>
                    <div>
                        <p class="kpi-value">Professional</p>
                        <p class="kpi-label">Current Active Plan</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-check"></i> Renews Sep 30, 2026</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#28c76f;"><i class="fa-solid fa-percent"></i></div>
                    <div>
                        <p class="kpi-value">8%</p>
                        <p class="kpi-label">Your Commission Rate</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-arrow-down"></i> vs 12% Free Tier</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#ff9f43;"><i class="fa-solid fa-money-bill"></i></div>
                    <div>
                        <p class="kpi-value">BDT 6,000</p>
                        <p class="kpi-label">Monthly Subscription</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-calendar"></i> Paid Monthly</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#00cfe8;"><i class="fa-solid fa-ranking-star"></i></div>
                    <div>
                        <p class="kpi-value">Priority #2</p>
                        <p class="kpi-label">Search Placement</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-arrow-up"></i> Upgrade for #1</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
    </div>

    {{-- Plans Comparison Table --}}
    <div class="data-table-card mb-4">
        <div class="data-table-card-header">
            <h6>All Available SaaS Partner Plans — Compare &amp; Select</h6>
            <span class="badge-gateway">3 Plans Available</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
                <thead>
                    <tr>
                        <th>Plan Name</th>
                        <th>Monthly Fee (BDT)</th>
                        <th>Commission Rate</th>
                        <th>Max Properties</th>
                        <th>Search Placement</th>
                        <th>Analytics</th>
                        <th>Support Level</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($plans ?? [
                    ['id'=>'starter','name'=>'Starter','price'=>2500,'commission'=>'12%','max_props'=>2,'placement'=>'Standard','analytics'=>'Basic','support'=>'Email Only','popular'=>false],
                    ['id'=>'professional','name'=>'Professional','price'=>6000,'commission'=>'8%','max_props'=>5,'placement'=>'Priority #2','analytics'=>'Advanced','support'=>'Chat + Email','popular'=>true],
                    ['id'=>'enterprise','name'=>'Enterprise','price'=>15000,'commission'=>'5%','max_props'=>-1,'placement'=>'Top #1 Spot','analytics'=>'Full BI Suite','support'=>'Dedicated Manager','popular'=>false],
                ] as $plan)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            @if($plan['popular'] ?? false)
                                <span style="background:var(--primary); color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:4px;">POPULAR</span>
                            @endif
                            <strong style="font-size:13px; color:#1e293b;">{{ $plan['name'] }}</strong>
                        </div>
                    </td>
                    <td><strong style="color:var(--primary); font-size:13px;">BDT {{ number_format($plan['price']) }} / mo</strong></td>
                    <td>
                        <span class="badge-status {{ $plan['commission'] === '5%' ? 'confirmed' : ($plan['commission'] === '8%' ? 'active' : 'pending') }}">
                            {{ $plan['commission'] }} per booking
                        </span>
                    </td>
                    <td style="text-align:center; font-weight:700; color:#334155;">{{ $plan['max_props'] === -1 ? 'Unlimited' : $plan['max_props'] }}</td>
                    <td style="font-size:12.5px; color:#334155;">{{ $plan['placement'] }}</td>
                    <td style="font-size:12.5px; color:#334155;">{{ $plan['analytics'] }}</td>
                    <td style="font-size:12.5px; color:#334155;">{{ $plan['support'] }}</td>
                    <td style="text-align:right;">
                        <form action="{{ route('vendor.plans.select') }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan['id'] }}">
                            <button type="submit" class="btn-table-action {{ ($plan['popular'] ?? false) ? 'primary' : '' }}">
                                Select {{ $plan['name'] }} <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Plan Cards Grid (Visual) --}}
    <div class="row g-3">
        @foreach($plans ?? [
            ['id'=>'starter','name'=>'Starter','price'=>2500,'commission'=>'12%','popular'=>false,'color'=>'#8c8c8c','features'=>['2 Property Listings','Standard Search Position','Basic Analytics Dashboard','Email Support Only']],
            ['id'=>'professional','name'=>'Professional','price'=>6000,'commission'=>'8%','popular'=>true,'color'=>'#1890ff','features'=>['5 Property Listings','Priority #2 Search Position','Advanced Analytics + Reports','Chat + Email Support','bKash / Nagad Payout Weekly']],
            ['id'=>'enterprise','name'=>'Enterprise','price'=>15000,'commission'=>'5%','popular'=>false,'color'=>'#7367f0','features'=>['Unlimited Property Listings','Top #1 Priority Search Spot','Full BI Suite + Custom Reports','Dedicated Account Manager','Daily Payout Settlement']],
        ] as $plan)
        <div class="col-md-4">
            <div class="plan-card {{ ($plan['popular'] ?? false) ? 'popular' : '' }}">
                @if($plan['popular'] ?? false)
                    <span class="plan-popular-badge">MOST POPULAR</span>
                @endif
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                    <div style="width:36px; height:36px; background:{{ $plan['color'] }}; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:16px;">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <div>
                        <strong style="font-size:15px; color:#1e293b; display:block;">{{ $plan['name'] }}</strong>
                        <span style="font-size:11.5px; color:#8c8c8c;">Commission: <strong style="color:#28c76f;">{{ $plan['commission'] }}</strong>/booking</span>
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <span style="font-size:26px; font-weight:800; color:#1e293b; font-family:var(--font-heading);">BDT {{ number_format($plan['price']) }}</span>
                    <span style="font-size:12px; color:#8c8c8c;">/ month</span>
                </div>
                <ul style="list-style:none; padding:0; margin:0 0 20px; display:flex; flex-direction:column; gap:8px; flex:1;">
                    @foreach($plan['features'] as $feat)
                    <li style="display:flex; align-items:center; gap:8px; font-size:12.5px; color:#595959;">
                        <i class="fa-solid fa-circle-check" style="color:#28c76f; font-size:13px; flex-shrink:0;"></i>
                        {{ $feat }}
                    </li>
                    @endforeach
                </ul>
                <form action="{{ route('vendor.plans.select') }}" method="POST" style="margin-top:auto;">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan['id'] }}">
                    <button type="submit" style="width:100%; padding:8px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; border:{{ ($plan['popular'] ?? false) ? 'none' : '1.5px solid #d9d9d9' }}; background:{{ ($plan['popular'] ?? false) ? 'var(--primary)' : '#fff' }}; color:{{ ($plan['popular'] ?? false) ? '#fff' : '#595959' }}; transition:all 0.15s;">
                        Select {{ $plan['name'] }} <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
