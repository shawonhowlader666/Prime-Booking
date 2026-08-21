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

    <!-- PWA & Mobile Web App Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2067e1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Prime Booking">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Agoda Custom 100% Design System -->
    <link rel="stylesheet" href="{{ asset('css/agoda-style.css') }}?v={{ time() }}">
    
    <style>
        :root {
            --agoda-blue: #2067e1;
            --agoda-navy: #002d72;
            --agoda-red: #ff567d;
            --agoda-font: 'Barlow', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* ── Ultra-Smooth 60/120 FPS Scrolling & Hardware Acceleration ── */
        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
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
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: none;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Barlow Medium & Crisp Global Typography */
        body, input, button, select, textarea,
        h1, h2, h3, h4, h5, h6, p, a, li, label, td, th {
            font-family: 'Barlow', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-weight: 500;
        }
        .fw-bold, .fw-bolder, strong, b, h1, h2, h3, h4, h5, h6, .hero-title, .search-btn {
            font-weight: 600 !important;
        }

        /* ── Virtual Viewport Content-Visibility (Insanely fast scrolling on 1000+ hotel items) ── */
        .agoda-hotel-card,
        .agoda-room-listing-card,
        .verified-review-card,
        .property-listing-card,
        .agoda-room-card {
            content-visibility: auto;
            contain-intrinsic-size: auto 340px;
            transform: translateZ(0);
            backface-visibility: hidden;
            will-change: transform, opacity;
        }

        /* ── GPU Optimized Image Rendering ── */
        img {
            image-rendering: -webkit-optimize-contrast;
            max-width: 100%;
            height: auto;
        }

        /* ── Sleek Minimalist Scrollbar ── */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
    @yield('head')
</head>
<body>
    {{-- Global Agoda 1:1 Fast 7-Color Dots Loading Overlay --}}
    @include('components.search.loading-skeleton-modal')

    <!-- Master Header Navigation (Starts directly at top, exactly like Agoda.com) -->
    @include('components.layout.header', ['active' => $activePage ?? 'home'])

    <!-- Master Flash Alerts with Auto-Dismiss -->
    @if(session('success'))
        <div class="container mt-3" id="globalMasterSuccessAlert">
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert" style="transition: opacity 0.5s ease, transform 0.5s ease;">
                <i class="fa-solid fa-circle-check me-2 fs-5 align-middle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <script>
            setTimeout(function() {
                var el = document.getElementById('globalMasterSuccessAlert');
                if (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-10px)';
                    setTimeout(function() { el.remove(); }, 500);
                }
            }, 3200);
        </script>
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

    <!-- Agoda / Honey Exact 1:1 Floating Rewards & QR App Popups (Homepage) -->
    @if(request()->routeIs('home') || ($activePage ?? '') === 'home')
        @include('components.floating-marketing-widgets')
    @endif

    <!-- Agoda Native App Bottom Dock Navigation (Mobile Only) -->
    @include('components.layout.mobile-bottom-nav')

    <!-- Agoda Recently Viewed Properties Floating Dock & Drawer -->
    @include('components.recently-viewed-drawer')

    {{-- Mobile PWA Install Floating Prompt (Agoda 1:1 Parity) --}}
    <div id="pwaInstallBanner" style="display: none; position: fixed; bottom: 75px; left: 16px; right: 16px; z-index: 99999; background: #0f172a; color: #fff; padding: 12px 16px; border-radius: 16px; box-shadow: 0 12px 36px rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.15);">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <img src="{{ asset('images/logo.png') }}" style="width: 38px; height: 38px; border-radius: 10px; background: #fff; padding: 2px; flex-shrink: 0;" alt="Prime Booking">
            <div class="flex-grow-1" style="min-width: 0;">
                <div style="font-size: 13px; font-weight: 700; color: #fff; line-height: 1.2;">Install Prime Booking App</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Faster booking &amp; exclusive VIP deals</div>
            </div>
            <button id="pwaInstallBtn" class="btn btn-primary btn-sm fw-bold rounded-pill px-3 py-1.5" style="font-size: 11.5px; background: #2067e1; border: none; flex-shrink: 0;">INSTALL</button>
            <button onclick="dismissPwaPrompt()" class="btn-close btn-close-white p-1" style="font-size: 10px; flex-shrink: 0;" title="Dismiss"></button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Ultra Fast Intersection Observer for scroll-reveal
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, { threshold: 0.05, rootMargin: '0px 0px 80px 0px' });

            document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));

            // 2. High-Performance Native Async Image Decoder for Butter-Smooth Scroll
            document.querySelectorAll('img:not([loading])').forEach(img => {
                img.setAttribute('loading', 'lazy');
                img.setAttribute('decoding', 'async');
            });

            // 3. Service Worker & PWA Installation Logic
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').catch(function(){});
            }

            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                if (!localStorage.getItem('pwa_prompt_dismissed') && window.innerWidth <= 768) {
                    const banner = document.getElementById('pwaInstallBanner');
                    if (banner) banner.style.display = 'block';
                }
            });

            const installBtn = document.getElementById('pwaInstallBtn');
            if (installBtn) {
                installBtn.addEventListener('click', async () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        deferredPrompt = null;
                        document.getElementById('pwaInstallBanner').style.display = 'none';
                    }
                });
            }

            window.dismissPwaPrompt = function() {
                document.getElementById('pwaInstallBanner').style.display = 'none';
                localStorage.setItem('pwa_prompt_dismissed', 'true');
            };
        });
    </script>
    @stack('scripts')
</body>
</html>
