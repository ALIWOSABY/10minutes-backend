<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Session extends Model
{
    use HasFactory;

    protected $table = 'training_sessions';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'full_description',
        'image',
        'category_id',
        'trainer_id',
        'platform_type',
        'price_coins',
        'duration_minutes',
        'level',
        'views_count',
        'bookings_count',
        'rating',
        'reviews_count',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'price_coins' => 'decimal:2',
        'duration_minutes' => 'integer',
        'views_count' => 'integer',
        'bookings_count' => 'integer',
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->slug)) {
                $session->slug = Str::slug($session->title);
            }
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // Helper Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function updateRating()
    {
        $avgRating = $this->reviews()->avg('rating');
        $reviewCount = $this->reviews()->count();

        $this->update([
            'rating' => $avgRating ?? 0,
            'reviews_count' => $reviewCount,
        ]);
    }
}
