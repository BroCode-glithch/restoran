<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'actor_user_id',
        'level',
        'category',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($log) {
            if (empty($log->business_id)) {
                $log->business_id = currentBusinessId();
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
