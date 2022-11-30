<?php

namespace App\Http\Controllers\Admin;

use App\ClientProject;
use App\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectTasksRequest;
use App\Http\Requests\Admin\UpdateProjectTasksRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\DynamicOptions\Entities\DynamicOption;
class ProjectTasksController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of ProjectTask.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $project_id = '' )
    {
        if (! Gate::allows('project_task_access')) {
            return prepareBlockUserMessage();
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        
        if (request()->ajax()) {
            $query = ProjectTask::query();
            $query->with("recurring");
            $query->with("project");
            $query->with("mile_stone");
            $query->with("created_by");
            $query->with("assigned_to");
            
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
                if (! Gate::allows('project_task_delete')) {
                    return prepareBlockUserMessage();
                }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'project_tasks.id',
                'project_tasks.name',
                'project_tasks.description',
                'project_tasks.priority',
                'project_tasks.startdate',
                'project_tasks.duedate',
                'project_tasks.datefinished',
                'project_tasks.status',
                'project_tasks.recurring_id',
                'project_tasks.recurring_type',
                'project_tasks.recurring_value',
                'project_tasks.cycles',
                'project_tasks.total_cycles',
                'project_tasks.last_recurring_date',
                'project_tasks.is_public',
                'project_tasks.billable',
                'project_tasks.billed',
                'project_tasks.project_id',
                'project_tasks.hourly_rate',
                'project_tasks.milestone',
                'project_tasks.kanban_order',
                'project_tasks.milestone_order',
                'project_tasks.visible_to_client',
                'project_tasks.deadline_notified',
                
                'project_tasks.created_by_id',
            ]);
            $query->where('project_id', '=', $project_id );

            if ( ! isAdmin() ) {
                if ( isEmployee() ) {
                    $query->whereHas("assigned_to",
                    function ($query) {
                        $query->where('id', Auth::id());
                    });
                }
                
                if ( isClient() ) {
                    $query->where("visible_to_client", 'yes');
                }
            }
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'project_task_';
                $routeKey = 'admin.project_tasks';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                $name = $row->name ? $row->name : '';

                if ( isAdmin() ) {
                    $timer = \App\TimeEntry::where('task_id', '=', $row->id)->whereNull('end_date')->first();
                } else {
                    $timer = \App\TimeEntry::where('task_id', '=', $row->id)->whereNull('end_date')->where('user_id', Auth::id())->first();
                }
                if ( $timer ) {
                    $name .= '<i class="fa fa-clock-o fa-fw"></i>';
                }
                if ( ! empty( $row->milestone ) ) {
                    $name .= '<p class="badge">'.$row->mile_stone->name.'</p>';
                }
                $assigned_to = array();
               
                if ( ! empty( $row->assigned_to ) ) {
                    $assigned_to = $row->assigned_to->pluck('id')->toArray();
                }
               

                if ( ! in_array(Auth::id(), $assigned_to ) || $row->status == STATUS_COMPLETED ) {
                    $name .= '<p><span data-toggle="tooltip" data-title="'.trans('global.client-projects.start-timer-info').'" style="opacity:0.6;cursor: not-allowed;" data-original-title="" title="">
        <a href="javascript:void(0);" class="text-dark disabled tasks-table-start-timer">'.trans('global.client-projects.start-timer').'</a>
        </span></p>';
                } else {
                    if ( $timer ) {
                        $name .= '<p><a href="'.route('admin.project_tasks.stop-timer', [ 'id' => $row->id, 'timer_id' => $timer->id] ).'">'.trans('global.client-projects.stop-timer').'</a></p>';
                    } else {
                        $name .= '<p><a href="'.route('admin.project_tasks.start-timer', $row->id).'">'.trans('global.client-projects.start-timer').'</a></p>';
                    }
                }
                return $name;
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('priority.title', function ($row) {
                return $row->priority ? $row->task_priority->title : '';
            });
            $table->editColumn('startdate', function ($row) {
                return $row->startdate ? digiDate( $row->startdate ) : '';
            });
            $table->editColumn('duedate', function ($row) {
                return $row->duedate ? digiDate( $row->duedate ) : '';
            });
            $table->editColumn('datefinished', function ($row) {
                return $row->datefinished ? $row->datefinished : '';
            });
            $table->editColumn('status.title', function ($row) {
                $status = $row->status ? $row->task_status->title : '';
                if ( 'yes' === $row->billable && 'yes' === $row->billed ) {
                    $status .= '<p><span class="badge">'.trans('global.project-tasks.fields.billed').'</span></p>';
                }
                return $status;
            });
            $table->editColumn('recurring.title', function ($row) {
                return $row->recurring ? $row->recurring->title : '';
            });
            $table->editColumn('recurring_type', function ($row) {
                return $row->recurring_type ? $row->recurring_type : '';
            });
            $table->editColumn('recurring_value', function ($row) {
                return $row->recurring_value ? $row->recurring_value : '';
            });
            $table->editColumn('cycles', function ($row) {
                return $row->cycles ? $row->cycles : '';
            });
            $table->editColumn('total_cycles', function ($row) {
                return $row->total_cycles ? $row->total_cycles : '';
            });
            $table->editColumn('last_recurring_date', function ($row) {
                return $row->last_recurring_date ? $row->last_recurring_date : '';
            });
            $table->editColumn('is_public', function ($row) {
                return $row->is_public ? $row->is_public : '';
            });
            $table->editColumn('billable', function ($row) {
                return $row->billable ? $row->billable : '';
            });
            $table->editColumn('billed', function ($row) {
                return $row->billed ? $row->billed : '';
            });
            $table->editColumn('project.title', function ($row) {
                return $row->project ? $row->project->title : '';
            });
            $table->editColumn('hourly_rate', function ($row) {
                return $row->hourly_rate ? $row->hourly_rate : '';
            });
            $table->editColumn('milestone', function ($row) {
                return $row->milestone ? $row->milestone : '';
            });
            $table->editColumn('kanban_order', function ($row) {
                return $row->kanban_order ? $row->kanban_order : '';
            });
            $table->editColumn('milestone_order', function ($row) {
                return $row->milestone_order ? $row->milestone_order : '';
            });
            $table->editColumn('visible_to_client', function ($row) {
                return $row->visible_to_client ? $row->visible_to_client : '';
            });
            $table->editColumn('deadline_notified', function ($row) {
                return $row->deadline_notified ? $row->deadline_notified : '';
            });
            $table->editColumn('mile_stone.name', function ($row) {
                return $row->mile_stone ? $row->mile_stone->name : '';
            });
            $table->editColumn('attachments', function ($row) {
                $build  = '';
                foreach ($row->getMedia('attachments') as $media) {
                    $build .= '<p class="form-group"><a href="' . route('admin.home.media-download', $media->id) . '" >' . $media->name . '</a></p>';
                }
                
                return $build;
            });
            $table->editColumn('created_by.name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions','massDelete','attachments', 'name', 'status.title']);

            return $table->make(true);
        }

        return view('admin.project_tasks.index', compact('project'));
    }

    public function startTimer( $id ) {
  
        $task = ProjectTask::find( $id );

        if ( ! $task ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        if ( $task->status == PROJECT_TASK_STATUS_NOTSTARTED ) { // If the task is "Not Started" status, let us update it to "in progress"
            $task->status = PROJECT_TASK_STATUS_PROGRESS;
            $task->save();
        }
        $hourly_rate = $task->hourly_rate;
        if ( empty( $hourly_rate ) ) {
            $hourly_rate = Auth::User()->hourly_rate;
            if ( empty( $hourly_rate ) ) {
                $hourly_rate = $task->project->hourly_rate;
            }
        }
        $data = array(
            'task_id' => $id,
            'start_date' => date('Y-m-d H:i:s'),
            'project_id' => $task->project_id,
            'user_id' => Auth::id(),
            'hourly_rate' => $hourly_rate,
            'description' => $task->description,
        );
        \App\TimeEntry::create( $data );
        
        flashMessage('success', 'create', trans('global.client-projects.timer-started'));
        return redirect()->route('admin.project_tasks.index', $task->project_id);
    }

    public function stopTimer( $id, $timer_id ) {
  
        $task = ProjectTask::find( $id );

        if ( ! $task ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $timer = \App\TimeEntry::where('id', '=', $timer_id)->first();
        if ( $timer ) {
            $timer->end_date = date('Y-m-d H:i:s');
            $timer->completed_by_id = Auth::id();
            $timer->save();
        }

        
        
        flashMessage('success', 'create', trans('global.client-projects.timer-stopped'));
        return redirect()->route('admin.project_tasks.index', $task->project_id);
    }

    /**
     * Show the form for creating new ProjectTask.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $project_id = '' )
    {
        if (! Gate::allows('project_task_create')) {
            return prepareBlockUserMessage();
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $priorities = DynamicOption::where('module', 'projecttasks')->where('type', 'priorities')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $statuses = DynamicOption::where('module', 'projecttasks')->where('type', 'taskstatus')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $recurrings = \App\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $projects = \App\ClientProject::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $mile_stones = \App\MileStone::where('project_id','=',$project->id)->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_recurring_type = ProjectTask::$enum_recurring_type;
        $enum_is_public = ProjectTask::$enum_is_public;
        $enum_billable = ProjectTask::$enum_billable;
        $enum_billed = ProjectTask::$enum_billed;
        $enum_visible_to_client = ProjectTask::$enum_visible_to_client;
        $enum_deadline_notified = ProjectTask::$enum_deadline_notified;
        
        $users = $project->assigned_to->pluck('name', 'id');
            
        return view('admin.project_tasks.create', compact('enum_recurring_type', 'enum_is_public', 'enum_billable', 'enum_billed', 'enum_visible_to_client', 'enum_deadline_notified', 'recurrings', 'projects', 'mile_stones', 'created_bies', 'priorities', 'statuses', 'project', 'users'));
    }

    /**
     * Store a newly created ProjectTask in storage.
     *
     * @param  \App\Http\Requests\StoreProjectTasksRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectTasksRequest $request, $project_id = '')
    {
        if (! Gate::allows('project_task_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $hourly_rate = $request->hourly_rate;
        if ( empty( $hourly_rate ) ) {
            $hourly_rate = Auth::User()->hourly_rate;
            if ( empty( $hourly_rate ) ) {
                $hourly_rate = $project->hourly_rate;
            }
        }
        $addtional = array(
            'project_id' => $project_id,
            'hourly_rate' => $hourly_rate,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

          $date_set = getCurrentDateFormat();

         $additional = array(           
            'startdate' => ! empty( $request->startdate ) ? Carbon::createFromFormat($date_set, $request->startdate)->format('Y-m-d') : NULL,
            'duedate' => ! empty( $request->duedate ) ? Carbon::createFromFormat($date_set, $request->duedate)->format('Y-m-d') : NULL,
            'datefinished' => ! empty( $request->datefinished ) ? Carbon::createFromFormat($date_set, $request->datefinished)->format('Y-m-d') : NULL,
        );  
        $request->request->add( $additional ); 

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_task = ProjectTask::create($request->all());

        $project_task->assigned_to()->sync(array_filter((array)$request->input('assigned_to')));


        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $project_task->id;
            $file->save();
        }

        flashMessage( 'success', 'create');
        return redirect()->route('admin.project_tasks.index', $project_task->project_id);
    }


    /**
     * Show the form for editing ProjectTask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $id)
    {
        if (! Gate::allows('project_task_edit')) {
            return prepareBlockUserMessage();
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $priorities = DynamicOption::where('module', 'projecttasks')->where('type', 'priorities')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $statuses = DynamicOption::where('module', 'projecttasks')->where('type', 'taskstatus')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $recurrings = \App\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $projects = \App\ClientProject::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $mile_stones = \App\MileStone::where('project_id','=',$project->id)->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_recurring_type = ProjectTask::$enum_recurring_type;
        $enum_is_public = ProjectTask::$enum_is_public;
        $enum_billable = ProjectTask::$enum_billable;
        $enum_billed = ProjectTask::$enum_billed;
        $enum_visible_to_client = ProjectTask::$enum_visible_to_client;
        $enum_deadline_notified = ProjectTask::$enum_deadline_notified;
        
        $users = $project->assigned_to->pluck('name', 'id');
            
        $project_task = ProjectTask::findOrFail($id);

        return view('admin.project_tasks.edit', compact('project_task', 'enum_recurring_type', 'enum_is_public', 'enum_billable', 'enum_billed', 'enum_visible_to_client', 'enum_deadline_notified', 'recurrings', 'projects', 'mile_stones', 'created_bies', 'priorities', 'statuses', 'project', 'users'));
    }

    /**
     * Update ProjectTask in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectTasksRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectTasksRequest $request, $project_id, $id)
    {
        if (! Gate::allows('project_task_edit')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $project_task = ProjectTask::findOrFail($id);

        $project = ClientProject::find( $project_id );

        $hourly_rate = $request->hourly_rate;
        if ( empty( $hourly_rate ) ) {
            $hourly_rate = Auth::User()->hourly_rate;
            if ( empty( $hourly_rate ) ) {
                $hourly_rate = $project->hourly_rate;
            }
        }
        $addtional = array(
            'project_id' => $project_id,
            'hourly_rate' => $hourly_rate,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

         $date_set = getCurrentDateFormat();

         $additional = array(           
            'startdate' => ! empty( $request->startdate ) ? Carbon::createFromFormat($date_set, $request->startdate)->format('Y-m-d') : NULL,
            'duedate' => ! empty( $request->duedate ) ? Carbon::createFromFormat($date_set, $request->duedate)->format('Y-m-d') : NULL,
            'datefinished' => ! empty( $request->datefinished ) ? Carbon::createFromFormat($date_set, $request->datefinished)->format('Y-m-d') : NULL,
        );  
        $request->request->add( $additional ); 

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_task->update($request->all());

        $project_task->assigned_to()->sync(array_filter((array)$request->input('assigned_to')));


        $media = [];
        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $project_task->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $project_task->updateMedia($media, 'attachments');

        flashMessage( 'success', 'update');
        return redirect()->route('admin.project_tasks.index', $project_task->project_id);
    }


    /**
     * Display ProjectTask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($project_id, $id)
    {
        if (! Gate::allows('project_task_view')) {
            return prepareBlockUserMessage();
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $project_task = ProjectTask::findOrFail($id);

        return view('admin.project_tasks.show', compact('project_task'));
    }


    /**
     * Remove ProjectTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $project_id, $id)
    {
        if (! Gate::allows('project_task_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_task = ProjectTask::findOrFail($id);
        $project_id = $project_task->project_id;
        $project_task->deletePreservingMedia();
        flashMessage( 'success', 'delete');
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.project_tasks.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected ProjectTask at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('project_task_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ProjectTask::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }
            flashMessage( 'success', 'deletes');
        }
    }


    /**
     * Restore ProjectTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($project_id, $id)
    {
        if (! Gate::allows('project_task_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_task = ProjectTask::onlyTrashed()->findOrFail($id);
        $project_id = $project_task->project_id;
        $project_task->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete ProjectTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($project_id, $id)
    {
        if (! Gate::allows('project_task_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_task = ProjectTask::onlyTrashed()->findOrFail($id);
        $project_id = $project_task->project_id;
        $project_task->forceDelete();

        flashMessage( 'success', 'delete');

        return back();
    }
}
