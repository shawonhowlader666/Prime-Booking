<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use App\Models\BookingAddon;
use Illuminate\Support\Str;

$demoVendors = User::where('role', 'vendor')
    ->orWhere('name', 'like', '%Vendor%')
    ->orWhere('email', 'like', '%vendor%')
    ->get(['id', 'name', 'email', 'role']);

echo "Found Vendors:\n";
foreach ($demoVendors as $v) {
    $propCount = Property::where('vendor_id', $v->id)->count();
    echo "ID: {$v->id} | Name: {$v->name} | Email: {$v->email} | Properties Owned: {$propCount}\n";
}

// Ensure every vendor has at least 1 property and some bookings
foreach ($demoVendors as $v) {
    $properties = Property::where('vendor_id', $v->id)->get();
    if ($properties->isEmpty()) {
        // Assign or create a property for this vendor
        $prop = Property::whereNull('vendor_id')->first();
        if ($prop) {
            $prop->update(['vendor_id' => $v->id]);
            $properties = collect([$prop]);
        }
    }

    foreach ($properties as $property) {
        $room = Room::where('property_id', $property->id)->first();
        if (!$room) {
            $room = Room::create([
                'property_id' => $property->id,
                'name' => 'Deluxe King Suite',
                'room_type' => 'deluxe',
                'price_per_night' => 8500,
                'total_rooms' => 10,
                'max_adults' => 2,
                'max_children' => 1,
            ]);
        }

        // Create 2 realistic bookings for this vendor
        $ref1 = 'PRM-' . date('Y') . '-' . strtoupper(Str::random(6));
        Booking::create([
            'booking_reference'  => $ref1,
            'property_id'        => $property->id,
            'room_id'            => $room->id,
            'user_id'            => $v->id,
            'guest_name'         => 'Tanvir Ahmed',
            'guest_email'        => 'tanvir.ahmed@gmail.com',
            'guest_phone'        => '01819283746',
            'check_in'           => date('Y-m-d', strtotime('+2 days')),
            'check_out'          => date('Y-m-d', strtotime('+4 days')),
            'guests'             => 2,
            'nights'             => 2,
            'price_per_night'    => $room->price_per_night,
            'subtotal'           => $room->price_per_night * 2,
            'tax_amount'         => round(($room->price_per_night * 2) * 0.075),
            'total_price'        => ($room->price_per_night * 2) + round(($room->price_per_night * 2) * 0.075),
            'total_amount'       => ($room->price_per_night * 2) + round(($room->price_per_night * 2) * 0.075),
            'payment_method'     => 'bkash',
            'payment_status'     => 'paid',
            'status'             => 'confirmed',
            'booking_status'     => 'confirmed',
            'special_requests'   => 'Honeymoon setup, flower bouquet requested.',
        ]);

        $ref2 = 'PRM-' . date('Y') . '-' . strtoupper(Str::random(6));
        Booking::create([
            'booking_reference'  => $ref2,
            'property_id'        => $property->id,
            'room_id'            => $room->id,
            'user_id'            => $v->id,
            'guest_name'         => 'Sabrina Rahman',
            'guest_email'        => 'sabrina.r@yahoo.com',
            'guest_phone'        => '01712987654',
            'check_in'           => date('Y-m-d', strtotime('+5 days')),
            'check_out'          => date('Y-m-d', strtotime('+8 days')),
            'guests'             => 3,
            'nights'             => 3,
            'price_per_night'    => $room->price_per_night,
            'subtotal'           => $room->price_per_night * 3,
            'tax_amount'         => round(($room->price_per_night * 3) * 0.075),
            'total_price'        => ($room->price_per_night * 3) + round(($room->price_per_night * 3) * 0.075),
            'total_amount'       => ($room->price_per_night * 3) + round(($room->price_per_night * 3) * 0.075),
            'payment_method'     => 'card',
            'payment_status'     => 'paid',
            'status'             => 'confirmed',
            'booking_status'     => 'confirmed',
            'special_requests'   => 'Airport pickup needed at 11 AM.',
        ]);

        echo "Created 2 test bookings for Vendor [{$v->name}] Property [{$property->name}]!\n";
    }
}
