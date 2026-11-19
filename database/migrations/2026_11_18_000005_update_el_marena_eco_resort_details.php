<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\Room;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Updates El Marena Eco Resort (Saint Martin) with 100% authentic data from elmarena.com
     * Removing AC, Pool, Spa, Geyser and correcting room capacities, rates, and policies.
     */
    public function up(): void
    {
        $property = Property::where('id', 145)
            ->orWhere('slug', 'el-marena-eco-resort-saint-martin')
            ->first();

        if (!$property) {
            return;
        }

        $amenities = [
            "Beachfront & Direct Sea Access",
            "Complimentary Breakfast",
            "Private Balcony & Hammock",
            "Solar & Eco-Generator Power",
            "Seafood & Beachside BBQ Dining",
            "24/7 Front Desk & Travel Assistance",
            "Campfire & Stargazing Area",
            "Daily Housekeeping",
            "Fresh Island Water",
            "Ship Ticket & Transfer Guidance"
        ];

        $description = '<p><strong>El Marena Eco Resort</strong> is a peaceful, eco-friendly sanctuary located along the serene southern coast near Chera Dwip on Saint Martin\'s Island.</p>' .
            '<p>Surrounded by swaying coconut palms, natural coral shores, and the azure waters of the Bay of Bengal, our wooden duplex cottages, single cottages, and beachside camping tents provide an authentic tropical island experience. In harmony with Saint Martin\'s delicate coral ecology, our resort runs on clean solar energy and quiet backup generator power.</p>' .
            '<p>Guests can enjoy fresh seafood BBQ, gentle ocean breezes from personal hammocks, and magical sunsets over the sea.</p>' .
            '<p class="text-muted"><small><em>🌿 Eco-Resort Notice: To preserve the natural island environment, our resort operates on solar & generator power and does not feature air conditioning (AC), swimming pools, spas, or geysers. Clean fresh water, room fans, and cooling natural ocean breezes ensure a comfortable, sustainable stay.</em></small></p>';

        $property->update([
            'name'             => 'El Marena Eco Resort',
            'city'             => 'Saint Martin',
            'address'          => 'South Beach, Near Chera Dwip Road, Saint Martin Island, Cox\'s Bazar, Bangladesh',
            'price_per_night'  => 1200.00,
            'amenities'        => $amenities,
            'description'      => $description,
            'contact_phone'    => '+880 1925-726665, +880 1988-862306',
            'contact_email'    => 'elmarenaresort@gmail.com',
            'checkin_time'     => '12:00 PM',
            'checkout_time'    => '10:30 AM',
            'house_rules'      => 'Eco-friendly property. National ID / Passport required at check-in. Non-smoking inside wooden cottages. Bonfire in designated beach areas only.',
            'status'           => 'active',
        ]);

        // Update Room #1160: Duplex Wooden Cottage (Capacity 4, ৳4,000)
        $duplex = Room::where('property_id', $property->id)
            ->where(function ($q) {
                $q->where('id', 1160)->orWhere('name', 'like', '%Duplex%');
            })->first();

        if ($duplex) {
            $duplex->update([
                'name'            => 'Duplex Wooden Cottage',
                'room_type'       => 'cottage',
                'price_per_night' => 4000.00,
                'capacity'        => 4,
                'max_adults'      => 4,
                'max_children'    => 2,
                'bed_type'        => '1 Double Bed + 1 Loft Double Bed',
                'room_size'       => '38 sqm',
                'description'     => 'Authentic two-storey wooden cottage on stilts overlooking the ocean and coconut palms. Features 1 double bed downstairs, sleeping loft upstairs, attached private washroom, personal balcony hammock, and solar-powered lighting and fan. Complimentary breakfast included.',
                'amenities'       => [
                    "Attached Private Washroom",
                    "Private Balcony with Hammock",
                    "Lookout Watchtower",
                    "Sea & Garden View",
                    "Ceiling / Stand Fan",
                    "Free Breakfast",
                    "Solar Power & Backup",
                    "Fresh Towels & Toiletries"
                ],
                'status'          => 'active',
            ]);
        }

        // Update Room #1161: Single Wooden Cottage (Capacity 2, ৳2,000)
        $single = Room::where('property_id', $property->id)
            ->where(function ($q) {
                $q->where('id', 1161)->orWhere('name', 'like', '%Single%');
            })->first();

        if ($single) {
            $single->update([
                'name'            => 'Single Wooden Cottage',
                'room_type'       => 'cottage',
                'price_per_night' => 2000.00,
                'capacity'        => 2,
                'max_adults'      => 2,
                'max_children'    => 1,
                'bed_type'        => '1 Double Bed',
                'room_size'       => '22 sqm',
                'description'     => 'Cozy single wooden cottage crafted from natural wood with a comfortable double bed, wardrobe, room fan, peaceful veranda, and clean washroom access. Complimentary breakfast included.',
                'amenities'       => [
                    "1 Double Bed",
                    "Private Veranda",
                    "Room Fan",
                    "Free Breakfast",
                    "Solar Power & Backup",
                    "Clean Washroom Access",
                    "Fresh Linens"
                ],
                'status'          => 'active',
            ]);
        }

        // Update Room #1159: Beach Camping Tent (Capacity 2, ৳1,200)
        $tent = Room::where('property_id', $property->id)
            ->where(function ($q) {
                $q->where('id', 1159)->orWhere('name', 'like', '%Tent%');
            })->first();

        if ($tent) {
            $tent->update([
                'name'            => 'Beachside Camping Tent',
                'room_type'       => 'tent',
                'price_per_night' => 1200.00,
                'capacity'        => 2,
                'max_adults'      => 2,
                'max_children'    => 0,
                'bed_type'        => '2 Sleeping Mats / Foam Mattresses',
                'room_size'       => '10 sqm',
                'description'     => 'Camp directly beside the seashore of Saint Martin under the open starry sky. Weatherproof tent equipped with comfortable foam mattresses, clean pillows, blankets, and access to clean shared restrooms and campfire zone.',
                'amenities'       => [
                    "Beachfront Camping Pitch",
                    "Weatherproof Tent",
                    "Foam Mattresses & Blankets",
                    "Shared Clean Restroom Access",
                    "Campfire & Stargazing Access",
                    "Island Sea Breeze"
                ],
                'status'          => 'active',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};

