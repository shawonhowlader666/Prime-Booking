<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use App\Models\Room;

/**
 * RoomUpsellService — Agoda-Style Dynamic Upselling & Room Upgrade Engine.
 * Recommends high-margin room upgrades and personalized stay add-ons.
 */
class RoomUpsellService
{
    /**
     * Recommend upgrade room options for a given selected room.
     */
    public function getUpgradeRecommendations(Property $property, Room $selectedRoom, int $nights = 1): array
    {
        $allRooms = $property->rooms()->where('status', 'active')->where('id', '!=', $selectedRoom->id)->get();
        $upgrades = [];

        foreach ($allRooms as $room) {
            $diffPerNight = (float)($room->price_per_night - $selectedRoom->price_per_night);

            // Only recommend rooms that are upgrades (more features or higher class within reasonable delta)
            if ($diffPerNight > 0 && $diffPerNight <= ($selectedRoom->price_per_night * 0.80)) {
                $totalDiff = round($diffPerNight * $nights, 2);
                $upgrades[] = [
                    'room_id'            => $room->id,
                    'room_name'          => $room->name,
                    'price_per_night'    => (float)$room->price_per_night,
                    'upgrade_diff_night' => $diffPerNight,
                    'total_upgrade_cost' => $totalDiff,
                    'badge'              => 'POPULAR UPGRADE',
                    'tagline'            => "Upgrade to {$room->name} for only ৳" . number_format($diffPerNight) . "/night more!",
                    'features'           => $room->amenities ?? ['Larger Space', 'Premium View', 'Free High-Speed WiFi'],
                ];
            }
        }

        // Sort by lowest price jump for highest conversion
        usort($upgrades, fn($a, $b) => $a['upgrade_diff_night'] <=> $b['upgrade_diff_night']);

        return array_slice($upgrades, 0, 3);
    }
}
