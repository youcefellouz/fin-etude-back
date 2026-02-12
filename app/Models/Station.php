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
}
