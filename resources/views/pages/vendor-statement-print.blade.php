<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Financial Settlement Statement - PBS-{{ date('Ym') }}-{{ str_pad((string)$vendor->id, 4, '0', STR_PAD_LEFT) }} - Prime Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            padding: 24px 12px;
            font-size: 13px;
            line-height: 1.5;
        }
        .invoice-container {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            overflow: hidden;
            position: relative;
        }
        .invoice-header {
            background: linear-gradient(135deg, #090d16 0%, #1e293b 100%);
            color: #ffffff;
            padding: 28px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #2067E1;
        }
        .logo-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-badge {
            width: 36px;
            height: 36px;
            background: #2067E1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 18px;
        }
        .invoice-body {
            padding: 32px;
        }
        .invoice-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .meta-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px 18px;
        }
        .meta-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #2067E1;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* 4 KPI Cards Grid */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        .sum-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px;
            border-top: 3px solid #2067E1;
        }
        .sum-card.warning { border-top-color: #f59e0b; }
        .sum-card.info    { border-top-color: #06b6d4; }
        .sum-card.success { border-top-color: #00b074; }
        .sum-card span {
            font-size: 10.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }
        .sum-card h4 {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        /* Itemized Table */
        .table-invoice {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .table-invoice th {
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 11px 14px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }
        .table-invoice th.text-right { text-align: right; }
        .table-invoice td {
            padding: 11px 14px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            font-size: 12px;
        }
        .table-invoice td.text-right { text-align: right; }
        .table-invoice tr:nth-child(even) { background-color: #fafbfc; }

        .paid-stamp {
            position: absolute;
            top: 265px;
            right: 48px;
            border: 3px solid #16a34a;
            color: #16a34a;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 6px;
            letter-spacing: 2px;
            transform: rotate(-12deg);
            opacity: 0.85;
            pointer-events: none;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .invoice-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 18px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11.5px;
            color: #64748b;
        }
        .btn-action-bar {
            max-width: 860px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: #2067E1;
            color: #ffffff;
            border: none;
            padding: 9px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-print:hover { background: #1754be; }
        .btn-back {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 9px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        @media print {
            body { background: #ffffff; padding: 0; }
            .btn-action-bar { display: none !important; }
            .invoice-container { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body>

    <!-- Action Bar -->
    <div class="btn-action-bar">
        <a href="javascript:window.history.back()" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-print">
                <i class="fa-solid fa-print"></i> Print Statement / Save as PDF
            </button>
        </div>
    </div>

    <!-- Main Printable Statement -->
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header" style="background: #ffffff; border-bottom: 2px solid #e2e8f0; padding: 24px 30px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <img src="{{ asset('images/logo.png') }}" style="height: 44px; width: auto; object-fit: contain;" alt="Prime Booking">
                <div style="border-left: 2px solid #e2e8f0; padding-left: 14px;">
                    <div class="logo-title" style="font-size: 18px; font-weight: 800; letter-spacing: -0.3px; color: #0f172a; line-height: 1.2;">PRIME BOOKING</div>
                    <div style="font-size: 11.5px; font-weight: 500; color: #64748b; margin-top: 2px;">Prime Booking Bangladesh &bull; OTA Hospitality Network</div>
                    <div style="font-size: 10.5px; color: #94a3b8;">BIN / VAT Reg: 004928192-0101 &bull; Dhaka, Bangladesh</div>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 12.5px; font-weight: 800; letter-spacing: 0.8px; text-transform: uppercase; color: #2067e1;">OFFICIAL FINANCIAL STATEMENT</div>
                <div class="font-mono" style="font-size: 12px; font-weight: 700; color: #0f172a; margin-top: 2px;">PBS-{{ date('Ym') }}-{{ str_pad((string)$vendor->id, 4, '0', STR_PAD_LEFT) }}</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Generated on: {{ date('d M Y, h:i A') }}</div>
            </div>
        </div>

        <!-- Verified Paid Stamp -->
        <div class="paid-stamp">
            <i class="fa-solid fa-shield-halved"></i> AUDIT VERIFIED
        </div>

        <!-- Body -->
        <div class="invoice-body">
            <div class="invoice-meta-grid">
                <!-- Beneficiary Card -->
                <div class="meta-card">
                    <div class="meta-title"><i class="fa-solid fa-building-user"></i> Partner / Hotel Beneficiary</div>
                    <div style="font-size: 14px; font-weight: 700; color: #0f172a;">{{ $vendor->name }}</div>
                    <div style="color: #475569; font-size: 12px; margin-top: 2px;">Email: {{ $vendor->email }}</div>
                    <div style="color: #475569; font-size: 12px;">Phone: {{ $vendor->phone ?? 'N/A' }}</div>
                    <div style="color: #475569; font-size: 12px;">Properties: <strong>{{ $properties->pluck('name')->implode(', ') ?: 'Hotel Listing' }}</strong></div>
                </div>

                <!-- Settlement Details Card -->
                <div class="meta-card">
                    <div class="meta-title"><i class="fa-solid fa-receipt"></i> Settlement Period &amp; Currency</div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                        <span style="color: #64748b;">Accounting Period:</span>
                        <strong style="color: #0f172a;">Lifetime to Date ({{ date('d M Y') }})</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                        <span style="color: #64748b;">Base Currency:</span>
                        <strong class="font-mono" style="color: #2067E1;">BDT (৳)</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                        <span style="color: #64748b;">OTA Commission:</span>
                        <strong style="color: #0f172a;">12.00% Standard Fee</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Payout Schedule:</span>
                        <strong style="color: #0f172a;">Bi-Monthly Auto Settlement</strong>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="summary-cards">
                <div class="sum-card">
                    <span>Gross Turnover (GBV)</span>
                    <h4 class="font-mono">৳{{ number_format($finance['gross_revenue'], 2) }}</h4>
                </div>
                <div class="sum-card warning">
                    <span>Commission (12%)</span>
                    <h4 class="font-mono" style="color: #f59e0b;">-৳{{ number_format($finance['commission_paid'], 2) }}</h4>
                </div>
                <div class="sum-card info">
                    <span>Total Settled (Paid)</span>
                    <h4 class="font-mono" style="color: #06b6d4;">৳{{ number_format($finance['payouts_paid'], 2) }}</h4>
                </div>
                <div class="sum-card success">
                    <span>Current Withdrawable</span>
                    <h4 class="font-mono" style="color: #00b074;">৳{{ number_format($finance['withdrawable_balance'], 2) }}</h4>
                </div>
            </div>

            <!-- Itemized Table -->
            <table class="table-invoice">
                <thead>
                    <tr>
                        <th>TXN Reference</th>
                        <th>Booking / Property Details</th>
                        <th>Date</th>
                        <th class="text-right">Gross (BDT)</th>
                        <th class="text-right">Commission</th>
                        <th class="text-right">Net to Vendor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgers as $l)
                    <tr>
                        <td class="font-mono" style="font-weight: 700; color: #0f172a;">{{ $l->txn_reference }}</td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">{{ $l->property?->name ?? 'Hotel Stay' }}</div>
                            <small style="color: #64748b; display: block; font-size: 11px;">{{ $l->description }}</small>
                        </td>
                        <td style="color: #475569;">{{ $l->created_at ? $l->created_at->format('d M Y') : 'N/A' }}</td>
                        <td class="font-mono text-right">৳{{ number_format($l->gross_amount, 2) }}</td>
                        <td class="font-mono text-right" style="color: #f59e0b;">-৳{{ number_format($l->commission_amount, 2) }}</td>
                        <td class="font-mono text-right" style="font-weight: 700; color: #00b074;">৳{{ number_format($l->net_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 24px; color: #64748b;">No transaction records found for this partner account.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: #f1f5f9; font-weight: 800;">
                        <td colspan="3" style="text-align: right; font-size: 12px; padding: 12px;">NET ACCRUED EARNINGS (AFTER COMMISSION):</td>
                        <td class="font-mono text-right" style="font-size: 12px; padding: 12px;">৳{{ number_format($finance['gross_revenue'], 2) }}</td>
                        <td class="font-mono text-right" style="font-size: 12px; padding: 12px; color: #f59e0b;">-৳{{ number_format($finance['commission_paid'], 2) }}</td>
                        <td class="font-mono text-right" style="font-size: 13.5px; padding: 12px; color: #2067E1;">৳{{ number_format($finance['net_earnings'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Official Authentication & QR Code Section -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: center; padding-top: 14px; border-top: 1px dashed #cbd5e1;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=72x72&data={{ urlencode(url()->current()) }}" alt="QR Code" style="width: 72px; height: 72px; border: 1px solid #cbd5e1; padding: 2px; border-radius: 4px;">
                    <div style="font-size: 11px; color: #64748b;">
                        <strong style="color: #0f172a;">Digital Audit Verification</strong><br>
                        Scan this QR code to verify authentic financial record on Prime Booking central ledger vault.
                    </div>
                </div>

                <div style="text-align: right;">
                    <div style="display: inline-block; text-align: center;">
                        <div style="font-family: cursive; font-size: 18px; color: #2067E1; font-weight: 700; margin-bottom: 2px;">Prime Accounts</div>
                        <div style="width: 150px; border-bottom: 1px solid #94a3b8; margin: 0 auto 4px auto;"></div>
                        <div style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Finance Controller Signature</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div>
                <strong>Prime Booking Network</strong> &bull; House #42, Road #11, Banani, Dhaka-1213, Bangladesh
            </div>
            <div>
                Hotline: <strong class="font-mono">01770887733</strong> &bull; Email: accounts@primebooking.com
            </div>
        </div>
    </div>

    @include('partials.prime_toast')
</body>
</html>
