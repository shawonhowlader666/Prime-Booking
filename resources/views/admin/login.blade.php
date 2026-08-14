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
        }
        .admin-glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 40px rgba(32, 103, 225, 0.15);
            padding: 40px 36px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }
        .admin-badge {
            background: linear-gradient(135deg, rgba(32,103,225,0.2) 0%, rgba(6,182,212,0.2) 100%);
            border: 1px solid rgba(6,182,212,0.4);
            color: #38bdf8;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .form-control-custom {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
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
            border-radius: 12px;
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
            border-radius: 10px;
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

        <!-- Quick 1-Click Demo Credentials -->
        <div class="mb-4">
            <button type="button" class="demo-autofill-btn" id="btnAdminAutofill">
                <i class="fa-solid fa-bolt text-warning"></i> Quick Auto-Fill Admin Credentials
            </button>
        </div>

        <!-- Login Form -->
        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf

            <!-- Email Field -->
            <div class="mb-3">
                <label for="adminEmail" class="form-label fw-semibold" style="font-size: 12.5px; color: #cbd5e1;">Administrator Email</label>
                <div class="position-relative">
                    <input type="email" name="email" id="adminEmail" class="form-control form-control-custom ps-5" placeholder="admin@primebooking.com" value="{{ old('email') }}" required autocomplete="email">
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
            <button type="submit" class="btn btn-admin-submit">
                Sign In to Admin Portal <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </form>

        <!-- Back to Website -->
        <div class="text-center mt-4 pt-2 border-top border-slate-800" style="border-color: rgba(255,255,255,0.08)!important;">
            <a href="{{ route('home') }}" style="color: #94a3b8; font-size: 13px; text-decoration: none; font-weight: 500;" class="hover-white">
                <i class="fa-solid fa-house me-1"></i> Back to Prime Booking Homepage
            </a>
        </div>

    </div>

    <script>
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

        // Auto Fill Admin Credentials Button
        const autofillBtn = document.getElementById('btnAdminAutofill');
        if (autofillBtn) {
            autofillBtn.addEventListener('click', function() {
                document.getElementById('adminEmail').value = 'admin@primebooking.com';
                document.getElementById('adminPassword').value = 'password123';
            });
        }
    </script>
</body>
</html>
