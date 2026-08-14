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
            background-color: #061a23;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }
        .glow-sphere-vendor-1 {
            position: fixed;
            top: -120px;
            left: -120px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(16,185,129,0.25) 0%, rgba(16,185,129,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .glow-sphere-vendor-2 {
            position: fixed;
            bottom: -150px;
            right: -150px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(14,165,233,0.22) 0%, rgba(14,165,233,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .vendor-glass-card {
            background: rgba(15, 30, 39, 0.78);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65), 0 0 40px rgba(16, 185, 129, 0.15);
            padding: 40px 36px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }
        .vendor-badge {
            background: linear-gradient(135deg, rgba(16,185,129,0.2) 0%, rgba(14,165,233,0.2) 100%);
            border: 1px solid rgba(16,185,129,0.4);
            color: #34d399;
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
            background: rgba(22, 43, 56, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            color: #ffffff;
            font-size: 14px;
            padding: 12px 16px;
            transition: all 0.25s ease;
        }
        .form-control-custom:focus {
            background: rgba(22, 43, 56, 0.95);
            border-color: #34d399;
            box-shadow: 0 0 16px rgba(52, 211, 153, 0.25);
            color: #ffffff;
        }
        .btn-vendor-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            padding: 13px;
            width: 100%;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .btn-vendor-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(16, 185, 129, 0.6);
            color: #ffffff;
        }
        .demo-autofill-btn {
            background: rgba(52, 211, 153, 0.1);
            border: 1px dashed rgba(52, 211, 153, 0.4);
            color: #34d399;
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
            background: rgba(52, 211, 153, 0.2);
        }
    </style>
</head>
<body>

    <div class="glow-sphere-vendor-1"></div>
    <div class="glow-sphere-vendor-2"></div>

    <div class="vendor-glass-card">
        
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="mb-3">
                <span class="vendor-badge">
                    <i class="fa-solid fa-handshake"></i> VENDOR PARTNER PORTAL
                </span>
            </div>
            <h2 class="fw-bold text-white mb-1" style="font-size: 24px; letter-spacing: -0.5px;">Partner Sign In</h2>
            <p class="mb-0" style="font-size: 13px; color: #a7f3d0;">
                <i class="fa-solid fa-chart-line me-1"></i> Earn up to 90% Net Revenue Share
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
                    <input type="email" name="email" id="vendorEmail" class="form-control form-control-custom ps-5" placeholder="vendor@primebooking.com" value="{{ old('email') }}" required autocomplete="email">
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
                    <input class="form-check-input" type="checkbox" name="remember" id="vendorRemember" checked style="background-color: #1e293b; border-color: #475569;">
                    <label class="form-check-label text-slate-300" for="vendorRemember" style="color: #cbd5e1;">
                        Remember me
                    </label>
                </div>
                <a href="{{ route('contact') }}" style="color: #34d399; text-decoration: none; font-weight: 600;">List your Property?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-vendor-submit">
                Sign In to Partner Portal <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </form>

        <!-- Back to Website -->
        <div class="text-center mt-4 pt-2 border-top border-slate-800" style="border-color: rgba(255,255,255,0.08)!important;">
            <a href="{{ route('home') }}" style="color: #94a3b8; font-size: 13px; text-decoration: none; font-weight: 500;">
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
