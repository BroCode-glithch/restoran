<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        "key",
        "value",
        "business_id",
        "section",
        "is_public",
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($setting) {
            if (empty($setting->business_id)) {
                $setting->business_id = currentBusinessId();
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
