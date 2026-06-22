<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'availability',
        'type',
        'preparation_time_minutes',
        'is_featured',
    ];

    protected $casts = [
        'availability' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'preparation_time_minutes' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->business_id)) {
                $product->business_id = currentBusinessId();
            }

            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability', true);
    }
}
