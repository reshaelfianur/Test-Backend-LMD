<?php

namespace App\Http\Controllers\Api\TaskManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task as mTask;

class Task extends Controller
{
    public function index(Request $req)
    {
        $args = [];

        if (!empty($req->created_by)) {
            $args += ['tasks.created_by' => $req->created_by];
        }

        $get = mTask::fetch($args);

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
                    stristr($col->task_title, $search) ||
                    stristr($col->task_description, $search));
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
        if ($req->task_id == 'undefined') {
            $duplicate = mTask::orWhere([
                'task_title'            => $req->task_title,
                'task_description'      => $req->task_description,
            ])
                ->where('user_id', $req->user_id)
                ->get();
        } else {
            $duplicate = mTask::orWhere([
                'task_title'            => $req->task_title,
                'task_description'      => $req->task_description,
            ])
                ->where('user_id', $req->user_id)
                ->where('task_id', '<>', $req->task_id)->get();
        }

        $response = [];

        if ($duplicate->count() > 0) {
            $duplicate = $duplicate->first();

            if ($duplicate->task_title == $req->task_title) {
                $message = 'Task Title has already exists.';
            } elseif ($duplicate->task_description == $req->task_description) {
                $message = 'Task Description has already exists.';
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
        $data = mTask::create([
            'user_id'                   => $req->user_id,
            'task_title'                => trim($req->task_title),
            'task_description'          => trim($req->task_description),
            'task_status'               => $req->task_status,
            'task_hours'                => $req->task_hours,
            'task_planned_start_date'   => $req->task_planned_start_date != 'null' ? $req->task_planned_start_date : null,
            'task_planned_end_date'     => $req->task_planned_end_date != 'null' ? $req->task_planned_end_date : null,
            'task_actual_start_date'    => $req->task_actual_start_date != 'null' ? $req->task_actual_start_date : null,
            'task_actual_end_date'      => $req->task_actual_end_date != 'null' ? $req->task_actual_end_date : null,
            'task_notes'                => trim($req->task_notes),
            'created_by'                => $req->created_by,
        ]);

        return response()->json([
            'data'      => $data,
            'status'    => true,
            'message'   => 'New record has been saved.'
        ], 200);
    }

    public function save(Request $req)
    {
        $row = mTask::find($req->task_id);

        $row->user_id                   = $req->user_id;
        $row->task_title                = trim($req->task_title);
        $row->task_description          = trim($req->task_description);
        $row->task_status               = $req->task_status;
        $row->task_hours                = $req->task_hours;
        $row->task_planned_start_date   = $req->task_planned_start_date != 'null' ? $req->task_planned_start_date : null;
        $row->task_planned_end_date     = $req->task_planned_end_date != 'null' ? $req->task_planned_end_date : null;
        $row->task_actual_start_date    = $req->task_actual_start_date != 'null' ? $req->task_actual_start_date : null;
        $row->task_actual_end_date      = $req->task_actual_end_date != 'null' ? $req->task_actual_end_date : null;
        $row->task_notes                = trim($req->task_notes);
        $row->updated_by                = $req->updated_by;

        $row->save();

        return response()->json([
            'data'      => $row,
            'status'    => true,
            'message'   => 'Record has been successfully modified.'
        ], 200);
    }

    public function destroy(Request $req)
    {
        $row = mTask::find($req->task_id);

        $row->deleted_by = $req->deleted_by;

        $row->save();
        $row->delete();

        return response()->json([
            'data'      => $row,
            'status'    => true,
            'message'   => 'Record has been successfully deleted.'
        ], 200);
    }
}
