<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'bio',
        'full_bio',
        'avatar',
        'specialization',
        'skills',
        'languages',
        'education',
        'experience',
        'certifications',
        'rating',
        'total_sessions',
        'total_reviews',
        'platform_type',
        'is_active',
    ];

    protected $casts = [
        'skills' => 'array',
        'languages' => 'array',
        'education' => 'array',
        'experience' => 'array',
        'certifications' => 'array',
        'rating' => 'decimal:2',
        'total_sessions' => 'integer',
        'total_reviews' => 'integer',
        'is_active' => 'boolean',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($trainer) {
            if (empty($trainer->slug)) {
                $trainer->slug = Str::slug($trainer->name);
            }
        });
    }

    // Relationships
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Session::class);
    }
}
