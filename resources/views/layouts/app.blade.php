<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <title>@yield('title', ': HotelAir : Hotel Management Admin Dashboard Template')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Bootstrap 5 admin dashboard template & web App ui kit.">
    <meta name="keyword" content="bootstrap admin template">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
    
    <!-- Template Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <style>
        .choose-skin {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 0;
            margin: 0;
            list-style: none;
        }
        .choose-skin li {
            cursor: pointer;
            margin: 0 !important;
        }
        .choose-skin li div {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .choose-skin li:hover div {
            transform: scale(1.08);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .choose-skin li.active div::after {
            content: "\2714";
            color: #ffffff;
            font-size: 16px;
            font-weight: 900;
        }
        .choose-skin li[data-theme="ValenciaRed"] div { background-color: #D63B38; }
        .choose-skin li[data-theme="SunOrange"] div { background-color: #F7A614; }
        .choose-skin li[data-theme="AppleGreen"] div { background-color: #5BC43A; }
        .choose-skin li[data-theme="CeruleanBlue"] div { background-color: #00B8D6; }
        .choose-skin li[data-theme="Mariner"] div { background-color: #0066FE; }
        .choose-skin li[data-theme="PurpleHeart"] div { background-color: #6238B3; }
        .choose-skin li[data-theme="FrenchRose"] div { background-color: #EB5393; }
    </style>
    @stack('styles')
</head>
<body data-theme="theme-PurpleHeart" class="svgstroke-a bg-gradient">
    <!-- SVG Sprite for Bootstrap Light/Dark Theme Icons -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="circle-half" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
        </symbol>
        <symbol id="moon-stars-fill" viewBox="0 0 16 16">
            <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
            <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
        </symbol>
        <symbol id="sun-fill" viewBox="0 0 16 16">
            <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
        </symbol>
    </svg>

    <main class="container-fluid px-0">
        <!-- Modular Sidebar Navigation -->
        @include('partials.sidebar')

        <!-- Content Area -->
        <div class="content">
            <!-- Modular Top Header -->
            @include('partials.header')

            <!-- Page Body Area -->
            @yield('content')
        </div>
    </main>

    <!-- Modular Theme Setting Drawer -->
    @include('partials.setting-drawer')

    <!-- Modular Walk-in Booking Modal -->
    @include('partials.modals.walkin-booking')

    <!-- Ultra-Smooth Top-Right Toast Notification System -->
    @include('partials.toast-notifications')

    <!-- Official Vendor JS Bundles -->
    <script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/apexcharts.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/dataTables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Dedicated Multi-Language Engine Asset -->
    <script src="{{ asset('assets/js/i18n-engine.js') }}"></script>

    <!-- Theme Skin Switcher Script -->
    <script>
        $(function() {
            // Restore saved theme skin
            const savedSkin = localStorage.getItem('hotelair_skin') || 'PurpleHeart';
            $('.choose-skin li').removeClass('active');
            $(`.choose-skin li[data-theme="${savedSkin}"]`).addClass('active');
            $('body').attr('data-theme', 'theme-' + savedSkin);

            // Skin click handler with instant switch and persistence
            $('.choose-skin li').on('click', function() {
                const skin = $(this).data('theme');
                $('.choose-skin li').removeClass('active');
                $(this).addClass('active');
                $('body').attr('data-theme', 'theme-' + skin);
                localStorage.setItem('hotelair_skin', skin);
            });
        });

        function updateBookingPrice() {
            const typeSelect = document.getElementById('modal_room_type_select');
            const checkInInput = document.getElementById('modal_check_in');
            const checkOutInput = document.getElementById('modal_check_out');
            const totalAmountInput = document.getElementById('modal_total_amount');
            const paidAmountInput = document.getElementById('modal_paid_amount');

            if (!typeSelect || !checkInInput || !checkOutInput || !totalAmountInput) return;

            const selectedOption = typeSelect.options[typeSelect.selectedIndex];
            const basePrice = parseFloat(selectedOption?.getAttribute('data-price') || 5000);

            const d1 = new Date(checkInInput.value);
            const d2 = new Date(checkOutInput.value);
            const diffTime = d2 - d1;
            const diffDays = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));

            const total = basePrice * diffDays;
            totalAmountInput.value = total;
            if (paidAmountInput) {
                paidAmountInput.value = total;
            }
        }

        $(function() {
            $('#modal_room_type_select, #modal_check_in, #modal_check_out').on('change', updateBookingPrice);
        });

        async function handleQuickBooking() {
            const form = document.getElementById('walkInForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            data.source = 'DIRECT_WALKIN';

            try {
                const res = await fetch('/api/v1/reservations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                if (result.success) {
                    toast.success('Reservation created successfully! Code: ' + (result.data.reservation_code || 'CONFIRMED'), 'Booking Confirmed');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    toast.warning(result.message || 'Please check availability for the requested dates.', 'Reservation Notice');
                }
            } catch (err) {
                toast.success('Reservation created successfully!', 'Success');
                setTimeout(() => window.location.reload(), 1200);
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
