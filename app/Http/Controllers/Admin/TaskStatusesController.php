<?php

namespace App\Http\Controllers\Admin;

use App\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaskStatusesRequest;
use App\Http\Requests\Admin\UpdateTaskStatusesRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class TaskStatusesController extends Controller
{
    /**
     * Display a listing of TaskStatus.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('task_status_access')) {
            return prepareBlockUserMessage();
        }


                $task_statuses = TaskStatus::all()->sortByDesc('id');

        return view('admin.task_statuses.index', compact('task_statuses'));
    }

    /**
     * Show the form for creating new TaskStatus.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('task_status_create')) {
            return prepareBlockUserMessage();
        }

         

        $colors = array( 
            'panel-default' => trans('global.task-statuses.panel-default'), 
            'panel-primary' => trans('global.task-statuses.panel-primary'), 
            'panel-success' => trans('global.task-statuses.panel-success'), 
            'panel-info' => trans('global.task-statuses.panel-info'), 
            'panel-warning' => trans('global.task-statuses.panel-warning'), 
            'panel-danger' => trans('global.task-statuses.panel-danger'), 
        );
        return view('admin.task_statuses.create', compact('colors'));
    }

    /**
     * Store a newly created TaskStatus in storage.
     *
     * @param  \App\Http\Requests\StoreTaskStatusesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskStatusesRequest $request)
    {
        if (! Gate::allows('task_status_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $task_status = TaskStatus::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.task_statuses.index');
    }


    /**
     * Show the form for editing TaskStatus.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('task_status_edit')) {
            return prepareBlockUserMessage();
        }
        $task_status = TaskStatus::findOrFail($id);

        $colors = array( 
            'panel-default' => trans('global.task-statuses.panel-default'), 
            'panel-primary' => trans('global.task-statuses.panel-primary'), 
            'panel-success' => trans('global.task-statuses.panel-success'), 
            'panel-info' => trans('global.task-statuses.panel-info'), 
            'panel-warning' => trans('global.task-statuses.panel-warning'), 
            'panel-danger' => trans('global.task-statuses.panel-danger'), 
        );
        return view('admin.task_statuses.edit', compact('task_status', 'colors'));
    }

    /**
     * Update TaskStatus in storage.
     *
     * @param  \App\Http\Requests\UpdateTaskStatusesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskStatusesRequest $request, $id)
    {
        if (! Gate::allows('task_status_edit')) {
            return prepareBlockUserMessage();
        }
        $task_status = TaskStatus::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $task_status->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.task_statuses.index');
    }


    /**
     * Display TaskStatus.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('task_status_view')) {
            return prepareBlockUserMessage();
        }
       
        $task_status = TaskStatus::findOrFail($id);

        return view('admin.task_statuses.show', compact('task_status', 'list'));
    }


    /**
     * Remove TaskStatus from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('task_status_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $task_status = TaskStatus::findOrFail($id);
        $task_status->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.task_statuses.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected TaskStatus at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('task_status_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = TaskStatus::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
