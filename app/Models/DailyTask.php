<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'coins_reward',
        'max_completions_per_day',
        'is_active',
    ];

    protected $casts = [
        'coins_reward' => 'decimal:2',
        'max_completions_per_day' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function completions()
    {
        return $this->hasMany(UserTaskCompletion::class);
    }

    // Helper Methods
    public function canBeCompletedByUser($userId)
    {
        $today = now()->toDateString();

        $completionsToday = $this->completions()
            ->where('user_id', $userId)
            ->where('completed_date', $today)
            ->count();

        return $completionsToday < $this->max_completions_per_day;
    }
}
