<!--
========================================================================
 Prime Booking Online Travel Platform
 Architect & Lead Software Engineer: Shawon Howlader (Software Engineer)
 Contact / Support Hotline: +880 1606-352642
 Tech Stack: Laravel 11, PHP 8.3+, MySQL, Bootstrap 5, Agoda Design System
========================================================================
-->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Shawon Howlader - Software Engineer (+8801606352642)">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Prime Booking | Online Travel Agency & Lowest Price Guarantee')</title>

    <!-- Brand Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-icon.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Agoda Custom 100% Design System -->
    <link rel="stylesheet" href="{{ asset('css/agoda-style.css') }}">
    
    <style>
        :root {
            --agoda-blue: #2067e1;
            --agoda-navy: #002d72;
            --agoda-red: #ff567d;
            /* Agoda.com Exact Confirmed Font Stack */
            --agoda-font: BlinkMacSystemFont, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        /* Agoda Exact Global Font Reset */
        html, body, input, button, select, textarea,
        h1, h2, h3, h4, h5, h6, p, span, a, li, div, label {
            font-family: BlinkMacSystemFont, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
        body {
            background: linear-gradient(180deg, #e2eafc 0%, #edf2fb 50%, #f4f7fc 100%);
            background-attachment: fixed;
            color: #1b2631;
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 1.5;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    {{-- Global Agoda 1:1 Skeleton Loading Overlay --}}
    @include('components.search.loading-skeleton-modal')

    <!-- Master Header Navigation (Starts directly at top, exactly like Agoda.com) -->
    @include('components.layout.header', ['active' => $activePage ?? 'home'])

    <!-- Master Flash Alerts -->
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2 fs-5 align-middle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content Slot -->
    <main>
        @yield('content')
    </main>

    <!-- Master Footer -->
    @include('components.layout.footer')

    <!-- Agoda Sign In / Register Modal -->
    @include('components.auth-modal')

    <!-- Google One Tap Sign In Card Widget (Guest Mode) -->
    @include('components.google-one-tap')


    <!-- Agoda Native App Bottom Dock Navigation (Mobile Only) -->
    @include('components.layout.mobile-bottom-nav')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, { threshold: 0.05, rootMargin: '0px 0px 80px 0px' });

            document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
        });
    </script>
    @stack('scripts')
</body>
</html>
