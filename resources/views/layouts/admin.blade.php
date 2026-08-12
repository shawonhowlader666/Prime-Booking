<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PRIME BOOKING Admin Panel')</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ============================================================
         * CSS Variables — Exact match of Stockifly-SaaS app.css :root
         * ============================================================ */
        :root {
            --primary: #1890ff;
            --primary-hover: #40a9ff;
            --primary-active: #096dd9;
            --primary-bg: #e6f7ff;
            --primary-bg-hover: #bae7ff;
            --primary-shadow: rgba(24, 144, 255, 0.20);
            --primary-transparent-10: rgba(24, 144, 255, 0.10);
            --primary-transparent-20: rgba(24, 144, 255, 0.20);

            /* Stockifly layout vars */
            --sidebar-width: 250px;
            --admin-body-bg: #f0f2f5;
            --admin-card-border: #e8e8e8;

            /* Stockifly font scale — exact from app.css */
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-heading: 'Plus Jakarta Sans', sans-serif;
            --font-size-base: 13px;
            --font-size-btn: 13px;
            --font-size-input: 13px;
            --font-size-sidebar: 13px;
            --font-size-card-title: 14px;
            --font-size-card-body: 13px;
            --font-size-table-header: 12px;
            --font-size-table-body: 13px;
            --font-size-page-header: 19px;
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

        /* ============================================================
         * Base — exact body style from Stockifly
         * ============================================================ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

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
            height: 100vh;
            max-height: 100vh;
            background-color: #001529;
            color: rgba(255, 255, 255, 0.85);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1040;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        /* Custom Scrollbar for Admin Sidebar */
        #stockiflySidebar::-webkit-scrollbar,
        #stockiflySidebar nav::-webkit-scrollbar {
            width: 5px;
        }

        #stockiflySidebar::-webkit-scrollbar-thumb,
        #stockiflySidebar nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        #stockiflySidebar::-webkit-scrollbar-thumb:hover,
        #stockiflySidebar nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        /* Sidebar brand bar */
        .sb-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            background-color: #002140;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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
            color: rgba(255, 255, 255, 0.45);
            display: block;
        }

        /* Sidebar section header */
        .sb-section-header {
            font-size: 11px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.35);
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
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            font-weight: 500;
            font-size: var(--font-size-sidebar);
            transition: background-color 0.15s ease, color 0.15s ease;
            border-radius: 0;
            cursor: pointer;
            line-height: 1.4;
        }

        .sb-nav-item i {
            width: 16px;
            text-align: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .sb-nav-item:hover {
            color: #ffffff;
            background-color: rgba(24, 144, 255, 0.12);
        }

        .sb-nav-item.active {
            color: #ffffff;
            background-color: var(--primary);
        }

        .sb-nav-item.active i {
            color: #ffffff;
        }

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
            border-bottom: 1px solid #e8e8e8;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .admin-topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .admin-topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .engine-badge {
            background: rgba(24, 144, 255, 0.08);
            color: #1890ff;
            font-size: 12.5px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 4px !important;
            border: 1px solid rgba(24, 144, 255, 0.2);
            letter-spacing: 0.3px;
        }

        .topbar-user-name {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            display: block;
            line-height: 1.2;
        }

        .topbar-user-role {
            font-size: 11.5px;
            color: #64748b;
            display: block;
            margin-top: 1px;
        }

        .topbar-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }

        .btn-signout {
            font-size: 13px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 4px !important;
            border: 1.5px solid #ff4d4f;
            color: #ff4d4f;
            background: #fff;
            transition: all 0.15s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
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
            border: 1px solid #e8e8e8;
            padding: 16px 24px;
            margin: 18px 24px 18px 24px;
            border-radius: 4px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
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

        .page-breadcrumb a:hover {
            color: var(--primary);
        }

        .page-breadcrumb .sep {
            color: #d9d9d9;
            margin: 0 2px;
        }

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

        .btn-export-pdf:hover {
            background: var(--primary-active);
            border-color: var(--primary-active);
        }

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

        .btn-add-primary:hover {
            background: var(--primary-active);
            color: #fff;
        }

        /* ============================================================
         * Filter Bar — exact .admin-page-filters-wrapper from Stockifly
         * padding: 10px 16px, bg: white
         * ============================================================ */
        .page-filters-bar {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            padding: 14px 24px;
            margin: 0 24px 18px 24px;
            border-radius: 4px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
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
            border-radius: 4px !important;
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
            border-radius: 0 4px 4px 0 !important;
            background: var(--primary);
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
        }

        .page-filters-bar .btn-search:hover {
            background: var(--primary-active);
        }

        .page-filters-bar .input-group .form-control {
            border-radius: 4px 0 0 4px !important;
        }

        /* ============================================================
         * Page Content Area (admin-page-table-content)
         * padding: 0 24px 24px 24px — exact Stockifly spacing
         * ============================================================ */
        .page-content-area {
            padding: 0 24px 24px 24px;
            flex: 1;
        }

        /* ============================================================
         * Table Card — exact Stockifly .table-card / .main-table-card
         * border: 1px solid #e8e8e8, border-radius: 8px, shadow: 0 4px 20px rgba(0,0,0,0.05)
         * ============================================================ */
        .data-table-card,
        .stockifly-card {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 4px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            min-height: 220px;
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
            border-right: 1px solid rgba(255, 255, 255, 0.15) !important;
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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.10);
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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
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

        .form-card textarea.form-control {
            height: auto;
        }

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

        .btn-table-action:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-table-action.danger {
            border-color: #ff4d4f;
            color: #ff4d4f;
        }

        .btn-table-action.danger:hover {
            background: #ff4d4f;
            color: #fff;
        }

        .btn-table-action.primary {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-table-action.primary:hover {
            background: var(--primary);
            color: #fff;
        }

        .btn-table-action.success {
            border-color: #52c41a;
            color: #52c41a;
        }

        .btn-table-action.success:hover {
            background: #52c41a;
            color: #fff;
        }

        /* Status badges */
        .badge-status {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 10px;
            display: inline-block;
        }

        .badge-status.confirmed {
            background: rgba(40, 199, 111, 0.12);
            color: #28c76f;
        }

        .badge-status.pending {
            background: rgba(255, 159, 67, 0.12);
            color: #ff9f43;
        }

        .badge-status.cancelled {
            background: rgba(234, 84, 85, 0.12);
            color: #ea5455;
        }

        .badge-status.active {
            background: rgba(24, 144, 255, 0.10);
            color: #1890ff;
        }

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

        .order-ref-link:hover {
            color: var(--primary-active);
            text-decoration: underline;
        }

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

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.4);
            }
        }

        /* ============================================================
         * Mobile Responsive
         * ============================================================ */
        @media (max-width: 991.98px) {
            #stockiflySidebar {
                transform: translateX(-100%);
            }

            #stockiflySidebar.show-mobile {
                transform: translateX(0);
            }

            #stockiflyContent {
                margin-left: 0 !important;
            }

            .btn-mobile-toggle {
                display: flex !important;
            }

            .page-content-area {
                padding: 16px;
            }

            .page-header-card {
                padding: 12px 16px;
            }

            .page-filters-bar {
                padding: 10px 16px;
            }

            .admin-topbar {
                padding: 0 16px;
            }

            .kpi-card .kpi-value {
                font-size: 18px;
            }
        }

        /* Sidebar overlay backdrop */
        #sbBackdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1030;
        }

        /* Smooth hover transitions */
        .form-control,
        .form-select,
        .btn-table-action,
        .btn-export-csv,
        .btn-export-pdf,
        .btn-add-primary,
        .btn-signout,
        .kpi-card,
        .data-table-card {
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

        .admin-alert.success {
            background: rgba(40, 199, 111, 0.08);
            border-color: rgba(40, 199, 111, 0.3);
            color: #15803d;
        }

        .admin-alert.error {
            background: rgba(234, 84, 85, 0.08);
            border-color: rgba(234, 84, 85, 0.3);
            color: #dc2626;
        }

        .admin-alert.info {
            background: rgba(24, 144, 255, 0.08);
            border-color: rgba(24, 144, 255, 0.3);
            color: #1890ff;
        }

        /* ============================================================
         * Action Gear Hover Dropdown System (Stockifly Style)
         * ============================================================ */
        .action-gear-dropdown {
            position: relative;
            display: inline-block;
        }

        .action-gear-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 4px !important;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .action-gear-btn:hover,
        .action-gear-dropdown:hover .action-gear-btn {
            background: var(--primary) !important;
            color: #ffffff !important;
            border-color: var(--primary) !important;
            box-shadow: 0 2px 8px rgba(32, 103, 225, 0.25);
        }

        .action-gear-dropdown:hover .dropdown-menu {
            display: block !important;
            margin-top: 0;
        }

        .action-gear-dropdown .dropdown-menu {
            min-width: 180px;
            padding: 4px 0;
            border-radius: 4px !important;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            background: #ffffff;
            z-index: 2000 !important;
            right: 0 !important;
            left: auto !important;
        }

        /* Auto dropup for last 3 rows in tables to prevent bottom clipping */
        tbody tr:nth-last-child(-n+3) .action-gear-dropdown .dropdown-menu {
            top: auto !important;
            bottom: 100% !important;
            margin-bottom: 4px !important;
            margin-top: 0 !important;
        }

        .action-gear-dropdown .dropdown-item {
            padding: 7px 14px;
            font-size: 12.5px;
            font-weight: 500;
            color: #334155;
            display: flex;
            align-items: center;
            transition: background 0.12s ease, color 0.12s ease;
        }

        .action-gear-dropdown .dropdown-item:hover {
            background: #f8fafc;
            color: var(--primary);
        }

        .action-gear-dropdown .dropdown-item.text-danger:hover {
            background: #fef2f2;
            color: #dc2626 !important;
        }

        .action-gear-dropdown .dropdown-item.text-success:hover {
            background: #f0fdf4;
            color: #16a34a !important;
        }

        .action-gear-dropdown .dropdown-item.text-warning:hover {
            background: #fffbeb;
            color: #d97706 !important;
        }

        /* ============================================================
         * Stockifly Table Footer & Pagination System
         * ============================================================ */
        .stockifly-table-footer {
            padding: 12px 18px;
            border-top: 1px solid #f0f0f0;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 12.5px;
            color: #64748b;
        }

        .stockifly-table-footer .footer-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stockifly-table-footer .footer-right {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stockifly-table-footer select.per-page-select {
            height: 30px;
            padding: 2px 8px;
            font-size: 12px;
            border-radius: 4px !important;
            border: 1px solid #cbd5e1;
            color: #334155;
            background-color: #ffffff;
            cursor: pointer;
            outline: none;
        }

        .stockifly-table-footer select.per-page-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary-transparent-10);
        }

        /* Bootstrap Pagination override */
        .pagination {
            margin: 0;
            gap: 4px;
            display: flex;
            align-items: center;
        }

        .pagination .page-item .page-link {
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 4px !important;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .pagination .page-item:hover .page-link {
            background: #f1f5f9;
            color: var(--primary);
            border-color: #cbd5e1;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary) !important;
            color: #ffffff !important;
            border-color: var(--primary) !important;
            box-shadow: 0 2px 6px rgba(32, 103, 225, 0.25);
        }

        .pagination .page-item.disabled .page-link {
            color: #cbd5e1;
            background: #f8fafc;
            border-color: #f1f5f9;
            cursor: not-allowed;
        }

        /* ============================================================
         * Stockifly Accordion Sub-menu System
         * ============================================================ */
        .sb-nav-group {
            margin-bottom: 2px;
        }

        .sb-nav-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 9px 16px;
            color: rgba(255, 255, 255, 0.65);
            background: transparent;
            border: none;
            font-weight: 500;
            font-size: var(--font-size-sidebar);
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: left;
        }

        .sb-nav-toggle:hover {
            color: #ffffff;
            background-color: rgba(24, 144, 255, 0.12);
        }

        .sb-nav-toggle .chevron-icon {
            font-size: 10px;
            transition: transform 0.2s ease;
            opacity: 0.7;
        }

        .sb-nav-toggle[aria-expanded="true"] {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.06);
        }

        .sb-nav-toggle[aria-expanded="true"] .chevron-icon {
            transform: rotate(90deg);
        }

        .sb-sub-menu {
            background-color: #000c17;
            padding: 4px 0;
            border-left: 2px solid var(--primary);
        }

        .sb-sub-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 16px 7px 36px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-weight: 500;
            font-size: 12.5px;
            transition: all 0.15s ease;
        }

        .sb-sub-item:hover {
            color: #ffffff;
            background-color: rgba(24, 144, 255, 0.12);
        }

        .sb-sub-item.active {
            color: #ffffff;
            background-color: var(--primary);
            font-weight: 600;
        }
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
            <button onclick="toggleSidebar()" class="ms-auto d-lg-none"
                style="background:none;border:none;color:rgba(255,255,255,0.5);font-size:16px;cursor:pointer;padding:4px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Nav Menu -->
        <nav style="padding: 8px 0 60px 0; flex: 1; overflow-y: auto;">

            <div class="sb-section-header">Overview</div>
            <a href="{{ route('admin.dashboard') }}"
                class="sb-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> <span>Sales &amp; Summary</span>
            </a>

            <div class="sb-section-header">Core Management</div>

            {{-- 1. Bookings & Reservations --}}
            @php $isBookingsActive = request()->routeIs('admin.bookings.*', 'admin.inquiries.*', 'admin.reviews.*'); @endphp
            <div class="sb-nav-group">
                <button class="sb-nav-toggle {{ $isBookingsActive ? 'active' : '' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#menuBookings"
                    aria-expanded="{{ $isBookingsActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-receipt" style="width:16px;text-align:center;"></i> <span>Reservations
                            &amp; Inbox</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-icon"></i>
                </button>
                <div class="collapse {{ $isBookingsActive ? 'show' : '' }}" id="menuBookings">
                    <div class="sb-sub-menu">
                        <a href="{{ route('admin.bookings.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> All Bookings
                        </a>
                        <a href="{{ route('admin.inquiries.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Guest Inquiries
                        </a>
                        <a href="{{ route('admin.reviews.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Guest Reviews
                        </a>
                    </div>
                </div>
            </div>

            {{-- 2. Properties & Inventory --}}
            @php $isPropertiesActive = request()->routeIs('admin.properties.*', 'admin.import-hotels.*'); @endphp
            <div class="sb-nav-group">
                <button class="sb-nav-toggle {{ $isPropertiesActive ? 'active' : '' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#menuProperties"
                    aria-expanded="{{ $isPropertiesActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-hotel" style="width:16px;text-align:center;"></i> <span>Properties &amp;
                            Inventory</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-icon"></i>
                </button>
                <div class="collapse {{ $isPropertiesActive ? 'show' : '' }}" id="menuProperties">
                    <div class="sb-sub-menu">
                        <a href="{{ route('admin.properties.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.properties.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Property Inventory
                        </a>
                        <a href="{{ route('admin.import-hotels.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.import-hotels.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> OTA Data Importer
                        </a>
                        <a href="{{ route('admin.properties.create') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.properties.create') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Add New Listing
                        </a>
                    </div>
                </div>
            </div>

            {{-- 3. Marketing & Sales --}}
            @php $isMarketingActive = request()->routeIs('admin.promotions.*', 'admin.packages.*', 'admin.deals.*', 'admin.coupons.*'); @endphp
            <div class="sb-nav-group">
                <button class="sb-nav-toggle {{ $isMarketingActive ? 'active' : '' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#menuMarketing"
                    aria-expanded="{{ $isMarketingActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-bullhorn" style="width:16px;text-align:center;"></i> <span>Marketing &amp;
                            Sales</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-icon"></i>
                </button>
                <div class="collapse {{ $isMarketingActive ? 'show' : '' }}" id="menuMarketing">
                    <div class="sb-sub-menu">
                        <a href="{{ route('admin.promotions.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Promotions Manager
                        </a>
                        <a href="{{ route('admin.packages.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Tour Packages
                        </a>
                        <a href="{{ route('admin.deals.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.deals.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Deals &amp; Offers
                        </a>
                        <a href="{{ route('admin.coupons.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Promo Coupons
                        </a>
                    </div>
                </div>
            </div>

            {{-- 4. Users, Vendors & Payouts --}}
            @php $isAccountsActive = request()->routeIs('admin.users.*', 'admin.tenants.*', 'admin.payouts.*'); @endphp
            <div class="sb-nav-group">
                <button class="sb-nav-toggle {{ $isAccountsActive ? 'active' : '' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#menuAccounts"
                    aria-expanded="{{ $isAccountsActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-users-gear" style="width:16px;text-align:center;"></i> <span>Users &amp;
                            Vendors</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-icon"></i>
                </button>
                <div class="collapse {{ $isAccountsActive ? 'show' : '' }}" id="menuAccounts">
                    <div class="sb-sub-menu">
                        <a href="{{ route('admin.users.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> User Accounts
                        </a>
                        <a href="{{ route('admin.tenants.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Vendor Tenants
                        </a>
                        <a href="{{ route('admin.payouts.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Vendor Payouts
                        </a>
                    </div>
                </div>
            </div>

            {{-- 5. Website CMS & Banners --}}
            @php $isCMSActive = request()->routeIs('admin.cms.*', 'admin.content.hero', 'admin.destinations.*', 'admin.amenities.*'); @endphp
            <div class="sb-nav-group">
                <button class="sb-nav-toggle {{ $isCMSActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse"
                    data-bs-target="#menuCMS" aria-expanded="{{ $isCMSActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-lines" style="width:16px;text-align:center;"></i> <span>Website
                            CMS</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-icon"></i>
                </button>
                <div class="collapse {{ $isCMSActive ? 'show' : '' }}" id="menuCMS">
                    <div class="sb-sub-menu">
                        <a href="{{ route('admin.cms.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Website Pages CMS
                        </a>
                        <a href="{{ route('admin.content.hero') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.content.hero') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Hero Banner Slider
                        </a>
                        <a href="{{ route('admin.destinations.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.destinations.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Featured Destinations
                        </a>
                        <a href="{{ route('admin.amenities.index') }}"
                            class="sb-sub-item {{ request()->routeIs('admin.amenities.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot me-1" style="font-size:8px;"></i> Amenities Catalog
                        </a>
                    </div>
                </div>
            </div>

            {{-- 6. System & Settings --}}
            <div class="sb-section-header">System</div>
            <a href="{{ route('admin.settings.index') }}"
                class="sb-nav-item {{ request()->routeIs('admin.settings.*', 'admin.site-settings.*', 'admin.gateways.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> <span>Settings</span>
            </a>
            <a href="{{ route('admin.activity.index') }}"
                class="sb-nav-item {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved"></i> <span>Activity Audit Logs</span>
            </a>

            <div class="pt-3 pb-1">
                <form action="/admin/system/cache-clear" method="POST" style="padding:0 12px;">
                    @csrf
                    <button type="submit" class="sb-nav-item border-0 w-100"
                        style="background:rgba(255,255,255,0.04); border-radius:4px; font-size:12px; cursor:pointer;"
                        onclick="return confirm('Clear all Redis/file caches?')">
                        <i class="fa-solid fa-rotate text-warning me-2"></i> <span>Flush System Cache</span>
                    </button>
                </form>
            </div>

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
                <div
                    style="display:flex; align-items:center; gap:8px; padding-right:12px; border-right:1px solid #f0f0f0;">
                    <img src="https://ui-avatars.com/api/?name=Shawon+Ahmed&background=1890ff&color=fff&size=64"
                        class="topbar-avatar" alt="Admin">
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