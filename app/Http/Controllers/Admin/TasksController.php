<?php

namespace App\Http\Controllers\Admin;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTasksRequest;
use App\Http\Requests\Admin\UpdateTasksRequest;
use App\Http\Controllers\Traits\FileUploadTrait;


use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Notification;

class TasksController extends Controller
{
    use FileUploadTrait;
    
    public function __construct() {
        $this->middleware('plugin:task');
    }

    /**
     * Display a listing of Task.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {
        if (! Gate::allows('task_access')) {
            return prepareBlockUserMessage();
        }

         if (request()->ajax()) {
            $query = Task::query();
           
            
            $query->with("status");
            $query->with("tag");
            $query->with("user");
          
            $template = 'actionsTemplate';
           
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('task_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'tasks.id',
                'tasks.name',
                'tasks.description',
                'tasks.attachment',
                'tasks.due_date',
                'tasks.status_id',
                'tasks.user_id',
                'tasks.start_date',
                
            ]);
            

            // Custom Filters Start.
            $query->when(request('date_filter', false), function ($q, $date_filter) {
                $parts = explode(' - ' , $date_filter);
                $date_type = request('date_type');
                $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
                $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');
                if ( ! empty( $date_type ) ) {
                    if ( in_array($date_type, array( 'created_at') ) ) {
                        return $q->where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to);
                    } else {
                        return $q->whereBetween($date_type, [$date_from, $date_to]);
                    }
                }
            });
            $query->when(request('status', false), function ($q, $status_id) {                
                return $q->where('status_id',  $status_id );                
            });
            $query->when(request('employee', false), function ($q, $employee) { 
                return $q->where('user_id', $employee);
            });
            /// Custom Filters End.

            if ( ! empty( $type ) && 'status' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('tasks.status_id', $type_id);
                });
            }            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'task_';
                $routeKey = 'admin.tasks';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->editColumn('attachment', function ($row) {
                if($row->attachment) { 
                    return '<a href="'. asset(env('UPLOAD_PATH').'/' . $row->attachment) .'" target="_blank"><img src="'. asset(env('UPLOAD_PATH').'/thumb/' . $row->attachment) .'"/></a>'; 
                };
            });

            $table->editColumn('user.name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->editColumn('due_date', function ($row) {
                return $row->due_date ? digiDate( $row->due_date ) : '';
            });

            $table->editColumn('status_id', function ($row) {
                return $row->status ? $row->status->name : '';
            });
            
            $table->editColumn('start_date', function ($row) {
                return $row->start_date ? digiDate( $row->start_date ) : '';
            });


            $table->rawColumns(['actions','massDelete', 'attachment', 'user.name', 'status.name']);
            return $table->make(true);
        }

        return view('admin.tasks.index');
    }



    /**
     * Show the form for creating new Task.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('task_create')) {
            return prepareBlockUserMessage();
        }
        
        $statuses = \App\TaskStatus::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $tags = \App\TaskTag::get()->pluck('name', 'id');

        $users = \App\User:: whereHas("role",
            function ($query) {
                $query->where('id', getRoleIdSlug( ROLE_EMPLOYEE ));
            })->get()->pluck('name', 'id');

        return view('admin.tasks.create', compact('statuses', 'tags', 'users'));
        }

    /**
     * Store a newly created Task in storage.
     *
     * @param  \App\Http\Requests\StoreTasksRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTasksRequest $request)
    {

dd($request);
        if (! Gate::allows('task_create')) {
            return prepareBlockUserMessage();
        }
        //dd(getCurrentDateFormat());
        $date_set = getCurrentDateFormat();

        $additional = array(           
        'start_date' => ! empty( $request->start_date ) ? Carbon::createFromFormat($date_set, $request->start_date)->format('Y-m-d') : NULL,
        'due_date' => ! empty( $request->due_date ) ? Carbon::createFromFormat($date_set, $request->due_date)->format('Y-m-d') : NULL,
        );  
        $request->request->add( $additional );
        if ( ! isDemo() ) {
         $request = $this->saveFiles($request);
        }

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $task = Task::create($request->all());

        $task->tag()->sync(array_filter((array)$request->input('tag')));


        $user = $task->user()->first();
        if ( $user ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'client_name' => $user->name,
                'name' => $task->name,
                'description' => $task->description,
                'start_date' => digiDate( $task->start_date),
                'due_date' => digiDate( $task->due_date),
                'status_id' => $task->status_id,
                'user_id' => $task->user_id,

                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),                
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'site_url' => env('APP_URL'),
            );

            if ( $task->status->name ) {
                $templatedata['status_id'] = $task->status->name;
            }
            
            if ( $task->user->name ) {
                $templatedata['user_id'] = $task->user->name;
            }
            
            
            $data = [
                "action" => "Created",
                "crud_name" => "User",
                'template' => 'task-assigned',
                'model' => 'App\Task',
                'data' => $templatedata,
            ];
            $user->notify(new QA_EmailNotification($data));
        }

        flashMessage( 'success', 'create' );
        return redirect()->route('admin.tasks.index');
    }


    /**
     * Show the form for editing Task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('task_edit')) {
            return prepareBlockUserMessage();
        }
        
        $statuses = \App\TaskStatus::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $tags = \App\TaskTag::get()->pluck('name', 'id');

        $users = \App\User::whereHas("role",
            function ($query) {
                $query->where('id', getRoleIdSlug( ROLE_EMPLOYEE ));
            })->get()->pluck('name', 'id');

        $task = Task::findOrFail($id);

        return view('admin.tasks.edit', compact('task', 'statuses', 'tags', 'users'));
    }

    /**
     * Update Task in storage.
     *
     * @param  \App\Http\Requests\UpdateTasksRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTasksRequest $request, $id)
    {

        if (! Gate::allows('task_edit')) {
            return prepareBlockUserMessage();
        }

         $additional = array(           
            'start_date' => ! empty( $request->start_date ) ? Carbon::createFromFormat(config('app.date_format'), $request->start_date)->format('Y-m-d') : NULL,
            'due_date' => ! empty( $request->due_date ) ? Carbon::createFromFormat(config('app.date_format'), $request->due_date)->format('Y-m-d') : NULL,
        );  

        $request->request->add( $additional );  
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);  
       }
        $task = Task::findOrFail($id);

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $task->update($request->all());
        $task->tag()->sync(array_filter((array)$request->input('tag')));


        $user = $task->user()->first();
        if ( $user ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'client_name' => $user->name,
                'name' => $task->name,
                'description' => $task->description,
                'due_date' => digiDate( $task->due_date ),
                'start_date' => digiDate( $task->start_date ),
                'status_id' => $task->status_id,
                'user_id' => $task->user_id,                

                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),                
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'site_url' => env('APP_URL'),
            );

            if ( $task->status->name ) {
                $templatedata['status_id'] = $task->status->name;
            }
            
            if ( $task->user->name ) {
                $templatedata['user_id'] = $task->user->name;
            }
            
            
            $data = [
                "action" => "Created",
                "crud_name" => "User",
                'template' => 'task-assigned',
                'model' => 'App\Task',
                'attachment' => asset(env('UPLOAD_PATH').'/' . $task->attachment),
                'data' => $templatedata,
            ];
            $user->notify(new QA_EmailNotification($data));
        }

        flashMessage( 'success', 'update' );
        return redirect()->route('admin.tasks.index');
    }


    /**
     * Display Task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('task_view')) {
            return prepareBlockUserMessage();
        }
        $task = Task::findOrFail($id);

        return view('admin.tasks.show', compact('task'));
    }


    /**
     * Remove Task from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('task_delete')) {
            return prepareBlockUserMessage();
        }
        $task = Task::findOrFail($id);
        $task->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.tasks.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Task at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('task_delete')) {
            return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = Task::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}

