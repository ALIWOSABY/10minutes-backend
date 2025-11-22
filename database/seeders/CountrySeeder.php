<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Saudi Arabia',
                'code' => 'SA',
                'currency_code' => 'SAR',
                'currency_symbol' => 'SR',
                'exchange_rate' => 1.0000,
                'flag_icon' => '🇸🇦',
                'is_active' => true,
            ],
            [
                'name' => 'United Arab Emirates',
                'code' => 'AE',
                'currency_code' => 'AED',
                'currency_symbol' => 'AED',
                'exchange_rate' => 1.0200,
                'flag_icon' => '🇦🇪',
                'is_active' => true,
            ],
            [
                'name' => 'United States',
                'code' => 'US',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'exchange_rate' => 3.7500,
                'flag_icon' => '🇺🇸',
                'is_active' => true,
            ],
            [
                'name' => 'United Kingdom',
                'code' => 'GB',
                'currency_code' => 'GBP',
                'currency_symbol' => '£',
                'exchange_rate' => 4.7500,
                'flag_icon' => '🇬🇧',
                'is_active' => true,
            ],
            [
                'name' => 'Egypt',
                'code' => 'EG',
                'currency_code' => 'EGP',
                'currency_symbol' => 'E£',
                'exchange_rate' => 0.1200,
                'flag_icon' => '🇪🇬',
                'is_active' => true,
            ],
            [
                'name' => 'Kuwait',
                'code' => 'KW',
                'currency_code' => 'KWD',
                'currency_symbol' => 'KD',
                'exchange_rate' => 12.2500,
                'flag_icon' => '🇰🇼',
                'is_active' => true,
            ],
            [
                'name' => 'Qatar',
                'code' => 'QA',
                'currency_code' => 'QAR',
                'currency_symbol' => 'QR',
                'exchange_rate' => 1.0300,
                'flag_icon' => '🇶🇦',
                'is_active' => true,
            ],
            [
                'name' => 'Bahrain',
                'code' => 'BH',
                'currency_code' => 'BHD',
                'currency_symbol' => 'BD',
                'exchange_rate' => 9.9500,
                'flag_icon' => '🇧🇭',
                'is_active' => true,
            ],
            [
                'name' => 'Oman',
                'code' => 'OM',
                'currency_code' => 'OMR',
                'currency_symbol' => 'OMR',
                'exchange_rate' => 9.7500,
                'flag_icon' => '🇴🇲',
                'is_active' => true,
            ],
            [
                'name' => 'Jordan',
                'code' => 'JO',
                'currency_code' => 'JOD',
                'currency_symbol' => 'JD',
                'exchange_rate' => 5.2900,
                'flag_icon' => '🇯🇴',
                'is_active' => true,
            ],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
