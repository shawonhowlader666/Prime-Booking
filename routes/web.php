<?php

use App\Http\Controllers\Web\AdminUserController;
use App\Http\Controllers\Web\AmenityController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CabController;
use App\Http\Controllers\Web\CommunicationController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\FinancialBillingController;
use App\Http\Controllers\Web\HousekeepingController;
use App\Http\Controllers\Web\HrmsController;
use App\Http\Controllers\Web\InventoryController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\RoomConfigController;
use App\Http\Controllers\Web\RoomOperationsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Prime Cloud PMS (Secured & Modular)
|--------------------------------------------------------------------------
*/

// Public / Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/signin.html', [AuthController::class, 'showLogin']);
    Route::get('/signup.html', fn() => view('pages.signup'))->name('register');
    Route::post('/signup', [AuthController::class, 'register'])->name('register.post');
    Route::get('/password-reset.html', fn() => view('pages.password-reset'))->name('password.reset');
    Route::post('/password-reset', fn() => redirect('/signin.html')->with('success', 'Password reset instructions dispatched.'))->name('password.email');
    Route::get('/two-step.html', fn() => view('pages.two-step'))->name('auth.two_step');
    Route::get('/lockscreen.html', fn() => view('pages.lockscreen'))->name('auth.lockscreen');
    Route::get('/maintenance.html', fn() => view('pages.maintenance'))->name('maintenance');
    Route::get('/404.html', fn() => view('pages.404'))->name('error.404');
});

