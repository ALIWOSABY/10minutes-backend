<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'user_id',
        'session_id',
        'price_paid',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'session_notes',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Auto-generate booking number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = 'BK' . strtoupper(Str::random(10));
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'reference');
    }

    // Helper Methods
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeReviewed()
    {
        return $this->status === 'completed' && !$this->review()->exists();
    }
}
