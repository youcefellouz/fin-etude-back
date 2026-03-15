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

      public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}

