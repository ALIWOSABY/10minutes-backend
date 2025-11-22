<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTaskCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'daily_task_id',
        'completed_date',
        'coins_earned',
    ];

    protected $casts = [
        'completed_date' => 'date',
        'coins_earned' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dailyTask()
    {
        return $this->belongsTo(DailyTask::class);
    }
}
