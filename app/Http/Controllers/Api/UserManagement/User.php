<?php

namespace App\Http\Controllers\Api\Entity;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\Access_module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User as mUser;
use App\Models\Role;
use App\Models\Grade;

use App\Mail\UserResetPassword;

use App\Traits\DynamicConfig;

class User extends Controller
{
    use DynamicConfig;

    private $_compCode;
    private $_compCodeSelect;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->_compCode        = $this->setConfigDatabaseConnection($request);
            $this->_compCodeSelect  = $this->setConfigDatabaseConnectionSelect($request);

            return $next($request);
        });
    }

    public function index(Request $req)
    {
        $user = new mUser;
        $user->setConnection($this->_compCode);

        $args = [];

        if (!empty($req->input('_companySearch'))) {
            $args += ['b.comp_id' => $req->input('_companySearch')];
        }

        if (!empty($req->input('user_type'))) {
            $args += ['users.user_type' => $req->input('user_type')];
        }

        if (!empty($req->input('created_by'))) {
            $args += ['users.created_by' => $req->input('created_by')];
        }

        $get = $user->fetch($args);

        if (empty($req->input('_page'))) {
            return response()->json([
                'data'      => $get->makeVisible('password'),
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
                return (stristr($col->comp_name, $search) ||
                    stristr($col->user_fullname, $search) ||
                    stristr($col->user_email, $search) ||
                    stristr($col->username, $search));
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
        $user = new mUser;
        $user->setConnection($this->_compCode);

        if ($req->input('user_id') == 'undefined') {
            $duplicate = $user->where('username', $req->input('username'))
                ->when(!empty($req->input('user_is_ho')), function ($query) use ($req) {
                    return $query->whereIn('comp_id', array_column(json_decode(base64_decode($req->input('companies_assigned'))), 'comp_id'));
                })
                ->get();
        } else {
            $duplicate = $user->where('username', $req->input('username'))
                ->when(!empty($req->input('user_is_ho')), function ($query)  use ($req) {
                    return $query->whereIn('comp_id', array_column(json_decode(base64_decode($req->input('companies_assigned_all'))), 'comp_id'))
                        ->whereNotIn('user_id', json_decode(base64_decode($req->input('user_ids'))));
                })
                ->when(empty($req->input('user_is_ho')), function ($query)  use ($req) {
                    return $query->where('user_id', '<>', $req->input('user_id'));
                })
                ->get();
        }

        $response = [];

        if ($duplicate->count() > 0) {
            $duplicate = $duplicate->first();

            $response = [
                'data'      => $duplicate,
                'status'    => 'failed',
            ];

            if ($duplicate->username == $req->input('username')) {
                $response['message'] = 'Username already exists.';
            } elseif ($duplicate->user_email == $req->input('user_email')) {
                $response['message'] = 'Email already exists.';
            }
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
        $userDBDefault = new mUser;
        $userDBDefault->setConnection($this->_compCode);

        $companiesAssigned  = json_decode(base64_decode($req->input('companies_assigned')));

        $userData = [
            'user_fullname'                 => trim($req->input('user_fullname')),
            'user_email'                    => trim($req->input('user_email')),
            'username'                      => trim($req->input('username')),
            'grade_from_id'                 => $req->input('grade_from_id'),
            'grade_to_id'                   => $req->input('grade_to_id'),
            'loc_id'                        => $req->input('loc_id'),
            'user_active_date'              => $req->input('user_active_date'),
            'user_inactive_date'            => $req->input('user_inactive_date') != 'null' ? $req->input('user_inactive_date') : null,
            'user_status'                   => $req->input('user_status'),
            'password'                      => Hash::make(trim($req->input('password'))),
            'user_need_change_password'     => 2,
            'user_type'                     => $req->input('user_type'),
            'created_by'                    => $req->input('created_by'),
        ];

        if (!empty($req->input('user_is_ho')) && $this->_compCodeSelect != env('DB_DATABASE')) {
            $roleData = Role::find(3);

            foreach ($companiesAssigned as $key => $value) {
                $userData['comp_id'] = $value->comp_id;

                $rowUserData    = $userDBDefault->create($userData);

                $rowUserData->attachRole($roleData);

                $compCode       = $value->comp_code;

                if ($compCode != 'axiasolusi') {
                    $config     = App::make('config');
                    $connection = $config->get('database.connections')['mysql'];

                    $connection['database'] = $compCode;

                    $config->set('database.connections.' . $compCode, $connection);

                    $user = new mUser;
                    $user->setConnection($compCode);
                    $user->insert(array_merge($userData, ['user_id' => $rowUserData->user_id]));

                    $rowUserData2 = $user->find($rowUserData->user_id);

                    $rowUserData2->attachRole($roleData);
                }
            }
        } else {
            $userData['comp_id'] = $req->input('comp_id');

            $rowUserData = $userDBDefault->create($userData);

            $role = new Role;
            $role->setConnection($this->_compCode);

            $roleData = $role->where('id', $req->input('role_id'))->first();

            $rowUserData->attachRole($roleData);
        }

        return response()->json([
            'data'      => $rowUserData,
            'status'    => 'success',
            'message'   => 'New record has been saved.'
        ], 200);
    }

    public function save(Request $req)
    {
        $user = new mUser;
        $user->setConnection($this->_compCode);

        $updatedBy                  = $req->input('updated_by');
        $username                   = trim($req->input('username'));
        $usersAssigned              = json_decode(base64_decode($req->input('users_assigned')));
        $companiesAssignedAdd       = json_decode(base64_decode($req->input('companies_assigned_add')));
        $companiesAssignedDelete    = json_decode(base64_decode($req->input('companies_assigned_delete')));
        $companiesAssignedDeleteIds = array_column($companiesAssignedDelete, 'comp_id');

        $userData = [
            'user_fullname'         => trim($req->input('user_fullname')),
            'user_email'            => trim($req->input('user_email')),
            'username'              => $username,
            'grade_from_id'         => $req->input('grade_from_id'),
            'grade_to_id'           => $req->input('grade_to_id'),
            'loc_id'                => $req->input('loc_id'),
            'user_active_date'      => $req->input('user_active_date'),
            'user_inactive_date'    => $req->input('user_inactive_date') != 'null' ? $req->input('user_inactive_date') : null,
            'user_status'           => $req->input('user_status'),
            'updated_by'            => $updatedBy,
        ];

        if (!empty($req->input('user_is_ho')) && $this->_compCodeSelect != env('DB_DATABASE')) {
            $updateUser = $user->whereIn('user_id', array_column($usersAssigned, 'user_id'))->update($userData);

            $delUsers = [
                'users.deleted_at'  => Carbon::now()->toDateTimeString(),
                'users.deleted_by'  => $updatedBy
            ];

            $user->where('username', $username)
                ->whereIn('comp_id', $companiesAssignedDeleteIds)
                ->update($delUsers);

            foreach ($usersAssigned as $key => $value) {
                $compCode = $value->comp_code;

                if ($compCode != 'axiasolusi') {
                    $config     = App::make('config');
                    $connection = $config->get('database.connections')['mysql'];
                    $userId     = $value->user_id;

                    $connection['database'] = $compCode;

                    $config->set('database.connections.' . $compCode, $connection);

                    $user2 = new mUser;
                    $user2->setConnection($compCode);
                    $updateUser = $user2->where('user_id', $userId)->update($userData);

                    if ($req->input('old_role_id') != $req->input('role_id')) {
                        $rowUser2 = $user2->find($userId);

                        $role = new Role;
                        $role->setConnection($compCode);

                        $oldRoleData = $role->where('id', $req->input('old_role_id'))->first();
                        $newRoleData = $role->where('id', $req->input('role_id'))->first();

                        $rowUser2->detachRole($oldRoleData);
                        $rowUser2->attachRole($newRoleData);
                    }

                    if (in_array($value->comp_id, $companiesAssignedDeleteIds)) {
                        $user2->where('user_id', $userId)->update($delUsers);
                    }
                }
            }

            if (!empty($companiesAssignedAdd)) {
                $roleData       = Role::find(3);
                $userPassword   = $user->where('username', $username)->first()->makeVisible('password')->password;

                $userData['created_by']                 = $updatedBy;
                $userData['password']                   = $userPassword;
                $userData['user_type']                  = 3;
                $userData['user_need_change_password']  = 2;

                foreach ($companiesAssignedAdd as $key => $value) {
                    $userData['comp_id'] = $value->comp_id;

                    $rowUserData    = $user->create($userData);
                    $compCode       = $value->comp_code;

                    $rowUserData->attachRole($roleData);

                    if ($compCode != 'axiasolusi') {
                        $config     = App::make('config');
                        $connection = $config->get('database.connections')['mysql'];

                        $connection['database'] = $compCode;

                        $config->set('database.connections.' . $compCode, $connection);

                        $user2 = new mUser;
                        $user2->setConnection($compCode);
                        $user2->insert(array_merge($userData, ['user_id' => $rowUserData->user_id]));

                        $rowUserData2 = $user2->find($rowUserData->user_id);

                        $rowUserData2->attachRole($roleData);
                    }
                }
            }
        } else {
            $userData['comp_id'] = $req->input('comp_id');

            $updateUser = $user->where('user_id', $req->input('user_id'))->update($userData);

            if ($req->input('old_role_id') != $req->input('role_id')) {
                $rowUser = $user->find($req->input('user_id'));

                $role = new Role;
                $role->setConnection($this->_compCode);

                $oldRoleData = $role->where('id', $req->input('old_role_id'))->first();
                $newRoleData = $role->where('id', $req->input('role_id'))->first();

                $rowUser->detachRole($oldRoleData);
                $rowUser->attachRole($newRoleData);
            }
        }

        return response()->json([
            'data'      => $updateUser,
            'status'    => 'success',
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function destroy(Request $req)
    {
        $user = new mUser;
        $user->setConnection($this->_compCode);

        $deletedBy = $req->input('deleted_by');

        if (empty($req->input('user_is_ho'))) {
            $row = $user->find($req->input('user_id'));

            $row->deleted_by = $deletedBy;

            $row->save();
            $row->delete();
        } else {
            $username       = $req->input('username');
            $usersAssigned  = json_decode(base64_decode($req->input('users_assigned')));

            $delUsers = [
                'users.deleted_at'  => Carbon::now()->toDateTimeString(),
                'users.deleted_by'  => $deletedBy
            ];

            $row = $user->where('username', $username)->update($delUsers);

            foreach ($usersAssigned as $key => $value) {
                $compCode   = $value->comp_code;
                $userId     = $value->user_id;

                if ($compCode != 'axiasolusi') {
                    $config     = App::make('config');
                    $connection = $config->get('database.connections')['mysql'];

                    $connection['database'] = $compCode;

                    $config->set('database.connections.' . $compCode, $connection);

                    $user2 = new mUser;
                    $user2->setConnection($compCode);
                    $user2->where('user_id', $userId)->update($delUsers);
                }
            }
        }

        return response()->json([
            'data'      => $row,
            'status'    => 'success',
            'message'   => 'Record has been successfully deleted'
        ], 200);
    }

    public function resetPassword(Request $req)
    {
        $user = new mUser;
        $user->setConnection($this->_compCode);

        $ramdom         = Str::random(8);
        $newPassword    = trim($req->input('password'));
        $hashPassword   = Hash::make($newPassword);

        if (empty($req->input('user_is_ho'))) {
            $row    = $user->find($req->input('user_id'));

            $username       = $row->username;
            $userFullname   = $row->user_fullname;
            $userEmail      = $row->user_email;

            $row->password = $hashPassword;
            $row->save();
        } else {
            $username       = $req->input('username');
            $userEmail      = $req->input('user_email');
            $userFullname   = $req->input('user_fullname');
            $usersAssigned  = json_decode(base64_decode($req->input('users_assigned')));

            $row            = $user->where('username', $username)->update(['password' => $hashPassword]);

            foreach ($usersAssigned as $key => $value) {
                $compCode   = $value->comp_code;
                $userId     = $value->user_id;

                if ($compCode != 'axiasolusi') {
                    $config     = App::make('config');
                    $connection = $config->get('database.connections')['mysql'];

                    $connection['database'] = $compCode;

                    $config->set('database.connections.' . $compCode, $connection);

                    $user2 = new mUser;
                    $user2->setConnection($compCode);
                    $user2->where('user_id', $userId)->update(['password' => $hashPassword]);
                }
            }
        }

        //send notification email
        Mail::to($userEmail)->send(new UserResetPassword([
            'fullname'  => $userFullname,
            'username'  => $username,
            'password'  => $newPassword
        ]));
        //end

        return response()->json([
            'data'      => $row,
            'status'    => 'success',
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function getAccessRights(Request $req)
    {
        $user = new mUser;
        $user->setConnection($this->_compCode);

        $get    = $user->find($req->input('user_id'));
        $role   = $get->roles[0];

        $accessModule = new Access_module;
        $accessModule->setConnection($this->_compCode);

        $getAM      = $accessModule->where('role_id', $role->id)->get();

        if (empty($role->permissions)) {
            $role->permissions = [];
        }

        $userRow    = $get->first();
        $gradeIds   = [];

        if ($userRow->grade_from_id != null && $userRow->grade_to_id != null) {
            $gradeFromLevel = $get->gradeFrom->grade_level;
            $gradeToLevel   = $get->gradeTo->grade_level;

            $grade = new Grade;
            $grade->setConnection($this->_compCode);

            $gradeIds = $grade->whereBetween('grade_level', [$gradeFromLevel, $gradeToLevel])
                ->pluck('grade_id')
                ->toArray();
        }

        return response()->json([
            'data'      => [
                'access_module' => $getAM,
                'role'          => $role,
                'user'          => [
                    'user_id'   => $userRow->user_id,
                    'loc_id'    => $userRow->loc_id,
                    'grade_ids' => $gradeIds,
                ],
            ],
            'status'    => 'success'
        ], 200);
    }
}
