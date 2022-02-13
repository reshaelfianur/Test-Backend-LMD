<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub_module extends Model
{
    use HasFactory;

    protected $table        = 'sub_modules';
    protected $primaryKey   = 'submod_id';
    protected $fillable     = [
        'mod_id',
        'submod_code',
        'submod_name',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'mod_id');
    }

    public function permission()
    {
        return $this->hasMany(Permission::class, 'submod_id');
    }
}
