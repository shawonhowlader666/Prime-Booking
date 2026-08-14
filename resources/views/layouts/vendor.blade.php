<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vendor Partner Portal | PRIME BOOKING')</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --font-main:              'Barlow', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-heading:           'Barlow', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-size-base:         13px;
            --font-size-btn:          13px;
            --font-size-input:        13px;
            --font-size-sidebar:      13px;
            --font-size-table-header: 11.5px;
            --font-size-table-body:   13px;
            --font-size-page-header:  18px;
        }

        /* ============================================================
         * Medium & Fine Typography Rule (Crisp Medium Weight 500)
         * ============================================================ */
        body, button, input, select, textarea, th, td, h1, h2, h3, h4, h5, h6, label, span, p, a, div {
            font-weight: 500 !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .fw-bold, .fw-bolder, strong, b, .page-title, .kpi-value, .topbar-user-name, .engine-badge {
            font-weight: 600 !important;
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
        /* ===== Sidebar — Vendor teal accent instead of blue ===== */
        #vendorSidebar {
            width: var(--sidebar-width);
            height: 100vh;
            max-height: 100vh;
            background-color: #001529;
            color: rgba(255,255,255,0.85);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1040;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.25s ease, transform 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        /* ─── Desktop Sidebar Collapse Toggle ───────────────────────── */
        .btn-sidebar-collapse {
            background: none;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 4px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: rgba(255,255,255,0.6);
            font-size: 13px;
            transition: all 0.15s;
            flex-shrink: 0;
            margin-left: auto;
        }
        .btn-sidebar-collapse:hover { background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.3); }
        #vendorSidebar.sb-collapsed .btn-sidebar-collapse { margin: 0 auto; }

        /* Icon-only collapsed sidebar state */
        @media (min-width: 992px) {
            #vendorSidebar.sb-collapsed {
                width: 64px !important;
            }
            #vendorSidebar.sb-collapsed .sb-brand-text,
            #vendorSidebar.sb-collapsed .sb-section-header,
            #vendorSidebar.sb-collapsed .sb-nav-item span,
            #vendorSidebar.sb-collapsed .sb-nav-toggle span,
            #vendorSidebar.sb-collapsed .chevron-icon,
            #vendorSidebar.sb-collapsed .sb-sub-menu {
                display: none !important;
            }
            #vendorSidebar.sb-collapsed .sb-brand {
                padding: 14px 10px;
                justify-content: center;
            }
            #vendorSidebar.sb-collapsed .sb-nav-item,
            #vendorSidebar.sb-collapsed .sb-nav-toggle {
                padding: 10px 0;
                justify-content: center;
                position: relative;
            }
            #vendorSidebar.sb-collapsed .sb-nav-item i,
            #vendorSidebar.sb-collapsed .sb-nav-toggle i {
                margin: 0;
            }
            #vendorSidebar.sb-collapsed .sb-nav-item[data-label]:hover::after,
            #vendorSidebar.sb-collapsed .sb-nav-toggle[data-label]:hover::after {
                content: attr(data-label);
                position: absolute;
                left: 68px;
                top: 50%;
                transform: translateY(-50%);
                background: #001529;
                color: #fff;
                font-size: 12px;
                font-weight: 600;
                padding: 4px 10px;
                border-radius: 4px;
                white-space: nowrap;
                z-index: 1090;
                box-shadow: 0 4px 12px rgba(0,0,0,0.25);
                pointer-events: none;
            }
            #vendorContent.sb-collapsed-content {
                margin-left: 64px !important;
            }
        }

        /* Custom Scrollbar for Vendor Sidebar */
        #vendorSidebar::-webkit-scrollbar,
        #vendorSidebar nav::-webkit-scrollbar {
            width: 5px;
        }
        #vendorSidebar::-webkit-scrollbar-thumb,
        #vendorSidebar nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
        #vendorSidebar::-webkit-scrollbar-thumb:hover,
        #vendorSidebar nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        .sb-brand {
            display: flex; align-items: center; gap: 10px;
            padding: 0 16px;
            height: 56px; min-height: 56px; max-height: 56px;
            background-color: #002140;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            border-right: 1px solid rgba(255, 255, 255, 0.12);
            flex-shrink: 0;
            box-sizing: border-box;
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

        /* ===== Top bar — Dark Navy Blue #002140 & Equal 56px Height ===== */
        .admin-topbar {
            background-color: #002140;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            border-left: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0 20px;
            height: 56px; min-height: 56px; max-height: 56px;
            display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 100; flex-shrink: 0;
            box-sizing: border-box;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .admin-topbar-left { display:flex; align-items:center; gap:12px; }
        .admin-topbar-right { display:flex; align-items:center; gap:12px; }
        .engine-badge {
            background: rgba(250, 140, 22, 0.12); color: #ffc069;
            font-size: 11px; font-weight: 600; padding: 4px 12px;
            border-radius: 20px !important; border: 1px solid rgba(250, 140, 22, 0.3);
            letter-spacing: 0.2px; display: inline-flex; align-items: center; gap: 6px;
        }
        .engine-badge .pulse-dot {
            width: 6px; height: 6px; background-color: #fa8c16;
            border-radius: 50%; display: inline-block;
            box-shadow: 0 0 0 2px rgba(250, 140, 22, 0.25);
        }
        .topbar-user-name { font-size:13px; font-weight:600; color:#ffffff; display:block; line-height:1.2; }
        .topbar-user-role { font-size:11px; color:rgba(255,255,255,0.55); display:block; margin-top:1px; }
        .topbar-avatar { width:32px; height:32px; border-radius:50%; object-fit:cover; border:1.5px solid rgba(255,255,255,0.25); }
        .btn-signout {
            font-size: 12px; font-weight: 600; padding: 4px 12px; height: 30px;
            border-radius: 4px !important; border: 1px solid rgba(239, 68, 68, 0.6) !important;
            color: #fca5a5 !important; background: rgba(239, 68, 68, 0.12) !important;
            transition: all 0.15s ease-in-out; cursor: pointer;
            display: inline-flex; align-items: center; gap: 5px;
            outline: none !important; box-shadow: none !important; text-decoration: none; line-height: 1;
        }
        .btn-signout:hover {
            background: #ef4444 !important; color: #ffffff !important;
            border-color: #ef4444 !important; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3) !important;
        }
        .btn-mobile-toggle {
            background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 6px;
            width: 32px; height: 32px; display: none; align-items: center;
            justify-content: center; cursor: pointer; color: #ffffff; font-size: 14px;
        }
        .topbar-global-search-input {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            height: 34px;
            font-size: 12.5px;
            padding-left: 34px !important;
            padding-right: 12px !important;
            border-radius: 4px !important;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }
        .topbar-global-search-input::placeholder { color: rgba(255, 255, 255, 0.5) !important; font-weight: 400; }
        .topbar-global-search-input:focus {
            background: rgba(255, 255, 255, 0.14) !important;
            border-color: #fa8c16 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 2px rgba(250, 140, 22, 0.25) !important;
            outline: none !important;
        }

        /* ===== Page Header — Full-width attached seamlessly below navbar ===== */
        .page-header-card {
            background: #ffffff;
            border-bottom: 1px solid #e8e8e8;
            border-top: none;
            border-left: none;
            border-right: none;
            padding: 14px 24px;
            margin: 0 0 20px 0;
            width: 100%;
            border-radius: 0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
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

        /* ============================================================
         * Stockifly SaaS Data Table Toolbar & Action Buttons (Vendor)
         * ============================================================ */
        .saas-table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 12px 20px;
            background: #ffffff;
            border-bottom: 1px solid #e8e8e8;
            border-radius: 4px 4px 0 0 !important;
        }

        .saas-toolbar-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn-tbl-copy {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; font-size: 12px; font-weight: 600;
            border-radius: 4px !important; border: 1.5px solid #d9d9d9;
            color: #595959; background: #fff; cursor: pointer;
            transition: all 0.15s; line-height: 1.5; white-space: nowrap; text-decoration: none;
        }
        .btn-tbl-copy:hover { background: #f5f5f5; border-color: #bfbfbf; color: #262626; }

        .btn-tbl-excel {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; font-size: 12px; font-weight: 600;
            border-radius: 4px !important; border: 1.5px solid #52c41a;
            color: #52c41a; background: #fff; cursor: pointer;
            transition: all 0.15s; line-height: 1.5; white-space: nowrap; text-decoration: none;
        }
        .btn-tbl-excel:hover { background: #52c41a; color: #fff; }

        .btn-export-csv {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; font-size: 12px; font-weight: 600;
            border-radius: 4px !important; border: 1.5px solid #13c2c2;
            color: #13c2c2; background: #fff; cursor: pointer;
            transition: all 0.15s; line-height: 1.5; white-space: nowrap; text-decoration: none;
        }
        .btn-export-csv:hover { background: #13c2c2; color: #fff; }

        .btn-export-pdf {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; font-size: 12px; font-weight: 600;
            border-radius: 4px !important; border: 1.5px solid #ff4d4f;
            color: #ff4d4f; background: #fff; cursor: pointer;
            transition: all 0.15s; line-height: 1.5; white-space: nowrap; text-decoration: none;
        }
        .btn-export-pdf:hover { background: #ff4d4f; color: #fff; }

        .btn-tbl-print {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; font-size: 12px; font-weight: 600;
            border-radius: 4px !important; border: 1.5px solid #722ed1;
            color: #722ed1; background: #fff; cursor: pointer;
            transition: all 0.15s; line-height: 1.5; white-space: nowrap; text-decoration: none;
        }
        .btn-tbl-print:hover { background: #722ed1; color: #fff; }

        .btn-tbl-col {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; font-size: 12px; font-weight: 600;
            border-radius: 4px !important; border: 1.5px solid #8c8c8c;
            color: #595959; background: #fff; cursor: pointer;
            transition: all 0.15s; line-height: 1.5; white-space: nowrap; text-decoration: none;
        }
        .btn-tbl-col:hover { background: #f5f5f5; border-color: #595959; color: #262626; }
        .btn-tbl-col.active-col { border-color: var(--primary); color: var(--primary); background: #e6f7ff; }

        .btn-tbl-select {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; font-size: 12px; font-weight: 600;
            border-radius: 4px !important; border: 1.5px solid #fa8c16;
            color: #fa8c16; background: #fff; cursor: pointer;
            transition: all 0.15s; line-height: 1.5; white-space: nowrap; text-decoration: none;
        }
        .btn-tbl-select:hover { background: #fa8c16; color: #fff; }
        .btn-tbl-select.is-selecting { background: #fa8c16; color: #fff; }

        .btn-add-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 14px; font-size: 12px; font-weight: 600;
            border-radius: 4px !important; border: none;
            color: #ffffff; background: var(--primary); cursor: pointer;
            transition: all 0.15s; line-height: 1.5; text-decoration: none;
        }
        .btn-add-primary:hover { background: var(--primary-active); color: #fff; }

        .btn-tbl-gear {
            display: inline-flex; align-items: center; justify-content: center;
            width: 26px; height: 26px; padding: 0; font-size: 12px;
            border-radius: 4px !important; border: 1px solid #d9d9d9;
            color: #595959; background: #fff; cursor: pointer;
            transition: all 0.15s; line-height: 1; vertical-align: middle;
        }
        .btn-tbl-gear:hover { background: #f5f5f5; border-color: #bfbfbf; color: var(--primary); }
        .btn-tbl-gear.active-col { border-color: var(--primary); color: var(--primary); background: #e6f7ff; }

        .col-vis-dropdown {
            position: absolute; top: calc(100% + 6px); right: 0; z-index: 1090;
            background: #fff; border: 1px solid #e8e8e8;
            border-radius: 4px !important; box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            min-width: 190px; padding: 6px 0; text-align: left; font-weight: normal;
        }
        .col-vis-item {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 14px; font-size: 12.5px; color: #333;
            cursor: pointer; user-select: none;
        }
        .col-vis-item:hover { background: #f5f5f5; }
        .col-vis-item input[type=checkbox] { accent-color: var(--primary); width: 14px; height: 14px; }

        tr.row-selected td { background: #e6f7ff !important; }
        .tbl-select-checkbox { accent-color: var(--primary); width: 15px; height: 15px; cursor: pointer; vertical-align: middle; }

        .tbl-search-wrap { position: relative; display: flex; align-items: center; }
        .tbl-search-input {
            height: 32px; padding: 4px 12px 4px 30px; font-size: 12px;
            border-radius: 4px !important; border: 1px solid #d9d9d9;
            color: #334155; background: #ffffff; width: 220px; transition: all 0.15s;
        }
        .tbl-search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 2px var(--primary-transparent-10); outline: none; }
        .tbl-search-icon { position: absolute; left: 10px; z-index: 2; color: #bfbfbf; font-size: 12px; pointer-events: none; }

        .page-filters-bar {
            background:#fff;
            border: 1px solid #e8e8e8;
            padding: 14px 20px;
            margin-bottom: 18px;
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
        .data-table-card,
        .stockifly-card {
            background:#fff; border:1px solid #e8e8e8; border-radius:4px !important;
            box-shadow:0 4px 20px rgba(0,0,0,0.05); min-height:220px;
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
            color:#fff !important; font-size: 11.5px !important;
            font-weight:600 !important; text-transform:uppercase !important;
            letter-spacing:0.3px !important; padding:6px 12px !important;
            border-bottom:1px solid #e8e8e8 !important;
            border-right:1px solid rgba(255,255,255,0.15) !important; white-space:nowrap;
        }
        .table-stockifly thead tr th:last-child { border-right:none !important; }
        .table-stockifly tbody tr td {
            padding:5px 12px !important; border-bottom:1px solid #f0f0f0 !important;
            border-right:1px solid #f0f0f0 !important;
            font-size: var(--font-size-table-body) !important;
            color:#1e293b !important; font-weight:500 !important; vertical-align:middle; white-space:nowrap; background:#fff;
        }
        .table-stockifly tbody tr td:last-child { border-right:none !important; }
        .table-stockifly tbody tr:nth-child(even) td { background: var(--primary-transparent-10) !important; }
        .table-stockifly tbody tr:hover td { background: var(--primary-bg) !important; }
        .table-stockifly tbody tr:last-child td { border-bottom:none !important; }

        /* ===== KPI Cards (same as admin) ===== */
        .kpi-card {
            background:#fff; border:1px solid #e8e8e8; border-radius:6px !important;
            padding:14px 16px; box-shadow:0 1px 3px rgba(0,0,0,0.03);
            position:relative; overflow:hidden; transition:transform 0.15s, box-shadow 0.15s;
            height:100%; display:flex; flex-direction:column; justify-content:space-between;
        }
        .kpi-card:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(0,0,0,0.08); }
        .kpi-card .kpi-icon {
            width:36px; height:36px; border-radius:6px;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; color:#fff; flex-shrink:0;
        }
        .kpi-card .kpi-value { font-size:17px; font-weight:700; color:#0f172a; margin:0; line-height:1.25; letter-spacing:-0.2px; }
        .kpi-card .kpi-label { font-size:11px; color:#64748b; margin:2px 0 0; text-transform:uppercase; letter-spacing:0.3px; font-weight:600; min-height:28px; display:flex; align-items:center; line-height:1.3; }
        .kpi-card .kpi-growth-up   { font-size:11px; color:#28c76f; font-weight:600; margin-top:6px; line-height:1.2; }
        .kpi-card .kpi-growth-down { font-size:11px; color:#ea5455; font-weight:600; margin-top:6px; line-height:1.2; }
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

        /* ============================================================
         * Action Gear Hover Dropdown System (Stockifly Style)
         * ============================================================ */
        .action-gear-dropdown { position: relative; display: inline-block; }
        .action-gear-btn {
            width: 32px; height: 32px; padding: 0; border-radius: 4px !important;
            background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1 !important;
            display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.15s ease;
        }
        .action-gear-btn:hover,
        .action-gear-dropdown:hover .action-gear-btn {
            background: var(--primary) !important; color: #ffffff !important;
            border-color: var(--primary) !important; box-shadow: 0 2px 8px rgba(32,103,225,0.25);
        }
        .action-gear-dropdown:hover .dropdown-menu { display: block !important; margin-top: 0; }
        .action-gear-dropdown .dropdown-menu {
            min-width: 180px; padding: 4px 0; border-radius: 4px !important; border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            background: #ffffff; z-index: 2000 !important; right: 0 !important; left: auto !important;
        }
        tbody tr:nth-last-child(-n+3) .action-gear-dropdown .dropdown-menu {
            top: auto !important; bottom: 100% !important; margin-bottom: 4px !important; margin-top: 0 !important;
        }
        .action-gear-dropdown .dropdown-item {
            padding: 7px 14px; font-size: 12.5px; font-weight: 500; color: #334155;
            display: flex; align-items: center; transition: background 0.12s ease, color 0.12s ease;
        }
        .action-gear-dropdown .dropdown-item:hover { background: #f8fafc; color: var(--primary); }
        .action-gear-dropdown .dropdown-item.text-danger:hover { background: #fef2f2; color: #dc2626 !important; }
        .action-gear-dropdown .dropdown-item.text-success:hover { background: #f0fdf4; color: #16a34a !important; }
        .action-gear-dropdown .dropdown-item.text-warning:hover { background: #fffbeb; color: #d97706 !important; }

        /* ============================================================
         * Stockifly Table Footer & Pagination System
         * ============================================================ */
        .stockifly-table-footer {
            padding: 12px 18px; border-top: 1px solid #f0f0f0; background: #ffffff;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px; font-size: 12.5px; color: #64748b;
        }
        .stockifly-table-footer .footer-left { display: flex; align-items: center; gap: 8px; }
        .stockifly-table-footer .footer-right { display: flex; align-items: center; gap: 4px; }
        .stockifly-table-footer select.per-page-select {
            height: 30px; padding: 2px 8px; font-size: 12px; border-radius: 4px !important;
            border: 1px solid #cbd5e1; color: #334155; background-color: #ffffff; cursor: pointer; outline: none;
        }
        .stockifly-table-footer select.per-page-select:focus {
            border-color: var(--primary); box-shadow: 0 0 0 2px var(--primary-transparent-10);
        }

        .pagination { margin: 0; gap: 4px; display: flex; align-items: center; }
        .pagination .page-item .page-link {
            padding: 5px 12px; font-size: 12px; font-weight: 600; color: #475569;
            background: #ffffff; border: 1px solid #e2e8f0; border-radius: 4px !important;
            transition: all 0.15s ease; text-decoration: none;
        }
        .pagination .page-item:hover .page-link { background: #f1f5f9; color: var(--primary); border-color: #cbd5e1; }
        .pagination .page-item.active .page-link {
            background: var(--primary) !important; color: #ffffff !important;
            border-color: var(--primary) !important; box-shadow: 0 2px 6px rgba(32,103,225,0.25);
        }
        .pagination .page-item.disabled .page-link { color: #cbd5e1; background: #f8fafc; border-color: #f1f5f9; cursor: not-allowed; }

        /* ============================================================
         * Stockifly Accordion Sub-menu System
         * ============================================================ */
        .sb-nav-group { margin-bottom: 2px; }
        .sb-nav-toggle {
            display: flex; align-items: center; justify-content: space-between;
            width: 100%; padding: 9px 16px; color: rgba(255,255,255,0.65);
            background: transparent; border: none; font-weight: 500;
            font-size: var(--font-size-sidebar); cursor: pointer;
            transition: all 0.15s ease; text-align: left;
        }
        .sb-nav-toggle:hover { color: #ffffff; background-color: rgba(24,144,255,0.12); }
        .sb-nav-toggle .chevron-icon { font-size: 10px; transition: transform 0.2s ease; opacity: 0.7; }
        .sb-nav-toggle[aria-expanded="true"] { color: #ffffff; background-color: rgba(255,255,255,0.06); }
        .sb-nav-toggle[aria-expanded="true"] .chevron-icon { transform: rotate(90deg); }
        .sb-sub-menu { background-color: #000c17; padding: 4px 0; border-left: 2px solid var(--primary); }
        .sb-sub-item {
            display: flex; align-items: center; gap: 10px;
            padding: 7px 16px 7px 36px; color: rgba(255,255,255,0.6);
            text-decoration: none; font-weight: 500; font-size: 12.5px;
            transition: all 0.15s ease;
        }
        .sb-sub-item:hover { color: #ffffff; background-color: rgba(24,144,255,0.12); }
        .sb-sub-item.active { color: #ffffff; background-color: var(--primary); font-weight: 600; }
    </style>
    @yield('head')
</head>
<body>
    <div id="sbBackdrop" onclick="toggleSidebar()"></div>

    <!-- ===================== VENDOR SIDEBAR ===================== -->
    <aside id="vendorSidebar">
        <div class="sb-brand">
            <div class="sb-brand-icon"><i class="fa-solid fa-hotel"></i></div>
            <div class="sb-brand-text">
                <span class="sb-brand-title">VENDOR PORTAL</span>
                <span class="sb-brand-subtitle">Hotel Partner Dashboard</span>
            </div>
            <button class="btn-sidebar-collapse d-none d-lg-flex" onclick="collapseVendorSidebar()" title="Toggle Sidebar">
                <i class="fa-solid fa-chevron-left" id="collapseIconVendor"></i>
            </button>
            <button onclick="toggleSidebar()" class="ms-auto d-lg-none"
                style="background:none;border:none;color:rgba(255,255,255,0.5);font-size:16px;cursor:pointer;padding:4px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav style="padding:8px 0 80px 0; flex:1; overflow-y:auto;">

            {{-- OVERVIEW --}}
            <div class="sb-section-header">Overview</div>
            <a href="{{ route('vendor.dashboard') }}" class="sb-nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('vendor.notifications') }}" class="sb-nav-item {{ request()->routeIs('vendor.notifications') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i> <span>Notifications</span>
                @php $pendingBookingsCount = \App\Models\Booking::whereIn('property_id', \App\Models\Property::where('vendor_id', auth()->id() ?? 1)->pluck('id'))->where(fn($q) => $q->where('status','pending')->orWhere('booking_status','pending'))->count(); @endphp
                @if($pendingBookingsCount > 0)
                    <span style="margin-left:auto;background:#ff9f43;color:#fff;font-size:9.5px;font-weight:700;padding:2px 6px;border-radius:10px;">{{ $pendingBookingsCount }}</span>
                @endif
            </a>

            {{-- PROPERTY MANAGEMENT --}}
            <div class="sb-section-header">Property Management</div>
            @php $isPropActive = request()->routeIs('vendor.properties.*', 'vendor.property.*', 'vendor.availability.*'); @endphp
            <div class="sb-nav-group">
                <button class="sb-nav-toggle {{ $isPropActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuVendorProps" aria-expanded="{{ $isPropActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-hotel" style="width:16px;text-align:center;"></i> <span>Properties</span></div>
                    <i class="fa-solid fa-chevron-right chevron-icon"></i>
                </button>
                <div class="collapse {{ $isPropActive ? 'show' : '' }}" id="menuVendorProps">
                    <div class="sb-sub-menu">
                        <a href="{{ route('vendor.properties.index') }}" class="sb-sub-item {{ request()->routeIs('vendor.properties.index') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> My Properties</a>
                        <a href="{{ route('vendor.properties.create') }}" class="sb-sub-item {{ request()->routeIs('vendor.properties.create','vendor.property.create') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Add New Property</a>
                        <a href="{{ route('vendor.availability.index') }}" class="sb-sub-item {{ request()->routeIs('vendor.availability.*') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Rates & Calendar</a>
                    </div>
                </div>
            </div>

            {{-- BOOKINGS & GUESTS --}}
            <div class="sb-section-header">Bookings & Guests</div>
            @php $isBookActive = request()->routeIs('vendor.bookings.*', 'vendor.inquiries'); @endphp
            <div class="sb-nav-group">
                <button class="sb-nav-toggle {{ $isBookActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuVendorBook" aria-expanded="{{ $isBookActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-calendar-days" style="width:16px;text-align:center;"></i> <span>Reservations</span></div>
                    <i class="fa-solid fa-chevron-right chevron-icon"></i>
                </button>
                <div class="collapse {{ $isBookActive ? 'show' : '' }}" id="menuVendorBook">
                    <div class="sb-sub-menu">
                        <a href="{{ route('vendor.bookings.index') }}" class="sb-sub-item {{ request()->routeIs('vendor.bookings.index') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> All Bookings</a>
                        <a href="{{ route('vendor.inquiries') }}" class="sb-sub-item {{ request()->routeIs('vendor.inquiries') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Guest Inquiries</a>
                    </div>
                </div>
            </div>

            {{-- MARKETING --}}
            <div class="sb-section-header">Marketing & Reviews</div>
            @php $isMktActive = request()->routeIs('vendor.packages.*', 'vendor.promotions.*', 'vendor.reviews.*'); @endphp
            <div class="sb-nav-group">
                <button class="sb-nav-toggle {{ $isMktActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuVendorMkt" aria-expanded="{{ $isMktActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-bullhorn" style="width:16px;text-align:center;"></i> <span>Marketing</span></div>
                    <i class="fa-solid fa-chevron-right chevron-icon"></i>
                </button>
                <div class="collapse {{ $isMktActive ? 'show' : '' }}" id="menuVendorMkt">
                    <div class="sb-sub-menu">
                        <a href="{{ route('vendor.packages.index') }}" class="sb-sub-item {{ request()->routeIs('vendor.packages.*') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Tour Packages</a>
                        <a href="{{ route('vendor.promotions.index') }}" class="sb-sub-item {{ request()->routeIs('vendor.promotions.*') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Promo Codes</a>
                        <a href="{{ route('vendor.reviews.index') }}" class="sb-sub-item {{ request()->routeIs('vendor.reviews.*') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Guest Reviews</a>
                    </div>
                </div>
            </div>

            {{-- FINANCE --}}
            <div class="sb-section-header">Finance & Billing</div>
            @php $isFinActive = request()->routeIs('vendor.payouts.*', 'vendor.earnings', 'vendor.reports', 'vendor.plans.*'); @endphp
            <div class="sb-nav-group">
                <button class="sb-nav-toggle {{ $isFinActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuVendorFin" aria-expanded="{{ $isFinActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-wallet" style="width:16px;text-align:center;"></i> <span>Finance</span></div>
                    <i class="fa-solid fa-chevron-right chevron-icon"></i>
                </button>
                <div class="collapse {{ $isFinActive ? 'show' : '' }}" id="menuVendorFin">
                    <div class="sb-sub-menu">
                        <a href="{{ route('vendor.earnings') }}" class="sb-sub-item {{ request()->routeIs('vendor.earnings') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Earnings Overview</a>
                        <a href="{{ route('vendor.payouts.index') }}" class="sb-sub-item {{ request()->routeIs('vendor.payouts.*') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Payouts</a>
                        <a href="{{ route('vendor.reports') }}" class="sb-sub-item {{ request()->routeIs('vendor.reports') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Financial Reports</a>
                        <a href="{{ route('vendor.plans.index') }}" class="sb-sub-item {{ request()->routeIs('vendor.plans.*') ? 'active' : '' }}"><i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> SaaS Plans & Billing</a>
                    </div>
                </div>
            </div>

            {{-- ACCOUNT --}}
            <div class="sb-section-header">My Account</div>
            <a href="{{ route('vendor.profile') }}" class="sb-nav-item {{ request()->routeIs('vendor.profile') ? 'active' : '' }}">
                <i class="fa-solid fa-user-circle"></i> <span>My Profile</span>
            </a>
            <a href="{{ route('vendor.support') }}" class="sb-nav-item {{ request()->routeIs('vendor.support') ? 'active' : '' }}">
                <i class="fa-solid fa-headset"></i> <span>Support & Help</span>
            </a>

            {{-- LIVE SITE --}}
            <div class="sb-section-header">Live Site</div>
            <a href="{{ route('home') }}" target="_blank" class="sb-nav-item" style="color:rgba(80,210,255,0.8);">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> <span>Public Website</span>
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
                <span class="engine-badge d-none d-xl-inline-flex"><i class="fa-solid fa-hotel me-1"></i> VENDOR PARTNER PORTAL</span>
                
                <!-- Topbar Search Bar (Sleek Compact 230px) -->
                <form action="{{ route('vendor.properties.index') }}" method="GET" class="d-none d-md-flex align-items-center position-relative m-0 ms-2" style="width: 230px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.45); font-size: 12px; pointer-events: none; z-index: 5;"></i>
                    <input type="text" name="q" class="form-control topbar-global-search-input" placeholder="Search..." value="{{ request('q') }}">
                </form>
            </div>
            <div class="admin-topbar-right">
                <div style="display:flex;align-items:center;gap:8px;padding-right:12px;border-right:1px solid rgba(255,255,255,0.12);">
                    @php $vendorAvatar = auth()->user()?->avatar ?: null; @endphp
                    <img src="{{ $vendorAvatar ?: ('https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Vendor') . '&background=fa8c16&color=fff&size=64') }}"
                        class="topbar-avatar" alt="Vendor"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Vendor') }}&background=fa8c16&color=fff&size=64'">
                    <div class="d-none d-sm-block">
                        <span class="topbar-user-name">{{ auth()->user()->name ?? 'Vendor Partner' }}</span>
                        <span class="topbar-user-role">{{ ucfirst(auth()->user()->role ?? 'Vendor') }} Account</span>
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
            @php $announcement = \App\Models\SiteSetting::get('announcement_text', null); @endphp
            @if($announcement)
                <div class="px-3 pt-3 mb-0">
                    <div class="alert alert-info border-0 shadow-xs mb-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #e6f7ff 0%, #bae7ff 100%); color: #0050b3; border-left: 4px solid #1890ff !important; font-size: 12.5px; border-radius: 4px;">
                        <div>
                            <i class="fa-solid fa-bullhorn me-2 text-primary"></i> <strong>PLATFORM ANNOUNCEMENT:</strong> {{ $announcement }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 10px;"></button>
                    </div>
                </div>
            @endif
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

    {{-- Global SaaS Table Toolbar JS (Vendor) --}}
    <script>
    function showSaasToast(message, type = 'success') {
        let toastContainer = document.getElementById('saasToastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'saasToastContainer';
            toastContainer.style = 'position:fixed; bottom:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:8px;';
            document.body.appendChild(toastContainer);
        }
        const toast = document.createElement('div');
        const bg = type === 'success' ? '#52c41a' : (type === 'info' ? '#1890ff' : '#ff4d4f');
        toast.style = `background:${bg}; color:#fff; padding:10px 18px; border-radius:4px; font-size:13px; font-weight:600; box-shadow:0 4px 12px rgba(0,0,0,0.15); display:flex; align-items:center; gap:8px; transition:all 0.2s ease; opacity:0; transform:translateY(10px);`;
        toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i> <span>${message}</span>`;
        toastContainer.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; }, 10);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(10px)'; setTimeout(() => toast.remove(), 200); }, 3000);
    }

    function copyTableToClipboard(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;
        let text = '';
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.style.display === 'none') return;
            const cols = row.querySelectorAll('th, td');
            let rowText = [];
            cols.forEach(col => {
                if (col.classList.contains('th-checkbox') || col.classList.contains('td-checkbox') || col.style.display === 'none') return;
                rowText.push(col.innerText.replace(/\s+/g, ' ').trim());
            });
            if (rowText.length) text += rowText.join('\t') + '\n';
        });
        navigator.clipboard.writeText(text).then(() => {
            showSaasToast('Table data copied to clipboard!', 'success');
        }).catch(() => {
            showSaasToast('Failed to copy table data.', 'error');
        });
    }

    function exportTableExcel(tableId, filename) {
        const table = document.getElementById(tableId);
        if (!table) return;
        let html = '<meta charset="UTF-8"><table>';
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.style.display === 'none') return;
            html += '<tr>';
            const cols = row.querySelectorAll('th, td');
            cols.forEach(col => {
                if (col.classList.contains('th-checkbox') || col.classList.contains('td-checkbox') || col.style.display === 'none') return;
                const tag = col.tagName.toLowerCase();
                html += `<${tag}>${col.innerText.trim()}</${tag}>`;
            });
            html += '</tr>';
        });
        html += '</table>';
        const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename + '_' + new Date().toISOString().slice(0, 10) + '.xls';
        link.click();
        showSaasToast('Excel report downloaded!', 'success');
    }

    function exportTableCSV(tableId, filename) {
        const table = document.getElementById(tableId);
        if (!table) return;
        let csv = [];
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.style.display === 'none') return;
            const cols = row.querySelectorAll('th, td');
            let rowData = [];
            cols.forEach(col => {
                if (col.classList.contains('th-checkbox') || col.classList.contains('td-checkbox') || col.style.display === 'none') return;
                const cellText = col.innerText.replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                rowData.push('"' + cellText + '"');
            });
            if (rowData.length) csv.push(rowData.join(','));
        });
        const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename + '_' + new Date().toISOString().slice(0, 10) + '.csv';
        link.click();
        showSaasToast('CSV report downloaded!', 'success');
    }

    function exportTablePDF(tableId, filename) {
        printTable(tableId);
    }

    function printTable(tableId) {
        const table = document.getElementById(tableId);
        if (!table) { window.print(); return; }
        const pageTitle = document.querySelector('.page-title')?.textContent?.trim() || 'Vendor Report';
        const printDate = new Date().toLocaleDateString('en-BD', { year: 'numeric', month: 'long', day: 'numeric' });

        const printWin = window.open('', '_blank', 'width=1050,height=700');
        printWin.document.write(`<!DOCTYPE html><html><head>
            <title>${pageTitle} — Print</title>
            <style>
                * { box-sizing: border-box; }
                body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
                .print-header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #fa8c16; padding-bottom: 10px; margin-bottom: 16px; }
                .print-header h1 { font-size: 16px; font-weight: 700; color: #fa8c16; margin: 0; }
                .print-header small { color: #8c8c8c; font-size: 11px; }
                table { width: 100%; border-collapse: collapse; }
                th { background: #fa8c16; color: #fff; padding: 7px 10px; text-align: left; font-size: 11px; font-weight: 600; }
                td { padding: 6px 10px; border-bottom: 1px solid #f0f0f0; font-size: 11px; vertical-align: middle; }
                tr:nth-child(even) td { background: #fafafa; }
                .th-checkbox, .td-checkbox { display: none; }
                @media print { body { padding: 10px; } }
            </style>
        </head><body>
            <div class="print-header">
                <div>
                    <h1>${pageTitle}</h1>
                    <small>Generated: ${printDate} — Vendor Partner Portal</small>
                </div>
            </div>
            ${table.outerHTML}
            <script>window.onload = function(){ window.print(); window.close(); }<\/script>
        </body></html>`);
        printWin.document.close();
    }

    function filterTableSearch(tableId, query) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        const q = query.toLowerCase().trim();
        rows.forEach(row => {
            if (row.querySelector('td[colspan]')) return;
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }

    function toggleAllRows(tableId, masterCheckbox) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const checkboxes = table.querySelectorAll('.tbl-row-check');
        checkboxes.forEach(cb => {
            cb.checked = masterCheckbox.checked;
            const row = cb.closest('tr');
            if (row) row.classList.toggle('row-selected', masterCheckbox.checked);
        });
    }

    function updateRowHighlight(checkbox) {
        const row = checkbox.closest('tr');
        if (row) row.classList.toggle('row-selected', checkbox.checked);
        const table = checkbox.closest('table');
        if (table) {
            const all = table.querySelectorAll('.tbl-row-check');
            const checked = table.querySelectorAll('.tbl-row-check:checked');
            const master = table.querySelector('.tbl-master-check');
            if (master) master.checked = all.length > 0 && all.length === checked.length;
        }
    }

    function toggleColVis(tableId, btn) {
        const dropId = 'colVisDropdown_' + tableId;
        const drop = document.getElementById(dropId);
        if (!drop) return;
        const isVisible = drop.style.display === 'block';
        document.querySelectorAll('.col-vis-dropdown').forEach(d => d.style.display = 'none');
        document.querySelectorAll('.btn-tbl-gear, .btn-tbl-col').forEach(b => b.classList.remove('active-col'));

        if (!isVisible) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const headers = table.querySelectorAll('thead th');
            drop.innerHTML = '';
            headers.forEach((th, i) => {
                if (th.classList.contains('th-checkbox') || th.querySelector('.tbl-master-check')) return;
                let label = th.innerText.replace(/\n/g, ' ').replace('Actions', '').trim() || 'Column ' + (i + 1);
                if (!label) return;
                const item = document.createElement('label');
                item.className = 'col-vis-item';
                const checked = th.style.display !== 'none';
                item.innerHTML = `<input type="checkbox" ${checked ? 'checked' : ''} onchange="toggleColumn('${tableId}', ${i}, this)"> ${label}`;
                drop.appendChild(item);
            });
            drop.style.display = 'block';
            btn.classList.add('active-col');
        }
    }

    function toggleColumn(tableId, colIndex, checkbox) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const display = checkbox.checked ? '' : 'none';
        table.querySelectorAll('tr').forEach(row => {
            const cells = Array.from(row.children);
            if (cells[colIndex]) cells[colIndex].style.display = display;
        });
    }

    function collapseVendorSidebar() {
        const sb = document.getElementById('vendorSidebar');
        const content = document.getElementById('vendorContent');
        const icon = document.getElementById('collapseIconVendor');
        const isCollapsed = sb.classList.toggle('sb-collapsed');
        content.classList.toggle('sb-collapsed-content', isCollapsed);

        if (icon) {
            icon.className = isCollapsed ? 'fa-solid fa-chevron-right' : 'fa-solid fa-chevron-left';
        }

        if (isCollapsed) {
            document.querySelectorAll('#vendorSidebar .collapse.show').forEach(el => el.classList.remove('show'));
        }

        localStorage.setItem('vendorSbCollapsed', isCollapsed ? '1' : '0');
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (localStorage.getItem('vendorSbCollapsed') === '1') {
            const sb = document.getElementById('vendorSidebar');
            const content = document.getElementById('vendorContent');
            const icon = document.getElementById('collapseIconVendor');
            if (sb && content) {
                sb.classList.add('sb-collapsed');
                content.classList.add('sb-collapsed-content');
                if (icon) icon.className = 'fa-solid fa-chevron-right';
                document.querySelectorAll('#vendorSidebar .collapse.show').forEach(el => el.classList.remove('show'));
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('[onclick*="toggleColVis"]') && !e.target.closest('.col-vis-dropdown')) {
            document.querySelectorAll('.col-vis-dropdown').forEach(d => d.style.display = 'none');
            document.querySelectorAll('.btn-tbl-gear, .btn-tbl-col').forEach(b => b.classList.remove('active-col'));
        }
    });
    </script>
    @yield('scripts')
</body>
</html>

