<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\FeaturedDestination;
use App\Models\SiteSetting;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Seed Default Site Settings ─────────────────────────────────────

        $settings = [
            ['key'=>'vip_silver_threshold',   'group'=>'vip', 'value'=>'2',  'type'=>'number', 'label'=>'Silver Tier (min bookings)', 'description'=>'Min bookings in last 2 years'],
            ['key'=>'vip_gold_threshold',     'group'=>'vip', 'value'=>'5',  'type'=>'number', 'label'=>'Gold Tier (min bookings)', 'description'=>''],
            ['key'=>'vip_platinum_threshold', 'group'=>'vip', 'value'=>'10', 'type'=>'number', 'label'=>'Platinum Tier (min bookings)', 'description'=>''],
            ['key'=>'vip_diamond_threshold',  'group'=>'vip', 'value'=>'15', 'type'=>'number', 'label'=>'Diamond Tier (min bookings)', 'description'=>''],
            ['key'=>'vip_bronze_discount',    'group'=>'vip', 'value'=>'0',  'type'=>'number', 'label'=>'Bronze Discount %', 'description'=>''],
            ['key'=>'vip_silver_discount',    'group'=>'vip', 'value'=>'5',  'type'=>'number', 'label'=>'Silver Discount %', 'description'=>''],
            ['key'=>'vip_gold_discount',      'group'=>'vip', 'value'=>'8',  'type'=>'number', 'label'=>'Gold Discount %', 'description'=>''],
            ['key'=>'vip_platinum_discount',  'group'=>'vip', 'value'=>'12', 'type'=>'number', 'label'=>'Platinum Discount %', 'description'=>''],
            ['key'=>'vip_diamond_discount',   'group'=>'vip', 'value'=>'18', 'type'=>'number', 'label'=>'Diamond Discount %', 'description'=>''],
            ['key'=>'platform_commission',    'group'=>'booking', 'value'=>'12',  'type'=>'number', 'label'=>'Platform Commission %', 'description'=>''],
            ['key'=>'tax_rate',               'group'=>'booking', 'value'=>'7.5', 'type'=>'number', 'label'=>'Tax Rate (VAT) %', 'description'=>''],
            ['key'=>'min_booking_nights',     'group'=>'booking', 'value'=>'1',   'type'=>'number', 'label'=>'Min Booking Nights', 'description'=>''],
            ['key'=>'max_booking_nights',     'group'=>'booking', 'value'=>'60',  'type'=>'number', 'label'=>'Max Booking Nights', 'description'=>''],
            ['key'=>'cancellation_free_hours','group'=>'booking', 'value'=>'24',  'type'=>'number', 'label'=>'Free Cancellation Hours', 'description'=>''],
            ['key'=>'payment_bkash_enabled',  'group'=>'payment', 'value'=>'1', 'type'=>'boolean', 'label'=>'Enable bKash', 'description'=>''],
            ['key'=>'payment_nagad_enabled',  'group'=>'payment', 'value'=>'1', 'type'=>'boolean', 'label'=>'Enable Nagad', 'description'=>''],
            ['key'=>'payment_card_enabled',   'group'=>'payment', 'value'=>'1', 'type'=>'boolean', 'label'=>'Enable Card Payment', 'description'=>''],
            ['key'=>'currency',               'group'=>'payment', 'value'=>'BDT', 'type'=>'text', 'label'=>'Default Currency', 'description'=>''],
            ['key'=>'site_name',              'group'=>'general', 'value'=>'Prime Aviation', 'type'=>'text', 'label'=>'Site Name', 'description'=>''],
            ['key'=>'site_tagline',           'group'=>'general', 'value'=>"Bangladesh's #1 Hotel Booking Platform", 'type'=>'text', 'label'=>'Tagline', 'description'=>''],
            ['key'=>'support_email',          'group'=>'general', 'value'=>'support@primeaviation.com', 'type'=>'text', 'label'=>'Support Email', 'description'=>''],
            ['key'=>'support_phone',          'group'=>'general', 'value'=>'+880 1700 000000', 'type'=>'text', 'label'=>'Support Phone', 'description'=>''],
        ];

        foreach ($settings as $s) {
            SiteSetting::firstOrCreate(['key' => $s['key']], array_merge($s, ['is_public' => false]));
        }

        // ── Seed Featured Destinations ─────────────────────────────────────

        $destinations = [
            ['city'=>"Cox's Bazar", 'country'=>'Bangladesh', 'image_url'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400', 'description'=>'World\'s longest sea beach', 'sort_order'=>1, 'is_featured'=>true, 'is_active'=>true, 'property_count_override'=>340, 'min_price_override'=>null],
            ['city'=>'Dhaka',       'country'=>'Bangladesh', 'image_url'=>'https://images.unsplash.com/photo-1599766566-741b6bbfe1a1?w=400', 'description'=>'Capital city of Bangladesh', 'sort_order'=>2, 'is_featured'=>true, 'is_active'=>true, 'property_count_override'=>512, 'min_price_override'=>null],
            ['city'=>'Chittagong',  'country'=>'Bangladesh', 'image_url'=>'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=400', 'description'=>'Port city & scenic hills', 'sort_order'=>3, 'is_featured'=>true, 'is_active'=>true, 'property_count_override'=>210, 'min_price_override'=>null],
            ['city'=>'Sylhet',      'country'=>'Bangladesh', 'image_url'=>'https://images.unsplash.com/photo-1552733407-5d5c46c3bb3b?w=400', 'description'=>'Tea gardens & waterfalls', 'sort_order'=>4, 'is_featured'=>true, 'is_active'=>true, 'property_count_override'=>190, 'min_price_override'=>null],
            ['city'=>'Sreemangal',  'country'=>'Bangladesh', 'image_url'=>'https://images.unsplash.com/photo-1587393855524-087f83d95bc9?w=400', 'description'=>'Tea capital of Bangladesh', 'sort_order'=>5, 'is_featured'=>true, 'is_active'=>true, 'property_count_override'=>85, 'min_price_override'=>null],
            ['city'=>'Khulna',      'country'=>'Bangladesh', 'image_url'=>'https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?w=400', 'description'=>'Gateway to Sundarbans', 'sort_order'=>6, 'is_featured'=>true, 'is_active'=>true, 'property_count_override'=>95, 'min_price_override'=>null],
            ['city'=>'Kuakata',     'country'=>'Bangladesh', 'image_url'=>'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400', 'description'=>'Sunrise & sunset beach', 'sort_order'=>7, 'is_featured'=>false, 'is_active'=>true, 'property_count_override'=>45, 'min_price_override'=>null],
            ['city'=>'Bandarban',   'country'=>'Bangladesh', 'image_url'=>'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=400', 'description'=>'Highest peaks in Bangladesh', 'sort_order'=>8, 'is_featured'=>false, 'is_active'=>true, 'property_count_override'=>60, 'min_price_override'=>null],
        ];

        foreach ($destinations as $d) {
            FeaturedDestination::firstOrCreate(['city' => $d['city']], $d);
        }

        // ── Seed Promotions ────────────────────────────────────────────────

        // Accommodation Promotions
        Promotion::firstOrCreate(['title' => 'Grab all your DEALS'], [
            'subtitle'     => 'Exclusive hotel savings',
            'badge_text'   => 'HOT DEALS',
            'cta_text'     => 'Browse Deals',
            'cta_link'     => '/deals',
            'bg_color'     => '#7c3aed',
            'bg_color_end' => '#4f46e5',
            'text_color'   => '#ffffff',
            'badge_bg'     => '#f59e0b',
            'type'         => 'accommodation',
            'is_active'    => true,
            'is_featured'  => true,
            'sort_order'   => 1,
        ]);

        Promotion::firstOrCreate(['title' => 'Last Minute Hotel Deals'], [
            'subtitle'     => 'Book now & save',
            'badge_text'   => 'LIMITED TIME',
            'cta_text'     => 'Up to 40% OFF',
            'cta_link'     => '/hotels',
            'icon'         => '🏨',
            'bg_color'     => '#0ea5e9',
            'bg_color_end' => '#0284c7',
            'text_color'   => '#ffffff',
            'badge_bg'     => '#f59e0b',
            'type'         => 'accommodation',
            'is_active'    => true,
            'is_featured'  => true,
            'sort_order'   => 2,
        ]);

        Promotion::firstOrCreate(['title' => 'Weekend Getaway Deals'], [
            'subtitle'     => 'Escape the city',
            'badge_text'   => 'ESCAPE NOW',
            'cta_text'     => 'From ৳ 1,500/night',
            'cta_link'     => '/search?type=resort',
            'icon'         => '🌴',
            'bg_color'     => '#059669',
            'bg_color_end' => '#047857',
            'text_color'   => '#ffffff',
            'badge_bg'     => '#f59e0b',
            'type'         => 'accommodation',
            'is_active'    => true,
            'is_featured'  => false,
            'sort_order'   => 3,
        ]);

        // Flights & Activities Promotions
        Promotion::firstOrCreate(['title' => 'Ready, Set, Go!'], [
            'subtitle'     => 'Worldwide',
            'badge_text'   => 'WORLDWIDE',
            'cta_text'     => 'Up to 7% off',
            'cta_link'     => '/airport-transfer',
            'icon'         => '✈️',
            'bg_color'     => '#8b5cf6',
            'bg_color_end' => '#6d28d9',
            'text_color'   => '#ffffff',
            'badge_bg'     => '#10b981',
            'type'         => 'flights',
            'is_active'    => true,
            'is_featured'  => true,
            'sort_order'   => 1,
        ]);

        Promotion::firstOrCreate(['title' => 'Dhaka → Cox\'s Bazar'], [
            'subtitle'     => 'Daily Departures',
            'badge_text'   => 'BANGLADESH FLIGHTS',
            'cta_text'     => 'From ৳ 3,500',
            'cta_link'     => '/search?destination=cox',
            'icon'         => '✈️',
            'bg_color'     => '#f97316',
            'bg_color_end' => '#ea580c',
            'text_color'   => '#ffffff',
            'badge_bg'     => '#fbbf24',
            'type'         => 'flights',
            'is_active'    => true,
            'is_featured'  => true,
            'sort_order'   => 2,
        ]);

        Promotion::firstOrCreate(['title' => 'Adventure Activities BD'], [
            'subtitle'     => 'Things to do',
            'badge_text'   => 'THINGS TO DO',
            'cta_text'     => 'Book & Save 15%',
            'cta_link'     => '/tour-packages',
            'icon'         => '🎯',
            'bg_color'     => '#db2777',
            'bg_color_end' => '#be185d',
            'text_color'   => '#ffffff',
            'badge_bg'     => '#10b981',
            'type'         => 'activities',
            'is_active'    => true,
            'is_featured'  => false,
            'sort_order'   => 3,
        ]);

        $this->command->info('✅ Promotions, Destinations & Settings seeded successfully!');
    }
}
