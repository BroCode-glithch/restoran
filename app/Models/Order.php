<?php

namespace App\Models;

use App\Services\RoleManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'assigned_staff_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'delivery_type',
        'delivery_area',
        'status',
        'payment_status',
        'payment_method',
        'currency',
        'subtotal',
        'delivery_fee',
        'tax',
        'discount',
        'total',
        'delivery_address',
        'notes',
        'payment_reference',
        'placed_at',
        'confirmed_at',
        'preparing_at',
        'ready_at',
        'out_for_delivery_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'placed_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'preparing_at' => 'datetime',
        'ready_at' => 'datetime',
        'out_for_delivery_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->business_id)) {
                $order->business_id = currentBusinessId();
            }

            if (empty($order->order_number)) {
                $order->order_number = 'FO-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
            }

            if (empty($order->status)) {
                $order->status = 'placed';
            }

            if (empty($order->payment_status)) {
                $order->payment_status = 'pending';
            }

            if (empty($order->currency)) {
                $order->currency = getSetting('operations.currency', 'NGN');
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function customerUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function statusLabel()
    {
        return app(RoleManager::class)->statusLabel($this->status);
    }

    public function statusBadge()
    {
        return app(RoleManager::class)->statusBadge($this->status);
    }
}
