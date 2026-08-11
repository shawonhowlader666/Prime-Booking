<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Master Reservations Financial Report | Prime Aviation</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #1e293b; margin: 20px; background: #fff; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #2067e1; padding-bottom: 12px; margin-bottom: 20px; }
        .logo { font-size: 20px; font-weight: 800; color: #2067e1; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; }
        th { background: #f8fafc; font-weight: 700; color: #334155; font-size: 11px; text-transform: uppercase; }
        .status-badge { padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .status-confirmed { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .no-print { margin-bottom: 15px; text-align: right; }
        .btn-print { background: #2067e1; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 700; cursor: pointer; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Print / Save as PDF</button>
    </div>

    <div class="header">
        <div>
            <div class="logo">PRIME AVIATION</div>
            <div style="font-size: 11px; color: #64748b;">Master Reservations & Financial Ledger Report</div>
        </div>
        <div style="text-align: right; font-size: 11px; color: #64748b;">
            <div>Generated: {{ now()->format('M d, Y h:i A') }}</div>
            <div>Total Records: {{ count($bookings) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Booking Ref</th>
                <th>Guest Details</th>
                <th>Property</th>
                <th>Check-In / Out</th>
                <th>Amount (BDT)</th>
                <th>Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $idx => $b)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><strong>{{ $b->booking_reference }}</strong></td>
                    <td>
                        <strong>{{ $b->guest_name }}</strong><br>
                        <small style="color: #64748b;">{{ $b->guest_phone }}</small>
                    </td>
                    <td>{{ $b->property?->name ?? 'Hotel Stay' }}</td>
                    <td>{{ $b->check_in }} → {{ $b->check_out }}</td>
                    <td><strong>BDT {{ number_format($b->total_amount, 0) }}</strong></td>
                    <td>{{ ucfirst($b->payment_status) }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($b->booking_status) }}">
                            {{ ucfirst($b->booking_status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No reservation records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 10px; color: #94a3b8; text-align: center;">
        Prime Aviation Confidential Audit Log — Computer Generated Document
    </div>
</body>
</html>
