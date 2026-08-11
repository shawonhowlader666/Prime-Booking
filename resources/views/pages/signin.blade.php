@extends('layouts.main')

@section('title', 'Sign In to PRIME BOOKING | Admin, Vendor & User Login')

@section('content')
<div class="py-5" style="background-color: #f8fafc; min-height: 82vh; display: flex; align-items: center; justify-content: center;">
    <div class="container d-flex justify-content-center">
        <div class="card border-0 shadow-lg" style="width: 100%; max-width: 460px; border-radius: 16px; overflow: hidden; background: #ffffff;">
            <div class="card-body p-4 p-md-4">
                
                {{-- Brand Logo & Title --}}
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <x-logo height="44" />
                    </div>
                    <h4 class="fw-bold text-dark mb-1" style="font-size: 20px; letter-spacing: -0.3px;">Sign in to your account</h4>
                    <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.4;">Access Super Admin Control, Vendor Hotel Management, or Guest Bookings.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3 small" role="alert">
                        <i class="fa-solid fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3 small" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Role Quick Demo Buttons --}}
                <div class="mb-4 p-3 bg-light rounded-3 text-center border">
                    <small class="text-muted fw-bold d-block mb-2 uppercase" style="font-size: 11px;">QUICK ROLE DEMO SIGN-IN</small>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="quickFill('admin@primeavn.com', 'admin123')">
                            👑 Super Admin
                        </button>
                        <button type="button" class="btn btn-sm btn-warning text-dark fw-bold" onclick="quickFill('vendor@primeavn.com', 'vendor123')">
                            🏨 Hotel Vendor
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" onclick="quickFill('01770887733', '123456')">
                            👤 Guest User
                        </button>
                    </div>
                </div>

                {{-- Email/Phone Login Form --}}
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    
                    {{-- Email or Phone Input --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Email Address or Mobile Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-secondary border-end-0"><i class="fa-solid fa-user" style="color: #2067e1;"></i></span>
                            <input type="text" name="login_credential" id="inputCredential" class="form-control border-start-0 rounded-end-2" placeholder="gmail (e.g. admin@primeavn.com) or phone" required style="font-size: 14px; height: 44px;">
                        </div>
                    </div>

                    {{-- Password Input --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-secondary border-end-0"><i class="fa-solid fa-lock" style="color: #2067e1;"></i></span>
                            <input type="password" name="password" id="inputPassword" class="form-control border-start-0 rounded-end-2" placeholder="Enter password" required style="font-size: 14px; height: 44px;">
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3" style="font-size: 12.5px;">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
                            <label class="form-check-label text-secondary" for="rememberMe">Remember me</label>
                        </div>
                        <a href="#" class="text-decoration-none fw-semibold" style="color: #2067e1;">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn text-white w-100 fw-bold py-2.5 shadow-xs mb-3" style="background-color: #2067e1; border-radius: 24px; font-size: 15px; height: 46px; border: none;">
                        Sign In <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </form>

                {{-- Footer Disclaimer --}}
                <div class="text-center mt-3 text-secondary" style="font-size: 11.5px; line-height: 1.5; color: #64748b !important;">
                    By signing in, you agree to PRIME BOOKING's 
                    <a href="{{ route('terms') }}" class="text-decoration-none fw-semibold" style="color: #2067e1;">Terms of Use</a> 
                    and 
                    <a href="{{ route('privacy') }}" class="text-decoration-none fw-semibold" style="color: #2067e1;">Privacy Policy</a>.
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function quickFill(cred, pass) {
    document.getElementById('inputCredential').value = cred;
    document.getElementById('inputPassword').value = pass;
}
</script>
@endsection

