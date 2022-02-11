<?php

namespace App\Http\Controllers\Api\Entity\ModuleManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module as mModule;

use App\Traits\DynamicConfig;

class Module extends Controller
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
        $role = new mModule;
        $role->setConnection($this->_compCode);

        $get = $role->all();

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
                return (stristr($col->mod_code, $search) ||
                    stristr($col->mod_name, $search));
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
        $role = new mModule;
        $role->setConnection($this->_compCode);

        if ($req->input('mod_id') == 'undefined') {
            $duplicate = $role->orWhere([
                'mod_code'  => $req->input('mod_code'),
                'mod_name'  => $req->input('mod_name')
            ])
                ->get();
        } else {
            $duplicate = $role->orWhere([
                'mod_code'  => $req->input('mod_code'),
                'mod_name'  => $req->input('mod_name')
            ])
                ->where('mod_id', '<>', $req->input('mod_id'))
                ->get();
        }

        $response = [];

        if ($duplicate->count() > 0) {
            $duplicate = $duplicate->first();

            if ($duplicate->mod_code == $req->input('mod_code')) {
                $message = 'Module Code has already exists';
            } elseif ($duplicate->mod_name == $req->input('mod_name')) {
                $message = 'Module Name has already exists';
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
        $role = new mModule;
        $role->setConnection($this->_compCode);

        $data = $role->create([
            'mod_code'  => trim($req->input('mod_code')),
            'mod_name'  => trim($req->input('mod_name')),
        ]);

        return response()->json([
            'data'      => $data,
            'status'    => 'success',
            'message'   => 'New record has been saved.'
        ], 200);
    }

    public function save(Request $req)
    {
        $role = new mModule;
        $role->setConnection($this->_compCode);

        $row = $role->find($req->input('mod_id'));

        $row->mod_code      = trim($req->input('mod_code'));
        $row->mod_name      = trim($req->input('mod_name'));
        $row->mod_status    = $req->input('mod_status');

        $row->save();

        return response()->json([
            'data'      => $row,
            'status'    => 'success',
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function destroy(Request $req)
    {
        $role = new mModule;
        $role->setConnection($this->_compCode);

        $row = $role->find($req->input('mod_id'));

        if ($row->subModule()->count() > 0) {
            return response()->json([
                'status'    => 'failed',
                'message'   => 'This record can not be deleted because it is still being in used on Sub Module'
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
}
