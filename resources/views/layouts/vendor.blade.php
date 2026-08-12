<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vendor Partner Portal | PRIME BOOKING')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ===== Same CSS variables as admin layout ===== */
        :root {
            --primary:                #1890ff;
            --primary-hover:          #40a9ff;
            --primary-active:         #096dd9;
            --primary-bg:             #e6f7ff;
            --primary-shadow:         rgba(24,144,255,0.20);
            --primary-transparent-10: rgba(24,144,255,0.10);
            --sidebar-width:          250px;
            --admin-body-bg:          #f0f2f5;
            --admin-card-border:      #e8e8e8;
            --font-main:              'Plus Jakarta Sans', -apple-system, sans-serif;
            --font-heading:           'Plus Jakarta Sans', sans-serif;
            --font-size-base:         13px;
            --font-size-btn:          13px;
            --font-size-input:        13px;
            --font-size-sidebar:      13px;
            --font-size-table-header: 12px;
            --font-size-table-body:   13px;
            --font-size-page-header:  19px;
        }

        /* ============================================================
         * STRICT RULE: Admin & Vendor Panel — Maximum 4px Border-Radius
         * ============================================================ */
        .btn,
        .btn-primary,
        .btn-secondary,
        .btn-success,
        .btn-danger,
        .btn-warning,
        .btn-info,
        .btn-light,
        .btn-dark,
        .btn-outline-primary,
        .btn-outline-secondary,
        .btn-export-csv,
        .btn-export-pdf,
        .btn-add-primary,
        .btn-signout,
        .btn-table-action,
        .card,
        .form-card,
        .page-header-card,
        .modal-content,
        .form-control,
        .form-select,
        .input-group-text,
        .badge,
        .alert,
        .admin-alert,
        .nav-tabs .nav-link,
        .nav-pills .nav-link,
        .dropdown-menu,
        .table-responsive,
        .active-import-tab,
        .table,
        .modal-dialog,
        .modal-header,
        .modal-footer {
            border-radius: 4px !important;
        }

        /* ============================================================
         * Action Gear Hover & Dropdown Styles for Admin & Vendor Tables
         * ============================================================ */
        .action-gear-dropdown {
            position: relative;
        }
        .action-gear-dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
        }
        .action-gear-btn {
            transition: all 0.15s ease;
        }
        .action-gear-dropdown:hover .action-gear-btn {
            background: var(--primary) !important;
            color: #ffffff !important;
        }
        .dropdown-item {
            font-size: 12.5px;
            font-weight: 500;
            color: #334155;
            transition: background 0.12s ease;
        }
        .dropdown-item:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: var(--font-main);
            background-color: var(--admin-body-bg);
            color: #333333;
            font-size: var(--font-size-base);
            margin: 0;
        }

        /* ===== Sidebar — Vendor teal accent instead of blue ===== */
        #vendorSidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background-color: #001529;
            color: rgba(255,255,255,0.85);
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
            overflow-y: auto;
            transition: transform 0.25s ease;
            display: flex;
            flex-direction: column;
        }
        .sb-brand {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 16px;
            background-color: #002140;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }
        .sb-brand-icon {
            width: 32px; height: 32px;
            background-color: #fa8c16;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 15px; flex-shrink: 0;
        }
        .sb-brand-title  { font-size:14px; font-weight:700; color:#fff; display:block; line-height:1.2; }
        .sb-brand-subtitle { font-size:10px; color:rgba(255,255,255,0.45); display:block; }
        .sb-section-header {
            font-size:11px; font-weight:700; color:rgba(255,255,255,0.35);
            text-transform:uppercase; letter-spacing:0.8px;
            padding:16px 16px 5px;
        }
        .sb-nav-item {
            display:flex; align-items:center; gap:10px;
            padding:9px 16px;
            color:rgba(255,255,255,0.65);
            text-decoration:none; font-weight:500;
            font-size: var(--font-size-sidebar);
            transition: background-color 0.15s, color 0.15s;
        }
        .sb-nav-item i { width:16px; text-align:center; font-size:14px; flex-shrink:0; }
        .sb-nav-item:hover { color:#fff; background:rgba(24,144,255,0.12); }
        .sb-nav-item.active { color:#fff; background: var(--primary); }

        /* ===== Content ===== */
        #vendorContent {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex; flex-direction: column;
            transition: margin-left 0.25s ease;
        }

        /* ===== Top bar ===== */
        .admin-topbar {
            background:#fff; border-bottom:1px solid #f0f0f0;
            padding:0 24px; height:48px;
            display:flex; align-items:center; justify-content:space-between;
            position:sticky; top:0; z-index:100; flex-shrink:0;
        }
        .admin-topbar-left { display:flex; align-items:center; gap:12px; }
        .admin-topbar-right { display:flex; align-items:center; gap:12px; }
        .engine-badge {
            background:rgba(250,140,22,0.1); color:#fa8c16;
            font-size:11px; font-weight:700; padding:3px 10px;
            border-radius:4px; border:1px solid rgba(250,140,22,0.25);
        }
        .topbar-user-name { font-size:12.5px; font-weight:700; color:#1e293b; display:block; line-height:1.2; }
        .topbar-user-role { font-size:10.5px; color:#8c8c8c; display:block; }
        .topbar-avatar { width:32px; height:32px; border-radius:50%; object-fit:cover; border:2px solid #f0f0f0; }
        .btn-signout {
            font-size:12px; font-weight:600; padding:5px 14px;
            border-radius:6px; border:1.5px solid #ff4d4f; color:#ff4d4f;
            background:#fff; transition:all 0.15s; cursor:pointer; line-height:1.5;
        }
        .btn-signout:hover { background:#ff4d4f; color:#fff; }
        .btn-mobile-toggle {
            background:none; border:1px solid #d9d9d9; border-radius:6px;
            width:32px; height:32px; display:none; align-items:center;
            justify-content:center; cursor:pointer; color:#595959; font-size:14px;
        }

        /* ===== Page Header (same as admin) ===== */
        .page-header-card {
            background:#fff;
            border: 1px solid #e8e8e8;
            border-left: 4px solid var(--primary);
            padding: 16px 24px;
            margin: 18px 24px 18px 24px;
            border-radius: 4px !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .page-breadcrumb {
            font-size:12px; color:#8c8c8c; margin-bottom:6px;
            display:flex; align-items:center; gap:4px; flex-wrap:wrap;
        }
        .page-breadcrumb a { color:#595959; text-decoration:none; transition:color 0.15s; }
        .page-breadcrumb a:hover { color: var(--primary); }
        .page-breadcrumb .sep { color:#d9d9d9; margin:0 2px; }
        .page-title {
            font-size: var(--font-size-page-header);
            font-weight:800; color:#1e293b;
            font-family: var(--font-heading);
            margin:0; line-height:1.3;
        }

        /* ===== Buttons (same as admin) ===== */
        .btn-export-csv {
            display:inline-flex; align-items:center; gap:6px;
            padding:5px 14px; font-size:12.5px; font-weight:600;
            border-radius:6px; border:1.5px solid #52c41a; color:#52c41a;
            background:#fff; cursor:pointer; transition:all 0.15s; text-decoration:none; line-height:1.5;
        }
        .btn-export-csv:hover { background:#52c41a; color:#fff; }
        .btn-export-pdf {
            display:inline-flex; align-items:center; gap:6px;
            padding:5px 14px; font-size:12.5px; font-weight:600;
            border-radius:6px; border:none; color:#fff;
            background: var(--primary); cursor:pointer; transition:all 0.15s; text-decoration:none; line-height:1.5;
        }
        .btn-export-pdf:hover { background: var(--primary-active); color:#fff; }
        .btn-add-primary {
            display:inline-flex; align-items:center; gap:6px;
            padding:5px 16px; font-size:12.5px; font-weight:600;
            border-radius:6px; border:none; color:#fff;
            background: var(--primary); cursor:pointer; transition:all 0.15s; text-decoration:none; line-height:1.5;
        }
        .btn-add-primary:hover { background: var(--primary-active); color:#fff; }

        /* ===== Filter Bar (same as admin) ===== */
        .page-filters-bar {
            background:#fff;
            border: 1px solid #e8e8e8;
            padding: 14px 24px;
            margin: 0 24px 18px 24px;
            border-radius: 4px !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .page-filters-bar .form-label {
            font-size:11px; font-weight:600; color:#8c8c8c; margin-bottom:4px;
            text-transform:uppercase; letter-spacing:0.4px;
        }
        .page-filters-bar .form-select,
        .page-filters-bar .form-control {
            font-size: var(--font-size-input); height:32px; padding:3px 10px;
            border-radius:4px !important; border:1px solid #d9d9d9; color:#334155;
            background:#fff; transition:border-color 0.15s;
        }
        .page-filters-bar .form-select:focus,
        .page-filters-bar .form-control:focus {
            border-color: var(--primary); box-shadow:0 0 0 2px var(--primary-transparent-10);
        }
        .page-filters-bar .btn-search {
            height:32px; padding:0 12px; font-size:13px;
            border-radius:0 4px 4px 0 !important; background: var(--primary);
            color:#fff; border:none; cursor:pointer; transition:background 0.15s;
        }
        .page-filters-bar .btn-search:hover { background: var(--primary-active); }
        .page-filters-bar .input-group .form-control { border-radius:4px 0 0 4px !important; border-right:none; }

        /* ===== Content Area (same as admin) ===== */
        .page-content-area { padding: 0 24px 24px 24px; flex:1; }

        /* ===== Data Table Card (same as admin) ===== */
        .data-table-card {
            background:#fff; border:1px solid #e8e8e8; border-radius:8px;
            overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.05);
        }
        .data-table-card-header {
            padding:12px 16px; border-bottom:1px solid #f0f0f0;
            display:flex; align-items:center; justify-content:space-between; background:#fff;
        }
        .data-table-card-header h6 {
            font-size:14px; font-weight:700; color:#1e293b; margin:0;
        }

        /* ===== Table (same as admin) ===== */
        .table-stockifly { width:100%; border-collapse:collapse; background:#fff; font-size: var(--font-size-table-body); }
        .table-stockifly thead tr th {
            background: var(--primary) !important;
            color:#fff !important; font-size: var(--font-size-table-header) !important;
            font-weight:700 !important; text-transform:uppercase !important;
            letter-spacing:0.5px !important; padding:6px 12px !important;
            border-bottom:1px solid #e8e8e8 !important;
            border-right:1px solid rgba(255,255,255,0.15) !important; white-space:nowrap;
        }
        .table-stockifly thead tr th:last-child { border-right:none !important; }
        .table-stockifly tbody tr td {
            padding:5px 12px !important; border-bottom:1px solid #f0f0f0 !important;
            border-right:1px solid #f0f0f0 !important;
            font-size: var(--font-size-table-body) !important;
            color:#333 !important; vertical-align:middle; white-space:nowrap; background:#fff;
        }
        .table-stockifly tbody tr td:last-child { border-right:none !important; }
        .table-stockifly tbody tr:nth-child(even) td { background: var(--primary-transparent-10) !important; }
        .table-stockifly tbody tr:hover td { background: var(--primary-bg) !important; }
        .table-stockifly tbody tr:last-child td { border-bottom:none !important; }

        /* ===== KPI Cards (same as admin) ===== */
        .kpi-card {
            background:#fff; border:1px solid #e8e8e8; border-radius:8px;
            padding:16px 20px; box-shadow:0 4px 20px rgba(0,0,0,0.05);
            position:relative; overflow:hidden; transition:transform 0.2s, box-shadow 0.2s;
        }
        .kpi-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.10); }
        .kpi-card .kpi-icon {
            width:42px; height:42px; border-radius:8px;
            display:flex; align-items:center; justify-content:center;
            font-size:18px; color:#fff; flex-shrink:0;
        }
        .kpi-card .kpi-value { font-size:22px; font-weight:800; color:#1e293b; margin:0; line-height:1.2; }
        .kpi-card .kpi-label { font-size:12px; color:#64748b; margin:2px 0 0; text-transform:uppercase; letter-spacing:0.5px; font-weight:600; }
        .kpi-card .kpi-growth-up   { font-size:12px; color:#28c76f; font-weight:700; margin-top:4px; }
        .kpi-card .kpi-growth-down { font-size:12px; color:#ea5455; font-weight:700; margin-top:4px; }
        .kpi-card .kpi-accent-bar  { position:absolute; bottom:0; left:0; right:0; height:3px; }

        /* ===== Form Card (same as admin) ===== */
        .form-card {
            background:#fff; border:1px solid #e8e8e8; border-radius:8px;
            padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.05);
        }
        .form-section-title {
            font-size:13px; font-weight:700; color: var(--primary);
            margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f0f0f0;
            text-transform:uppercase; letter-spacing:0.5px;
        }
        .form-card .form-label {
            font-size:11.5px; font-weight:600; color:#8c8c8c; margin-bottom:4px;
            text-transform:uppercase; letter-spacing:0.3px;
        }
        .form-card .form-control,
        .form-card .form-select {
            font-size: var(--font-size-input); height:34px; padding:4px 10px;
            border-radius:6px; border:1px solid #d9d9d9; color:#334155; transition:border-color 0.15s;
        }
        .form-card textarea.form-control { height:auto; }
        .form-card .form-control:focus,
        .form-card .form-select:focus {
            border-color: var(--primary); box-shadow:0 0 0 2px var(--primary-transparent-10);
        }
        .form-card .input-group-text {
            font-size:12px; font-weight:700; color:#595959; background:#fafafa;
            border:1px solid #d9d9d9; border-radius:6px 0 0 6px; padding:0 10px;
        }

        /* ===== Action Buttons in table rows ===== */
        .btn-table-action {
            display:inline-flex; align-items:center; gap:5px;
            padding:3px 10px; font-size:12px; font-weight:600;
            border-radius:5px; border:1.5px solid #d9d9d9; color:#595959;
            background:#fff; cursor:pointer; transition:all 0.15s; text-decoration:none; white-space:nowrap;
        }
        .btn-table-action:hover { border-color: var(--primary); color: var(--primary); }
        .btn-table-action.danger { border-color:#ff4d4f; color:#ff4d4f; }
        .btn-table-action.danger:hover { background:#ff4d4f; color:#fff; }
        .btn-table-action.primary { border-color: var(--primary); color: var(--primary); }
        .btn-table-action.primary:hover { background: var(--primary); color:#fff; }
        .btn-table-action.success { border-color:#52c41a; color:#52c41a; }
        .btn-table-action.success:hover { background:#52c41a; color:#fff; }

        /* ===== Status & Gateway badges ===== */
        .badge-status {
            font-size:11px; font-weight:700; padding:2px 10px;
            border-radius:10px; display:inline-block;
        }
        .badge-status.confirmed { background:rgba(40,199,111,0.12); color:#28c76f; }
        .badge-status.pending   { background:rgba(255,159,67,0.12);  color:#ff9f43; }
        .badge-status.cancelled { background:rgba(234,84,85,0.12);   color:#ea5455; }
        .badge-status.active    { background:rgba(24,144,255,0.10);  color:#1890ff; }
        .badge-gateway {
            font-size:11px; font-weight:600; padding:2px 8px; border-radius:4px;
            background:#f0f0f0; color:#595959; border:1px solid #e8e8e8;
        }

        /* ===== Live feed badge ===== */
        .live-feed-badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:600; color:#52c41a; }
        .live-feed-badge::before { content:''; width:6px; height:6px; background:#52c41a; border-radius:50%; animation:pulse-dot 1.5s infinite; }
        @keyframes pulse-dot { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:0.5; transform:scale(1.4); } }

        /* ===== Alerts ===== */
        .admin-alert { padding:10px 16px; border-radius:6px; font-size:13px; font-weight:500; margin-bottom:16px; border:1px solid transparent; }
        .admin-alert.success { background:rgba(40,199,111,0.08); border-color:rgba(40,199,111,0.3); color:#15803d; }
        .admin-alert.error   { background:rgba(234,84,85,0.08);  border-color:rgba(234,84,85,0.3);  color:#dc2626; }
        .admin-alert.info    { background:rgba(24,144,255,0.08); border-color:rgba(24,144,255,0.3); color:#1890ff; }

        /* ===== Property grid card ===== */
        .property-grid-card {
            background:#fff; border:1px solid #e8e8e8; border-radius:8px;
            overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.05);
            transition:transform 0.2s, box-shadow 0.2s;
        }
        .property-grid-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.10); }
        .property-grid-card img { width:100%; height:150px; object-fit:cover; }
        .property-grid-card-body { padding:12px; }

        /* ===== Plan cards ===== */
        .plan-card {
            background:#fff; border:1px solid #e8e8e8; border-radius:8px;
            padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.05);
            transition:transform 0.2s, box-shadow 0.2s; position:relative; height:100%;
            display:flex; flex-direction:column;
        }
        .plan-card:hover { transform:translateY(-3px); box-shadow:0 12px 30px rgba(0,0,0,0.12); }
        .plan-card.popular { border:2px solid var(--primary); }
        .plan-popular-badge {
            position:absolute; top:0; right:0;
            background: var(--primary); color:#fff;
            font-size:10px; font-weight:700; padding:4px 12px;
            border-radius:0 6px 0 6px; letter-spacing:0.5px;
        }

        /* ===== Mobile ===== */
        @media (max-width: 991.98px) {
            #vendorSidebar { transform:translateX(-100%); }
            #vendorSidebar.show-mobile { transform:translateX(0); }
            #vendorContent { margin-left:0 !important; }
            .btn-mobile-toggle { display:flex !important; }
            .page-content-area { padding:16px; }
            .page-header-card { padding:12px 16px; }
            .page-filters-bar { padding:10px 16px; }
            .admin-topbar { padding:0 16px; }
        }
        #sbBackdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1030; }
    </style>
    @yield('head')
</head>
<body>
    <div id="sbBackdrop" onclick="toggleSidebar()"></div>

    <!-- ===================== VENDOR SIDEBAR ===================== -->
    <aside id="vendorSidebar">
        <div class="sb-brand">
            <div class="sb-brand-icon"><i class="fa-solid fa-hotel"></i></div>
            <div>
                <span class="sb-brand-title">VENDOR PORTAL</span>
                <span class="sb-brand-subtitle">Hotel Partner Dashboard</span>
            </div>
            <button onclick="toggleSidebar()" class="ms-auto d-lg-none"
                style="background:none;border:none;color:rgba(255,255,255,0.5);font-size:16px;cursor:pointer;padding:4px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav style="padding:8px 0; flex:1; overflow-y:auto;">
            <div class="sb-section-header">My Properties &amp; Inventory</div>
            <a href="{{ route('vendor.dashboard') }}" class="sb-nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> <span>Vendor Overview</span>
            </a>
            <a href="{{ route('vendor.availability.index') }}" class="sb-nav-item {{ request()->routeIs('vendor.availability.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-days"></i> <span>Rates &amp; Calendar</span>
            </a>
            <a href="{{ route('vendor.properties.create') }}" class="sb-nav-item {{ request()->routeIs('vendor.properties.create') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-plus"></i> <span>Add New Property</span>
            </a>

            <div class="sb-section-header">Finance &amp; Payouts</div>
            <a href="{{ route('vendor.payouts.index') }}" class="sb-nav-item {{ request()->routeIs('vendor.payouts.*') ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i> <span>Earnings &amp; Payouts</span>
            </a>

            <div class="sb-section-header">Marketing &amp; Content</div>
            <a href="{{ route('vendor.packages.index') }}" class="sb-nav-item {{ request()->routeIs('vendor.packages.*') ? 'active' : '' }}">
                <i class="fa-solid fa-suitcase-rolling"></i> <span>My Tour Packages</span>
            </a>
            <a href="{{ route('vendor.reviews.index') }}" class="sb-nav-item {{ request()->routeIs('vendor.reviews.*') ? 'active' : '' }}">
                <i class="fa-solid fa-star"></i> <span>Guest Reviews</span>
            </a>

            <div class="sb-section-header">Subscription</div>
            <a href="{{ route('vendor.plans.index') }}" class="sb-nav-item {{ request()->routeIs('vendor.plans.index') ? 'active' : '' }}">
                <i class="fa-solid fa-crown" style="color:#fa8c16;"></i> <span>SaaS Plans &amp; Billing</span>
            </a>

            <div class="sb-section-header">Live Site</div>
            <a href="{{ route('home') }}" target="_blank" class="sb-nav-item" style="color:rgba(80,210,255,0.8);">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> <span>View Public Site</span>
            </a>
        </nav>
    </aside>

    <!-- ===================== MAIN CONTENT ===================== -->
    <div id="vendorContent">
        <!-- Top Bar -->
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <button class="btn-mobile-toggle" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <span class="engine-badge"><i class="fa-solid fa-hotel me-1"></i> VENDOR PARTNER PORTAL</span>
            </div>
            <div class="admin-topbar-right">
                <div style="display:flex;align-items:center;gap:8px;padding-right:12px;border-right:1px solid #f0f0f0;">
                    <img src="https://ui-avatars.com/api/?name=Vendor+Partner&background=fa8c16&color=fff&size=64" class="topbar-avatar" alt="Vendor">
                    <div class="d-none d-sm-block">
                        <span class="topbar-user-name">Verified Hotel Partner</span>
                        <span class="topbar-user-role">Vendor Account</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-signout">
                        <i class="fa-solid fa-right-from-bracket"></i> Sign Out
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main style="flex:1;">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sb = document.getElementById('vendorSidebar');
            const bd = document.getElementById('sbBackdrop');
            sb.classList.toggle('show-mobile');
            bd.style.display = sb.classList.contains('show-mobile') ? 'block' : 'none';
        }
    </script>
    @yield('scripts')
</body>
</html>

