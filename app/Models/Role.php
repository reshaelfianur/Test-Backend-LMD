<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    public $guarded     = [];

    public function accessModule()
    {
        return $this->hasMany(Access_module::class, 'role_id');
    }
}
