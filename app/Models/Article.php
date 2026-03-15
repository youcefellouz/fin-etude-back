<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'brand_id',
        'image',
    ];
    protected $appends = ['price_after_discount'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_articles')->withTimestamps();
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_articles')->withPivot('quantity', 'unit_price');
    }

    public function stations()
    {
        return $this->belongsToMany(Station::class, 'stocks')->withPivot('quantity');
    }

    public function stocks()
{
    return $this->hasMany(\App\Models\Stock::class);
}

    public function stationDistributions()
{
    return $this->hasMany(OrderStationStock::class);
}
  public function productAnalytics()
    {
        return $this->hasOne(ProductAnalytics::class);
    }
    public function orderStationStocks()
{
    return $this->hasMany(OrderStationStock::class);
}
public function repairRequests()
{
    return $this->hasMany(RepairRequest::class);
}
    public function getPriceAfterDiscountAttribute()
    {
        $now = now();
        
        $discount = $this->discounts()
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->latest('start_date')
            ->first();

        if ($discount) {
            if ($discount->type === 'percentage') {
                return round($this->price * (1 - $discount->value / 100), 2);
            }
        }

        return $this->price;
    }
    public function reviews()
{
    return $this->hasMany(Review::class);
}
}