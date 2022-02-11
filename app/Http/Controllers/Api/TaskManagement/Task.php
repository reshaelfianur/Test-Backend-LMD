<?php

namespace App\Http\Controllers\Api\TaskManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task as mTask;

use App\Traits\DynamicConfig;

class Task extends Controller
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
        $in = [];

        if (!empty($req->input('comp_ids'))) {
            $in = (array) json_decode(base64_decode($req->input('comp_ids')));
        }

        $task = new mTask;
        $task->setConnection($this->_compCode);

        $get = $task->fetch(['task_is_default' => 2], $in);

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
                return (stristr($col->comp_name, $search) ||
                    stristr($col->task_code, $search) ||
                    stristr($col->task_name, $search) ||
                    stristr($col->task_level, $search));
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
        $task = new mTask;
        $task->setConnection($this->_compCode);

        if ($req->input('task_id') == 'undefined') {
            $duplicate = $task->orWhere([
                'task_code'     => $req->input('task_code'),
                'task_name'     => $req->input('task_name')
            ])
                ->where('comp_id', $req->input('comp_id'))
                ->get();
        } else {
            $duplicate = $task->orWhere([
                'task_code'     => $req->input('task_code'),
                'task_name'     => $req->input('task_name')
            ])
                ->where('comp_id', $req->input('comp_id'))
                ->where('task_id', '<>', $req->input('task_id'))->get();
        }

        $response = [];

        if ($duplicate->count() > 0) {
            $duplicate = $duplicate->first();

            if ($duplicate->task_code == $req->input('task_code')) {
                $message = 'Task Code has already exists';
            } elseif ($duplicate->task_name == $req->input('task_name')) {
                $message = 'Task Name has already exists';
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
        $task = new mTask;
        $task->setConnection($this->_compCode);

        $data = $task->create([
            'comp_id'           => $req->input('comp_id'),
            'task_code'        => trim($req->input('task_code')),
            'task_name'        => trim($req->input('task_name')),
            'task_is_default'  => 2,
            'task_level'       => trim($req->input('task_level')),
        ]);

        return response()->json([
            'data'      => $data,
            'status'    => 'success',
            'message'   => 'New record has been saved.'
        ], 200);
    }

    public function save(Request $req)
    {
        $task = new mTask;
        $task->setConnection($this->_compCode);

        $row = $task->find($req->input('task_id'));

        $row->comp_id       = trim($req->input('comp_id'));
        $row->task_code    = trim($req->input('task_code'));
        $row->task_name    = trim($req->input('task_name'));
        $row->task_level   = trim($req->input('task_level'));

        $row->save();

        return response()->json([
            'data'      => $row,
            'status'    => 'success',
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function destroy(Request $req)
    {
        $task = new mTask;
        $task->setConnection($this->_compCode);

        $row = $task->find($req->input('task_id'));

        if ($row->employee()->count() > 0) {
            return response()->json([
                'status'    => 'failed',
                'message'   => 'This record can not be deleted because it is still being in used on Employee Data'
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
