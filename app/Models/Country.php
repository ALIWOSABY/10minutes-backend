<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'currency_code',
        'currency_symbol',
        'exchange_rate',
        'flag_icon',
        'is_active',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
