<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'wallet_id',
        'order_id',
        'reference',
        'type',
        'method',
        'status',
        'amount',
        'balance_before',
        'balance_after',
        'narration',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'meta' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->business_id)) {
                $transaction->business_id = currentBusinessId();
            }
        });
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}