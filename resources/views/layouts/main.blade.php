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
    <title>@yield('title', 'Prime Booking | Hotels, Flights & Ship Cruise Booking in Bangladesh | হোটেল বুকিং বিডি')</title>

    <!-- Comprehensive SEO Meta Tags (Bilingual English & Bangla for BD Market) -->
    <meta name="description" content="@yield('meta_description', 'Book 2,000+ hotels, resorts, ship cruises, domestic flights & airport transfers across Bangladesh at guaranteed lowest prices. Coxs Bazar, Sajek, Sylhet, Sundarban & Dhaka hotel booking. কক্সবাজার, সাজেক ও সিলেট হোটেল বুকিং।')">
    <meta name="keywords" content="@yield('meta_keywords', 'hotel booking bangladesh, online hotel booking bd, cheap hotels coxs bazar, sajek valley resort, sundarban cruise booking, sylhet hotel booking, dhaka 5 star hotel, air ticket booking bangladesh, হোটেল বুকিং বাংলাদেশ, কক্সবাজার হোটেল, সাজেক রিসোর্ট, প্রাইম বুকিং')">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <!-- Geo-Location Meta Tags for Bangladesh SEO ranking -->
    <meta name="geo.region" content="BD-13">
    <meta name="geo.placename" content="Dhaka, Bangladesh">
    <meta name="geo.position" content="23.8103;90.4125">
    <meta name="ICBM" content="23.8103, 90.4125">

    <!-- Open Graph (OG) / Social Media Sharing Tags -->
    <meta property="og:locale" content="en_US">
    <meta property="og:locale:alternate" content="bn_BD">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Prime Booking Bangladesh">
    <meta property="og:title" content="@yield('og_title', 'Prime Booking | Lowest Price Hotel & Flight Booking in Bangladesh')">
    <meta property="og:description" content="@yield('og_description', 'Book top hotels in Coxs Bazar, Sajek, Sylhet & Dhaka with instant confirmation and free cancellation. সেরা দামে বাংলাদেশ হোটেল ও ক্রুজ বুকিং করুন।')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/logo-brand.png'))">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@PrimeBookingBD">
    <meta name="twitter:title" content="@yield('og_title', 'Prime Booking | Lowest Price Hotel & Flight Booking in Bangladesh')">
    <meta name="twitter:description" content="@yield('og_description', 'Book top hotels in Coxs Bazar, Sajek, Sylhet & Dhaka with instant confirmation.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/logo-brand.png'))">

    <!-- Schema.org JSON-LD Structured Data (Google Rich Snippet for Travel Agency) -->
    <script type="application/ld+json">
    {
      "{{ '@context' }}": "https://schema.org",
      "{{ '@type' }}": "TravelAgency",
      "name": "Prime Booking",
      "alternateName": "প্রাইম বুকিং বাংলাদেশ",
      "url": "https://primebooking.com.bd",
      "logo": "https://primebooking.com.bd/images/logo.png",
      "image": "https://primebooking.com.bd/images/logo.png",
      "description": "Leading online travel platform in Bangladesh for booking hotels, resorts, ship cruises, flights and airport transfers.",
      "telephone": "+8801606352642",
      "priceRange": "৳৳ - ৳৳৳৳",
      "address": {
        "{{ '@type' }}": "PostalAddress",
        "streetAddress": "Gulshan 2",
        "addressLocality": "Dhaka",
        "postalCode": "1212",
        "addressCountry": "BD"
      },
      "geo": {
        "{{ '@type' }}": "GeoCoordinates",
        "latitude": 23.8103,
        "longitude": 90.4125
      },
      "sameAs": [
        "https://www.facebook.com/primebookingbd"
      ]
    }
    </script>
    <!-- Brand Favicon & Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Agoda Custom 100% Design System -->
    <link rel="stylesheet" href="{{ asset('css/agoda-style.css') }}?v={{ time() }}">
    
    <style>
        :root {
            --agoda-blue: #2067e1;
            --agoda-navy: #002d72;
            --agoda-red: #ff567d;
            --agoda-font: 'Barlow', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        /* Barlow Medium & Crisp Global Font Reset */
        html, body, input, button, select, textarea,
        h1, h2, h3, h4, h5, h6, p, span, a, li, div, label, td, th {
            font-family: 'Barlow', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-weight: 500;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        /* FontAwesome Icon Exception: Preserve Font Awesome Font Family */
        i, i::before, i::after,
        .fa, .fas, .far, .fal, .fab,
        .fa-solid, .fa-regular, .fa-light, .fa-brands,
        [class*="fa-"], [class*="fa-"]::before, [class*="fa-"]::after {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", "FontAwesome" !important;
            font-weight: 900 !important;
            font-style: normal !important;
        }
        .fw-bold, .fw-bolder, strong, b, h1, h2, h3, h4, h5, h6, .hero-title, .search-btn {
            font-weight: 600 !important;
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
