<!-- Header (Exact 100% Navbar with All Interactive Dropdowns) -->
<header class="px-xl-5 px-lg-4 px-3 sticky-top" data-bs-theme="none">
    <div class="d-flex justify-content-between align-items-center py-2 w-100">
        <div class="d-flex align-items-center pe-4">
            <button class="btn d-inline-flex d-xl-none border-0 p-0 pe-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_Navbar">
                <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" height="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 21a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3" /><path d="M21 6v12a3 3 0 0 1 -6 0v-12a3 3 0 0 1 6 0z" fill="var(--accent-color)" /><path d="M15 12h-8" /><path d="M10 9l-3 3l3 3" /></svg>
            </button>
            <button class="btn d-xl-inline-flex d-none border-0 p-0 pe-2 sidebar-toggle-btn" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" height="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 21a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3" /><path d="M21 6v12a3 3 0 0 1 -6 0v-12a3 3 0 0 1 6 0z" fill="var(--accent-color)" /><path d="M15 12h-8" /><path d="M10 9l-3 3l3 3" /></svg>
            </button>
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="brand-icon text-decoration-none d-flex align-items-center" title="HotelAir Admin Template">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                    <path d="M9 8l0 8" /><path d="M9 12l6 0" /><path d="M15 8l0 8" />
                </svg>
                <span class="fw-bold ps-2 fs-5 d-none d-xl-inline-flex text-gradient">HotelAir</span>
            </a>
        </div>

        <!-- Search Dropdown with Recent Search, Help & New Users -->
        <form class="dropdown main-search me-md-4 w-100 d-none d-md-inline-flex">
            <div class="w-100" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <svg class="svg-stroke search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                    <path d="M21 21l-6 -6"></path>
                </svg>
                <input type="text" class="form-control" placeholder="Search..." aria-label="Search...">
            </div>
            <div class="dropdown-menu shadow rounded-4 p-3 p-xl-4 w-100">
                <h6 class="fl-title" style="--dynamic-color: var(--theme-color1)">Recent Search</h6>
                <div class="tagcloud mb-4">
                    <a href="/hotels.html" class="tag-link rounded-pill border px-3">Hotel</a>
                    <a href="/room-booking-list.html" class="tag-link rounded-pill border px-3">Room Booking</a>
                    <a href="/cab-booking.html" class="tag-link rounded-pill border px-3">Cab Booking</a>
                </div>
                <h6 class="fl-title" style="--dynamic-color: var(--theme-color2)">Help & Docs</h6>
                <div class="d-flex flex-column mb-4">
                    <a class="mb-1 text-decoration-none" href="/signup.html">
                        <svg class="svg-stroke me-1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"></path>
                            <path d="M11 13l9 -9"></path><path d="M15 4h5v5"></path>
                        </svg>
                        <span>New around here? Sign up</span>
                    </a>
                    <a class="mb-1 text-decoration-none" href="{{ route('sync-hub') }}">
                        <svg class="svg-stroke me-1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"></path>
                            <path d="M11 13l9 -9"></path><path d="M15 4h5v5"></path>
                        </svg>
                        <span>Prime Booking OTA Live Sync Hub</span>
                    </a>
                </div>
                <h6 class="fl-title" style="--dynamic-color: var(--theme-color3)">Team Members</h6>
                <div class="d-flex flex-column">
                    <a href="/my-profile.html" class="d-flex align-items-center mb-2 text-decoration-none text-dark">
                        <img class="avatar rounded-circle border border-3 me-2" src="{{ asset('assets/images/xs/avatar1.jpg') }}" alt="avatar" width="28">
                        Kevin Gill
                    </a>
                    <a href="/my-profile.html" class="d-flex align-items-center mb-2 text-decoration-none text-dark">
                        <img class="avatar rounded-circle border border-3 me-2" src="{{ asset('assets/images/xs/avatar2.jpg') }}" alt="avatar" width="28">
                        Michelle Glover
                    </a>
                </div>
            </div>
        </form>

        <!-- Header Right Menu -->
        <ul class="header-menu flex-grow-1">
            <!-- Notification Dropdown -->
            <li class="nav-item dropdown px-md-1 d-none d-md-inline-flex">
                <a class="dropdown-toggle gray-6" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="notification">
                    <span class="bullet-dot bg-primary animation-blink"></span>
                    <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"></path>
                        <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
                        <path d="M21 6.727a11.05 11.05 0 0 0 -2.794 -3.727"></path>
                        <path d="M3 6.727a11.05 11.05 0 0 1 2.792 -3.727"></path>
                    </svg>
                </a>
                <div class="dropdown-menu shadow rounded-4 notification" id="NotificationsDiv">
                    <div class="card border-0">
                        <div class="card-header d-flex justify-content-between align-items-center py-3">
                            <h4 class="mb-0 text-gradient title-font">Notifications</h4>
                            <a href="#" class="btn btn-link" title="view all">View all</a>
                        </div>
                        <ul class="card-body p-0 list-unstyled mb-0 custom_scroll ps-2" style="height: 320px; overflow-y: auto;">
                            <li class="pe-2 border-bottom">
                                <a class="d-flex p-3 rounded-3 text-decoration-none text-dark" href="javascript:void(0);">
                                    <div class="flex-fill">
                                        <span class="d-flex justify-content-between"><small class="text-primary fw-bold">New OTA Booking</small><small class="text-muted">Just now</small></span>
                                        <p class="mb-0 mt-1 small">Incoming reservation received from Prime Booking platform.</p>
                                    </div>
                                </a>
                            </li>
                            <li class="pe-2 border-bottom">
                                <a class="d-flex p-3 rounded-3 text-decoration-none text-dark" href="javascript:void(0);">
                                    <div class="flex-fill">
                                        <span class="d-flex justify-content-between"><small class="text-success fw-bold">Housekeeping Ready</small><small class="text-muted">10m ago</small></span>
                                        <p class="mb-0 mt-1 small">Room 101 has been sanitized and marked AVAILABLE.</p>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>

            <li class="nav-item py-2 py-md-1 col-auto">
                <div class="vr d-none d-sm-flex h-100 mx-sm-2"></div>
            </li>

            <!-- Language Dropdown (Exact 100% Original Author Design) -->
            <li class="nav-item dropdown px-md-1 d-none d-md-inline-flex">
                <a class="dropdown-toggle gray-6" href="#" data-bs-toggle="dropdown" role="button" title="language">
                    <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                        <path d="M3.6 9h16.8"></path><path d="M3.6 15h16.8"></path>
                        <path d="M11.5 3a17 17 0 0 0 0 18"></path><path d="M12.5 3a17 17 0 0 1 0 18"></path>
                    </svg>
                </a>
                <ul class="dropdown-menu shadow rounded-4 p-2 p-xl-3">
                    <li class="mb-1"><a class="dropdown-item rounded-pill active" href="javascript:void(0)" onclick="setAppLanguage('en')" id="lang-btn-en">English</a></li>
                    <li class="mb-1"><a class="dropdown-item rounded-pill" href="javascript:void(0)" onclick="setAppLanguage('bn')" id="lang-btn-bn">Bengali</a></li>
                    <li class="mb-1"><a class="dropdown-item rounded-pill" href="javascript:void(0)" onclick="setAppLanguage('hi')" id="lang-btn-hi">Hindi</a></li>
                    <li class="mb-1"><a class="dropdown-item rounded-pill" href="javascript:void(0)" onclick="setAppLanguage('es')" id="lang-btn-es">Spanish</a></li>
                    <li class="mb-1"><a class="dropdown-item rounded-pill" href="javascript:void(0)" onclick="setAppLanguage('ar')" id="lang-btn-ar">Arabic</a></li>
                </ul>
            </li>

            <!-- Theme Light / Dark Dropdown -->
            <li class="nav-item dropdown px-md-1">
                <a class="dropdown-toggle gray-6 d-flex align-items-center" href="#" id="bd-theme" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="theme-icon-active">
                        <use href="#sun-fill"></use>
                    </svg>
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-2 p-xl-3 shadow rounded-4" aria-labelledby="bd-theme">
                    <li class="mb-1">
                        <button type="button" class="dropdown-item rounded-pill d-flex align-items-center active" data-bs-theme-value="light">
                            <svg class="avatar xs me-2 opacity-50 theme-icon" fill="currentColor" viewBox="0 0 16 16"><use href="#sun-fill"></use></svg>
                            <span>Light</span>
                        </button>
                    </li>
                    <li class="mb-1">
                        <button type="button" class="dropdown-item rounded-pill d-flex align-items-center" data-bs-theme-value="dark">
                            <svg class="avatar xs me-2 opacity-50 theme-icon" fill="currentColor" viewBox="0 0 16 16"><use href="#moon-stars-fill"></use></svg>
                            <span>Dark</span>
                        </button>
                    </li>
                    <li class="mb-1">
                        <button type="button" class="dropdown-item rounded-pill d-flex align-items-center" data-bs-theme-value="auto">
                            <svg class="avatar xs me-2 opacity-50 theme-icon" fill="currentColor" viewBox="0 0 16 16"><use href="#circle-half"></use></svg>
                            <span>Auto</span>
                        </button>
                    </li>
                </ul>
            </li>

            <!-- Template Setting Drawer Trigger -->
            <li class="nav-item dropdown px-md-1">
                <a class="dropdown-toggle gray-6" href="#offcanvas_setting" data-bs-toggle="offcanvas" title="template setting">
                    <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"></path>
                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                    </svg>
                </a>
            </li>

            <li class="nav-item py-2 py-md-1 col-auto">
                <div class="vr d-none d-sm-flex h-100 mx-sm-2"></div>
            </li>

            <!-- User Detail Dropdown with Status Update & Menu Links -->
            <li class="nav-item user ms-3">
                <a class="dropdown-toggle gray-6 d-flex text-decoration-none align-items-center lh-sm p-0" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <img class="avatar rounded-circle border border-3" src="{{ asset('assets/images/profile_av.png') }}" alt="avatar" width="34">
                    <span class="ms-2 fs-6 d-none d-sm-inline-flex">{{ auth()->user()->name ?? 'Staff User' }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow p-2 p-xl-3 rounded-4">
                    <div class="bg-body p-3 rounded-3 mb-2">
                        <h5 class="mb-1 title-font text-gradient">{{ auth()->user()->name ?? 'Staff User' }}</h5>
                        <p class="small text-muted mb-1">{{ auth()->user()->email ?? '' }}</p>
                        <span class="badge bg-primary-subtle text-primary border">{{ auth()->user()->role ?? 'STAFF' }}</span>
                    </div>
                    <ul class="list-unstyled mt-2 mb-3">
                        <li><a class="dropdown-item rounded-pill" href="/my-profile.html"><i class="fa-regular fa-user me-2"></i> My Profile</a></li>
                        <li><a class="dropdown-item rounded-pill" href="{{ route('admin.user.control') }}"><i class="fa-solid fa-users-gear me-2"></i> User Control</a></li>
                        <li><a class="dropdown-item rounded-pill" href="/accounts-setting.html"><i class="fa-solid fa-gear me-2"></i> Settings</a></li>
                    </ul>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn py-2 btn-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-power-off"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</header>
