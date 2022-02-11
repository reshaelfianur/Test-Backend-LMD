<?php

namespace App\Models;

use Laratrust\Models\LaratrustPermission;

class Permission extends LaratrustPermission
{
    public $guarded     = [];

    public $fillable    = [
        'submod_id',
        'name',
        'display_name',
        'description',
        'type',
    ];

    public function subModule()
    {
        return $this->belongsTo('App\Models\Sub_module', 'submod_id');
    }
}
