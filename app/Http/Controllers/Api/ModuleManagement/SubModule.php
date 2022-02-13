<?php

namespace App\Http\Controllers\Api\Entity\ModuleManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sub_module;

class SubModule extends Controller
{
    public function index(Request $req)
    {
        $get = Sub_module::all();

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
                return (stristr($col->submod_code, $search) ||
                    stristr($col->submod_name, $search));
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
        if ($req->submod_id == 'undefined') {
            $duplicate = Sub_module::orWhere([
                'submod_code'  => $req->submod_code,
                'submod_name'  => $req->submod_name
            ])
                ->get();
        } else {
            $duplicate = Sub_module::orWhere([
                'submod_code'  => $req->submod_code,
                'submod_name'  => $req->submod_name
            ])
                ->where('submod_id', '<>', $req->submod_id)
                ->get();
        }

        $response = [];

        if ($duplicate->count() > 0) {
            $duplicate = $duplicate->first();

            if ($duplicate->submod_code == $req->submod_code) {
                $message = 'Sub module Code has already exists.';
            } elseif ($duplicate->submod_name == $req->submod_name) {
                $message = 'Sub module Name has already exists.';
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
        $data = Sub_module::create([
            'mod_id'       => $req->mod_id,
            'submod_code'  => trim($req->submod_code),
            'submod_name'  => trim($req->submod_name),
        ]);

        return response()->json([
            'data'      => $data,
            'status'    => true,
            'message'   => 'New record has been saved.'
        ], 200);
    }

    public function save(Request $req)
    {
        $row = Sub_module::find($req->submod_id);

        $row->mod_id       = $req->mod_id;
        $row->submod_code  = trim($req->submod_code);
        $row->submod_name  = trim($req->submod_name);

        $row->save();

        return response()->json([
            'data'      => $row,
            'status'    => true,
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function destroy(Request $req)
    {
        $row = Sub_module::find($req->submod_id);

        if ($row->permission()->count() > 0) {
            return response()->json([
                'status'    => false,
                'message'   => 'This record can not be deleted because it is still being in used on Permission.'
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
}
