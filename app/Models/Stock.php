<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'article_id',
        'quantity',
        'station_id',
    ];
}
