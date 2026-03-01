<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'global_price',
        'status',
        'user_id',
        //'station_id', 
        'guest_name',
        'guest_phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'order_articles')
                    ->withPivot('quantity', 'unit_price');
    }

    public function stationDistributions()
    {
        return $this->hasMany(OrderStationStock::class);
    }

    public function calculateGlobalPrice()
    {
        return $this->articles->sum(function ($article) {
            return $article->pivot->unit_price * $article->pivot->quantity;
        });
    }
}