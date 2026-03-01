<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'type',
        'value',
        'start_date',
        'end_date',
    ];
    function articles()
    {
        return $this->belongsToMany(Article::class, 'discount_articles')->withTimestamps();;
    }
   public function getPriceAfterDiscountAttribute()
{
    $now = now();
    $discount = $this->discounts()
                     ->where('start_date', '<=', $now)
                     ->where('end_date', '>=', $now)
                     ->latest('start_date')
                     ->first();

    if ($discount && $discount->type === 'percentage') {
        return round($this->price * (1 - $discount->value / 100), 2);
    }
    return $this->price;
}  // hetha na7ih
}
