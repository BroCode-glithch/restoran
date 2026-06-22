<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ["icon", "title", "description", "business_id"];

    protected static function booted()
    {
        static::creating(function ($service) {
            if (empty($service->business_id)) {
                $service->business_id = currentBusinessId();
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
