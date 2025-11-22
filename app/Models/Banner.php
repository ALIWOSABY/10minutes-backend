<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'type',
        'platform_type',
        'order',
        'clicks_count',
        'views_count',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'clicks_count' => 'integer',
        'views_count' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Helper Methods
    public function incrementClicks()
    {
        $this->increment('clicks_count');
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }
}
