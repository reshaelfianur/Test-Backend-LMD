<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access_module extends Model
{
    use HasFactory;

    protected $table        = 'access_modules';
    protected $primaryKey   = 'am_id';
    protected $fillable     = [
        'mod_id',
        'role_id',
        'am_rights',
    ];

    public function module()
    {
        return $this->belongsTo('App\Models\Module', 'mod_id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }
}
