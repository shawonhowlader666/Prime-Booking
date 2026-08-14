<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Partner Portal Sign In | Prime Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body {
            background: linear-gradient(135deg, rgba(4, 18, 26, 0.88) 0%, rgba(8, 30, 42, 0.80) 50%, rgba(2, 12, 18, 0.92) 100%),
                        url('/images/vendor-bg.jpg') center/cover no-repeat fixed;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            margin: 0;
            padding: 24px 20px;
        }

        /* Large SVG Watermark Background Overlay */
        .svg-bg-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90vw;
            max-width: 1100px;
            height: auto;
            opacity: 0.06;
            pointer-events: none;
            z-index: 1;
        }

        /* Background Grid Dot Matrix Overlay */
        .bg-grid-overlay {
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(52, 211, 153, 0.15) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.4;
            pointer-events: none;
            z-index: 1;
        }

        /* Ambient Glowing Spheres */
        .glow-sphere-1 {
            position: fixed;
            top: -120px;
            left: -120px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.28) 0%, rgba(16, 185, 129, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 2;
            animation: pulseGlow 8s ease-in-out infinite alternate;
        }
        
        .glow-sphere-2 {
            position: fixed;
            bottom: -140px;
            right: -140px;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.25) 0%, rgba(14, 165, 233, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 2;
            animation: pulseGlow 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes pulseGlow {
            0% { transform: scale(1) translate(0, 0); opacity: 0.8; }
            100% { transform: scale(1.12) translate(15px, -15px); opacity: 1; }
        }

        /* Clean Glassmorphism Portal Login Card — Strict 4px Border Radius */
        .vendor-glass-card {
            background: rgba(10, 24, 34, 0.84);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(16, 185, 129, 0.35);
            border-radius: 4px !important;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.8), 0 0 40px rgba(16, 185, 129, 0.18);
            padding: 42px 38px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .vendor-glass-card:hover {
            border-color: rgba(52, 211, 153, 0.55);
            box-shadow: 0 35px 70px rgba(0, 0, 0, 0.85), 0 0 50px rgba(16, 185, 129, 0.25);
        }

        /* Header Pill Badge — 4px Radius */
        .vendor-badge {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.22) 0%, rgba(14, 165, 233, 0.22) 100%);
            border: 1px solid rgba(52, 211, 153, 0.45);
            color: #34d399;
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 4px !important;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.15);
        }

        /* Form Inputs — Strict 4px Border Radius */
        .form-control-custom {
            background: rgba(18, 38, 50, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 4px !important;
            color: #ffffff;
            font-size: 14px;
            padding: 12px 16px;
            transition: all 0.25s ease;
        }

        .form-control-custom:focus {
            background: rgba(18, 38, 50, 0.95);
            border-color: #34d399;
            box-shadow: 0 0 18px rgba(52, 211, 153, 0.3);
            color: #ffffff;
        }

        .form-control-custom::placeholder {
            color: #64748b;
        }

        /* Quick Auto Fill Button — 4px Radius */
        .demo-autofill-btn {
            background: rgba(52, 211, 153, 0.12);
            border: 1px dashed rgba(52, 211, 153, 0.45);
            color: #34d399;
            border-radius: 4px !important;
            padding: 10px 14px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .demo-autofill-btn:hover {
            background: rgba(52, 211, 153, 0.24);
            border-color: #34d399;
            color: #6ee7b7;
            transform: translateY(-1px);
        }

        /* Submit Button — 4px Radius */
        .btn-vendor-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 4px !important;
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            padding: 14px;
            width: 100%;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.45);
            transition: all 0.25s ease;
            cursor: pointer;
            letter-spacing: 0.2px;
        }

        .btn-vendor-submit:hover {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(16, 185, 129, 0.65);
            color: #ffffff;
        }

        /* Alert Boxes — 4px Radius */
        .custom-alert {
            border-radius: 4px !important;
        }

        /* Minimal Feature Highlights inside card */
        .partner-trust-badges {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 11.5px;
            color: #94a3b8;
        }
        .partner-trust-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>

    <!-- Ambient Grid & Glow Effects -->
    <div class="bg-grid-overlay"></div>
    <div class="glow-sphere-1"></div>
    <div class="glow-sphere-2"></div>

    <!-- Unique Large SVG Travel Watermark in Background -->
    <svg class="svg-bg-watermark" viewBox="0 0 800 500" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Globe Rings -->
        <circle cx="400" cy="250" r="200" stroke="#10b981" stroke-width="1.5" stroke-dasharray="6 6"/>
        <circle cx="400" cy="250" r="140" stroke="#0ea5e9" stroke-width="1.2"/>
        <ellipse cx="400" cy="250" rx="200" ry="70" stroke="#34d399" stroke-width="1.2"/>
        <ellipse cx="400" cy="250" rx="70" ry="200" stroke="#34d399" stroke-width="1.2"/>
        <!-- Compass Points -->
        <path d="M400 30 L400 470 M180 250 L620 250" stroke="#10b981" stroke-width="1" stroke-dasharray="4 4"/>
        <!-- Aircraft Silhouette -->
        <path d="M400 160 L415 230 L490 260 L415 260 L410 320 L425 335 L400 330 L375 335 L390 320 L385 260 L310 260 L385 230 Z" fill="#34d399"/>
    </svg>

    <!-- Main Clean Glassmorphism Vendor Sign In Card (Border Radius: 4px) -->
    <div class="vendor-glass-card">
        
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="mb-3">
                <span class="vendor-badge">
                    <i class="fa-solid fa-handshake-angle text-emerald-400"></i> VENDOR PARTNER PORTAL
                </span>
            </div>
            <h2 class="fw-extrabold text-white mb-1" style="font-size: 25px; letter-spacing: -0.5px;">Partner Sign In</h2>
            <p class="mb-0" style="font-size: 13px; color: #a7f3d0; font-weight: 500;">
                <i class="fa-solid fa-chart-line me-1"></i> Earn up to 90% Net Revenue Share
            </p>
        </div>

        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="alert alert-success border-0 custom-alert mb-3 py-2 text-center" style="font-size: 13px; background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.35)!important;">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger border-0 custom-alert mb-3 py-2" style="font-size: 13px; background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.35)!important;">
                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <!-- Quick 1-Click Demo Credentials -->
        <div class="mb-4">
            <button type="button" class="demo-autofill-btn" id="btnVendorAutofill">
                <i class="fa-solid fa-bolt text-warning"></i> Quick Auto-Fill Partner Credentials
            </button>
        </div>

        <!-- Login Form -->
        <form action="{{ route('vendor.login.post') }}" method="POST">
            @csrf

            <!-- Email Field -->
            <div class="mb-3">
                <label for="vendorEmail" class="form-label fw-semibold" style="font-size: 12.5px; color: #cbd5e1;">Partner Email Address</label>
                <div class="position-relative">
                    <input type="email" name="email" id="vendorEmail" class="form-control form-control-custom ps-5" placeholder="vendor@primebooking.com.bd" value="{{ old('email') }}" required autocomplete="email">
                    <i class="fa-solid fa-envelope position-absolute" style="left: 16px; top: 15px; color: #64748b; font-size: 14px;"></i>
                </div>
            </div>

            <!-- Password Field -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="vendorPassword" class="form-label fw-semibold mb-0" style="font-size: 12.5px; color: #cbd5e1;">Password</label>
                </div>
                <div class="position-relative">
                    <input type="password" name="password" id="vendorPassword" class="form-control form-control-custom ps-5 pe-5" placeholder="••••••••" required autocomplete="current-password">
                    <i class="fa-solid fa-key position-absolute" style="left: 16px; top: 15px; color: #64748b; font-size: 14px;"></i>
                    <i class="fa-solid fa-eye position-absolute" id="toggleVendorPasswordBtn" style="right: 16px; top: 15px; color: #64748b; font-size: 14px; cursor: pointer;"></i>
                </div>
            </div>

            <!-- Remember Me & Host Property -->
            <div class="d-flex align-items-center justify-content-between mb-4" style="font-size: 13px;">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remember" id="vendorRemember" checked style="background-color: #1e293b; border-color: #475569; border-radius: 4px;">
                    <label class="form-check-label text-slate-300" for="vendorRemember" style="color: #cbd5e1;">
                        Remember me
                    </label>
                </div>
                <a href="{{ route('contact') }}" style="color: #34d399; text-decoration: none; font-weight: 600;">List your Property?</a>
            </div>

            <!-- Submit Button (Border Radius: 4px) -->
            <button type="submit" class="btn btn-vendor-submit">
                Sign In to Partner Portal <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </form>

        <!-- Minimal Trust Badges -->
        <div class="partner-trust-badges">
            <div class="partner-trust-item">
                <i class="fa-solid fa-shield-halved text-emerald-400"></i> Encrypted Session
            </div>
            <div class="partner-trust-item">
                <i class="fa-solid fa-bolt text-cyan-400"></i> Instant Payouts
            </div>
            <div class="partner-trust-item">
                <i class="fa-solid fa-headset text-amber-400"></i> 24/7 Support
            </div>
        </div>

        <!-- Back to Homepage Link -->
        <div class="text-center mt-3 pt-3 border-top border-slate-800" style="border-color: rgba(255,255,255,0.08)!important;">
            <a href="{{ route('home') }}" style="color: #94a3b8; font-size: 13px; text-decoration: none; font-weight: 500; transition: color 0.2s;" onmouseover="this.style.color='#34d399'" onmouseout="this.style.color='#94a3b8'">
                <i class="fa-solid fa-house me-1"></i> Back to Prime Booking Homepage
            </a>
        </div>

    </div>

    <script>
        // Password Show/Hide Toggle
        const toggleVendorBtn = document.getElementById('toggleVendorPasswordBtn');
        const passVendorInput = document.getElementById('vendorPassword');
        if (toggleVendorBtn && passVendorInput) {
            toggleVendorBtn.addEventListener('click', function() {
                const isPass = passVendorInput.type === 'password';
                passVendorInput.type = isPass ? 'text' : 'password';
                toggleVendorBtn.classList.toggle('fa-eye', !isPass);
                toggleVendorBtn.classList.toggle('fa-eye-slash', isPass);
            });
        }

        // Auto Fill Vendor Credentials Button
        const autofillVendorBtn = document.getElementById('btnVendorAutofill');
        if (autofillVendorBtn) {
            autofillVendorBtn.addEventListener('click', function() {
                document.getElementById('vendorEmail').value = 'vendor@primebooking.com.bd';
                document.getElementById('vendorPassword').value = 'vendor123';
            });
        }
    </script>
</body>
</html>
