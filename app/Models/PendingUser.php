<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
     protected $fillable = [
        'name',
        'email',
        'password',
        'verification_code',
        'code_expires_at'
    ];
}
