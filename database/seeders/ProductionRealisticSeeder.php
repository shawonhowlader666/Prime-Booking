<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\DutyRoster;
use App\Models\EmployeeLeave;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\HousekeepingTask;
use App\Models\InventoryItem;
use App\Models\ItemCategory;
use App\Models\ItemSupplier;
use App\Models\ItemUnit;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\RatePlan;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Shift;
use App\Models\StockMovement;
use App\Models\SyncEvent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProductionRealisticSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Hotel Profile
        $hotel = Hotel::firstOrCreate(
            ['code' => 'HOTEL-GRAND-01'],
            [
                'name' => 'Grand Palace Hotel & Resort',
                'slug' => 'grand-palace-hotel-resort',
                'address' => 'Plot 42, Gulshan Avenue, Dhaka 1212',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'phone' => '+880 2 9876543',
                'email' => 'reservations@grandpalace.com',
                'currency' => 'BDT',
                'tax_percentage' => 15.00,
                'service_charge_percentage' => 5.00,
                'check_in_time' => '14:00:00',
                'check_out_time' => '12:00:00',
                'sync_api_key' => 'pms_live_key_grand_palace',
                'sync_webhook_secret' => 'webhook_secret_grand_palace_2026',
                'is_active' => true,
            ]
        );

        // 2. HR Departments
        $deptData = [
            ['name' => 'Front Office & Reception', 'code' => 'FOD', 'desc' => 'Guest check-in, reservations, concierge, and walk-in front desk operations.'],
            ['name' => 'Housekeeping & Sanitation', 'code' => 'HKD', 'desc' => 'Room cleaning, linen change, mini-bar restocking, and hotel hygiene maintenance.'],
            ['name' => 'Management & Administration', 'code' => 'ADM', 'desc' => 'General hotel management, night audit oversight, and human resource policies.'],
            ['name' => 'Accounts & Financials', 'code' => 'ACC', 'desc' => 'Folio reconciliations, bank settlements, invoice audits, and tax reporting.'],
        ];

        $departments = [];
        foreach ($deptData as $d) {
            $departments[$d['code']] = Department::updateOrCreate(
                ['hotel_id' => $hotel->id, 'code' => $d['code']],
                ['name' => $d['name'], 'description' => $d['desc'], 'is_active' => true]
            );
        }

        // 3. Staff & Users
        $staffMembers = [
            ['name' => 'Michelle Glover', 'email' => 'admin@primepms.com', 'role' => 'SUPERADMIN', 'dept' => 'ADM', 'desig' => 'General Hotel Director', 'salary' => 120000],
            ['name' => 'Kevin Gill', 'email' => 'kevin.gill@grandpalace.com', 'role' => 'RECEPTIONIST', 'dept' => 'FOD', 'desig' => 'Front Desk Executive', 'salary' => 32000],
            ['name' => 'Rahim Uddin', 'email' => 'rahim.cleaner@grandpalace.com', 'role' => 'HOUSEKEEPER', 'dept' => 'HKD', 'desig' => 'Lead Room Attendant', 'salary' => 22000],
            ['name' => 'Farhana Akter', 'email' => 'farhana.manager@grandpalace.com', 'role' => 'MANAGER', 'dept' => 'ADM', 'desig' => 'Operations Manager', 'salary' => 75000],
            ['name' => 'Tanvir Ahmed', 'email' => 'tanvir.accounts@grandpalace.com', 'role' => 'ACCOUNTANT', 'dept' => 'ACC', 'desig' => 'Chief Accounts Officer', 'salary' => 55000],
        ];

        $users = [];
        foreach ($staffMembers as $s) {
            $users[$s['role']] = User::updateOrCreate(
                ['email' => $s['email']],
                [
                    'hotel_id' => $hotel->id,
                    'department_id' => $departments[$s['dept']]->id ?? null,
                    'name' => $s['name'],
                    'password' => Hash::make('password'),
                    'role' => $s['role'],
                    'designation' => $s['desig'],
                    'salary' => $s['salary'],
                    'joining_date' => '2024-01-15',
                    'phone' => '+880 1711-223344',
                    'is_active' => true,
                ]
            );
        }

        // 4. Work Shifts
        $shiftData = [
            ['name' => 'Morning Shift (A)', 'start' => '06:00:00', 'end' => '14:00:00'],
            ['name' => 'Evening Shift (B)', 'start' => '14:00:00', 'end' => '22:00:00'],
            ['name' => 'Night Shift (C)', 'start' => '22:00:00', 'end' => '06:00:00'],
        ];

        $shifts = [];
        foreach ($shiftData as $sh) {
            $shifts[] = Shift::updateOrCreate(
                ['hotel_id' => $hotel->id, 'name' => $sh['name']],
                ['start_time' => $sh['start'], 'end_time' => $sh['end'], 'is_active' => true]
            );
        }

        // 5. Duty Rosters & Attendance for Today
        foreach ($users as $u) {
            DutyRoster::updateOrCreate(
                ['hotel_id' => $hotel->id, 'user_id' => $u->id, 'duty_date' => now()->toDateString()],
                ['shift_id' => $shifts[0]->id, 'status' => 'SCHEDULED', 'notes' => 'Primary floor duty assignment']
            );

            Attendance::updateOrCreate(
                ['hotel_id' => $hotel->id, 'user_id' => $u->id, 'date' => now()->toDateString()],
                ['clock_in' => '08:55', 'clock_out' => '17:05', 'status' => 'PRESENT', 'overtime_hours' => 0.5, 'notes' => 'On time arrival']
            );
        }

        // 6. Leave Applications
        EmployeeLeave::updateOrCreate(
            ['hotel_id' => $hotel->id, 'user_id' => $users['RECEPTIONIST']->id, 'start_date' => now()->addDays(5)->toDateString()],
            [
                'end_date' => now()->addDays(7)->toDateString(),
                'leave_type' => 'CASUAL',
                'total_days' => 3,
                'reason' => 'Family ceremony travel to Chittagong',
                'status' => 'APPROVED',
                'approved_by_user_id' => $users['MANAGER']->id,
            ]
        );

        // 7. Monthly Payroll Records
        foreach ($users as $u) {
            $base = $u->salary > 0 ? $u->salary : 25000;
            $allowance = $base * 0.10;
            Payroll::updateOrCreate(
                ['hotel_id' => $hotel->id, 'user_id' => $u->id, 'month_year' => date('Y-m')],
                [
                    'base_salary' => $base,
                    'allowances' => $allowance,
                    'overtime_amount' => 500.00,
                    'deductions' => 0.00,
                    'net_salary' => $base + $allowance + 500.00,
                    'status' => 'PAID',
                    'paid_at' => now(),
                    'payment_method' => 'BANK_TRANSFER',
                    'transaction_reference' => 'SAL-' . date('Ymd') . '-' . $u->id,
                ]
            );
        }

        // 8. Room Types
        $roomTypesData = [
            ['name' => 'Deluxe King Suite', 'code' => 'DLX-KNG', 'base_price' => 7500, 'max_adults' => 2, 'max_children' => 1],
            ['name' => 'Executive Ocean View', 'code' => 'EXEC-OCN', 'base_price' => 11000, 'max_adults' => 2, 'max_children' => 0],
            ['name' => 'Standard Double Room', 'code' => 'STD-DBL', 'base_price' => 4500, 'max_adults' => 2, 'max_children' => 1],
            ['name' => 'Presidential Penthouse', 'code' => 'PRES-PNT', 'base_price' => 25000, 'max_adults' => 4, 'max_children' => 2],
        ];

        $createdTypes = [];
        foreach ($roomTypesData as $rt) {
            $type = RoomType::updateOrCreate(
                ['hotel_id' => $hotel->id, 'code' => $rt['code']],
                [
                    'name' => $rt['name'],
                    'description' => "Ultra-luxury accommodation with high-speed WiFi, smart climate control, premium bedding, and scenic panoramic views.",
                    'base_price' => $rt['base_price'],
                    'max_adults' => $rt['max_adults'],
                    'max_children' => $rt['max_children'],
                    'amenities' => ['High-Speed WiFi', '4K Smart TV', 'Espresso Machine', 'Bathtub & Rain Shower', 'Minibar', 'King Size Bed'],
                    'is_active' => true,
                ]
            );
            $createdTypes[$rt['code']] = $type;

            // Rate Plan
            RatePlan::updateOrCreate(
                ['hotel_id' => $hotel->id, 'room_type_id' => $type->id, 'title' => 'Best Available Rate (BAR)'],
                [
                    'start_date' => now()->startOfYear()->toDateString(),
                    'end_date' => now()->endOfYear()->toDateString(),
                    'price_per_night' => $rt['base_price'],
                    'is_active' => true,
                ]
            );
        }

        // 9. Physical Rooms (Floors 1 to 3)
        $statuses = ['AVAILABLE', 'AVAILABLE', 'OCCUPIED', 'DIRTY', 'AVAILABLE', 'MAINTENANCE', 'OCCUPIED', 'AVAILABLE'];
        $statusIdx = 0;
        $roomUnits = [];

        for ($floor = 1; $floor <= 3; $floor++) {
            for ($num = 1; $num <= 8; $num++) {
                $roomNo = sprintf("%d%02d", $floor, $num);
                $typeCode = ($num <= 3) ? 'STD-DBL' : (($num <= 6) ? 'DLX-KNG' : (($num == 7) ? 'EXEC-OCN' : 'PRES-PNT'));
                $type = $createdTypes[$typeCode] ?? $createdTypes['STD-DBL'];

                $room = Room::updateOrCreate(
                    ['hotel_id' => $hotel->id, 'room_number' => $roomNo],
                    [
                        'room_type_id' => $type->id,
                        'floor' => (string)$floor,
                        'status' => $statuses[$statusIdx % count($statuses)],
                        'is_active' => true,
                    ]
                );
                $roomUnits[] = $room;
                $statusIdx++;
            }
        }

        // 10. Realistic Guests
        $guestsData = [
            ['first_name' => 'Shahriar', 'last_name' => 'Kabir', 'phone' => '+880 1712-345678', 'email' => 'shahriar.kabir@gmail.com', 'id_number_encrypted' => '19902692518293847', 'is_vip' => true],
            ['first_name' => 'Nusrat', 'last_name' => 'Jahan', 'phone' => '+880 1819-876543', 'email' => 'nusrat.jahan@yahoo.com', 'id_number_encrypted' => '19954783920194857', 'is_vip' => false],
            ['first_name' => 'Alexander', 'last_name' => 'Wright', 'phone' => '+1 415-555-0198', 'email' => 'alex.wright@techglobal.io', 'id_number_encrypted' => 'USA98472918', 'is_vip' => true],
            ['first_name' => 'Mahmudur', 'last_name' => 'Rahman', 'phone' => '+880 1911-554433', 'email' => 'mahmud.rahman@grameen.com', 'id_number_encrypted' => '19881234567890123', 'is_vip' => false],
            ['first_name' => 'Ayesha', 'last_name' => 'Siddiqua', 'phone' => '+880 1622-998877', 'email' => 'ayesha.sid@outlook.com', 'id_number_encrypted' => '19989876543210987', 'is_vip' => false],
        ];

        $guests = [];
        foreach ($guestsData as $g) {
            $guests[] = Guest::updateOrCreate(
                ['phone' => $g['phone']],
                array_merge($g, ['hotel_id' => $hotel->id, 'address' => 'Dhaka, Bangladesh', 'id_type' => 'NID'])
            );
        }

        // 11. Realistic Reservations with Folios & Payments
        $reservationsData = [
            [
                'guest' => $guests[0],
                'room_type' => $createdTypes['DLX-KNG'],
                'room' => $roomUnits[2], // Room 103 (OCCUPIED)
                'status' => 'CHECKED_IN',
                'source' => 'PRIME_BOOKING_OTA',
                'check_in' => now()->subDays(1)->toDateString(),
                'check_out' => now()->addDays(2)->toDateString(),
                'amount' => 22500,
                'paid' => 22500,
            ],
            [
                'guest' => $guests[1],
                'room_type' => $createdTypes['STD-DBL'],
                'room' => $roomUnits[0], // Room 101 (AVAILABLE)
                'status' => 'CONFIRMED',
                'source' => 'DIRECT_WALKIN',
                'check_in' => now()->toDateString(),
                'check_out' => now()->addDays(1)->toDateString(),
                'amount' => 4500,
                'paid' => 4500,
            ],
            [
                'guest' => $guests[2],
                'room_type' => $createdTypes['PRES-PNT'],
                'room' => $roomUnits[7], // Room 108
                'status' => 'CHECKED_IN',
                'source' => 'PRIME_BOOKING_OTA',
                'check_in' => now()->subDays(2)->toDateString(),
                'check_out' => now()->addDays(3)->toDateString(),
                'amount' => 125000,
                'paid' => 125000,
            ],
            [
                'guest' => $guests[3],
                'room_type' => $createdTypes['EXEC-OCN'],
                'room' => $roomUnits[6],
                'status' => 'CHECKED_OUT',
                'source' => 'DIRECT_WALKIN',
                'check_in' => now()->subDays(4)->toDateString(),
                'check_out' => now()->subDays(1)->toDateString(),
                'amount' => 33000,
                'paid' => 33000,
            ],
        ];

        foreach ($reservationsData as $idx => $r) {
            $code = 'RES-' . strtoupper(Str::random(6));
            $res = Reservation::updateOrCreate(
                ['reservation_code' => $code],
                [
                    'hotel_id' => $hotel->id,
                    'guest_id' => $r['guest']->id,
                    'room_type_id' => $r['room_type']->id,
                    'room_id' => $r['room']->id,
                    'check_in_date' => $r['check_in'],
                    'check_out_date' => $r['check_out'],
                    'adults' => 2,
                    'children' => 0,
                    'status' => $r['status'],
                    'total_amount' => $r['amount'],
                    'paid_amount' => $r['paid'],
                    'balance_amount' => $r['amount'] - $r['paid'],
                    'source' => $r['source'],
                ]
            );

            // Folio Provisioning
            $folio = Folio::updateOrCreate(
                ['reservation_id' => $res->id],
                [
                    'hotel_id' => $hotel->id,
                    'folio_number' => 'FOL-' . date('Ymd') . '-' . str_pad($res->id, 4, '0', STR_PAD_LEFT),
                    'total_charges' => $r['amount'],
                    'total_payments' => $r['paid'],
                    'balance' => $r['amount'] - $r['paid'],
                    'status' => ($r['status'] === 'CHECKED_OUT') ? 'SETTLED' : 'OPEN',
                ]
            );

            // Folio Line Items
            FolioItem::updateOrCreate(
                ['folio_id' => $folio->id, 'item_type' => 'ROOM_CHARGE'],
                [
                    'hotel_id' => $hotel->id,
                    'description' => "Room Tariff ({$r['room_type']->name})",
                    'unit_price' => $r['amount'] * 0.85,
                    'quantity' => 1,
                    'total_amount' => $r['amount'] * 0.85,
                    'posted_at' => now(),
                ]
            );

            FolioItem::updateOrCreate(
                ['folio_id' => $folio->id, 'item_type' => 'TAX'],
                [
                    'hotel_id' => $hotel->id,
                    'description' => "Government VAT & Service Charge (15%)",
                    'unit_price' => $r['amount'] * 0.15,
                    'quantity' => 1,
                    'total_amount' => $r['amount'] * 0.15,
                    'posted_at' => now(),
                ]
            );

            // Payment
            if ($r['paid'] > 0) {
                Payment::updateOrCreate(
                    ['folio_id' => $folio->id],
                    [
                        'hotel_id' => $hotel->id,
                        'reservation_id' => $res->id,
                        'amount' => $r['paid'],
                        'payment_method' => ($r['source'] === 'PRIME_BOOKING_OTA') ? 'CARD_POS' : 'BKASH',
                        'transaction_reference' => 'TRX-' . strtoupper(Str::random(10)),
                        'status' => 'SUCCESS',
                    ]
                );
            }
        }

        // 12. Housekeeping Tasks
        $dirtyRooms = Room::where('status', 'DIRTY')->get();
        foreach ($dirtyRooms as $dRoom) {
            HousekeepingTask::updateOrCreate(
                ['room_id' => $dRoom->id, 'status' => 'PENDING'],
                [
                    'hotel_id' => $hotel->id,
                    'assigned_to_user_id' => $users['HOUSEKEEPER']->id ?? null,
                    'priority' => 'HIGH',
                    'notes' => 'Complete linen change, vacuuming, bathroom deep sanitization and minibar replenishment.',
                ]
            );
        }

        // 13. Hotel Inventory Categories, Units, Suppliers & Items
        $catLinens = ItemCategory::updateOrCreate(['hotel_id' => $hotel->id, 'name' => 'Bedding & Linens'], ['description' => 'Bedsheets, duvet covers, pillowcases, and bathrobes', 'is_active' => true]);
        $catToiletries = ItemCategory::updateOrCreate(['hotel_id' => $hotel->id, 'name' => 'Bath & Toiletries'], ['description' => 'Luxury shampoos, conditioners, body wash, dental kits, and soaps', 'is_active' => true]);
        $catHousekeeping = ItemCategory::updateOrCreate(['hotel_id' => $hotel->id, 'name' => 'Housekeeping Chemicals'], ['description' => 'Disinfectants, surface cleaners, room fresheners, and glass cleaners', 'is_active' => true]);
        $catMinibar = ItemCategory::updateOrCreate(['hotel_id' => $hotel->id, 'name' => 'Mini-Bar Beverages & Snacks'], ['description' => 'Imported sparkling water, sodas, premium chocolates, and gourmet nuts', 'is_active' => true]);

        $unitPcs = ItemUnit::updateOrCreate(['hotel_id' => $hotel->id, 'name' => 'Pieces'], ['short_code' => 'Pcs', 'is_active' => true]);
        $unitBox = ItemUnit::updateOrCreate(['hotel_id' => $hotel->id, 'name' => 'Box'], ['short_code' => 'Box', 'is_active' => true]);
        $unitBottle = ItemUnit::updateOrCreate(['hotel_id' => $hotel->id, 'name' => 'Bottle'], ['short_code' => 'Btl', 'is_active' => true]);
        $unitLitre = ItemUnit::updateOrCreate(['hotel_id' => $hotel->id, 'name' => 'Litre'], ['short_code' => 'Ltr', 'is_active' => true]);

        $supTextile = ItemSupplier::updateOrCreate(
            ['hotel_id' => $hotel->id, 'name' => 'Mahfuzur Rahman'],
            ['company_name' => 'Bengal Luxury Textiles Ltd.', 'phone' => '+880 1711-223344', 'email' => 'sales@bengaltextiles.com', 'address' => 'Tejgaon I/A, Dhaka', 'tax_number' => 'BIN-88771122', 'is_active' => true]
        );

        $supAmenities = ItemSupplier::updateOrCreate(
            ['hotel_id' => $hotel->id, 'name' => 'Faridul Islam'],
            ['company_name' => 'Apex Hotel Amenities & Toiletries', 'phone' => '+880 1819-334455', 'email' => 'info@apexamenities.com', 'address' => 'Gulshan Avenue, Dhaka', 'tax_number' => 'BIN-99002233', 'is_active' => true]
        );

        $inventoryData = [
            ['name' => 'King Size 300TC Bed Linen Sheet', 'sku' => 'SKU-LIN-001', 'cat' => $catLinens, 'unit' => $unitPcs, 'sup' => $supTextile, 'cost' => 850.00, 'stock' => 120, 'min' => 20],
            ['name' => 'Plush White Bath Towel (700 GSM)', 'sku' => 'SKU-TWL-002', 'cat' => $catLinens, 'unit' => $unitPcs, 'sup' => $supTextile, 'cost' => 450.00, 'stock' => 85, 'min' => 15],
            ['name' => 'Luxury Botanical Shampoo (50ml)', 'sku' => 'SKU-SHM-003', 'cat' => $catToiletries, 'unit' => $unitBottle, 'sup' => $supAmenities, 'cost' => 65.00, 'stock' => 400, 'min' => 50],
            ['name' => 'Dental Care Kit (Biodegradable)', 'sku' => 'SKU-DNT-004', 'cat' => $catToiletries, 'unit' => $unitBox, 'sup' => $supAmenities, 'cost' => 35.00, 'stock' => 350, 'min' => 40],
            ['name' => 'Hospital Grade Surface Sanitizer', 'sku' => 'SKU-CHM-005', 'cat' => $catHousekeeping, 'unit' => $unitLitre, 'sup' => $supAmenities, 'cost' => 280.00, 'stock' => 8, 'min' => 10], // Low stock!
            ['name' => 'Perrier Sparkling Mineral Water (330ml)', 'sku' => 'SKU-BEV-006', 'cat' => $catMinibar, 'unit' => $unitBottle, 'sup' => $supAmenities, 'cost' => 180.00, 'stock' => 0, 'min' => 12], // Out of stock!
        ];

        foreach ($inventoryData as $item) {
            $invItem = InventoryItem::updateOrCreate(
                ['hotel_id' => $hotel->id, 'sku' => $item['sku']],
                [
                    'category_id' => $item['cat']->id,
                    'unit_id' => $item['unit']->id,
                    'supplier_id' => $item['sup']->id,
                    'name' => $item['name'],
                    'purchase_price' => $item['cost'],
                    'unit_price' => $item['cost'] * 1.3,
                    'current_stock' => $item['stock'],
                    'min_alert_stock' => $item['min'],
                    'is_active' => true,
                ]
            );

            if ($item['stock'] > 0) {
                StockMovement::updateOrCreate(
                    ['hotel_id' => $hotel->id, 'item_id' => $invItem->id, 'reference_no' => 'PO-INIT-' . $invItem->id],
                    [
                        'user_id' => $users['SUPERADMIN']->id,
                        'type' => 'PURCHASE_IN',
                        'quantity' => $item['stock'],
                        'unit_cost' => $item['cost'],
                        'total_cost' => $item['stock'] * $item['cost'],
                        'notes' => 'Initial warehouse supply received',
                    ]
                );
            }
        }

        // Add 1 sample damage write-off
        $firstItem = InventoryItem::where('hotel_id', $hotel->id)->first();
        if ($firstItem) {
            StockMovement::updateOrCreate(
                ['hotel_id' => $hotel->id, 'reference_no' => 'DMG-SAMPLE-01'],
                [
                    'item_id' => $firstItem->id,
                    'user_id' => $users['MANAGER']->id,
                    'type' => 'DAMAGED',
                    'quantity' => 2,
                    'unit_cost' => $firstItem->purchase_price,
                    'total_cost' => 2 * $firstItem->purchase_price,
                    'notes' => 'Torn during laundry wash cycle',
                ]
            );
        }
    }
}
