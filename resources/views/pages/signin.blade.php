<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Bootstrap 5 admin dashboard template & web App ui kit.">
    <meta name="keyword" content="bootstrap admin template">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>: HotelAir : Hotel Management Admin Dashboard</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}">

    <!-- Template main css file -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body data-theme="theme-PurpleHeart" class="svgstroke-a auth bg-gradient">

    <!-- start: main grid layout -->
    <main class="container-fluid px-0">

        <!-- start: project logo -->
        <div class="px-md-5 px-4 auth-header" data-bs-theme="none">
            <a href="/" class="brand-icon text-decoration-none d-flex align-items-center" title="HotelAir Admin Template">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"></path>
                    <path d="M9 8l0 8"></path>
                    <path d="M9 12l6 0"></path>
                    <path d="M15 8l0 8"></path>
                </svg>
                <span class="fw-bold ps-2 fs-5 text-gradient">HotelAir</span>
            </a>
        </div>

        <!-- start: page body area -->
        <div class="px-md-5 px-4 auth-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show small py-2 mb-3" role="alert">
                    <i class="fa-solid fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show small py-2 mb-3" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <ul class="row g-3 list-unstyled li_animate">
                    <li class="col-12">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="admin@primepms.com" value="{{ old('email', 'admin@primepms.com') }}" required autofocus>
                    </li>
                    <li class="col-12">
                        <div class="form-label">
                            <span class="d-flex justify-content-between align-items-center">
                                Password
                                <a class="text-primary" href="/password-reset.html">Forgot Password?</a>
                            </span>
                        </div>
                        <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="••••••••" value="password" required>
                    </li>
                    <li class="col-12">
                        <div class="form-check fs-5">
                            <input class="form-check-input" type="checkbox" name="remember" id="Rememberme" checked>
                            <label class="form-check-label fs-6" for="Rememberme">Remember this Device</label>
                        </div>
                    </li>
                    <li class="col-12 my-lg-4">
                        <button type="submit" class="btn btn-lg w-100 btn-primary text-uppercase mb-2 shadow-sm">SIGN IN</button>
                    </li>
                    <li class="col-12 text-center">
                        <span class="text-muted d-flex d-sm-inline-flex">New to HotelAir <a class="ms-2 text-primary" href="/signup.html" title="Sign up">Sign up here</a></span>
                    </li>
                </ul><!--[ ul.row end ]-->
            </form>
        </div>

        <!-- start: page footer -->
        <footer class="px-xl-5 px-4">
            <p class="mb-0 text-muted">&copy; {{ date('Y') }} <a href="#" target="_blank" title="hotelair">HotelAir</a>, All Rights Reserved.</p>
        </footer>
        
    </main>

    <!-- Vendor Scripts -->
    <script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>

    <!-- Ultra-Smooth Top-Right Toast Notification System -->
    @include('partials.toast-notifications')
</body>
</html>
