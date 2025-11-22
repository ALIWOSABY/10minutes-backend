<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'age',
        'gender',
        'country_id',
        'preferred_language',
        'credit_balance',
        'role',
        'referral_code',
        'avatar',
        'is_active',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'credit_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    public function taskCompletions()
    {
        return $this->hasMany(UserTaskCompletion::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Helper Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function hasEnoughCredits($amount)
    {
        return $this->credit_balance >= $amount;
    }
}
