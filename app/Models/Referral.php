<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code',
        'coins_earned',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'coins_earned' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    // Helper Methods
    public function markAsCompleted($coins = 0)
    {
        $this->update([
            'status' => 'completed',
            'coins_earned' => $coins,
            'completed_at' => now(),
        ]);
    }
}
