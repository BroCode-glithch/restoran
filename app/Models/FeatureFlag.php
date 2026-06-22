<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'key',
        'label',
        'description',
        'enabled',
        'created_by',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($flag) {
            if (empty($flag->business_id)) {
                $flag->business_id = currentBusinessId();
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
