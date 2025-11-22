<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Main Category: Languages
        $languages = Category::create([
            'name' => 'Languages',
            'slug' => 'languages',
            'description' => 'Learn different languages with AI trainers',
            'icon' => '🌍',
            'platform_type' => 'training',
            'order' => 1,
            'is_active' => true,
        ]);

        // Sub-categories for Languages
        $english = Category::create([
            'name' => 'English',
            'slug' => 'english',
            'description' => 'Learn English language',
            'icon' => '🇬🇧',
            'parent_id' => $languages->id,
            'platform_type' => 'training',
            'order' => 1,
            'is_active' => true,
        ]);

        // Sub-sub-categories for English
        Category::create([
            'name' => 'Beginner English',
            'slug' => 'beginner-english',
            'description' => 'English for beginners',
            'parent_id' => $english->id,
            'platform_type' => 'training',
            'order' => 1,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Intermediate English',
            'slug' => 'intermediate-english',
            'description' => 'English for intermediate learners',
            'parent_id' => $english->id,
            'platform_type' => 'training',
            'order' => 2,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Advanced English',
            'slug' => 'advanced-english',
            'description' => 'English for advanced learners',
            'parent_id' => $english->id,
            'platform_type' => 'training',
            'order' => 3,
            'is_active' => true,
        ]);

        // Main Category: Business
        $business = Category::create([
            'name' => 'Business & Marketing',
            'slug' => 'business-marketing',
            'description' => 'Business skills and marketing strategies',
            'icon' => '💼',
            'platform_type' => 'both',
            'order' => 2,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Digital Marketing',
            'slug' => 'digital-marketing',
            'description' => 'Learn digital marketing strategies',
            'parent_id' => $business->id,
            'platform_type' => 'both',
            'order' => 1,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Social Media Marketing',
            'slug' => 'social-media-marketing',
            'description' => 'Master social media marketing',
            'parent_id' => $business->id,
            'platform_type' => 'both',
            'order' => 2,
            'is_active' => true,
        ]);

        // Main Category: Technology
        $tech = Category::create([
            'name' => 'Technology & Programming',
            'slug' => 'technology-programming',
            'description' => 'Learn programming and tech skills',
            'icon' => '💻',
            'platform_type' => 'training',
            'order' => 3,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'Build websites and web applications',
            'parent_id' => $tech->id,
            'platform_type' => 'training',
            'order' => 1,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Mobile Development',
            'slug' => 'mobile-development',
            'description' => 'Build mobile applications',
            'parent_id' => $tech->id,
            'platform_type' => 'training',
            'order' => 2,
            'is_active' => true,
        ]);

        // Main Category: Consultation
        Category::create([
            'name' => 'Financial Consultation',
            'slug' => 'financial-consultation',
            'description' => 'Get expert financial advice',
            'icon' => '💰',
            'platform_type' => 'consultation',
            'order' => 4,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Legal Consultation',
            'slug' => 'legal-consultation',
            'description' => 'Get legal advice and guidance',
            'icon' => '⚖️',
            'platform_type' => 'consultation',
            'order' => 5,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Career Consultation',
            'slug' => 'career-consultation',
            'description' => 'Career guidance and advice',
            'icon' => '🎯',
            'platform_type' => 'consultation',
            'order' => 6,
            'is_active' => true,
        ]);
    }
}
