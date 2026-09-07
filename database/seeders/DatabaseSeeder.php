<?php

namespace Database\Seeders;

use App\Models\AmenityService;
use App\Models\AuditLog;
use App\Models\BedType;
use App\Models\BookingType;
use App\Models\Cab;
use App\Models\Commission;
use App\Models\ComplementaryService;
use App\Models\Department;
use App\Models\FloorPlan;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\HotelPaymentGateway;
use App\Models\HousekeepingChecklist;
use App\Models\HousekeepingTask;
use App\Models\InventoryItem;
use App\Models\ItemCategory;
use App\Models\ItemSupplier;
use App\Models\ItemUnit;
use App\Models\Payment;
use App\Models\Promocode;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\RoomSize;
use App\Models\RoomType;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Hotel
        $hotel = Hotel::firstOrCreate(['slug' => 'grand-palace-resort'], [
            'name' => 'Grand Palace Hotel & Resort',
            'code' => 'GPH-01',
            'email' => 'info@grandpalace.com',
            'phone' => '+880 1711-000111',
            'address' => 'Plot 14, Block B, Marine Drive',
            'city' => "Cox's Bazar",
            'country' => 'Bangladesh',
            'currency' => 'BDT',
            'tax_percentage' => 15.00,
            'service_charge_percentage' => 5.00,
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'sync_api_key' => 'pms_' . Str::random(32),
            'sync_webhook_secret' => 'sec_' . Str::random(32),
            'is_active' => true,
        ]);

        // 2. Create Users
        $owner = User::firstOrCreate(['email' => 'admin@primepms.com'], [
            'hotel_id' => $hotel->id,
            'name' => 'Tanvir Ahmed (Owner)',
            'role' => 'SUPERADMIN',
            'phone' => '+880 1811-222333',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $manager = User::firstOrCreate(['email' => 'manager@grandpalace.com'], [
            'hotel_id' => $hotel->id,
            'name' => 'Mahmud Hasan (Manager)',
            'role' => 'MANAGER',
            'phone' => '+880 1911-333444',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $receptionist = User::firstOrCreate(['email' => 'reception@grandpalace.com'], [
            'hotel_id' => $hotel->id,
            'name' => 'Sadia Islam (Receptionist)',
            'role' => 'RECEPTIONIST',
            'phone' => '+880 1611-444555',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $housekeeper = User::firstOrCreate(['email' => 'cleaner@grandpalace.com'], [
            'hotel_id' => $hotel->id,
            'name' => 'Karim Mia (Housekeeper)',
            'role' => 'HOUSEKEEPER',
            'phone' => '+880 1511-555666',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // 3. Create Room Types
        $deluxe = RoomType::firstOrCreate(['hotel_id' => $hotel->id, 'code' => 'DLX-KNG'], [
            'name' => 'Deluxe King Suite',
            'base_price' => 7500.00,
            'max_adults' => 2,
            'max_children' => 1,
            'amenities' => ['King Bed', 'Sea View Balcony', 'High-speed WiFi', 'Mini Bar', 'Air Conditioning', 'Bathtub'],
            'description' => 'Luxurious room with private sea-facing balcony and premium king-size bed.',
        ]);

        $executive = RoomType::firstOrCreate(['hotel_id' => $hotel->id, 'code' => 'EXEC-TWN'], [
            'name' => 'Executive Twin Suite',
            'base_price' => 9000.00,
            'max_adults' => 2,
            'max_children' => 2,
            'amenities' => ['Twin Beds', 'Lounge Area', 'Free Breakfast', 'Smart TV', 'Jacuzzi Access'],
            'description' => 'Spacious suite designed for corporate executives and upscale travelers.',
        ]);

        $presidential = RoomType::firstOrCreate(['hotel_id' => $hotel->id, 'code' => 'PRES-VILLA'], [
            'name' => 'Presidential Ocean Villa',
            'base_price' => 25000.00,
            'max_adults' => 4,
            'max_children' => 2,
            'amenities' => ['Private Pool', 'Butler Service', 'Panoramic Ocean View', 'Dining Room', 'Master Jacuzzi'],
            'description' => 'Ultra-exclusive 3-bedroom villa with private infinity plunge pool and dedicated 24/7 butler.',
        ]);

        // 4. Create Rooms
        $roomsData = [
            ['number' => '101', 'type' => $deluxe, 'floor' => 1, 'status' => 'AVAILABLE'],
            ['number' => '102', 'type' => $deluxe, 'floor' => 1, 'status' => 'AVAILABLE'],
            ['number' => '103', 'type' => $deluxe, 'floor' => 1, 'status' => 'OCCUPIED'],
            ['number' => '104', 'type' => $deluxe, 'floor' => 1, 'status' => 'DIRTY'],
            ['number' => '105', 'type' => $deluxe, 'floor' => 1, 'status' => 'OCCUPIED'],
            ['number' => '201', 'type' => $executive, 'floor' => 2, 'status' => 'AVAILABLE'],
            ['number' => '202', 'type' => $executive, 'floor' => 2, 'status' => 'AVAILABLE'],
            ['number' => '203', 'type' => $executive, 'floor' => 2, 'status' => 'CLEANING'],
            ['number' => '301', 'type' => $presidential, 'floor' => 3, 'status' => 'AVAILABLE'],
        ];

        $createdRooms = [];
        foreach ($roomsData as $r) {
            $createdRooms[$r['number']] = Room::firstOrCreate(['hotel_id' => $hotel->id, 'room_number' => $r['number']], [
                'room_type_id' => $r['type']->id,
                'floor' => $r['floor'],
                'status' => $r['status'],
                'is_active' => true,
            ]);
        }

        // 5. Create Floor Plans
        FloorPlan::firstOrCreate(['hotel_id' => $hotel->id, 'floor_number' => 1], [
            'floor_name' => 'Level 1 - Executive Wing',
            'number_of_rooms' => 5,
        ]);
        FloorPlan::firstOrCreate(['hotel_id' => $hotel->id, 'floor_number' => 2], [
            'floor_name' => 'Level 2 - Royal Suites',
            'number_of_rooms' => 3,
        ]);

        // 6. Create HRMS Departments, Shifts, & Staff
        Department::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Front Office'], ['description' => 'Guest checkin & concierge']);
        Department::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Housekeeping'], ['description' => 'Room sanitation']);
        Department::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Food & Beverage'], ['description' => 'Restaurant and banquet']);

        Shift::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Morning Shift'], [
            'start_time' => '07:00:00',
            'end_time' => '15:30:00',
        ]);
        Shift::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Evening Shift'], [
            'start_time' => '15:00:00',
            'end_time' => '23:30:00',
        ]);

        // 7. Create Inventory Units, Categories & Items
        $unitKg = ItemUnit::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Kilogram'], ['short_code' => 'kg']);
        $unitPcs = ItemUnit::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Pieces'], ['short_code' => 'pcs']);
        $unitBottles = ItemUnit::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Bottles'], ['short_code' => 'btl']);

        $catLinen = ItemCategory::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Linen & Bedding']);
        $catToiletries = ItemCategory::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Guest Toiletries']);
        $catBeverages = ItemCategory::firstOrCreate(['hotel_id' => $hotel->id, 'name' => 'Mini-Bar & Beverages']);

        InventoryItem::firstOrCreate(['hotel_id' => $hotel->id, 'sku' => 'LIN-KNG-001'], [
            'name' => 'Egyptian Cotton King Sheet',
            'category_id' => $catLinen->id,
            'unit_id' => $unitPcs->id,
            'current_stock' => 120,
            'min_alert_stock' => 30,
            'unit_price' => 850.00,
        ]);

        InventoryItem::firstOrCreate(['hotel_id' => $hotel->id, 'sku' => 'TLT-SHM-002'], [
            'name' => 'Organic Lavender Shampoo 50ml',
            'category_id' => $catToiletries->id,
            'unit_id' => $unitBottles->id,
            'current_stock' => 450,
            'min_alert_stock' => 100,
            'unit_price' => 45.00,
        ]);

        // 8. Create Personalized Amenities, Cabs & Gateways
        AmenityService::firstOrCreate(['hotel_id' => $hotel->id, 'category' => 'SPA'], [
            'name' => 'Balinese Herbal Spa Experience',
            'price' => 3500.00,
            'description' => 'Full body aromatherapy and hot stone treatment',
            'is_active' => true,
        ]);

        Cab::firstOrCreate(['hotel_id' => $hotel->id, 'car_number' => 'DHK-MET-1122'], [
            'car_name' => 'Toyota Camry Hybrid',
            'car_type' => 'Sedan',
            'fuel_type' => 'Hybrid',
            'driver_name' => 'Kamal Hossain',
            'driver_phone' => '+880 1711-889900',
            'status' => 'AVAILABLE',
        ]);

        HotelPaymentGateway::firstOrCreate(['hotel_id' => $hotel->id, 'gateway' => 'STRIPE'], [
            'is_enabled' => true,
            'environment' => 'sandbox',
            'publishable_key' => 'pk_test_sample_key',
            'secret_key' => 'sk_test_sample_secret',
        ]);

        // 9. Create Sample Guests & Active Reservations
        $guest1 = Guest::firstOrCreate(['email' => 'tariq.rahman@gmail.com'], [
            'hotel_id' => $hotel->id,
            'first_name' => 'Tariq',
            'last_name' => 'Rahman',
            'phone' => '+880 1712-345678',
            'is_vip' => true,
        ]);

        $guest2 = Guest::firstOrCreate(['email' => 'john.smith@ukcorp.com'], [
            'hotel_id' => $hotel->id,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'phone' => '+44 7911 123456',
            'is_vip' => false,
        ]);

        $today = Carbon::today();

        $res1 = Reservation::firstOrCreate(['hotel_id' => $hotel->id, 'reservation_code' => 'RES-WLK-998811'], [
            'guest_id' => $guest1->id,
            'room_id' => $createdRooms['103']->id,
            'room_type_id' => $deluxe->id,
            'check_in_date' => $today->toDateString(),
            'check_out_date' => $today->copy()->addDays(3)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'status' => 'CHECKED_IN',
            'source' => 'WALK_IN',
            'total_amount' => 22500.00,
            'paid_amount' => 22500.00,
            'balance_amount' => 0.00,
            'checked_in_at' => now()->subHours(2),
        ]);

        $folio1 = Folio::firstOrCreate(['folio_number' => 'FOL-RES-WLK-998811'], [
            'hotel_id' => $hotel->id,
            'reservation_id' => $res1->id,
            'total_charges' => 22500.00,
            'total_payments' => 22500.00,
            'balance' => 0.00,
            'status' => 'CLOSED',
        ]);

        // Housekeeping tasks
        HousekeepingTask::firstOrCreate(['hotel_id' => $hotel->id, 'room_id' => $createdRooms['104']->id], [
            'assigned_to_user_id' => $housekeeper->id,
            'priority' => 'HIGH',
            'status' => 'PENDING',
            'notes' => 'Deep cleaning required after check-out.',
        ]);

        // Audit Log
        AuditLog::create([
            'hotel_id' => $hotel->id,
            'user_id' => $owner->id,
            'action' => 'HOTEL_SYSTEM_INITIALIZED',
            'entity_type' => Hotel::class,
            'entity_id' => $hotel->id,
            'new_values' => ['name' => $hotel->name, 'code' => $hotel->code],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Prime-PMS Core Seeder',
        ]);
    }
}
