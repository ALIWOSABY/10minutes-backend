<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'booking_id',
        'rating',
        'comment',
        'helpful_count',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'is_approved' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Helper Methods
    public function incrementHelpful()
    {
        $this->increment('helpful_count');
    }
}
