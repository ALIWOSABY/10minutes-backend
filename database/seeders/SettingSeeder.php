<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => '10Minutes',
                'type' => 'string',
                'description' => 'Platform name',
            ],
            [
                'key' => 'site_email',
                'value' => 'info@10minutes.com',
                'type' => 'string',
                'description' => 'Platform contact email',
            ],
            [
                'key' => 'site_phone',
                'value' => '+966 11 234 5678',
                'type' => 'string',
                'description' => 'Platform contact phone',
            ],

            // Currency & Credits
            [
                'key' => 'default_currency',
                'value' => 'SAR',
                'type' => 'string',
                'description' => 'Default currency code',
            ],
            [
                'key' => 'coins_per_dollar',
                'value' => '2',
                'type' => 'number',
                'description' => 'How many coins equal 1 dollar/currency unit',
            ],

            // Referral Program
            [
                'key' => 'referral_signup_reward',
                'value' => '5',
                'type' => 'number',
                'description' => 'Coins earned when referred user signs up',
            ],
            [
                'key' => 'referral_booking_reward',
                'value' => '10',
                'type' => 'number',
                'description' => 'Coins earned when referred user makes first booking',
            ],

            // Daily Tasks
            [
                'key' => 'daily_login_reward',
                'value' => '1',
                'type' => 'number',
                'description' => 'Coins for daily login',
            ],
            [
                'key' => 'review_reward',
                'value' => '3',
                'type' => 'number',
                'description' => 'Coins for writing a review',
            ],
            [
                'key' => 'profile_complete_reward',
                'value' => '5',
                'type' => 'number',
                'description' => 'One-time reward for completing profile',
            ],

            // Booking Settings
            [
                'key' => 'booking_cancellation_hours',
                'value' => '24',
                'type' => 'number',
                'description' => 'Hours before session to allow cancellation',
            ],
            [
                'key' => 'booking_auto_complete_hours',
                'value' => '1',
                'type' => 'number',
                'description' => 'Hours after session to auto-complete booking',
            ],

            // Payment Settings
            [
                'key' => 'min_topup_amount',
                'value' => '10',
                'type' => 'number',
                'description' => 'Minimum top-up amount in currency',
            ],
            [
                'key' => 'max_topup_amount',
                'value' => '1000',
                'type' => 'number',
                'description' => 'Maximum top-up amount in currency',
            ],

            // Feature Flags
            [
                'key' => 'enable_referral_program',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable/disable referral program',
            ],
            [
                'key' => 'enable_daily_tasks',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable/disable daily tasks',
            ],
            [
                'key' => 'enable_reviews',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable/disable reviews',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Enable/disable maintenance mode',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
