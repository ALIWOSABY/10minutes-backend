<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'user_id',
        'amount',
        'currency',
        'coins_purchased',
        'payment_method',
        'status',
        'gateway',
        'gateway_transaction_id',
        'gateway_response',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'coins_purchased' => 'decimal:2',
        'gateway_response' => 'array',
        'completed_at' => 'datetime',
    ];

    // Auto-generate payment number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = 'PAY' . strtoupper(Str::random(10));
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'reference');
    }
}
