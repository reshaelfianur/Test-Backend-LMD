<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table        = 'tasks';
    protected $primaryKey   = 'task_id';
    protected $fillable     = [
        'user_id',
        'task_title',
        'task_description',
        'task_status',
        'task_hours',
        'task_planned_start_date',
        'task_planned_end_date',
        'task_actual_start_date',
        'task_actual_end_date',
        'task_notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function fetch($args = [])
    {
        $i = new static;

        return self::select($i->table . '.*', 'b.user_full_name')
            ->join('users AS b', $i->table . '.user_id', '=', 'b.user_id')
            ->where($args)
            ->get();
    }
}
