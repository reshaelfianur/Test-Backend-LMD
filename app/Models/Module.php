<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table        = 'modules';
    protected $primaryKey   = 'mod_id';
    protected $fillable     = [
        'mod_code',
        'mod_name',
        'mod_status',
    ];

    public function subModule()
    {
        return $this->hasMany('App\Models\Sub_module', 'mod_id');
    }

    public function accessModule()
    {
        return $this->hasMany('App\Models\Access_module', 'mod_id');
    }
}
