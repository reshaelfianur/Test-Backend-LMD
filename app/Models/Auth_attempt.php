<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auth_attempt extends Model
{
    use HasFactory;

    protected $table        = 'auth_attempt';
    protected $fillable     = [
        'ip_address',
        'user_agent',
        'captcha',
        'attempt',
    ];
}
