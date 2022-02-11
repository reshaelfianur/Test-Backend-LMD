<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\Traits\CausesActivity;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'comp_id',
        'user_fullname',
        'user_email',
        'username',
        'password',
        'user_status',
        'user_need_change_password',
        'user_last_login',
        'user_last_reset',
        'user_last_lock',
        'user_last_change_password',
        'grade_from_id',
        'grade_to_id',
        'loc_id',
        'user_active_date',
        'user_inactive_date',
        'user_type',
        'created_by',
        'updated_by',
        'deleted_by',
        'api_token',
    ];

    protected $hidden = [
        'password',
        'api_token',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'comp_id');
    }

    public function gradeFrom()
    {
        return $this->belongsTo('App\Models\Grade', 'grade_from_id');
    }

    public function gradeTo()
    {
        return $this->belongsTo('App\Models\Grade', 'grade_to_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'loc_id');
    }

    public function fetch($args = [])
    {
        return self::on($this->getConnectionName())->select($this->table . '.*', 'b.comp_name', 'c.grade_name as grade_from_name', 'd.grade_name as grade_to_name', 'e.loc_name', 'f.role_id', 'g.display_name as role_name')
            ->distinct()
            ->join('companies AS b', $this->table . '.comp_id', '=', 'b.comp_id')
            ->leftJoin('grades AS c', $this->table . '.grade_from_id', '=', 'c.grade_id')
            ->leftJoin('grades AS d', $this->table . '.grade_to_id', '=', 'd.grade_id')
            ->leftJoin('location AS e', $this->table . '.loc_id', '=', 'e.loc_id')
            ->leftJoin('role_user AS f', $this->table . '.user_id', '=', 'f.user_id')
            ->join('roles AS g', 'f.role_id', '=', 'g.id')
            ->where($args)
            ->where($this->table . '.user_id', '<>', 1)
            ->get();
    }
}
