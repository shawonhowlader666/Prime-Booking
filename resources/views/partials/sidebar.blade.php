<!-- start: page menu link -->
<aside class="ps-xl-5 ps-lg-4 ps-3 pe-2 py-3 sidebar sticky-top" data-bs-theme="none">
    <nav class="navbar navbar-expand-xl py-0">
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvas_Navbar">
            <div class="offcanvas-header">
                <span class="fw-bold fs-5 text-gradient">HotelAir</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body flex-column custom_scroll ps-4 ps-xl-0">
                <div class="d-flex align-items-start mb-4">
                    <img class="avatar lg rounded-circle border border-3" src="{{ asset('assets/images/profile_av.png') }}" alt="avatar">
                    <div class="ms-3 mt-1">
                        <h4 class="mb-0 text-gradient title-font">Anny glover</h4>
                        <span class="text-muted">Super Admin</span>
                    </div>
                </div>

                <!-- start: Menu tab -->
                <ul class="nav nav-tabs tab-dynamic px-0 mb-4" role="tablist" style="--dynamic-color: var(--accent-color)">
                    <li class="nav-item" role="presentation">
                        <a class="ps-2 pe-3 nav-link active" data-bs-toggle="tab" href="#tab_hotels" role="tab">HOTELS</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="ps-2 pe-3 nav-link" data-bs-toggle="tab" href="#tab_hrms" role="tab">HRMS</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="ps-2 pe-3 nav-link" data-bs-toggle="tab" href="#tab_settings" role="tab">TUNING</a>
                    </li>
                </ul>

                <!-- start: Menu content -->
                <div class="tab-content">
                    <!-- start: Hotel tab-->
                    <div class="tab-pane fade active show" id="tab_hotels" role="tabpanel">
                        <h6 class="fl-title title-font ps-2 small text-uppercase text-muted" style="--dynamic-color: var(--theme-color1)">Universal</h6>
                        <ul class="list-unstyled mb-4 menu-list">
                            <li>
                                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" aria-label="Hotel Dashboard">
                                    <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                                        <path d="M10 12h4v4h-4z"></path>
                                    </svg>
                                    <span class="mx-3">Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="/index-two.html" aria-label="Hotel Activity">
                                    <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                                        <path d="M10 12h4v4h-4z"></path>
                                    </svg>
                                    <span class="mx-3">Hotel Activity</span>
                                </a>
                            </li>
                            <li>
                                <a href="/hotels.html" aria-label="Hotel Front">
                                    <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 21l18 0" />
                                        <path d="M4 21v-11l2.5 -4.5l5.5 -2.5l5.5 2.5l2.5 4.5v11" />
                                        <path d="M12 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M9 21v-5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v5" />
                                    </svg>
                                    <span class="mx-3">Hotels</span>
                                </a>
                            </li>
                            <li>
                                <a href="/transaction.html" aria-label="Transaction list">
                                    <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"></path>
                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"></path>
                                    </svg>
                                    <span class="mx-3">Transaction</span>
                                </a>
                            </li>
                            <li>
                                <a href="#RoomMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle" aria-label="Users">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 12v.01" />
                                        <path d="M3 21h18" />
                                        <path d="M6 21v-16a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v16" />
                                    </svg>
                                    <span class="mx-3">Room Book</span>
                                </a>
                                <ul class="collapse list-unstyled" id="RoomMenu">
                                    <li><a href="/room-booking-list.html">Booking List</a></li>
                                    <li><a href="/checkin-out.html">Room Checkout</a></li>
                                    <li><a href="/room-status.html">Room Status</a></li>
                                    <li><a href="{{ route('matrix') }}">Room Matrix Grid</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#RoomFacilitesMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle" aria-label="Users">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M7 3m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                        <path d="M12 3v2" />
                                        <path d="M10 15v.01" /><path d="M10 18v.01" /><path d="M14 18v.01" /><path d="M14 15v.01" />
                                    </svg>
                                    <span class="mx-3">Room Facilites</span>
                                </a>
                                <ul class="collapse list-unstyled" id="RoomFacilitesMenu">
                                    <li><a href="/room-facilites-list.html">Facilites List</a></li>
                                    <li><a href="/facilites-details.html">Facilites Details</a></li>
                                    <li><a href="/room-size.html">Room Size</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#HousekeepingMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle" aria-label="Users">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M8.5 10a1.5 1.5 0 0 1 -1.5 -1.5a5.5 5.5 0 0 1 11 0v10.5a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2c0 -1.38 .71 -2.444 1.88 -3.175l4.424 -2.765c1.055 -.66 1.696 -1.316 1.696 -2.56a2.5 2.5 0 1 0 -5 0a1.5 1.5 0 0 1 -1.5 1.5z" />
                                    </svg>
                                    <span class="mx-3">House keeping</span>
                                </a>
                                <ul class="collapse list-unstyled" id="HousekeepingMenu">
                                    <li><a href="/housekeeping-assign-room.html">Assign Room</a></li>
                                    <li><a href="/housekeeping-roomcleaning.html">Room Cleaning</a></li>
                                    <li><a href="/housekeeping-Checklist.html">Checklist</a></li>
                                    <li><a href="/housekeeping-qrlist.html">QR List</a></li>
                                    <li><a href="/housekeeping-report.html">Cleaning Report</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#CarbookingMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle" aria-label="Users">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6m-6 -6h15m-6 0v-5" />
                                    </svg>
                                    <span class="mx-3">Cab Facility</span>
                                </a>
                                <ul class="collapse list-unstyled" id="CarbookingMenu">
                                    <li><a href="/cab-list.html">Cab List</a></li>
                                    <li><a href="/cab-booking.html">Cab Booking</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#ItemsManageMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle" aria-label="Users">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M13 5h8" /><path d="M13 9h5" /><path d="M13 15h8" /><path d="M13 19h5" />
                                        <path d="M3 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                        <path d="M3 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    </svg>
                                    <span class="mx-3">Items Manage</span>
                                </a>
                                <ul class="collapse list-unstyled" id="ItemsManageMenu">
                                    <li><a href="/items-unit-list.html">Items Unit List</a></li>
                                    <li><a href="/items-list.html">Items List</a></li>
                                    <li><a href="/items-destroyed-list.html">Items Destroyed</a></li>
                                    <li><a href="/items-category-list.html">Items Category</a></li>
                                    <li><a href="/items-suppliers-list.html">Items Suppliers</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#PersonalisedMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle" aria-label="Users">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 8m-5 0a5 5 0 1 0 10 0a5 5 0 1 0 -10 0" />
                                        <path d="M8 11a5 5 0 1 0 3.998 1.997" />
                                        <path d="M12.002 19.003a5 5 0 1 0 3.998 -8.003" />
                                    </svg>
                                    <span class="mx-3">Personalised</span>
                                </a>
                                <ul class="collapse list-unstyled" id="PersonalisedMenu">
                                    <li><a href="/personalised-jacuzzi.html">Jacuzzi</a></li>
                                    <li><a href="/personalised-swimmingpool.html">Swimming pool</a></li>
                                    <li><a href="/personalised-spa.html">Spa</a></li>
                                    <li><a href="/personalised-pedicure.html">Pedicure</a></li>
                                    <li><a href="/personalised-theater.html">Theater</a></li>
                                    <li><a href="/personalised-banquet.html">Banquet</a></li>
                                    <li><a href="/personalised-settings.html">All Settings</a></li>
                                </ul>
                            </li>
                            <li class="py-2 mt-2">
                                <h6 class="fl-title title-font ps-2 small text-uppercase text-muted" style="--dynamic-color: var(--theme-color1)">Reports</h6>
                                <ul class="list-unstyled">
                                    <li><a href="/booking-report.html">Booking Reports</a></li>
                                    <li><a href="/purchase-report.html">Purchase Reports</a></li>
                                    <li><a href="/stock-report.html">Stock Reports</a></li>
                                </ul>
                            </li>
                            <li class="py-2 mt-2">
                                <h6 class="fl-title title-font ps-2 small text-uppercase text-muted" style="--dynamic-color: var(--theme-color1)">Rooms Settings</h6>
                                <ul class="list-unstyled">
                                    <li><a href="/bed-list.html">Bed List</a></li>
                                    <li><a href="/booking-type-list.html">Booking Type List</a></li>
                                    <li><a href="/commission-list.html">Booking Commission</a></li>
                                    <li><a href="/complementary-list.html">Complementary List</a></li>
                                    <li><a href="/floor-plan-list.html">Floor Plan List</a></li>
                                    <li><a href="/room-list.html">Room List</a></li>
                                    <li><a href="/room-image.html">Room Images</a></li>
                                    <li><a href="/promocode-list.html">Promocode List</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <!-- start: HR tab -->
                    <div class="tab-pane fade" id="tab_hrms" role="tabpanel">
                        <h6 class="fl-title title-font ps-2 small text-uppercase text-muted" style="--dynamic-color: var(--theme-color1)">Usual</h6>
                        <ul class="list-unstyled mb-4 menu-list">
                            <li>
                                <a href="/index-hr.html" aria-label="HRMS Dashboard">
                                    <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                                        <path d="M10 12h4v4h-4z"></path>
                                    </svg>
                                    <span class="mx-3">HR Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="/hrms.html" aria-label="hrms">
                                    <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M5 22v-5l-1 -1v-4a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4l-1 1v5" />
                                        <path d="M17 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M15 22v-4h-2l2 -6a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1l2 6h-2v4" />
                                    </svg>
                                    <span class="mx-3">HRMS</span>
                                </a>
                            </li>
                            <li>
                                <a href="#DutyMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle" aria-label="Users">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 0 0 5.998 8.485m12.002 -8.485a9 9 0 1 0 -18 0" />
                                        <path d="M12 7v5" /><path d="M12 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                        <path d="M18 15v2a1 1 0 0 0 1 1h1" />
                                        <path d="M21 15v6" />
                                    </svg>
                                    <span class="mx-3">Duty Changes</span>
                                </a>
                                <ul class="collapse list-unstyled" id="DutyMenu">
                                    <li><a href="/duty-assigned-list.html">Assigned List</a></li>
                                    <li><a href="/duty-shift-list.html">Shift List</a></li>
                                    <li><a href="/duty-roster-list.html">Roster List</a></li>
                                </ul>
                            </li>
                            <li class="py-2 mt-2">
                                <h6 class="fl-title title-font ps-2 small text-uppercase text-muted" style="--dynamic-color: var(--theme-color1)">Employees [ <strong>58</strong> ]</h6>
                                <ul class="list-unstyled">
                                    <li><a href="/employees-all.html">All Employees</a></li>
                                    <li><a href="/employees-leave.html">Leave Tracking</a></li>
                                    <li><a href="/employees-attendance.html">Attendance</a></li>
                                    <li><a href="/employees-departments.html">Departments</a></li>
                                    <li><a href="/employees-loan.html">Employees Loan</a></li>
                                    <li><a href="/employees-appraisal.html">Appraisal</a></li>
                                    <li><a href="/employees-add.html" class="fw-bold">Add Employees</a></li>
                                </ul>
                            </li>
                            <li class="py-2 mt-2">
                                <h6 class="fl-title title-font ps-2 small text-uppercase text-muted" style="--dynamic-color: var(--theme-color1)">Payroll</h6>
                                <ul class="list-unstyled">
                                    <li><a href="/payroll-employee-salary.html">Employee Salary</a></li>
                                    <li><a href="/payroll-payslip.html">Payslip</a></li>
                                    <li><a href="/payroll-report.html">Report</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <!-- start: Setting tab-->
                    <div class="tab-pane fade" id="tab_settings" role="tabpanel">
                        <h6 class="fl-title title-font ps-2 small text-uppercase text-muted" style="--dynamic-color: var(--theme-color1)">Configuration</h6>
                        <ul class="list-unstyled mb-4 menu-list">
                            <li>
                                <a href="/tuning.html">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M19.875 6.27a2.225 2.225 0 0 1 1.125 1.948v7.284c0 .809 -.443 1.555 -1.158 1.948l-6.75 4.27a2.269 2.269 0 0 1 -2.184 0l-6.75 -4.27a2.225 2.225 0 0 1 -1.158 -1.948v-7.285c0 -.809 .443 -1.554 1.158 -1.947l6.75 -3.98a2.33 2.33 0 0 1 2.25 0l6.75 3.98h-.033z" />
                                        <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                    </svg>
                                    <span class="mx-3">Tuning</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.user.control') }}" class="text-primary fw-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                    </svg>
                                    <span class="mx-3">User & Access Control</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sync-hub') }}" class="text-danger fw-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                    </svg>
                                    <span class="mx-3">Prime OTA Sync</span>
                                </a>
                            </li>
                            <li>
                                <a href="/common-settings.html">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" />
                                    </svg>
                                    <span class="mx-3">Common</span>
                                </a>
                            </li>
                            <li>
                                <a href="/payment-set.html">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 19h-6a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v4.5" />
                                        <path d="M3 10h18" /><path d="M16 19h6" /><path d="M19 16l3 3l-3 3" /><path d="M7.005 15h.005" /><path d="M11 15h2" />
                                    </svg>
                                    <span class="mx-3">Payment Set</span>
                                </a>
                            </li>
                            <li>
                                <a href="/accounts-setting.html">
                                    <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                        <path d="M9 7l1 0"></path><path d="M9 13l6 0"></path><path d="M13 17l2 0"></path>
                                    </svg>
                                    <span class="mx-3">Accounts</span>
                                </a>
                            </li>
                            <li class="py-2 mt-2">
                                <h6 class="fl-title title-font ps-2 small text-uppercase text-muted" style="--dynamic-color: var(--theme-color1)">Role Managment</h6>
                                <ul class="list-unstyled">
                                    <li><a href="/role-add.html">Role Add</a></li>
                                    <li><a href="/role-list.html">Role List</a></li>
                                    <li><a href="/role-permission.html">Role Permission</a></li>
                                    <li><a href="/role-create-list.html">Role Create</a></li>
                                    <li><a href="/role-assign.html">Role Assign</a></li>
                                    <li><a href="/role-access-list.html">Role Access</a></li>
                                </ul>
                            </li>
                            <li class="py-2 mt-2">
                                <h6 class="fl-title title-font ps-2 small text-uppercase text-muted" style="--dynamic-color: var(--theme-color1)">Front Pages</h6>
                                <ul class="list-unstyled">
                                    <li><a href="/about-us.html">About Us</a></li>
                                    <li><a href="/contact-us.html">Contact Us</a></li>
                                    <li><a href="/privacy-policy.html">Privacy Policy</a></li>
                                    <li><a href="/terms-conditions.html">Terms Conditions</a></li>
                                </ul>
                            </li>
                            <li class="py-2 mt-2">
                                <h6 class="fl-title title-font ps-2 small text-uppercase text-muted" style="--dynamic-color: var(--theme-color1)">Application</h6>
                                <ul class="list-unstyled">
                                    <li><a href="/calendar.html">My Calendar</a></li>
                                    <li><a href="/inbox.html">Inbox (98)</a></li>
                                    <li><a href="/chat.html">Chat (2)</a></li>
                                    <li><a href="/blog.html">Blog Article</a></li>
                                    <li><a href="/filemanager.html">File Manager</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</aside>
