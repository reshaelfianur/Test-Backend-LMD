<?php

namespace App\Http\Controllers\Api\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Role as mRole;
use App\Models\Module;
use App\Models\Access_module;
use Mavinoo\Batch\Batch;
use Illuminate\Support\Facades\DB;

class Role extends Controller
{
    public function index(Request $req)
    {
        $get = mRole::when(!empty($req->except_admin), function ($query) {
            return $query->where('name', '!=', 'admin');
        })->get();

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
                return (stristr($col->name, $search) ||
                    stristr($col->display_name, $search));
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
        if ($req->id == 'undefined') {
            $duplicate = mRole::orWhere([
                'name'          => $req->name,
                'display_name'  => $req->display_name
            ])->get();
        } else {
            $duplicate = mRole::orWhere([
                'name'          => $req->name,
                'display_name'  => $req->display_name
            ])->where('id', '<>', $req->id)
                ->get();
        }

        $response = [];

        if ($duplicate->count() > 0) {
            $duplicate = $duplicate->first();

            if ($duplicate->name == $req->name) {
                $message = 'Role Code has already exists.';
            } elseif ($duplicate->display_name == $req->display_name) {
                $message = 'Role Name has already exists.';
            }

            $response = [
                'data'      => $duplicate,
                'status'    => false,
                'message'   => $message,
            ];
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
        $accessModuleArray  = json_decode(base64_decode($req->accessModule));
        $permissionArray    = json_decode(base64_decode($req->permission));

        $permissionIds      = array_column($permissionArray, 'id');

        $role = mRole::create([
            'name'          => trim($req->name),
            'display_name'  => trim($req->display_name),
        ]);

        foreach ($accessModuleArray as $key => $value) {
            $value->role_id             = $role->id;
            $value->created_at          = now();

            $accessModuleArray[$key]    = (array) $value;
        }

        $accessModule   = Access_module::insert($accessModuleArray);
        $permission     = Permission::whereIn('id', $permissionIds)->get();

        foreach ($permission as $key => $value) {
            $role->attachPermission($value);
        }

        return response()->json([
            'data'      => $role,
            'status'    => true,
            'message'   => 'New record has been saved.'
        ], 200);
    }

    public function save(Request $req)
    {
        $accessModuleArray  = json_decode(base64_decode($req->accessModule));
        $permissionArray    = json_decode(base64_decode($req->permission));

        $permissionIds      = array_column($permissionArray, 'id');
        $roleId             = $req->id;

        $row = mRole::find($roleId);

        $row->name          = trim($req->name);
        $row->display_name  = trim($req->display_name);

        $row->save();

        foreach ($accessModuleArray as $key => $value) {
            $value->updated_at          = now();

            $accessModuleArray[$key]    = (array) $value;
        }

        Batch::update(Access_module::class, $accessModuleArray, 'am_id');

        $permissionRole     = DB::table('permission_role')->where('role_id', $roleId)->get();
        $oldPermissionIds   = array_column($permissionRole->toArray(), 'permission_id');

        $oldPermission = Permission::whereIn('id', $oldPermissionIds)->get();
        $newPermission = Permission::whereIn('id', $permissionIds)->get();

        foreach ($oldPermission as $key => $value) {
            $row->detachPermission($value);
        }

        foreach ($newPermission as $key => $value) {
            $row->attachPermission($value);
        }

        return response()->json([
            'data'      => $row,
            'status'    => true,
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function destroy(Request $req)
    {
        $row = mRole::find($req->id);

        if ($row->users->count() > 0) {
            return response()->json([
                'status'    => false,
                'message'   => 'This record can not be deleted because it is still being in used on Role User.'
            ], 200);
        } else if ($row->permissions->count() > 0) {
            return response()->json([
                'status'    => false,
                'message'   => 'This record can not be deleted because it is still being in used on Permission User.'
            ], 200);
        }

        $row->save();
        $row->delete();

        return response()->json([
            'data'      => $row,
            'status'    => true,
            'message'   => 'Record has been successfully deleted.'
        ], 200);
    }

    public function getPermission(Request $req)
    {
        $get = Module::where('mod_status', 1)->with([
            'subModule' => function ($query) {
                return $query->with('permission');
            }
        ])->get();

        return response()->json([
            'data'      => $get,
            'status'    => true
        ], 200);
    }

    public function getRole(Request $req)
    {
        $get    = mRole::find($req->id);

        return response()->json([
            'data'      => [
                'access_module' => $get->accessModule,
                'permissions'   => $get->permissions,
            ],
            'status'    => true
        ], 200);
    }
}
