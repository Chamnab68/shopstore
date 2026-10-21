<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'sale_price',
        'stock',
        'unit',
        'image',
        'is_active',
        'is_featured',
        'is_organic',
        'is_cold_sale',
        'is_reward_only', // 👈 ត្រូវមានទីនេះ
        'sale_ends_at',
        'point_multiplier',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_organic' => 'boolean',
        'is_cold_sale' => 'boolean',
        'is_reward_only' => 'boolean', // 👈 និងត្រូវមានទីនេះដែរ
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFeatured($query)
{
    return $query->where('is_featured', true);
}


    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-product.png');
    }

    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }
}