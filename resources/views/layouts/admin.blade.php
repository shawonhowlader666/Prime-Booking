<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PRIME BOOKING Admin Panel')</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ============================================================
         * CSS Variables — Exact match of Stockifly-SaaS app.css :root
         * ============================================================ */
        :root {
            --primary:                #1890ff;
            --primary-hover:          #40a9ff;
            --primary-active:         #096dd9;
            --primary-bg:             #e6f7ff;
            --primary-bg-hover:       #bae7ff;
            --primary-shadow:         rgba(24, 144, 255, 0.20);
            --primary-transparent-10: rgba(24, 144, 255, 0.10);
            --primary-transparent-20: rgba(24, 144, 255, 0.20);

            /* Stockifly layout vars */
            --sidebar-width:          250px;
            --admin-body-bg:          #f0f2f5;
            --admin-card-border:      #e8e8e8;

            /* Stockifly font scale — exact from app.css */
            --font-main:              'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-heading:           'Plus Jakarta Sans', sans-serif;
            --font-size-base:         13px;
            --font-size-btn:          13px;
            --font-size-input:        13px;
            --font-size-sidebar:      13px;
            --font-size-card-title:   14px;
            --font-size-card-body:    13px;
            --font-size-table-header: 12px;
            --font-size-table-body:   13px;
            --font-size-page-header:  19px;
        }

        /* ============================================================
         * Base — exact body style from Stockifly
         * ============================================================ */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: var(--font-main);
            background-color: var(--admin-body-bg);
            color: #333333;
            font-size: var(--font-size-base);
            margin: 0;
        }

        /* ============================================================
         * Sidebar — Stockifly dark sidebar #001529
         * ============================================================ */
        #stockiflySidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background-color: #001529;
            color: rgba(255,255,255,0.85);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            overflow-y: auto;
            transition: transform 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        /* Sidebar brand bar */
        .sb-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            background-color: #002140;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }
        .sb-brand-icon {
            width: 32px;
            height: 32px;
            background-color: #1890ff;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 15px;
            flex-shrink: 0;
        }
        .sb-brand-title {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
            display: block;
        }
        .sb-brand-subtitle {
            font-size: 10px;
            color: rgba(255,255,255,0.45);
            display: block;
        }

        /* Sidebar section header */
        .sb-section-header {
            font-size: 11px;
            font-weight: 700;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 16px 16px 5px;
        }

        /* Sidebar nav item — exact Stockifly ant-menu-item style */
        .sb-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-weight: 500;
            font-size: var(--font-size-sidebar);
            transition: background-color 0.15s ease, color 0.15s ease;
            border-radius: 0;
            cursor: pointer;
            line-height: 1.4;
        }
        .sb-nav-item i { width: 16px; text-align: center; font-size: 14px; flex-shrink: 0; }
        .sb-nav-item:hover {
            color: #ffffff;
            background-color: rgba(24,144,255,0.12);
        }
        .sb-nav-item.active {
            color: #ffffff;
            background-color: var(--primary);
        }
        .sb-nav-item.active i { color: #ffffff; }

        /* ============================================================
         * Content Area
         * ============================================================ */
        #stockiflyContent {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s ease;
        }

        /* ============================================================
         * Top Header Bar — exact Stockifly header height & style
         * ============================================================ */
        .admin-topbar {
            background: #ffffff;
            border-bottom: 1px solid #f0f0f0;
            padding: 0 24px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-shrink: 0;
        }
        .admin-topbar-left { display: flex; align-items: center; gap: 12px; }
        .admin-topbar-right { display: flex; align-items: center; gap: 12px; }

        .engine-badge {
            background: rgba(24,144,255,0.08);
            color: #1890ff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 4px;
            border: 1px solid rgba(24,144,255,0.2);
            letter-spacing: 0.3px;
        }

        .topbar-user-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #1e293b;
            display: block;
            line-height: 1.2;
        }
        .topbar-user-role {
            font-size: 10.5px;
            color: #8c8c8c;
            display: block;
        }
        .topbar-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f0f0f0;
        }
        .btn-signout {
            font-size: 12px;
            font-weight: 600;
            padding: 5px 14px;
            border-radius: 6px;
            border: 1.5px solid #ff4d4f;
            color: #ff4d4f;
            background: #fff;
            transition: all 0.15s;
            cursor: pointer;
            line-height: 1.5;
        }
        .btn-signout:hover {
            background: #ff4d4f;
            color: #fff;
        }

        .btn-mobile-toggle {
            background: none;
            border: 1px solid #d9d9d9;
            border-radius: 6px;
            width: 32px;
            height: 32px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #595959;
            font-size: 14px;
        }

        /* ============================================================
         * Page Header (AdminPageHeader) — exact .page-content-sub-header
         * from Stockifly app.css — white bg, left blue border accent
         * ============================================================ */
        .page-header-card {
            background: #ffffff;
            border-bottom: 1px solid #f0f0f0;
            padding: 14px 24px;
            border-left: 3px solid var(--primary);
        }
        .page-breadcrumb {
            font-size: 12px;
            color: #8c8c8c;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }
        .page-breadcrumb a {
            color: #595959;
            text-decoration: none;
            transition: color 0.15s;
        }
        .page-breadcrumb a:hover { color: var(--primary); }
        .page-breadcrumb .sep { color: #d9d9d9; margin: 0 2px; }
        .page-title {
            font-size: var(--font-size-page-header);
            font-weight: 800;
            color: #1e293b;
            font-family: var(--font-heading);
            margin: 0;
            line-height: 1.3;
        }

        /* Page header action buttons — exact Stockifly ExportTable button style */
        .btn-export-csv {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            font-size: 12.5px;
            font-weight: 600;
            border-radius: 6px;
            border: 1.5px solid #52c41a;
            color: #52c41a;
            background: #fff;
            cursor: pointer;
            transition: all 0.15s;
            line-height: 1.5;
            text-decoration: none;
        }
        .btn-export-csv:hover {
            background: #52c41a;
            color: #fff;
        }
        .btn-export-pdf {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            font-size: 12.5px;
            font-weight: 600;
            border-radius: 6px;
            border: 1.5px solid var(--primary);
            color: #ffffff;
            background: var(--primary);
            cursor: pointer;
            transition: all 0.15s;
            line-height: 1.5;
            text-decoration: none;
        }
        .btn-export-pdf:hover { background: var(--primary-active); border-color: var(--primary-active); }

        .btn-add-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 16px;
            font-size: 12.5px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            color: #ffffff;
            background: var(--primary);
            cursor: pointer;
            transition: all 0.15s;
            line-height: 1.5;
            text-decoration: none;
        }
        .btn-add-primary:hover { background: var(--primary-active); color: #fff; }

        /* ============================================================
         * Filter Bar — exact .admin-page-filters-wrapper from Stockifly
         * padding: 10px 16px, bg: white
         * ============================================================ */
        .page-filters-bar {
            background: #ffffff;
            border-bottom: 1px solid #f0f0f0;
            padding: 10px 24px;
        }
        .page-filters-bar .form-label {
            font-size: 11px;
            font-weight: 600;
            color: #8c8c8c;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .page-filters-bar .form-select,
        .page-filters-bar .form-control {
            font-size: var(--font-size-input);
            height: 32px;
            padding: 3px 10px;
            border-radius: 6px;
            border: 1px solid #d9d9d9;
            color: #334155;
            background-color: #ffffff;
            transition: border-color 0.15s;
        }
        .page-filters-bar .form-select:focus,
        .page-filters-bar .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary-transparent-10);
        }
        .page-filters-bar .btn-search {
            height: 32px;
            padding: 0 12px;
            font-size: 13px;
            border-radius: 0 6px 6px 0;
            background: var(--primary);
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
        }
        .page-filters-bar .btn-search:hover { background: var(--primary-active); }
        .page-filters-bar .input-group .form-control { border-radius: 6px 0 0 6px; }

        /* ============================================================
         * Page Content Area (admin-page-table-content)
         * padding: 24px — exact Stockifly spacing
         * ============================================================ */
        .page-content-area {
            padding: 24px;
            flex: 1;
        }

        /* ============================================================
         * Table Card — exact Stockifly .table-card / .main-table-card
         * border: 1px solid #e8e8e8, border-radius: 8px, shadow: 0 4px 20px rgba(0,0,0,0.05)
         * ============================================================ */
        .data-table-card {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .data-table-card-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }
        .data-table-card-header h6 {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            font-family: var(--font-heading);
        }

        /* ============================================================
         * Table — exact Stockifly #app .ant-table style
         * ============================================================ */
        .table-stockifly {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            font-size: var(--font-size-table-body);
        }
        /* thead — exact from Stockifly: background: var(--primary), font 12px, uppercase */
        .table-stockifly thead tr th {
            background: var(--primary) !important;
            color: #ffffff !important;
            font-size: var(--font-size-table-header) !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            padding: 6px 12px !important;
            border-bottom: 1px solid #e8e8e8 !important;
            border-right: 1px solid rgba(255,255,255,0.15) !important;
            white-space: nowrap;
        }
        .table-stockifly thead tr th:last-child {
            border-right: none !important;
        }
        /* tbody — exact: padding 5px 12px, border-bottom: 1px solid #f0f0f0 */
        .table-stockifly tbody tr td {
            padding: 5px 12px !important;
            border-bottom: 1px solid #f0f0f0 !important;
            border-right: 1px solid #f0f0f0 !important;
            font-size: var(--font-size-table-body) !important;
            color: #333333 !important;
            vertical-align: middle;
            white-space: nowrap;
            background: #ffffff;
        }
        .table-stockifly tbody tr td:last-child {
            border-right: none !important;
        }
        /* Striped even rows — exact: var(--primary-transparent-10) */
        .table-stockifly tbody tr:nth-child(even) td {
            background: var(--primary-transparent-10) !important;
        }
        /* Row hover — exact: var(--primary-bg) = #e6f7ff */
        .table-stockifly tbody tr:hover td {
            background: var(--primary-bg) !important;
        }
        .table-stockifly tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* ============================================================
         * KPI Stats Cards — exact Stockifly StateWidget
         * ============================================================ */
        .kpi-card {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 16px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.10);
        }
        .kpi-card .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #ffffff;
            flex-shrink: 0;
        }
        .kpi-card .kpi-value {
            font-size: 22px;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            line-height: 1.2;
            font-family: var(--font-heading);
        }
        .kpi-card .kpi-label {
            font-size: 12px;
            color: #64748b;
            margin: 2px 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .kpi-card .kpi-growth-up {
            font-size: 12px;
            color: #28c76f;
            font-weight: 700;
            margin-top: 4px;
        }
        .kpi-card .kpi-growth-down {
            font-size: 12px;
            color: #ea5455;
            font-weight: 700;
            margin-top: 4px;
        }
        .kpi-card .kpi-accent-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        /* ============================================================
         * Form Card — for create/edit pages
         * ============================================================ */
        .form-card {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .form-section-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-card .form-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #8c8c8c;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .form-card .form-control,
        .form-card .form-select {
            font-size: var(--font-size-input);
            height: 34px;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #d9d9d9;
            color: #334155;
            transition: border-color 0.15s;
        }
        .form-card textarea.form-control { height: auto; }
        .form-card .form-control:focus,
        .form-card .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary-transparent-10);
        }
        .form-card .input-group-text {
            font-size: 12px;
            font-weight: 700;
            color: #595959;
            background: #fafafa;
            border: 1px solid #d9d9d9;
            border-radius: 6px 0 0 6px;
            padding: 0 10px;
        }

        /* Action Buttons in table rows */
        .btn-table-action {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 5px;
            border: 1.5px solid #d9d9d9;
            color: #595959;
            background: #fff;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-table-action:hover { border-color: var(--primary); color: var(--primary); }
        .btn-table-action.danger { border-color: #ff4d4f; color: #ff4d4f; }
        .btn-table-action.danger:hover { background: #ff4d4f; color: #fff; }
        .btn-table-action.primary { border-color: var(--primary); color: var(--primary); }
        .btn-table-action.primary:hover { background: var(--primary); color: #fff; }
        .btn-table-action.success { border-color: #52c41a; color: #52c41a; }
        .btn-table-action.success:hover { background: #52c41a; color: #fff; }

        /* Status badges */
        .badge-status {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 10px;
            display: inline-block;
        }
        .badge-status.confirmed { background: rgba(40,199,111,0.12); color: #28c76f; }
        .badge-status.pending   { background: rgba(255,159,67,0.12); color: #ff9f43; }
        .badge-status.cancelled { background: rgba(234,84,85,0.12); color: #ea5455; }
        .badge-status.active    { background: rgba(24,144,255,0.10); color: #1890ff; }

        /* Payment gateway badge */
        .badge-gateway {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            background: #f0f0f0;
            color: #595959;
            border: 1px solid #e8e8e8;
        }

        /* Order Ref link */
        .order-ref-link {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            font-size: 13px;
        }
        .order-ref-link:hover { color: var(--primary-active); text-decoration: underline; }
        .order-date {
            font-size: 11px;
            color: #8c8c8c;
            display: block;
            margin-top: 1px;
        }

        /* Real-time feed badge */
        .live-feed-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            color: #52c41a;
        }
        .live-feed-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #52c41a;
            border-radius: 50%;
            animation: pulse-dot 1.5s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%  { opacity: 0.5; transform: scale(1.4); }
        }

        /* ============================================================
         * Mobile Responsive
         * ============================================================ */
        @media (max-width: 991.98px) {
            #stockiflySidebar { transform: translateX(-100%); }
            #stockiflySidebar.show-mobile { transform: translateX(0); }
            #stockiflyContent { margin-left: 0 !important; }
            .btn-mobile-toggle { display: flex !important; }
            .page-content-area { padding: 16px; }
            .page-header-card { padding: 12px 16px; }
            .page-filters-bar { padding: 10px 16px; }
            .admin-topbar { padding: 0 16px; }
            .kpi-card .kpi-value { font-size: 18px; }
        }

        /* Sidebar overlay backdrop */
        #sbBackdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1030;
        }

        /* Smooth hover transitions */
        .form-control, .form-select, .btn-table-action,
        .btn-export-csv, .btn-export-pdf, .btn-add-primary,
        .btn-signout, .kpi-card, .data-table-card {
            transition: all 0.15s ease;
        }

        /* Alert messages */
        .admin-alert {
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
            border: 1px solid transparent;
        }
        .admin-alert.success { background: rgba(40,199,111,0.08); border-color: rgba(40,199,111,0.3); color: #15803d; }
        .admin-alert.error   { background: rgba(234,84,85,0.08); border-color: rgba(234,84,85,0.3); color: #dc2626; }
        .admin-alert.info    { background: rgba(24,144,255,0.08); border-color: rgba(24,144,255,0.3); color: #1890ff; }
    </style>
    @yield('head')
</head>
<body>

    <!-- Mobile backdrop -->
    <div id="sbBackdrop" onclick="toggleSidebar()"></div>

    <!-- =====================================================
         LEFT SIDEBAR
         ===================================================== -->
    <aside id="stockiflySidebar">

        <!-- Brand Bar -->
        <div class="sb-brand">
            <div class="sb-brand-icon">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div>
                <span class="sb-brand-title">PRIME BOOKING</span>
                <span class="sb-brand-subtitle">OTA Travel &amp; Hospitality</span>
            </div>
            <button onclick="toggleSidebar()" class="ms-auto d-lg-none" style="background:none;border:none;color:rgba(255,255,255,0.5);font-size:16px;cursor:pointer;padding:4px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Nav Menu -->
        <nav style="padding: 8px 0; flex: 1; overflow-y: auto;">

            <div class="sb-section-header">Main Dashboard</div>
            <a href="{{ route('admin.dashboard') }}" class="sb-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> <span>Sales &amp; Summary</span>
            </a>

            <div class="sb-section-header">Reservations</div>
            <a href="{{ route('admin.bookings.index') }}" class="sb-nav-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i> <span>All Bookings</span>
            </a>

            <div class="sb-section-header">Properties &amp; Stock</div>
            <a href="{{ route('admin.properties.index') }}" class="sb-nav-item {{ request()->routeIs('admin.properties.index') ? 'active' : '' }}">
                <i class="fa-solid fa-hotel"></i> <span>Property Inventory</span>
            </a>
            <a href="{{ route('admin.properties.create') }}" class="sb-nav-item {{ request()->routeIs('admin.properties.create') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-plus"></i> <span>Add New Listing</span>
            </a>

            <div class="sb-section-header">Marketing &amp; Sales</div>
            <a href="{{ route('admin.promotions.index') }}" class="sb-nav-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bullhorn"></i> <span>Promotions Manager</span>
            </a>
            <a href="{{ route('admin.packages.index') }}" class="sb-nav-item {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                <i class="fa-solid fa-suitcase-rolling"></i> <span>Tour Packages</span>
            </a>
            <a href="{{ route('admin.deals.index') }}" class="sb-nav-item {{ request()->routeIs('admin.deals.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tag"></i> <span>Deals &amp; Offers</span>
            </a>
            <a href="{{ route('admin.coupons.index') }}" class="sb-nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                <i class="fa-solid fa-ticket"></i> <span>Promo Coupons</span>
            </a>

            <div class="sb-section-header">Finance &amp; Payouts</div>
            <a href="{{ route('admin.payouts.index') }}" class="sb-nav-item {{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}">
                <i class="fa-solid fa-hand-holding-dollar"></i> <span>Vendor Payouts</span>
            </a>

            <div class="sb-section-header">User &amp; Vendor Management</div>
            <a href="{{ route('admin.users.index') }}" class="sb-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> <span>User Accounts</span>
            </a>
            <a href="{{ route('admin.tenants.index') }}" class="sb-nav-item {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i> <span>Vendor Tenants</span>
            </a>

            <div class="sb-section-header">Website Content (CMS)</div>
            <a href="{{ route('admin.cms.index') }}" class="sb-nav-item {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i> <span>Website Pages CMS</span>
            </a>
            <a href="{{ route('admin.content.hero') }}" class="sb-nav-item {{ request()->routeIs('admin.content.hero') ? 'active' : '' }}">
                <i class="fa-solid fa-images"></i> <span>Hero Banner Slider</span>
            </a>
            <a href="{{ route('admin.destinations.index') }}" class="sb-nav-item {{ request()->routeIs('admin.destinations.*') ? 'active' : '' }}">
                <i class="fa-solid fa-map-location-dot"></i> <span>Featured Destinations</span>
            </a>
            <a href="{{ route('admin.amenities.index') }}" class="sb-nav-item {{ request()->routeIs('admin.amenities.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> <span>Amenities Catalog</span>
            </a>

            <div class="sb-section-header">Quality &amp; Inbox</div>
            <a href="{{ route('admin.reviews.index') }}" class="sb-nav-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <i class="fa-solid fa-star"></i> <span>Guest Reviews</span>
            </a>
            <a href="{{ route('admin.inquiries.index') }}" class="sb-nav-item {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope"></i> <span>Guest Inquiries</span>
            </a>

            <div class="sb-section-header">Settings &amp; Config</div>
            <a href="{{ route('admin.gateways.index') }}" class="sb-nav-item {{ request()->routeIs('admin.gateways.*') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card"></i> <span>Payment Gateways Vault</span>
            </a>
            <a href="{{ route('admin.site-settings.index') }}" class="sb-nav-item {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-sliders"></i> <span>Platform Settings</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="sb-nav-item {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> <span>Currency &amp; System</span>
            </a>

            <div class="sb-section-header">Security &amp; Audit</div>
            <a href="{{ route('admin.activity.index') }}" class="sb-nav-item {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved"></i> <span>Activity Log</span>
            </a>
            {{-- System Cache Flush --}}
            <form action="/admin/system/cache-clear" method="POST" style="padding:0 8px; margin:2px 0;">
                @csrf
                <button type="submit" class="sb-nav-item" style="width:100%; text-align:left; background:none; border:none; cursor:pointer;"
                    onclick="return confirm('Clear all Redis/file caches?')">
                    <i class="fa-solid fa-rotate" style="color:#ff9f43;"></i> <span style="color:rgba(255,255,255,0.7);">Flush Cache</span>
                </button>
            </form>

            <div class="sb-section-header">Live Site</div>
            <a href="{{ route('home') }}" target="_blank" class="sb-nav-item" style="color: rgba(80,210,255,0.8);">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> <span>Public Website</span>
            </a>

        </nav>

    </aside>

    <!-- =====================================================
         MAIN CONTENT
         ===================================================== -->
    <div id="stockiflyContent">

        <!-- Top Header Bar -->
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <button class="btn-mobile-toggle" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <span class="engine-badge">PRIME BOOKING ENGINE v1.0</span>
            </div>

            <div class="admin-topbar-right">
                <div style="display:flex; align-items:center; gap:8px; padding-right:12px; border-right:1px solid #f0f0f0;">
                    <img src="https://ui-avatars.com/api/?name=Shawon+Ahmed&background=1890ff&color=fff&size=64" class="topbar-avatar" alt="Admin">
                    <div class="d-none d-sm-block">
                        <span class="topbar-user-name">Shawon Ahmed</span>
                        <span class="topbar-user-role">Super Administrator</span>
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
        <main style="flex: 1;">
            @yield('content')
        </main>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sb = document.getElementById('stockiflySidebar');
            const bd = document.getElementById('sbBackdrop');
            sb.classList.toggle('show-mobile');
            bd.style.display = sb.classList.contains('show-mobile') ? 'block' : 'none';
        }
    </script>

    @yield('scripts')
</body>
</html>

