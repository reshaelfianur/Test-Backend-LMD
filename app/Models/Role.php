<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    public $guarded     = [];

    public function accessModule()
    {
        return $this->hasMany('App\Models\Access_module', 'role_id');
    }
}
