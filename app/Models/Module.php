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
        return $this->hasMany(Sub_module::class, 'mod_id');
    }

    public function accessModule()
    {
        return $this->hasMany(Access_module::class, 'mod_id');
    }
}
