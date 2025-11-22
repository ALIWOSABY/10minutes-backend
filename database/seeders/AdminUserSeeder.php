<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $saudiArabia = Country::where('code', 'SA')->first();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@10minutes.com',
            'password' => Hash::make('admin123456'),
            'phone' => '+966500000000',
            'age' => 30,
            'gender' => 'male',
            'country_id' => $saudiArabia?->id,
            'preferred_language' => 'en',
            'credit_balance' => 1000.00,
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        // Demo Customer
        User::create([
            'name' => 'Test Customer',
            'email' => 'customer@10minutes.com',
            'password' => Hash::make('customer123456'),
            'phone' => '+966500000001',
            'age' => 25,
            'gender' => 'male',
            'country_id' => $saudiArabia?->id,
            'preferred_language' => 'en',
            'credit_balance' => 50.00,
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
    }
}
