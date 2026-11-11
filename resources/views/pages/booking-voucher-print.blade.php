<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation Voucher - Prime Booking</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            width: 595.0pt;
            min-height: 842.0pt;
            margin: 0 auto;
            position: relative;
            background: #ffffff;
            font-family: Arial, "Helvetica Neue", Helvetica, "Liberation Sans", sans-serif;
            color: #000000;
            overflow: hidden;
        }
        @media screen {
            body {
                margin: 20px auto;
                box-shadow: 0 0 15px rgba(0,0,0,0.15);
            }
            .no-print {
                display: block;
                text-align: center;
                margin-bottom: 15px;
            }
        }
        @media print {
            body {
                margin: 0;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
        }

        /* Outer Frame */
        .outer-frame {
            position: absolute;
            left: 35.2pt;
            top: 38.6pt;
            width: 531.0pt;
            height: 495.7pt;
            border: 1.1pt solid #000000;
            background: #ffffff;
            z-index: 0;
        }

        /* Header */
        .logo {
            position: absolute;
            left: 41.9pt;
            top: 41.0pt;
            height: 33.0pt;
            width: auto;
            z-index: 1;
        }
        .title-booking {
            position: absolute;
            left: 342.2pt;
            top: 43.5pt;
            font-size: 20.2pt;
            font-weight: bold;
            color: #000000;
            letter-spacing: -0.3px;
            z-index: 1;
        }
        .title-confirmation {
            position: absolute;
            left: 428.5pt;
            top: 43.5pt;
            font-size: 20.2pt;
            font-weight: bold;
            color: #ff0000;
            letter-spacing: -0.3px;
            z-index: 1;
        }
        .subtitle {
            position: absolute;
            left: 250.3pt;
            top: 66.4pt;
            font-size: 7.3pt;
            color: #000000;
            z-index: 1;
        }

        /* Watermark bar */
        .watermark-bar {
            position: absolute;
            left: 41.9pt;
            top: 78.3pt;
            width: 517.6pt;
            height: 10.1pt;
            background: #c7c7c4;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10pt;
            z-index: 1;
        }
        .watermark-item { font-size: 6.2pt; font-weight: bold; color: #ffffff; letter-spacing: 0.5px; }

        /* Left column labels & boxes */
        .f73 { font-size: 7.3pt; line-height: 7.3pt; color: #000000; z-index: 2; }
        .bold { font-weight: bold; }
        .white-box {
            border: 1.12pt solid #dddddd;
            background: #ffffff;
            padding: 0 5pt;
            font-size: 7.3pt;
            font-weight: bold;
            color: #000000;
            display: flex;
            align-items: center;
            z-index: 2;
        }

        .lbl-booking-id { position: absolute; left: 44.7pt; top: 101.1pt; }
        .val-booking-id { position: absolute; left: 144.5pt; top: 101.1pt; }
        
        .lbl-ref-no { position: absolute; left: 44.7pt; top: 116.8pt; }
        .val-ref-no { position: absolute; left: 144.5pt; top: 116.8pt; }

        .lbl-client { position: absolute; left: 44.7pt; top: 132.5pt; }
        .val-client { position: absolute; left: 144.5pt; top: 132.5pt; }

        .lbl-member-id { position: absolute; left: 44.7pt; top: 148.1pt; }
        .val-member-id { position: absolute; left: 144.5pt; top: 148.1pt; }

        .lbl-residence { position: absolute; left: 44.7pt; top: 163.8pt; }
        .val-residence { position: absolute; left: 144.5pt; top: 163.8pt; }

        .lbl-property { position: absolute; left: 44.7pt; top: 179.5pt; }
        .val-property { position: absolute; left: 138.8pt; top: 175.3pt; width: 145.6pt; height: 14.6pt; }

        .lbl-address { position: absolute; left: 44.7pt; top: 196.9pt; }
        .val-address { position: absolute; left: 138.8pt; top: 192.6pt; width: 145.6pt; height: 34.7pt; line-height: 9.5pt; padding: 3pt 5pt; display: block; }

        .lbl-contact { position: absolute; left: 44.7pt; top: 234.4pt; }
        .val-contact { position: absolute; left: 138.8pt; top: 230.2pt; width: 145.6pt; height: 14.6pt; }

        /* Right column grey panel */
        .right-panel {
            position: absolute;
            left: 292.9pt;
            top: 96.8pt;
            width: 266.6pt;
            height: 150.7pt;
            background: #dddddd;
            z-index: 1;
        }
        .right-box {
            border: 1.12pt solid #ffffff !important;
            background: #dddddd !important;
            font-size: 7.3pt;
            font-weight: bold;
            color: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .lbl-rooms { position: absolute; left: 301.3pt; top: 101.1pt; }
        .val-rooms { position: absolute; left: 380.8pt; top: 99.6pt; width: 170.3pt; height: 14.6pt; }

        .lbl-extra-beds { position: absolute; left: 301.3pt; top: 118.5pt; }
        .val-extra-beds { position: absolute; left: 380.8pt; top: 117.0pt; width: 170.3pt; height: 14.6pt; }

        .lbl-adults { position: absolute; left: 301.3pt; top: 135.8pt; }
        .val-adults { position: absolute; left: 380.8pt; top: 134.4pt; width: 170.3pt; height: 14.6pt; }

        .lbl-children { position: absolute; left: 301.3pt; top: 153.2pt; }
        .val-children { position: absolute; left: 380.8pt; top: 151.7pt; width: 170.3pt; height: 14.6pt; }

        .lbl-room-type { position: absolute; left: 301.3pt; top: 170.6pt; }
        .val-room-type { position: absolute; left: 380.8pt; top: 169.1pt; width: 170.3pt; height: 14.6pt; }

        .lbl-promotion { position: absolute; left: 301.3pt; top: 187.9pt; }
        .val-promotion { position: absolute; left: 380.8pt; top: 186.5pt; width: 170.3pt; height: 10.1pt; }

        .promo-subtext {
            position: absolute;
            left: 301.3pt;
            top: 203.6pt;
            font-size: 7.3pt;
            color: #000000;
            z-index: 2;
        }

        /* Cancellation policy */
        .cancellation-box {
            position: absolute;
            left: 41.9pt;
            top: 250.3pt;
            width: 517.6pt;
            height: 25.8pt;
            background: #dddddd;
            padding: 3.5pt 5pt;
            font-size: 7.3pt;
            line-height: 9.5pt;
            color: #000000;
            z-index: 1;
        }

        /* Benefits Included */
        .benefits-box {
            position: absolute;
            left: 41.9pt;
            top: 278.9pt;
            width: 517.6pt;
            height: 15.7pt;
            background: #dddddd;
            padding: 3.5pt 5pt;
            font-size: 7.3pt;
            color: #000000;
            z-index: 1;
        }

        /* Middle Box */
        .middle-frame {
            position: absolute;
            left: 41.9pt;
            top: 297.4pt;
            width: 517.6pt;
            height: 91.9pt;
            border: 1.1pt solid #dddddd;
            background: #ffffff;
            z-index: 1;
        }

        .lbl-arrival { position: absolute; left: 48.7pt; top: 305.6pt; z-index: 2; }
        .pill-arrival {
            position: absolute;
            left: 91.8pt;
            top: 304.1pt;
            width: 131.1pt;
            height: 12.3pt;
            background: #dddddd;
            text-align: center;
            font-size: 7.3pt;
            font-weight: bold;
            line-height: 12.3pt;
            z-index: 2;
        }

        .lbl-departure { position: absolute; left: 228.5pt; top: 305.6pt; z-index: 2; }
        .pill-departure {
            position: absolute;
            left: 294.6pt;
            top: 304.1pt;
            width: 131.1pt;
            height: 12.3pt;
            background: #dddddd;
            text-align: center;
            font-size: 7.3pt;
            font-weight: bold;
            line-height: 12.3pt;
            z-index: 2;
        }

        .pill-payment {
            position: absolute;
            left: 48.7pt;
            top: 322.0pt;
            width: 116.0pt;
            height: 12.3pt;
            background: #dddddd;
            padding-left: 5.6pt;
            font-size: 7.3pt;
            line-height: 12.3pt;
            color: #808080;
            z-index: 2;
        }
        .pill-card {
            position: absolute;
            left: 231.3pt;
            top: 322.0pt;
            width: 127.2pt;
            height: 12.3pt;
            background: #dddddd;
            padding-left: 5.6pt;
            font-size: 7.3pt;
            line-height: 12.3pt;
            color: #808080;
            z-index: 2;
        }

        .lbl-booked-through {
            position: absolute;
            left: 48.7pt;
            top: 338.6pt;
            font-size: 7.3pt;
            font-weight: bold;
            z-index: 2;
        }

        .box-booked-through {
            position: absolute;
            left: 54.3pt;
            top: 350.0pt;
            width: 371.4pt;
            height: 32.5pt;
            background: #dddddd;
            padding: 3.5pt 5.6pt;
            font-size: 7.3pt;
            line-height: 9.5pt;
            color: #000000;
            z-index: 2;
        }

        .sig-container {
            position: absolute;
            left: 434.6pt;
            top: 311.4pt;
            width: 117.1pt;
            height: 70.0pt;
            background: #ffffff;
            z-index: 2;
        }
        .sig-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Remarks & Special Requests */
        .lbl-remarks { position: absolute; left: 41.9pt; top: 396.6pt; }
        .val-remarks { position: absolute; left: 41.9pt; top: 406.8pt; }
        .val-guestlist { position: absolute; left: 41.9pt; top: 416.9pt; }
        .val-special-req { position: absolute; left: 41.9pt; top: 427.0pt; font-weight: bold; }

        /* Notes Box */
        .notes-box {
            position: absolute;
            left: 41.9pt;
            top: 440.0pt;
            width: 517.6pt;
            height: 87.0pt;
            border: 1.1pt solid #dddddd;
            background: #ffffff;
            padding: 7pt 7pt 7pt 13pt;
            font-size: 7.3pt;
            line-height: 10.1pt;
            z-index: 1;
        }
        .notes-title {
            margin-bottom: 3.5pt;
            color: #000000;
        }
        .notes-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .notes-list li {
            position: relative;
            padding-left: 9pt;
            margin-bottom: 1.5pt;
            color: #000000;
        }
        .notes-list li::before {
            content: "●";
            position: absolute;
            left: 0;
            top: -0.5pt;
            font-size: 6pt;
            color: #000000;
        }
        .important-red {
            color: #ee0000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 10px; background: #222; color: #fff;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Print Voucher (PDF)</button>
        <button onclick="window.close()" style="padding: 8px 18px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Close Window</button>
    </div>

    <!-- Outer Frame -->
    <div class="outer-frame"></div>

    <!-- Header -->
    <img src="{{ asset('images/prime_logo_voucher.png') }}" class="logo" alt="Prime Booking">
    <div class="title-booking">Booking</div>
    <div class="title-confirmation">Confirmation</div>
    <div class="subtitle">Please present either an electronic or paper copy of your booking confirmation upon check-in.</div>

    <!-- Watermark Bar (8 items with space) -->
    <div class="watermark-bar">
        <span class="watermark-item">prime booking</span>
        <span class="watermark-item">prime booking</span>
        <span class="watermark-item">prime booking</span>
        <span class="watermark-item">prime booking</span>
        <span class="watermark-item">prime booking</span>
        <span class="watermark-item">prime booking</span>
        <span class="watermark-item">prime booking</span>
        <span class="watermark-item">prime booking</span>
    </div>

    <!-- Left Column Details -->
    <div class="f73 lbl-booking-id">Booking ID :</div>
    <div class="f73 bold val-booking-id">{{ $booking->id ? str_pad($booking->id, 10, '0', STR_PAD_LEFT) : ($booking->booking_id ?? '0969004389') }}</div>

    <div class="f73 lbl-ref-no">Booking Reference No :</div>
    <div class="f73 bold val-ref-no">{{ $booking->booking_reference ?? 'PRM-2026-RL4KNX' }}</div>

    <div class="f73 lbl-client">Client :</div>
    <div class="f73 bold val-client">{{ strtoupper($booking->guest_name ?? $booking->lead_guest_name ?? 'NAZMUL MIA') }}</div>

    <div class="f73 lbl-member-id">Member ID :</div>
    <div class="f73 bold val-member-id">PB-{{ $booking->user_id ?? '20462293' }}</div>

    <div class="f73 lbl-residence">Country of Residence :</div>
    <div class="f73 bold val-residence">{{ $booking->country_of_residence ?? 'Bangladesh / Bangladesh' }}</div>

    <div class="f73 lbl-property">Property :</div>
    <div class="white-box val-property">{{ $booking->property?->name ?? ($booking->hotel_name ?? 'Sea Pearl Beach Resort & Spa') }}</div>

    <div class="f73 lbl-address">Address :</div>
    <div class="white-box val-address">{{ $booking->property?->address ?? ($booking->hotel_address ?? "Jaliapalong, Inani Beach, Marine Drive, Cox's Bazar 4750, Bangladesh") }}</div>

    <div class="f73 lbl-contact">Property Contact Number :</div>
    <div class="white-box val-contact">{{ $booking->property?->contact_phone ?? ($booking->hotel_phone ?? '+8801606352642') }}</div>

    <!-- Right Column Grey Panel -->
    <div class="right-panel"></div>

    <div class="f73 lbl-rooms">Number of Rooms:</div>
    <div class="right-box val-rooms">{{ $booking->rooms_count ?? ($booking->total_rooms ?? '1') }}</div>

    <div class="f73 lbl-extra-beds">Number of Extra Beds :</div>
    <div class="right-box val-extra-beds">{{ $booking->extra_beds ?? '0' }}</div>

    <div class="f73 lbl-adults">Number of Adults :</div>
    <div class="right-box val-adults">{{ $booking->adults ?? ($booking->guests ?? '2') }}</div>

    <div class="f73 lbl-children">Number of Children :</div>
    <div class="right-box val-children">{{ $booking->children ?? '0' }}</div>

    <div class="f73 lbl-room-type">Room Type :</div>
    <div class="right-box val-room-type">{{ $booking->room?->name ?? ($booking->room_name ?? 'Superior Double Room') }}</div>

    <div class="f73 lbl-promotion">Promotion :</div>
    <div class="right-box val-promotion">{{ $booking->promotion ?? 'Special Promotion Deal' }}</div>

    <div class="promo-subtext">For Full Promotion details and conditions see confirmation email</div>

    <!-- Cancellation Policy -->
    <div class="cancellation-box">
        Cancellation Policy: {{ $booking->cancellation_policy ?? 'Stay flexible! Cancel for free before ' . ($booking->check_in ? \Carbon\Carbon::parse($booking->check_in)->subDays(3)->format('F j, Y') : 'arrival date') . '. Any cancellation received within 3 days prior to the arrival date will be charged for the entire stay. Failure to arrive at your hotel or property will be treated as a No-Show and will incur a charge of 100% of the booking value (Hotel policy).' }}
    </div>

    <!-- Benefits Included -->
    <div class="benefits-box">
        Benefits Included {{ $booking->benefits ?? 'Parking, Welcome drink, Free WiFi' }}
    </div>

    <!-- Middle Frame -->
    <div class="middle-frame"></div>

    <div class="f73 bold lbl-arrival">Arrival :</div>
    <div class="pill-arrival">{{ $booking->check_in ? \Carbon\Carbon::parse($booking->check_in)->format('F j, Y') : ($booking->check_in_date ?? 'October 14, 2026') }}</div>

    <div class="f73 bold lbl-departure">Departure :</div>
    <div class="pill-departure">{{ $booking->check_out ? \Carbon\Carbon::parse($booking->check_out)->format('F j, Y') : ($booking->check_out_date ?? 'October 15, 2026') }}</div>

    <div class="pill-payment">Payment Method : {{ $booking->payment_method ? strtoupper(str_replace('_', ' ', $booking->payment_method)) : 'CARD' }}</div>
    <div class="pill-card">Card No : {{ $booking->card_number ?? 'XXXX-XXXX-XXXX-3907' }}</div>

    <div class="lbl-booked-through">Booked And Payable Through :</div>
    <div class="box-booked-through">
        Prime Booking Bangladesh Ltd.<br>
        Gulshan 2, Dhaka 1212, Bangladesh<br>
        Helpline: +880 1606-352642
    </div>

    <div class="sig-container">
        <img src="{{ asset('images/voucher_signature_stamp.jpg') }}" alt="Authorized Stamp & Signature">
    </div>

    <!-- Remarks -->
    <div class="f73 bold lbl-remarks">Remarks :</div>
    <div class="f73 val-remarks">{{ $booking->special_requests ?? ($booking->remarks ?? 'HighFloor, LargeBed, ArrivalTime:14 00 - 15 00, AdditionalNotes:Car parking.') }}</div>
    <div class="f73 val-guestlist">Guest List: {{ strtoupper($booking->guest_name ?? $booking->lead_guest_name ?? 'NAZMUL MIA') }}</div>
    <div class="f73 val-special-req">All special requests are subject to availability upon arrival</div>

    <!-- Notes -->
    <div class="notes-box">
        <div class="notes-title">Notes</div>
        <ul class="notes-list">
            <li><span class="important-red">IMPORTANT:</span> At check-in, you must present a valid photo ID with your address confirming the same name as the lead guest on the booking.</li>
            <li>All rooms are guaranteed on the day of arrival. In the case of a no-show, your room(s) will be released and you will be subject to the terms and conditions of the Cancellation/No-Show Policy specified at the time you made the booking as well as noted in the Confirmation Email.</li>
            <li>The total price for this booking does not include mini-bar items, telephone usage, laundry service, etc. The property will bill you directly.</li>
            <li>In cases where Breakfast is included with the room rate, please note that certain properties may charge extra for children travelling with their parents. If applicable, the property will bill you directly. Upon arrival, if you have any questions, please verify with the property.</li>
        </ul>
    </div>
</body>
</html>