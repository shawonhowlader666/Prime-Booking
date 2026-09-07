<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <title>@yield('title', 'Prime Cloud PMS | Sign In')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Template Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body data-theme="theme-PurpleHeart" class="svgstroke-a bg-light">
    <main class="container-fluid px-0">
        @yield('content')
    </main>

    <!-- JS Scripts -->
    <script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>
    @stack('scripts')
</body>
</html>
