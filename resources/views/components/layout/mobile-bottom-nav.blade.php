<!-- Agoda Native App Bottom Navigation Bar (Visible only on Mobile & Mobile Devices < 768px) -->
<div class="d-flex d-md-none position-fixed bottom-0 start-0 end-0 bg-white border-top shadow-lg justify-content-around align-items-center py-2 px-1" 
     style="z-index: 1050; border-top: 1px solid #e2e8f0 !important; background: #ffffff !important; backdrop-filter: blur(10px);">

    <!-- Nav Item 1: Search / Explore (Active) -->
    <a href="{{ route('home') }}" class="text-decoration-none d-flex flex-column align-items-center flex-fill py-1 {{ request()->routeIs('home') ? 'text-primary' : 'text-secondary' }}" style="color: {{ request()->routeIs('home') ? '#2067e1 !important' : '#64748b !important' }};">
        <i class="fa-solid fa-magnifying-glass fs-5 mb-1" style="{{ request()->routeIs('home') ? 'transform: scale(1.1);' : '' }}"></i>
        <span style="font-size: 10px; font-weight: 700;">Explore</span>
    </a>

    <!-- Nav Item 2: Packages & Deals -->
    <a href="{{ route('packages') }}" class="text-decoration-none d-flex flex-column align-items-center flex-fill py-1 {{ request()->routeIs('packages') ? 'text-primary' : 'text-secondary' }}" style="color: {{ request()->routeIs('packages') ? '#2067e1 !important' : '#64748b !important' }};">
        <i class="fa-solid fa-tags fs-5 mb-1"></i>
        <span style="font-size: 10px; font-weight: 700;">Deals</span>
    </a>

    <!-- Nav Item 3: Services & Transport -->
    <a href="{{ route('services') }}" class="text-decoration-none d-flex flex-column align-items-center flex-fill py-1 {{ request()->routeIs('services') ? 'text-primary' : 'text-secondary' }}" style="color: {{ request()->routeIs('services') ? '#2067e1 !important' : '#64748b !important' }};">
        <i class="fa-solid fa-bus-simple fs-5 mb-1"></i>
        <span style="font-size: 10px; font-weight: 700;">Transport</span>
    </a>

    <!-- Nav Item 4: Contact / Inquiry -->
    <a href="{{ route('contact') }}" class="text-decoration-none d-flex flex-column align-items-center flex-fill py-1 {{ request()->routeIs('contact') ? 'text-primary' : 'text-secondary' }}" style="color: {{ request()->routeIs('contact') ? '#2067e1 !important' : '#64748b !important' }};">
        <i class="fa-solid fa-headset fs-5 mb-1"></i>
        <span style="font-size: 10px; font-weight: 700;">Support</span>
    </a>

    <!-- Nav Item 5: Account & Bookings -->
    @auth
    <a href="{{ route('account.bookings') }}" class="text-decoration-none d-flex flex-column align-items-center flex-fill py-1 {{ request()->routeIs('account.bookings') ? 'text-primary' : 'text-secondary' }}" style="color: {{ request()->routeIs('account.bookings') ? '#2067e1 !important' : '#64748b !important' }};">
        <i class="fa-solid fa-ticket fs-5 mb-1"></i>
        <span style="font-size: 10px; font-weight: 700;">Bookings</span>
    </a>
    @else
    <button type="button" data-bs-toggle="modal" data-bs-target="#agodaAuthModal" class="btn p-0 text-decoration-none d-flex flex-column align-items-center flex-fill py-1 border-0 bg-transparent text-secondary" style="color: #64748b !important;">
        <i class="fa-solid fa-circle-user fs-5 mb-1"></i>
        <span style="font-size: 10px; font-weight: 700;">Account</span>
    </button>
    @endauth

</div>

<style>
    /* Add padding to body on mobile so bottom bar does not overlap footer/content */
    @media (max-width: 767px) {
        body {
            padding-bottom: 64px !important;
        }
    }
</style>
