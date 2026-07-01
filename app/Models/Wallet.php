<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'currency',
        'balance',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($wallet) {
            if (empty($wallet->business_id)) {
                $wallet->business_id = currentBusinessId();
            }

            if (empty($wallet->currency)) {
                $wallet->currency = getSetting('operations.currency', 'NGN');
            }

            if (empty($wallet->status)) {
                $wallet->status = 'active';
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}