<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "image",
        "category",
        "status",
        "description",
        "price",
        "business_id",
    ];

    protected static function booted()
    {
        static::creating(function ($menu) {
            if (empty($menu->business_id)) {
                $menu->business_id = currentBusinessId();
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
