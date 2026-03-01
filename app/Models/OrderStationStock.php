<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// OrderStationStock to store the stock distribution for each order
class OrderStationStock extends Model
{
    protected $fillable = [
        'order_id',
        'article_id',
        'station_id',
        'quantity',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}