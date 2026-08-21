<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Financial Settlement Statement — Prime Booking</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #2067E1;
            --dark: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            padding: 30px 15px;
            font-size: 13px;
        }
        .statement-wrapper {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 36px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            position: relative;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .brand-logo {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .brand-logo span { color: #0f172a; }
        .statement-title-box {
            text-align: right;
        }
        .statement-badge {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .meta-block h5 {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .meta-block p {
            margin-bottom: 3px;
            font-size: 12.5px;
        }
        .meta-block strong { color: var(--dark); }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        .sum-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 12px 14px;
            border-top: 3px solid var(--primary);
        }
        .sum-card.success { border-top-color: #28c76f; }
        .sum-card.warning { border-top-color: #ff9f43; }
        .sum-card.info { border-top-color: #00cfe8; }
        .sum-card span {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }
        .sum-card h4 {
            font-size: 16px;
            font-weight: 800;
            color: var(--dark);
            margin: 0;
        }
        table.statement-tbl {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        table.statement-tbl th {
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 9px 12px;
            text-align: left;
        }
        table.statement-tbl th.text-right { text-align: right; }
        table.statement-tbl td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            font-size: 12px;
        }
        table.statement-tbl td.text-right { text-align: right; }
        table.statement-tbl tr:nth-child(even) { background-color: #fafbfc; }
        
        .footer-signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px dashed var(--border);
        }
        .qr-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .qr-img {
            width: 72px;
            height: 72px;
            border: 1px solid var(--border);
            padding: 2px;
            border-radius: 3px;
        }
        .sign-line {
            width: 180px;
            border-top: 1px solid #0f172a;
            text-align: center;
            padding-top: 6px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .action-bar {
            max-width: 860px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #1754be; }

        @media print {
            body { background: #ffffff; padding: 0; }
            .statement-wrapper { border: none; box-shadow: none; padding: 10px 0; width: 100%; max-width: 100%; }
            .action-bar { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <a href="javascript:history.back()" style="color:#64748b; text-decoration:none; font-size:13px; font-weight:600;">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
        </a>
        <button class="btn-print" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Print Settlement Statement
        </button>
    </div>

    <div class="statement-wrapper">
        {{-- HEADER TOP --}}
        <div class="header-top">
            <div>
                <div class="brand-logo">
                    <i class="fa-solid fa-hotel"></i> PRIME <span>BOOKING</span>
                </div>
                <p style="color:var(--text-muted); font-size:11.5px; margin-top:3px;">Prime Booking Bangladesh &bull; OTA Hospitality Network</p>
                <p style="color:var(--text-muted); font-size:11px;">BIN / VAT Reg: 004928192-0101 &bull; Dhaka, Bangladesh</p>
            </div>
            <div class="statement-title-box">
                <span class="statement-badge">Official Financial Statement</span>
                <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Statement Ref: <strong style="color:var(--dark);">PBS-{{ date('Ym') }}-{{ str_pad((string)$vendor->id, 4, '0', STR_PAD_LEFT) }}</strong></p>
                <p style="font-size:11.5px; color:var(--text-muted);">Generated on: <strong>{{ date('d M Y, h:i A') }}</strong></p>
            </div>
        </div>

        {{-- METADATA GRID --}}
        <div class="meta-grid">
            <div class="meta-block">
                <h5>Partner / Hotel Beneficiary:</h5>
                <p><strong>{{ $vendor->name }}</strong></p>
                <p>Email: {{ $vendor->email }}</p>
                <p>Phone: {{ $vendor->phone ?? 'N/A' }}</p>
                <p>Properties: <strong>{{ $properties->pluck('name')->implode(', ') ?: 'Hotel Listing' }}</strong></p>
            </div>
            <div class="meta-block">
                <h5>Settlement Period &amp; Currency:</h5>
                <p>Accounting Period: <strong>Lifetime to Date ({{ date('d M Y') }})</strong></p>
                <p>Base Currency: <strong>BDT (Bangladeshi Taka - ৳)</strong></p>
                <p>OTA Commission Rate: <strong>12.00% Standard Service Fee</strong></p>
                <p>Payment Schedule: <strong>Weekly / Bi-Monthly Auto Settlement</strong></p>
            </div>
        </div>

        {{-- KPI SUMMARY CARDS --}}
        <div class="summary-cards">
            <div class="sum-card">
                <span>Gross Turnover (GBV)</span>
                <h4>৳{{ number_format($finance['gross_revenue'], 2) }}</h4>
            </div>
            <div class="sum-card warning">
                <span>Commission (12%)</span>
                <h4 style="color:#ff9f43;">-৳{{ number_format($finance['commission_paid'], 2) }}</h4>
            </div>
            <div class="sum-card info">
                <span>Total Settled (Paid)</span>
                <h4 style="color:#00cfe8;">৳{{ number_format($finance['payouts_paid'], 2) }}</h4>
            </div>
            <div class="sum-card success">
                <span>Current Withdrawable</span>
                <h4 style="color:#28c76f;">৳{{ number_format($finance['withdrawable_balance'], 2) }}</h4>
            </div>
        </div>

        {{-- DETAILED STATEMENT TABLE --}}
        <table class="statement-tbl">
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
                    <td><strong>{{ $l->txn_reference }}</strong></td>
                    <td>
                        {{ $l->property?->name ?? 'Hotel Stay' }}
                        <small style="color:var(--text-muted); display:block;">{{ $l->description }}</small>
                    </td>
                    <td>{{ $l->created_at ? $l->created_at->format('d M Y') : 'N/A' }}</td>
                    <td class="text-right">৳{{ number_format($l->gross_amount, 2) }}</td>
                    <td class="text-right" style="color:#ff9f43;">-৳{{ number_format($l->commission_amount, 2) }}</td>
                    <td class="text-right" style="font-weight:700; color:#28c76f;">৳{{ number_format($l->net_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:20px; color:var(--text-muted);">No transaction records found for this partner account.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background:#f1f5f9; font-weight:800;">
                    <td colspan="3" style="text-align:right; font-size:12.5px; padding:12px;">NET ACCRUED EARNINGS (AFTER COMMISSION):</td>
                    <td class="text-right" style="font-size:12.5px; padding:12px;">৳{{ number_format($finance['gross_revenue'], 2) }}</td>
                    <td class="text-right" style="font-size:12.5px; padding:12px; color:#ff9f43;">-৳{{ number_format($finance['commission_paid'], 2) }}</td>
                    <td class="text-right" style="font-size:13.5px; padding:12px; color:var(--primary);">৳{{ number_format($finance['net_earnings'], 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- FOOTER / SIGNATURES & QR --}}
        <div class="footer-signatures">
            <div class="qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(url()->current()) }}" class="qr-img" alt="QR Code">
                <div>
                    <strong style="font-size:11.5px; display:block;">Digital Audit Verification</strong>
                    <span style="font-size:10.5px; color:var(--text-muted);">Scan QR to verify authentic financial record on Prime Booking Vault.</span>
                </div>
            </div>
            <div>
                <div class="sign-line">
                    Finance Controller Signature<br>
                    <small style="color:var(--text-muted); font-size:10px;">Prime Booking Accounts &amp; Audit</small>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