// Authenticated Routes (Requires Active Staff Session)
Route::middleware('auth')->group(function () {
    // Session Management
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboards
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard.html', [DashboardController::class, 'index']);
    Route::get('/index.html', [DashboardController::class, 'index']);
    Route::get('/index-hr.html', [DashboardController::class, 'index'])->name('hrms.dashboard');
    Route::get('/hotel-dashboard', [DashboardController::class, 'hotelDashboard'])->name('hotel.dashboard');
    Route::get('/index-two.html', fn() => view('pages.index-two'))->name('hotel.activity');

    // Hotels & Rooms Management Module
    Route::get('/hotels.html', fn() => view('pages.hotels'))->name('hotels.front');
    Route::get('/matrix', [DashboardController::class, 'matrix'])->name('matrix');
    Route::get('/rooms', [RoomOperationsController::class, 'roomList'])->name('rooms');
    Route::get('/room-list.html', [RoomOperationsController::class, 'roomList'])->name('rooms.list');
    Route::post('/rooms', [RoomOperationsController::class, 'storeRoom'])->name('rooms.store');
    Route::get('/reservations', [DashboardController::class, 'reservations'])->name('reservations');
    Route::get('/room-booking-list.html', [RoomOperationsController::class, 'roomBookingList'])->name('room.bookings.list');
    Route::get('/checkin-out.html', [RoomOperationsController::class, 'checkinOut'])->name('checkin.out');
    Route::get('/room-status.html', [RoomOperationsController::class, 'roomStatus'])->name('room.status');
    // Room Master Configuration Module
    Route::get('/room-facilites-list.html', [RoomConfigController::class, 'facilities'])->name('room.facilities');
    Route::post('/room/facilities', [RoomConfigController::class, 'storeFacility'])->name('room.facilities.store');
    Route::delete('/room/facilities/{id}', [RoomConfigController::class, 'destroyFacility'])->name('room.facilities.destroy');
    Route::get('/facilites-details.html', [RoomConfigController::class, 'facilityDetails'])->name('room.facilities.details');

    Route::get('/bed-list.html', [RoomConfigController::class, 'beds'])->name('room.beds');
    Route::post('/room/beds', [RoomConfigController::class, 'storeBed'])->name('room.beds.store');
    Route::delete('/room/beds/{id}', [RoomConfigController::class, 'destroyBed'])->name('room.beds.destroy');

    Route::get('/room-size.html', [RoomConfigController::class, 'sizes'])->name('room.sizes');
    Route::post('/room/sizes', [RoomConfigController::class, 'storeSize'])->name('room.sizes.store');
    Route::delete('/room/sizes/{id}', [RoomConfigController::class, 'destroySize'])->name('room.sizes.destroy');

    Route::get('/booking-type-list.html', [RoomConfigController::class, 'bookingTypes'])->name('room.booking_types');
    Route::post('/room/booking-types', [RoomConfigController::class, 'storeBookingType'])->name('room.booking_types.store');
    Route::delete('/room/booking-types/{id}', [RoomConfigController::class, 'destroyBookingType'])->name('room.booking_types.destroy');

    Route::get('/commission-list.html', [RoomConfigController::class, 'commissions'])->name('room.commissions');
    Route::post('/room/commissions', [RoomConfigController::class, 'storeCommission'])->name('room.commissions.store');
    Route::delete('/room/commissions/{id}', [RoomConfigController::class, 'destroyCommission'])->name('room.commissions.destroy');

    Route::get('/complementary-list.html', [RoomConfigController::class, 'complementary'])->name('room.complementary');
    Route::post('/room/complementary', [RoomConfigController::class, 'storeComplementary'])->name('room.complementary.store');
    Route::delete('/room/complementary/{id}', [RoomConfigController::class, 'destroyComplementary'])->name('room.complementary.destroy');

    Route::get('/promocode-list.html', [RoomConfigController::class, 'promocodes'])->name('room.promocodes');
    Route::post('/room/promocodes', [RoomConfigController::class, 'storePromocode'])->name('room.promocodes.store');
    Route::delete('/room/promocodes/{id}', [RoomConfigController::class, 'destroyPromocode'])->name('room.promocodes.destroy');

    Route::get('/floor-plan-list.html', [RoomOperationsController::class, 'floorPlans'])->name('room.floor_plans');
    Route::post('/floor-plans', [RoomOperationsController::class, 'storeFloorPlan'])->name('room.floor_plans.store');
    Route::post('/floor-plans/assign', [RoomOperationsController::class, 'assignFloor'])->name('room.floor_plans.assign');
    Route::get('/room-image.html', [RoomOperationsController::class, 'roomImages'])->name('room.images');
    Route::post('/room-images', [RoomOperationsController::class, 'storeRoomImage'])->name('room.images.store');

    // Cab & Transport Facility Module
    Route::get('/cab-list.html', [CabController::class, 'index'])->name('cabs.index');
    Route::get('/cabs', [CabController::class, 'index']);
    Route::post('/cabs', [CabController::class, 'store'])->name('cabs.store');
    Route::post('/cabs/{id}', [CabController::class, 'update'])->name('cabs.update');
    Route::delete('/cabs/{id}', [CabController::class, 'destroy'])->name('cabs.destroy');

    Route::get('/cab-booking.html', [CabController::class, 'bookings'])->name('cabs.bookings');
    Route::get('/cab-booking', [CabController::class, 'bookings']);
    Route::post('/cab-booking', [CabController::class, 'storeBooking'])->name('cabs.bookings.store');

    // Housekeeping Operations Module
    Route::get('/housekeeping', [DashboardController::class, 'housekeeping'])->name('housekeeping');
    Route::get('/housekeeping-assign-room.html', [HousekeepingController::class, 'assignRoom'])->name('housekeeping.assign');
    Route::post('/housekeeping/assign', [HousekeepingController::class, 'storeAssignment'])->name('housekeeping.assign.store');

    Route::get('/housekeeping-roomcleaning.html', [HousekeepingController::class, 'roomCleaning'])->name('housekeeping.cleaning');
    Route::post('/housekeeping/complete/{id}', [HousekeepingController::class, 'completeCleaning'])->name('housekeeping.complete');

    Route::get('/housekeeping-Checklist.html', [HousekeepingController::class, 'checklist'])->name('housekeeping.checklist');
    Route::post('/housekeeping/checklist', [HousekeepingController::class, 'storeChecklist'])->name('housekeeping.checklist.store');
    Route::post('/housekeeping/checklist/{id}', [HousekeepingController::class, 'updateChecklist'])->name('housekeeping.checklist.update');
    Route::delete('/housekeeping/checklist/{id}', [HousekeepingController::class, 'destroyChecklist'])->name('housekeeping.checklist.destroy');

    Route::get('/housekeeping-qrlist.html', [HousekeepingController::class, 'qrList'])->name('housekeeping.qrlist');
    Route::get('/housekeeping-report.html', [HousekeepingController::class, 'report'])->name('housekeeping.report');

    // Inventory, Items & Stock Ledger Module
    Route::get('/items-list.html', [InventoryController::class, 'items'])->name('inventory.items');
    Route::get('/inventory/items', [InventoryController::class, 'items']);
    Route::post('/inventory/items', [InventoryController::class, 'storeItem'])->name('inventory.items.store');
    Route::post('/inventory/purchase', [InventoryController::class, 'purchaseStock'])->name('inventory.purchase');
    Route::post('/inventory/damage', [InventoryController::class, 'recordDamage'])->name('inventory.damage');

    Route::get('/items-category-list.html', [InventoryController::class, 'categories'])->name('inventory.categories');
    Route::get('/inventory/categories', [InventoryController::class, 'categories']);
    Route::post('/inventory/categories', [InventoryController::class, 'storeCategory'])->name('inventory.categories.store');

    Route::get('/items-unit-list.html', [InventoryController::class, 'units'])->name('inventory.units');
    Route::get('/inventory/units', [InventoryController::class, 'units']);
    Route::post('/inventory/units', [InventoryController::class, 'storeUnit'])->name('inventory.units.store');

    Route::get('/items-suppliers-list.html', [InventoryController::class, 'suppliers'])->name('inventory.suppliers');
    Route::get('/inventory/suppliers', [InventoryController::class, 'suppliers']);
    Route::post('/inventory/suppliers', [InventoryController::class, 'storeSupplier'])->name('inventory.suppliers.store');

    Route::get('/items-destroyed-list.html', [InventoryController::class, 'destroyedList'])->name('inventory.destroyed');

    // Personalised Hotel Amenities & Extra Services Module
    Route::get('/personalised-jacuzzi.html', [AmenityController::class, 'jacuzzi'])->name('amenities.jacuzzi');
    Route::get('/personalised-swimmingpool.html', [AmenityController::class, 'swimmingPool'])->name('amenities.swimmingpool');
    Route::get('/personalised-spa.html', [AmenityController::class, 'spa'])->name('amenities.spa');
    Route::get('/personalised-pedicure.html', [AmenityController::class, 'pedicure'])->name('amenities.pedicure');
    Route::get('/personalised-theater.html', [AmenityController::class, 'theater'])->name('amenities.theater');
    Route::get('/personalised-banquet.html', [AmenityController::class, 'banquet'])->name('amenities.banquet');
    Route::get('/personalised-settings.html', [AmenityController::class, 'settings'])->name('amenities.settings');
    Route::post('/amenities/book', [AmenityController::class, 'bookService'])->name('amenities.book');
    Route::post('/amenities/services', [AmenityController::class, 'storeService'])->name('amenities.services.store');

    // Financial Ledger, Transactions & Reports Module
    Route::get('/transaction.html', [FinancialBillingController::class, 'transactions'])->name('transactions');
    Route::get('/booking-report.html', [FinancialBillingController::class, 'bookingReport'])->name('reports.booking');
    Route::get('/folio-invoice/{id}', [FinancialBillingController::class, 'folioInvoice'])->name('folio.invoice');
    Route::get('/folio-invoice.html', fn() => redirect()->route('reservations'));
    Route::get('/purchase-report.html', [InventoryController::class, 'purchaseReport'])->name('inventory.reports.purchase');
    Route::get('/stock-report.html', [InventoryController::class, 'stockReport'])->name('inventory.reports.stock');

    // HRMS, Employees & Payroll Module
    Route::get('/hrms.html', [HrmsController::class, 'employees'])->name('hrms');
    Route::get('/employees', [HrmsController::class, 'employees'])->name('hrms.employees');
    Route::get('/employees-all.html', [HrmsController::class, 'employees']);
    Route::get('/employees/add', [HrmsController::class, 'createEmployee'])->name('hrms.employees.add');
    Route::get('/employees-add.html', [HrmsController::class, 'createEmployee']);
    Route::post('/employees', [HrmsController::class, 'storeEmployee'])->name('hrms.employees.store');

    Route::get('/departments', [HrmsController::class, 'departments'])->name('hrms.departments');
    Route::get('/employees-departments.html', [HrmsController::class, 'departments']);
    Route::post('/departments', [HrmsController::class, 'storeDepartment'])->name('hrms.departments.store');

    Route::get('/attendance', [HrmsController::class, 'attendance'])->name('hrms.attendance');
    Route::get('/employees-attendance.html', [HrmsController::class, 'attendance']);
    Route::post('/attendance', [HrmsController::class, 'recordAttendance'])->name('hrms.attendance.record');

    Route::get('/shifts', [HrmsController::class, 'shifts'])->name('hrms.shifts');
    Route::get('/duty-shift-list.html', [HrmsController::class, 'shifts']);
    Route::post('/shifts', [HrmsController::class, 'storeShift'])->name('hrms.shifts.store');

    Route::get('/duty-roster', [HrmsController::class, 'roster'])->name('hrms.roster');
    Route::get('/duty-roster-list.html', [HrmsController::class, 'roster']);
    Route::get('/duty-assigned-list.html', [HrmsController::class, 'roster']);
    Route::post('/duty-roster', [HrmsController::class, 'assignRoster'])->name('hrms.roster.assign');

    Route::get('/leaves', [HrmsController::class, 'leaves'])->name('hrms.leaves');
    Route::get('/employees-leave.html', [HrmsController::class, 'leaves']);
    Route::post('/leaves', [HrmsController::class, 'storeLeave'])->name('hrms.leaves.store');
    Route::post('/leaves/{id}/status', [HrmsController::class, 'updateLeaveStatus'])->name('hrms.leaves.status');

    Route::get('/payroll', [HrmsController::class, 'payrolls'])->name('hrms.payroll');
    Route::get('/payroll-employee-salary.html', [HrmsController::class, 'payrolls']);
    Route::post('/payroll/generate', [HrmsController::class, 'generatePayroll'])->name('hrms.payroll.generate');
    Route::get('/payroll/payslip/{id}', [HrmsController::class, 'payslip'])->name('hrms.payroll.payslip');
    Route::get('/payroll-payslip.html', [HrmsController::class, 'payrolls']);
    Route::get('/payroll-report.html', [HrmsController::class, 'payrolls']);

    Route::get('/loans', [DashboardController::class, 'loans'])->name('loans');
    Route::get('/employees-loan.html', fn() => view('pages.employees-loan'));
    Route::get('/appraisal', [DashboardController::class, 'appraisal'])->name('appraisal');
    Route::get('/employees-appraisal.html', fn() => view('pages.employees-appraisal'));

    // Settings & Configurations Module
    Route::get('/tuning.html', [DashboardController::class, 'tuning'])->name('settings.tuning');
    Route::get('/common-settings.html', [DashboardController::class, 'commonSettings'])->name('settings.common');
    Route::post('/common-settings', [DashboardController::class, 'saveCommonSettings'])->name('settings.common.save');
    Route::get('/payment-set.html', [FinancialBillingController::class, 'paymentSettings'])->name('settings.payments');
    Route::post('/payment-set', [FinancialBillingController::class, 'saveGateway'])->name('settings.payments.save');
    Route::get('/accounts-setting.html', [FinancialBillingController::class, 'accountsSetting'])->name('settings.accounts');

    // SaaS Subscription & License Hub
    Route::get('/subscription', [\App\Http\Controllers\Web\SubscriptionController::class, 'show'])->name('subscription.show');
    Route::get('/subscription.html', [\App\Http\Controllers\Web\SubscriptionController::class, 'show']);
    Route::post('/subscription/renew', [\App\Http\Controllers\Web\SubscriptionController::class, 'renew'])->name('subscription.renew');

    // Role & Permission / User Management Module (Restricted to Admins & Owners)
    Route::middleware('role:SUPERADMIN,HOTEL_OWNER,MANAGER')->group(function () {
        // SaaS Master Tenant Control Center
        Route::get('/admin/saas-tenants', [\App\Http\Controllers\Web\AdminTenantManagementController::class, 'index'])->name('admin.tenants.index');
        Route::get('/admin/saas-tenants.html', [\App\Http\Controllers\Web\AdminTenantManagementController::class, 'index']);
        Route::post('/admin/saas-tenants', [\App\Http\Controllers\Web\AdminTenantManagementController::class, 'store'])->name('admin.tenants.store');
        Route::put('/admin/saas-tenants/{id}', [\App\Http\Controllers\Web\AdminTenantManagementController::class, 'update'])->name('admin.tenants.update');
        Route::post('/admin/saas-tenants/{id}/toggle', [\App\Http\Controllers\Web\AdminTenantManagementController::class, 'toggle'])->name('admin.tenants.toggle');
        Route::post('/admin/saas-tenants/{id}/extend', [\App\Http\Controllers\Web\AdminTenantManagementController::class, 'extend'])->name('admin.tenants.extend');
        Route::delete('/admin/saas-tenants/{id}', [\App\Http\Controllers\Web\AdminTenantManagementController::class, 'destroy'])->name('admin.tenants.destroy');
        Route::get('/admin/saas-tenants/{id}/impersonate', [\App\Http\Controllers\Web\AdminTenantManagementController::class, 'impersonate'])->name('admin.tenants.impersonate');

        Route::get('/admin/user-control', [AdminUserController::class, 'index'])->name('admin.user.control');
        Route::get('/admin/tenants', [\App\Http\Controllers\Web\AdminTenantManagementController::class, 'index'])->name('admin.tenants');
        Route::post('/admin/user-control', [AdminUserController::class, 'store'])->name('admin.user.store');
        Route::post('/admin/user-control/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('admin.user.toggle');
        Route::delete('/admin/user-control/{id}', [AdminUserController::class, 'destroy'])->name('admin.user.delete');

        Route::get('/role-list.html', [AdminUserController::class, 'index'])->name('admin.role.list');
        Route::get('/role-add.html', [AdminUserController::class, 'roleAdd'])->name('admin.role.add');
        Route::post('/role-add', [AdminUserController::class, 'store'])->name('admin.role.store');
        Route::get('/role-permission.html', [AdminUserController::class, 'rolePermission'])->name('admin.role.permission');
        Route::post('/role-permission', [AdminUserController::class, 'saveRolePermission'])->name('admin.role.permission.save');
        Route::get('/role-create-list.html', [AdminUserController::class, 'roleCreateList'])->name('admin.role.create_list');
        Route::get('/role-assign.html', [AdminUserController::class, 'roleAssign'])->name('admin.role.assign');
        Route::post('/role-assign', [AdminUserController::class, 'saveRoleAssign'])->name('admin.role.assign.save');
        Route::get('/role-access-list.html', [AdminUserController::class, 'roleAccessList'])->name('admin.role.access_list');
    });

    // Applications Module (Calendar, Inbox, Chat, Blog, Filemanager)
    Route::get('/calendar.html', [CommunicationController::class, 'calendar'])->name('calendar');
    Route::post('/calendar/events', [CommunicationController::class, 'createCalendarEvent'])->name('calendar.events.store');
    Route::get('/inbox.html', [CommunicationController::class, 'inbox'])->name('inbox');
    Route::post('/inbox/send', [CommunicationController::class, 'sendEmail'])->name('inbox.send');
    Route::get('/chat.html', [CommunicationController::class, 'chat'])->name('chat');
    Route::post('/chat/send', [CommunicationController::class, 'sendChatMessage'])->name('chat.send');
    Route::get('/blog.html', [CommunicationController::class, 'blog'])->name('blog');
    Route::get('/filemanager.html', [CommunicationController::class, 'filemanager'])->name('filemanager');
    Route::get('/my-profile.html', [ProfileController::class, 'show'])->name('profile');
    Route::post('/my-profile.html', [ProfileController::class, 'update']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Front Static Pages
    Route::get('/about-us.html', fn() => view('pages.about-us'));
    Route::get('/contact-us.html', fn() => view('pages.contact-us'));
    Route::get('/privacy-policy.html', fn() => view('pages.privacy-policy'));
    Route::get('/terms-conditions.html', fn() => view('pages.terms-conditions'));
    Route::get('/search.html', [DashboardController::class, 'search'])->name('search');
    Route::get('/hotels.html', [DashboardController::class, 'hotels'])->name('hotels');
    Route::get('/index-two.html', [DashboardController::class, 'indexTwo'])->name('dashboard.two');
    Route::get('/changelog.html', fn() => view('pages.changelog'));

    // Billing & Guests Module
    Route::get('/folios/{id}/invoice', [DashboardController::class, 'folioInvoice'])->name('folios.invoice');
    Route::get('/guests', [DashboardController::class, 'guests'])->name('guests');

    // Prime Booking Sync Hub & Dashboards
    Route::get('/sync-hub', [DashboardController::class, 'syncHub'])->name('sync-hub');
    Route::get('/sync-hub.html', [DashboardController::class, 'syncHub']);
    Route::get('/hotel-dashboard.html', [DashboardController::class, 'hotelDashboard'])->name('dashboard.hotel');
    Route::get('/index-hr.html', [DashboardController::class, 'indexHr'])->name('dashboard.hr');
});
