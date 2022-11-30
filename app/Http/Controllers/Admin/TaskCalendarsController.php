<?php
namespace App\Http\Controllers\Admin;

use App\Task;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class TaskCalendarsController extends Controller
{
    public function index()
    {
        $events = Task::whereNotNull('start_date')->whereNotNull('due_date')->latest()->limit(100)->get();
        
        $statuses = \App\TaskStatus::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $users = \App\User::whereHas("role",
            function ($query) {
                $query->where('id', getRoleIdSlug( ROLE_EMPLOYEE ));
            })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        return view('admin.task_calendars.index', compact('events', 'statuses', 'users'));
    }

    public function tasksStatus()
    {
        $events = Task::whereNotNull('status_id')->get();
        $statuses = \App\TaskStatus::get();
        $users = \App\User::whereHas("role",
            function ($query) {
                $query->where('id', getRoleIdSlug( ROLE_EMPLOYEE ));
            })->get()->pluck('name', 'id');
        return view('admin.task_calendars.tasksstatuses', compact('events', 'statuses', 'users'));
    }

    public function addTask() {
    	if ( request()->ajax() ) {
    		
    		$id = request('record_id');
    		$name = request('name');
            $description = request('description');
            $status_id = request('status_id');
            

            $start = request('start');


            $start = Carbon::parse( $start )->format('Y-m-d');

            $end = request('end');
            $end = Carbon::parse( $end )->format('Y-m-d');
            $user_id = request('user_id');
            
            $data = array(
            	'name' => $name,
            	'description' => $description,
            	'start_date' => $start,
            	'due_date' => $end,
            	'status_id' => $status_id,
            
            );
            if ( $user_id ) {
                $data['user_id'] = $user_id;
            }
            $message = trans('custom.tasks.messages.task-created');
            if ( ! empty( $id ) ) {
            	$task = \App\Task::find($id);
               
            	if ( $task ) {
            		
                    $task->update( $data );
            		$message = trans('custom.tasks.messages.task-updated');
            	} else {
            		$task = \App\Task::create($data);
            	}
            } else {
            	$task = \App\Task::create($data);
        	}

            return response()->json([
			    'status' => 'success',
			    'message' => $message,
			]);
    	}
    }

    public function deleteTask() {

    	if ( request()->ajax() ) {
	    	$message = trans('custom.messages.not_allowed');

	    	if (! Gate::allows('task_delete')) {
	            return response()->json([
				    'status' => 'danger',
				    'message' => $message,
				]);
	        }
	        $id = request('record_id');

	        $task = Task::findOrFail($id);
	        $task->delete();

	        $message = trans('custom.tasks.messages.task-deleted');
	        return response()->json([
				    'status' => 'success',
				    'message' => $message,
				]);
	    }
    }

    public function updateTaskStatus() {
        if ( request()->ajax() ) {
            $item_id = request('item_id');
            $soucrce = request('soucrce');
            $target = request('target');

            $task = Task::find($item_id);

            $message = trans('global.tasks.task-status-updated');
            $status = 'success';
            if ( $task ) {
                $task->status_id = $target;
                $task->save();
            } else {
                $message = trans('custom.messages.not_found');
                $status = 'danger';
            }

            return response()->json([
                    'status' => $status,
                    'message' => $message,
                ]);
        }
    }
}
