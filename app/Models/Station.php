<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $fillable = [
        'name',
        'location',
        'status',
        'city',
        'type',
    ];
    function articles()
    {
        return $this->belongsToMany(Article::class, 'stocks')->withPivot('quantity');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function orderStationStocks()
{
    return $this->hasMany(OrderStationStock::class);
}
public function orderDistributions()
{
    return $this->hasMany(OrderStationStock::class);
}
public function repairRequests()
{
    return $this->hasMany(RepairRequest::class);
}
}
