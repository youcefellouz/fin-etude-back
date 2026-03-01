<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    protected $fillable = [
        'user_id',
        'station_id',
        'article_id',
        'description',
        'status',
        'warranty',
        'estimated_cost',
        'appointment_date',
        'technician_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}