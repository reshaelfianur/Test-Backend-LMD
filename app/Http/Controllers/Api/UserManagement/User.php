<?php

namespace App\Http\Controllers\Api\UserManagement;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Models\Access_module;
use App\Models\User as mUser;
use App\Models\Role;

class User extends Controller
{
    public function index(Request $req)
    {
        $args = [];

        if (!empty($req->user_id)) {
            $args += ['users.user_id' => $req->user_id];
        }

        if (!empty($req->user_type)) {
            $args += ['users.user_type' => $req->user_type];
        }

        if (!empty($req->created_by)) {
            $args += ['users.created_by' => $req->created_by];
        }

        $get = mUser::fetch($args);

        if (empty($req->_page)) {
            return response()->json([
                'data'      => $get,
                'status'    => true
            ], 200);
        }

        $search     = $req->_search;
        $limit      = $req->_pageSize;
        $offset     = ($req->_page - 1) * $limit;
        $sort       = explode(':', $req->_sortby);
        $column     = $sort[0];
        $get        = $sort[1] == 1 ? $get->sortBy($column) : $get->sortByDesc($column);
        $total      = $get->count();
        $numPage    = $total / $limit;

        if (!empty($search)) {
            $get = $get->filter(function ($col, $val) use ($search) {
                return (stristr($col->user_full_name, $search) ||
                    stristr($col->email, $search) ||
                    stristr($col->username, $search));
            });
        }

        return response()->json([
            'documentSize'  => $get->count(),
            'numberOfPages' => $numPage <= 1 ? 1 : floor($numPage) + 1,
            'page'          => $req->_page,
            'pageSize'      => $limit,
            'data'          => $get->slice($offset, $limit)->values()->all(),
            'status'        => true,
        ], 200);
    }

    public function unique(Request $req)
    {
        if ($req->user_id == 'undefined') {
            $duplicate = mUser::where(function ($q) use ($req) {
                $q->where('username', $req->username)
                    ->orWhere('email', $req->email);
            })->get();
        } else {
            $duplicate = mUser::where(function ($q) use ($req) {
                $q->where('username', $req->username)
                    ->orWhere('email', $req->email);
            })->where('user_id', '<>', $req->user_id)
                ->get();
        }

        $response = [];

        if ($duplicate->count() > 0) {
            $duplicate = $duplicate->first();

            $response = [
                'data'      => $duplicate,
                'status'    => false,
            ];

            if ($duplicate->username == $req->username) {
                $response['message'] = 'Username already exists.';
            } elseif ($duplicate->email == $req->email) {
                $response['message'] = 'Email already exists.';
            }
        } else {
            $response = [
                'data'      => true,
                'status'    => true
            ];
        }

        return response()->json($response, 200);
    }

    public function store(Request $req)
    {
        $user = mUser::create([
            'user_full_name'                => trim($req->user_full_name),
            'email'                         => trim($req->email),
            'username'                      => trim($req->username),
            'user_active_date'              => $req->user_active_date,
            'user_inactive_date'            => $req->user_inactive_date != 'null' ? $req->user_inactive_date : null,
            'user_status'                   => $req->user_status,
            'password'                      => Hash::make(trim($req->password)),
            'created_by'                    => $req->created_by,
        ]);

        $role = Role::find($req->role_id);

        $user->attachRole($role);

        return response()->json([
            'data'      => $user,
            'status'    => true,
            'message'   => 'New record has been saved.'
        ], 200);
    }

    public function save(Request $req)
    {
        $password   = trim($req->password);

        $user = mUser::find($req->user_id);

        $user->user_full_name       = trim($req->user_full_name);
        $user->email                = trim($req->email);
        $user->username             = trim($req->username);
        $user->user_active_date     = $req->user_active_date;
        $user->user_inactive_date   = $req->user_inactive_date != 'null' ? $req->user_inactive_date : null;
        $user->user_status          = $req->user_status;
        $user->updated_by           = $req->updated_by;

        if (!empty($password)) {
            $user->password = Hash::make($password);
        }

        $user->save();

        if ($req->old_role_id != $req->role_id) {
            $user       = mUser::find($req->user_id);

            $newRole    = Role::find($req->role_id);
            $oldRole    = Role::find($req->old_role_id);

            $user->detachRole($oldRole);
            $user->attachRole($newRole);
        }

        return response()->json([
            'data'      => $user,
            'status'    => true,
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function destroy(Request $req)
    {
        $row = mUser::find($req->user_id);

        // $row->detachRole(Role::find($row->role_id));

        $row->deleted_by = $req->deleted_by;

        $row->save();
        $row->delete();

        return response()->json([
            'data'      => $row,
            'status'    => true,
            'message'   => 'Record has been successfully deleted.'
        ], 200);
    }

    public function resetPassword(Request $req)
    {
        $ramdom         = Str::random(8);
        $hashPassword   = Hash::make($ramdom);

        $row    = mUser::find($req->user_id);

        $row->password = $hashPassword;
        $row->save();

        // Send New Password on Email

        return response()->json([
            'data'      => $row,
            'status'    => true,
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function getAccessRights(Request $req)
    {
        $get    = mUser::find($req->user_id);
        $role   = $get->roles[0];

        $getAM  = Access_module::where('role_id', $role->id)->get();

        if (empty($role->permissions)) {
            $role->permissions = [];
        }

        return response()->json([
            'data'      => [
                'access_module' => $getAM,
                'role'          => $role,
                'user'          => [
                    'user_id'   => $get->user_id,
                ],
            ],
            'status'    => true
        ], 200);
    }
}
