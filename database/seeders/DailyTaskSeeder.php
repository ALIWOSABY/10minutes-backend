<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DailyTask;

class DailyTaskSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = [
            [
                'name' => 'Daily Login',
                'slug' => 'daily-login',
                'description' => 'Login to your account',
                'coins_reward' => 1.00,
                'max_completions_per_day' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Complete Profile',
                'slug' => 'complete-profile',
                'description' => 'Fill in all your profile information',
                'coins_reward' => 5.00,
                'max_completions_per_day' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Write a Review',
                'slug' => 'write-review',
                'description' => 'Write a review for a completed session',
                'coins_reward' => 3.00,
                'max_completions_per_day' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Watch Introduction Video',
                'slug' => 'watch-intro-video',
                'description' => 'Watch the platform introduction video',
                'coins_reward' => 2.00,
                'max_completions_per_day' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Share on Social Media',
                'slug' => 'share-social',
                'description' => 'Share 10Minutes on your social media',
                'coins_reward' => 2.00,
                'max_completions_per_day' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Watch Advertisement',
                'slug' => 'watch-ad',
                'description' => 'Watch a short advertisement video',
                'coins_reward' => 1.00,
                'max_completions_per_day' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($tasks as $task) {
            DailyTask::create($task);
        }
    }
}
