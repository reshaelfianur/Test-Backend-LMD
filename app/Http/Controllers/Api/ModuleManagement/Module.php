<?php

namespace App\Http\Controllers\Api\Entity\ModuleManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module as mModule;

class Module extends Controller
{
    public function index(Request $req)
    {
        $get = mModule::all();

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
                return (stristr($col->mod_code, $search) ||
                    stristr($col->mod_name, $search));
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
        if ($req->mod_id == 'undefined') {
            $duplicate = mModule::orWhere([
                'mod_code'  => $req->mod_code,
                'mod_name'  => $req->mod_name
            ])
                ->get();
        } else {
            $duplicate = mModule::orWhere([
                'mod_code'  => $req->mod_code,
                'mod_name'  => $req->mod_name
            ])
                ->where('mod_id', '<>', $req->mod_id)
                ->get();
        }

        $response = [];

        if ($duplicate->count() > 0) {
            $duplicate = $duplicate->first();

            if ($duplicate->mod_code == $req->mod_code) {
                $message = 'Module Code has already exists.';
            } elseif ($duplicate->mod_name == $req->mod_name) {
                $message = 'Module Name has already exists.';
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
        $data = mModule::create([
            'mod_code'  => trim($req->mod_code),
            'mod_name'  => trim($req->mod_name),
        ]);

        return response()->json([
            'data'      => $data,
            'status'    => true,
            'message'   => 'New record has been saved.'
        ], 200);
    }

    public function save(Request $req)
    {
        $row = mModule::find($req->mod_id);

        $row->mod_code      = trim($req->mod_code);
        $row->mod_name      = trim($req->mod_name);
        $row->mod_status    = $req->mod_status;

        $row->save();

        return response()->json([
            'data'      => $row,
            'status'    => true,
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function destroy(Request $req)
    {
        $row = mModule::find($req->mod_id);

        if ($row->subModule()->count() > 0) {
            return response()->json([
                'status'    => false,
                'message'   => 'This record can not be deleted because it is still being in used on Sub Module.'
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
