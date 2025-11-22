<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Training Platform Menus
        $trainingAreas = Menu::create([
            'title' => 'Training Areas',
            'slug' => 'training-areas',
            'url' => '/training/categories',
            'icon' => '📚',
            'platform_type' => 'training',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'title' => 'Why 10Minutes?',
            'slug' => 'why-10minutes',
            'url' => '/about',
            'icon' => '❓',
            'platform_type' => 'both',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'title' => 'Our Trainers',
            'slug' => 'our-trainers',
            'url' => '/trainers',
            'icon' => '👨‍🏫',
            'platform_type' => 'training',
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'title' => 'Reviews',
            'slug' => 'reviews',
            'url' => '/reviews',
            'icon' => '⭐',
            'platform_type' => 'both',
            'order' => 4,
            'is_active' => true,
        ]);

        Menu::create([
            'title' => 'Free Credits',
            'slug' => 'free-credits',
            'url' => '/free-credits',
            'icon' => '🎁',
            'platform_type' => 'both',
            'order' => 5,
            'is_active' => true,
        ]);

        // Consultation Platform Menus
        Menu::create([
            'title' => 'Consultation Areas',
            'slug' => 'consultation-areas',
            'url' => '/consultation/categories',
            'icon' => '💼',
            'platform_type' => 'consultation',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'title' => 'Our Consultants',
            'slug' => 'our-consultants',
            'url' => '/consultants',
            'icon' => '👔',
            'platform_type' => 'consultation',
            'order' => 3,
            'is_active' => true,
        ]);
    }
}
