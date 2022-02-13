<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Traits\CausesActivity;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use LaratrustUserTrait;
    use HasFactory, Notifiable, SoftDeletes;

    protected $table        = 'users';
    protected $primaryKey   = 'user_id';

    protected $fillable     = [
        'email',
        'username',
        'password',
        'user_full_name',
        'user_need_change_password',
        'user_status',
        'user_type',
        'user_active_date',
        'user_inactive_date',
        'user_last_login',
        'user_last_lock',
        'user_last_reset_password',
        'user_last_change_password',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function fetch($args = [])
    {
        $i = new static;

        return self::select($i->table . '.*', 'b.role_id', 'c.display_name as role_name')
            ->distinct()
            ->leftJoin('role_user AS b', $i->table . '.user_id', '=', 'b.user_id')
            ->join('roles AS c', 'b.role_id', '=', 'c.id')
            ->where($args)
            ->get();
    }
}
