<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            SettingSeeder::class,
            DailyTaskSeeder::class,
            AdminUserSeeder::class,
            CategorySeeder::class,
            MenuSeeder::class,
        ]);
    }
}
