<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flight e-Ticket Voucher — {{ $ticket['pnr'] }} | Prime Aviation</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 13px; color: #1e293b; background: #f8fafc; margin: 0; padding: 20px; }
        .voucher-card { max-width: 760px; margin: 0 auto; background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; }
        .header-bar { background: linear-gradient(135deg, #0b2545 0%, #1d2b45 100%); color: #fff; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; }
        .pnr-badge { background: #2067e1; color: #fff; font-size: 14px; font-weight: 800; padding: 6px 14px; border-radius: 8px; letter-spacing: 1px; }
        .body-section { padding: 24px; }
        .route-banner { background: #f0f5fc; border-radius: 12px; padding: 18px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border: 1px solid #cbd5e1; }
        .city-code { font-size: 24px; font-weight: 800; color: #0b2545; }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .detail-item { background: #fafafa; border: 1px solid #f1f5f9; padding: 12px 14px; border-radius: 8px; }
        .detail-label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px; }
        .detail-val { font-size: 14px; font-weight: 700; color: #0f172a; }
        .footer-bar { background: #f1f5f9; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; }
        .btn-print { background: #2067e1; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px; }
        @media print { .btn-print, .no-print { display: none; } body { padding: 0; background: #fff; } .voucher-card { box-shadow: none; border: 1px solid #cbd5e1; } }
    </style>
</head>
<body>

    <div class="no-print" style="max-width: 760px; margin: 0 auto 15px auto; text-align: right;">
        <button onclick="window.print()" class="btn-print">🖨️ Print Flight Ticket / Save PDF</button>
    </div>

    <div class="voucher-card">
        <div class="header-bar">
            <div>
                <div style="font-size: 18px; font-weight: 800; letter-spacing: 0.5px;">PRIME AVIATION E-TICKET</div>
                <div style="font-size: 11px; opacity: 0.8;">Official Boarding Pass &amp; Flight Itinerary</div>
            </div>
            <div>
                <span class="pnr-badge">{{ $ticket['pnr'] }}</span>
            </div>
        </div>

        <div class="body-section">
            <div class="route-banner">
                <div>
                    <div class="city-code">{{ $ticket['origin'] }}</div>
                    <div style="font-size: 12px; color: #475569;">Departure: {{ $ticket['departure_time'] }}</div>
                </div>
                <div style="font-size: 20px; color: #2067e1; font-weight: 800;">✈️ ✈️ ✈️</div>
                <div style="text-align: right;">
                    <div class="city-code">{{ $ticket['destination'] }}</div>
                    <div style="font-size: 12px; color: #475569;">Date: {{ $ticket['flight_date'] }}</div>
                </div>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Passenger Name</div>
                    <div class="detail-val">{{ $ticket['passenger_name'] }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Operating Airline</div>
                    <div class="detail-val">{{ $ticket['airline_name'] }} ({{ $ticket['flight_number'] }})</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Assigned Seat</div>
                    <div class="detail-val" style="color: #2067e1;">Seat {{ $ticket['seat'] }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Baggage Allowance</div>
                    <div class="detail-val">20 KG Check-In + 7 KG Cabin</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Ticket Status</div>
                    <div class="detail-val" style="color: #16a34a;">✔ {{ $ticket['status'] }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Total Fare Paid</div>
                    <div class="detail-val">{{ \App\Services\CurrencyService::format($ticket['amount']) }}</div>
                </div>
            </div>

            <div style="border-top: 1px dashed #cbd5e1; padding-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 11px; color: #64748b;">Contact: {{ $ticket['passenger_phone'] }} | {{ $ticket['passenger_email'] }}</div>
                    <div style="font-size: 10.5px; color: #94a3b8; margin-top: 2px;">Please arrive at airport check-in counter 90 minutes before scheduled departure.</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-family: monospace; font-size: 10px; font-weight: bold; background: #f1f5f9; padding: 6px 10px; border-radius: 4px;">
                        QR-VERIFIED: {{ $ticket['pnr'] }}
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bar">
            <div style="font-size: 11px; color: #64748b;">Issued via Prime Aviation Air Mobility System</div>
            <div style="font-size: 11px; font-weight: 700; color: #0b2545;">Issue Timestamp: {{ $ticket['issued_at'] }}</div>
        </div>
    </div>

</body>
</html>
