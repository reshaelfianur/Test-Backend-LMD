<?php

namespace App\Http\Controllers\Api\Entity;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Role as mRole;
use App\Models\Module;
use App\Models\Access_module;
use App\Models\Sub_module;
use App\Traits\DynamicConfig;
use Mavinoo\Batch\Batch;
use DB;

class Role extends Controller
{
    use DynamicConfig;

    private $_compCode;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->_compCode = $this->setConfigDatabaseConnection($request);

            return $next($request);
        });
    }

    public function index(Request $req)
    {
        $role = new mRole;
        $role->setConnection($this->_compCode);

        $args = [];

        if (!empty($req->input('role_id'))) {
            $args += ['id'  => $req->input('role_id')];
        }

        $get = $role->where($args)
            ->when(!empty($req->input('except-super-user')), function ($query) {
                return $query->where('name', '!=', 'super-user');
            })
            ->when(!empty($req->input('except-axia-admin')), function ($query) {
                return $query->where('name', '!=', 'axia-admin');
            })
            ->when(!empty($req->input('except-super-admin')), function ($query) {
                return $query->where('name', '!=', 'super-admin');
            })
            ->get();

        if (empty($req->input('_page'))) {
            return response()->json([
                'data'      => $get,
                'status'    => 'success'
            ], 200);
        }

        $search     = $req->input('_search');
        $limit      = $req->input('_pageSize');
        $offset     = ($req->input('_page') - 1) * $limit;
        $sort       = explode(':', $req->input('_sortby'));
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
            'page'          => $req->input('_page'),
            'pageSize'      => $limit,
            'data'          => $get->slice($offset, $limit)->values()->all(),
            'status'        => 'success',
        ], 200);
    }

    public function unique(Request $req)
    {
        $role = new mRole;
        $role->setConnection($this->_compCode);

        if ($req->input('id') == 'undefined') {
            $duplicate = $role->orWhere([
                'name'          => $req->input('name'),
                'display_name'  => $req->input('display_name')
            ])
                ->get();
        } else {
            $duplicate = $role->orWhere([
                'name'          => $req->input('name'),
                'display_name'  => $req->input('display_name')
            ])
                ->where('id', '<>', $req->input('id'))
                ->get();
        }

        $response = [];

        if ($duplicate->count() > 0) {
            $duplicate = $duplicate->first();

            if ($duplicate->name == $req->input('name')) {
                $message = 'Role Code has already exists';
            } elseif ($duplicate->display_name == $req->input('display_name')) {
                $message = 'Role Name has already exists';
            }

            $response = [
                'data'      => $duplicate,
                'status'    => 'failed',
                'message'   => $message,
            ];
        } else {
            $response = [
                'data'      => true,
                'status'    => 'success'
            ];
        }

        return response()->json($response, 200);
    }

    public function store(Request $req)
    {
        $accessModuleArray  = json_decode(base64_decode($req->input('accessModule')));
        $permissionArray    = json_decode(base64_decode($req->input('permission')));

        $permissionIds      = array_column($permissionArray, 'id');

        $role = new mRole;
        $role->setConnection($this->_compCode);

        $role = $role->create([
            'name'          => trim($req->input('name')),
            'display_name'  => trim($req->input('display_name')),
        ]);

        foreach ($accessModuleArray as $key => $value) {
            $value->role_id             = $role->id;
            $value->created_at          = now();

            $accessModuleArray[$key]    = (array) $value;
        }

        $am = new Access_module;
        $am->setConnection($this->_compCode);
        $am->insert($accessModuleArray);

        $permission = new Permission;
        $permission->setConnection($this->_compCode);

        $permission = $permission->whereIn('id', $permissionIds)->get();

        foreach ($permission as $key => $value) {
            $role->attachPermission($value);
        }

        return response()->json([
            'data'      => $role,
            'status'    => 'success',
            'message'   => 'New record has been saved.'
        ], 200);
    }

    public function save(Request $req)
    {
        $accessModuleArray  = json_decode(base64_decode($req->input('accessModule')));
        $permissionArray    = json_decode(base64_decode($req->input('permission')));

        $permissionIds      = array_column($permissionArray, 'id');
        $roleId             = $req->input('id');

        $role = new mRole;
        $role->setConnection($this->_compCode);

        $row = $role->find($roleId);

        $row->name          = trim($req->input('name'));
        $row->display_name  = trim($req->input('display_name'));

        $row->save();

        foreach ($accessModuleArray as $key => $value) {
            $value->updated_at          = now();

            $accessModuleArray[$key]    = (array) $value;
        }

        $am = new Access_module;
        $am->setConnection($this->_compCode);

        \Batch::update($am, $accessModuleArray, 'am_id');

        $permissionRole     = \DB::connection($this->_compCode)->table('permission_role')->where('role_id', $roleId)->get();
        $oldPermissionIds   = array_column($permissionRole->toArray(), 'permission_id');

        $permission = new Permission;
        $permission->setConnection($this->_compCode);

        $oldPermission = $permission->whereIn('id', $oldPermissionIds)->get();
        $newPermission = $permission->whereIn('id', $permissionIds)->get();

        foreach ($oldPermission as $key => $value) {
            $row->detachPermission($value);
        }

        foreach ($newPermission as $key => $value) {
            $row->attachPermission($value);
        }

        return response()->json([
            'data'      => $row,
            'status'    => 'success',
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function destroy(Request $req)
    {
        $role = new mRole;
        $role->setConnection($this->_compCode);

        $row = $role->find($req->input('id'));

        if ($row->users->count() > 0) {
            return response()->json([
                'status'    => 'failed',
                'message'   => 'This record can not be deleted because it is still being in used on Role User'
            ], 200);
        } else if ($row->permissions->count() > 0) {
            return response()->json([
                'status'    => 'failed',
                'message'   => 'This record can not be deleted because it is still being in used on Permission User'
            ], 200);
        }

        $row->save();
        $row->delete();

        return response()->json([
            'data'      => $row,
            'status'    => 'success',
            'message'   => 'Record has been successfully deleted'
        ], 200);
    }

    public function getPermission(Request $req)
    {
        $module = new Module;
        $module->setConnection($this->_compCode);

        $get = $module->where('mod_status', 1)->with([
            'subModule' => function ($query) {
                return $query->with('permission');
            }
        ])->get();

        return response()->json([
            'data'      => $get,
            'status'    => 'success'
        ], 200);
    }

    public function getRole(Request $req)
    {
        $role = new mRole;
        $role->setConnection($this->_compCode);

        $get    = $role->find($req->input('id'));

        return response()->json([
            'data'      => [
                'access_module' => $get->accessModule,
                'permissions'   => $get->permissions,
            ],
            'status'    => 'success'
        ], 200);
    }
}
