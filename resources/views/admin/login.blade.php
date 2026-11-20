<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Portal Sign In | Prime Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background-color: #090d16;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }
        .glow-sphere-1 {
            position: fixed;
            top: -120px;
            right: -120px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(32,103,225,0.28) 0%, rgba(32,103,225,0) 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: pulseGlowAdmin 8s ease-in-out infinite alternate;
        }

        .admin-blob-mid {
            position: fixed;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.3) 0%, transparent 70%);
            filter: blur(80px);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 2;
            animation: pbBlobFloatAdmin 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes pbBlobFloatAdmin {
            0%   { transform: translate(-50%, -50%) scale(1);    }
            50%  { transform: translate(-50%, calc(-50% + 28px)) scale(1.07); }
            100% { transform: translate(-50%, calc(-50% - 22px)) scale(0.95); }
        }
        .glow-sphere-2 {
            position: fixed;
            bottom: -150px;
            left: -150px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(6,182,212,0.22) 0%, rgba(6,182,212,0) 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: pulseGlowAdmin 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes pulseGlowAdmin {
            0% { transform: scale(1) translate(0, 0); opacity: 0.8; }
            100% { transform: scale(1.12) translate(15px, -15px); opacity: 1; }
        }
        .admin-glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 4px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 40px rgba(32, 103, 225, 0.15);
            padding: 40px 36px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
            overflow: hidden;
        }

        .admin-glass-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                120deg,
                transparent   0%,
                transparent  30%,
                rgba(255,255,255,0.12) 48%,
                rgba(255,255,255,0.18) 50%,
                rgba(255,255,255,0.12) 52%,
                transparent  70%,
                transparent 100%
            );
            background-size: 240% 100%;
            background-position: 240% 0;
            border-radius: 4px !important;
            pointer-events: none;
            animation: pbShimmerSweep 3.6s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes pbShimmerSweep {
            0%   { background-position: 240%  0; }
            60%  { background-position: -40%  0; }
            100% { background-position: -40%  0; }
        }

        .admin-glass-card > * { position: relative; z-index: 1; }
        .admin-badge {
            background: linear-gradient(135deg, rgba(32,103,225,0.2) 0%, rgba(6,182,212,0.2) 100%);
            border: 1px solid rgba(6,182,212,0.4);
            color: #38bdf8;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 4px !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .form-control-custom {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 4px !important;
            color: #ffffff;
            font-size: 14px;
            padding: 12px 16px;
            transition: all 0.25s ease;
        }
        .form-control-custom:focus {
            background: rgba(30, 41, 59, 0.9);
            border-color: #38bdf8;
            box-shadow: 0 0 16px rgba(56, 189, 248, 0.25);
            color: #ffffff;
        }
        .btn-admin-submit {
            background: linear-gradient(135deg, #2067e1 0%, #0284c7 100%);
            border: none;
            border-radius: 4px !important;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            padding: 13px;
            width: 100%;
            box-shadow: 0 8px 24px rgba(32, 103, 225, 0.4);
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .btn-admin-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(32, 103, 225, 0.6);
            color: #ffffff;
        }
        .demo-autofill-btn {
            background: rgba(56, 189, 248, 0.1);
            border: 1px dashed rgba(56, 189, 248, 0.4);
            color: #38bdf8;
            border-radius: 4px !important;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .demo-autofill-btn:hover {
            background: rgba(56, 189, 248, 0.2);
        }
    </style>
</head>
<body>

    <div class="glow-sphere-1"></div>
    <div class="glow-sphere-2"></div>
    <div class="admin-blob-mid"></div>

    <div class="admin-glass-card">
        
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="mb-3">
                <span class="admin-badge">
                    <i class="fa-solid fa-crown"></i> SUPER ADMIN PORTAL
                </span>
            </div>
            <h2 class="fw-bold text-white mb-1" style="font-size: 24px; letter-spacing: -0.5px;">Control Center Sign In</h2>
            <p class="text-slate-400 mb-0" style="font-size: 13px; color: #94a3b8;">
                <i class="fa-solid fa-lock text-cyan-400 me-1" style="color: #38bdf8;"></i> 256-Bit SSL Encrypted Access
            </p>
        </div>

        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3 mb-3 py-2 text-center" style="font-size: 13px; background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3);">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-3 py-2" style="font-size: 13px; background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3);">
                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
            </div>
        @endif


        <!-- Google 1-Click Super Admin Sign In -->
        <a href="{{ route('oauth.redirect', 'google') }}" class="btn btn-outline-light w-100 mb-3 d-flex align-items-center justify-content-center gap-2 fw-semibold" style="height: 44px; border: 1px solid rgba(255,255,255,0.22); background: rgba(255,255,255,0.07); font-size: 13.5px; border-radius: 4px !important; color: #fff; text-decoration: none; transition: all 0.2s ease;">
            <svg width="18" height="18" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Continue with Google</span>
        </a>

        <div class="d-flex align-items-center gap-2 mb-3">
            <hr class="flex-grow-1 m-0" style="border-color: rgba(255,255,255,0.15);">
            <span class="text-slate-400" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8;">OR ENTER CREDENTIALS</span>
            <hr class="flex-grow-1 m-0" style="border-color: rgba(255,255,255,0.15);">
        </div>

        <!-- Login Form -->
        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf

            <!-- Email Field -->
            <div class="mb-3">
                <label for="adminEmail" class="form-label fw-semibold" style="font-size: 12.5px; color: #cbd5e1;">Administrator Email</label>
                <div class="position-relative">
                    <input type="email" name="email" id="adminEmail" class="form-control form-control-custom ps-5" placeholder="primebooking.bd@gmail.com" value="{{ old('email', 'primebooking.bd@gmail.com') }}" required autocomplete="email">
                    <i class="fa-solid fa-envelope position-absolute" style="left: 16px; top: 15px; color: #64748b; font-size: 14px;"></i>
                </div>
            </div>

            <!-- Password Field -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="adminPassword" class="form-label fw-semibold mb-0" style="font-size: 12.5px; color: #cbd5e1;">Secure Password</label>
                </div>
                <div class="position-relative">
                    <input type="password" name="password" id="adminPassword" class="form-control form-control-custom ps-5 pe-5" placeholder="••••••••" required autocomplete="current-password">
                    <i class="fa-solid fa-key position-absolute" style="left: 16px; top: 15px; color: #64748b; font-size: 14px;"></i>
                    <i class="fa-solid fa-eye position-absolute" id="togglePasswordBtn" style="right: 16px; top: 15px; color: #64748b; font-size: 14px; cursor: pointer;"></i>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="d-flex align-items-center justify-content-between mb-4" style="font-size: 13px;">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remember" id="adminRemember" checked style="background-color: #1e293b; border-color: #475569;">
                    <label class="form-check-label text-slate-300" for="adminRemember" style="color: #cbd5e1;">
                        Remember session
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-admin-submit mb-3">
                Sign In to Admin Portal <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>

            <!-- Quick Auto-Fill Helper -->
            <button type="button" class="demo-autofill-btn mb-2" onclick="fillAdminCreds()">
                <i class="fa-solid fa-key" style="color: #38bdf8;"></i> Auto-fill Official Admin Credentials
            </button>
            <div class="text-center" style="font-size: 11.5px; color: #94a3b8;">
                <i class="fa-solid fa-circle-info me-1" style="color: #38bdf8;"></i>
                Default: <strong style="color: #f1f5f9;">primebooking.bd@gmail.com</strong> • Pass: <strong style="color: #f1f5f9;">password</strong>
            </div>
        </form>

        <!-- Back to Website -->
        <div class="text-center mt-4 pt-2 border-top border-slate-800" style="border-color: rgba(255,255,255,0.08)!important;">
            <a href="{{ route('home') }}" style="color: #94a3b8; font-size: 13px; text-decoration: none; font-weight: 500;" class="hover-white">
                <i class="fa-solid fa-house me-1"></i> Back to Prime Booking Homepage
            </a>
        </div>

    </div>

    <script>
        function fillAdminCreds() {
            document.getElementById('adminEmail').value = 'primebooking.bd@gmail.com';
            document.getElementById('adminPassword').value = 'password';
        }

        // Password Show/Hide Toggle
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passInput = document.getElementById('adminPassword');
        if (toggleBtn && passInput) {
            toggleBtn.addEventListener('click', function() {
                const isPass = passInput.type === 'password';
                passInput.type = isPass ? 'text' : 'password';
                toggleBtn.classList.toggle('fa-eye', !isPass);
                toggleBtn.classList.toggle('fa-eye-slash', isPass);
            });
        }
    </script>
    @include('partials.prime_toast')
</body>
</html>
