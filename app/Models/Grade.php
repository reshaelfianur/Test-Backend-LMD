<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $table        = 'grades';
    protected $primaryKey   = 'grade_id';
    protected $fillable     = [
        'grade_code',
        'grade_name',
        'grade_is_default',
        'grade_level',
        'comp_id',
    ];

    public function user()
    {
        return $this->hasMany('App\Models\User', 'grade_id');
    }

    public function fetch($args = [], $in = [])
    {
        return self::on($this->getConnectionName())->select($this->table . '.*', 'companies.comp_code', 'companies.comp_name')
            ->join('companies', $this->table . '.comp_id', '=', 'companies.comp_id')
            ->where($args)
            ->when(!empty($in), function ($query) use ($in) {
                return $query->whereIn('companies.comp_id', $in);
            })
            ->get();
    }
}
