<?php

namespace App\Http\Controllers\Api\Entity\ModuleManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sub_module;

use App\Traits\DynamicConfig;

class SubModule extends Controller
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
        $role = new Sub_module;
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
                return (stristr($col->submod_code, $search) ||
                    stristr($col->submod_name, $search));
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
        $role = new Sub_module;
        $role->setConnection($this->_compCode);

        if ($req->input('submod_id') == 'undefined') {
            $duplicate = $role->orWhere([
                'submod_code'  => $req->input('submod_code'),
                'submod_name'  => $req->input('submod_name')
            ])
                ->get();
        } else {
            $duplicate = $role->orWhere([
                'submod_code'  => $req->input('submod_code'),
                'submod_name'  => $req->input('submod_name')
            ])
                ->where('submod_id', '<>', $req->input('submod_id'))
                ->get();
        }

        $response = [];

        if ($duplicate->count() > 0) {
            $duplicate = $duplicate->first();

            if ($duplicate->submod_code == $req->input('submod_code')) {
                $message = 'Sub module Code has already exists';
            } elseif ($duplicate->submod_name == $req->input('submod_name')) {
                $message = 'Sub module Name has already exists';
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
        $role = new Sub_module;
        $role->setConnection($this->_compCode);

        $data = $role->create([
            'mod_id'       => $req->input('mod_id'),
            'submod_code'  => trim($req->input('submod_code')),
            'submod_name'  => trim($req->input('submod_name')),
        ]);

        return response()->json([
            'data'      => $data,
            'status'    => 'success',
            'message'   => 'New record has been saved.'
        ], 200);
    }

    public function save(Request $req)
    {
        $role = new Sub_module;
        $role->setConnection($this->_compCode);

        $row = $role->find($req->input('submod_id'));

        $row->mod_id       = $req->input('mod_id');
        $row->submod_code  = trim($req->input('submod_code'));
        $row->submod_name  = trim($req->input('submod_name'));

        $row->save();

        return response()->json([
            'data'      => $row,
            'status'    => 'success',
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function destroy(Request $req)
    {
        $role = new Sub_module;
        $role->setConnection($this->_compCode);

        $row = $role->find($req->input('submod_id'));

        if ($row->permission()->count() > 0) {
            return response()->json([
                'status'    => 'failed',
                'message'   => 'This record can not be deleted because it is still being in used on Permission'
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
