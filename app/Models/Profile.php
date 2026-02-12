<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'date_birth',
        'image',
    ];
    function user()
    {
        return $this->belongsTo(User::class);
    }
}
