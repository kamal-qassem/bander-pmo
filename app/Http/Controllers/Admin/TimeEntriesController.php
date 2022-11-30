<?php

namespace App\Http\Controllers\Admin;

use App\ClientProject;
use App\TimeEntry;
use App\ProjectTask;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTimeEntriesRequest;
use App\Http\Requests\Admin\UpdateTimeEntriesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class TimeEntriesController extends Controller
{
    
    /**
     * Display a listing of TimeEntry.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $project_id = '' )
    {
        if (! Gate::allows('time_entry_access')) {
            return prepareBlockUserMessage();
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        if (request()->ajax()) {
            $query = TimeEntry::query();
            $query->with("project");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('time_entry_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'time_entries.id',
                'time_entries.project_id',
                'time_entries.start_date',
                'time_entries.end_date',
                'time_entries.description',
                'time_entries.task_id',
                'time_entries.completed_by_id',
            ]);
            $query->where('project_id', '=', $project_id );
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'time_entry_';
                $routeKey = 'admin.time_entries';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('project.title', function ($row) {
                return $row->project ? $row->project->title : '';
            });
            $table->editColumn('start_date', function ($row) {
                return $row->start_date ? digiDate( $row->start_date, true ) : '';
            });
            $table->editColumn('end_date', function ($row) {
                return $row->end_date ? digiDate( $row->end_date, true ) : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->editColumn('task.name', function ($row) {
                $name = $row->task_id ? $row->task->name : '';
                if ( ! empty( $name ) ) {
                    
                    $name = '<a href="'.route('admin.project_tasks.show', [ 'project_id' => $row->project_id, 'id' => $row->task_id] ).'">'.$name.'</a>';
                    if ( $row->end_date == null ) {
                        $name .= '<i class="fa fa-clock-o fa-fw"></i>';
                    }

                    $bradded = false;
                    if ( 'yes' === $row->task->billable ) {
                        if ( $row->task->billed === 'yes' ) {
                            $name .= '<br/><span class="label mtop5 label-warning inline-block">'.trans('global.time-entries.billed').'</span>';
                        } else {
                            $name .= '<br/><span class="label mtop5 label-warning inline-block">'.trans('global.time-entries.not-billed').'</span>';
                        }
                        $bradded = true;
                    }
                    if ( ! empty( $row->task->status ) ) {                      
                        $style = '';
                        $color = ($row->task->task_status->color) ? $row->task->task_status->color : '';
                        if ( ! empty( $color ) ) {
                            $style = ' style="background-color:'.$color.' !important"';
                        }
                        if ( ! $bradded ) {
                           $name .= '<br/>'; 
                        } else {
                            $name .= '&nbsp;'; 
                        }
                        $name .= '<span class="label mtop5 label-warning inline-block"'.$style.'>'.$row->task->task_status->title.'</span>';
                    }

                    if ( $row->end_date == NULL ) {
                        $name .= '<p><a href="'.route('admin.project_tasks.stop-timer', [ 'id' => $row->task_id, 'timer_id' => $row->id] ).'">'.trans('global.client-projects.stop-timer').'</a></p>';
                    }
                }
                return $name;
            });
            $table->editColumn('completed_by.name', function ($row) {
                $name = $row->completed_by_id ? $row->completed_by->name : '';
                if ( ! empty( $name ) ) {
                    $contact = $row->completed_by->contact_reference;
                    if ( $contact && $contact->thumbnail ) {
                        $name = '<img class="thumbnail-small" src="'.asset(env('UPLOAD_PATH').'/thumb/'.$contact->thumbnail).'" data-toggle="tooltip" data-original-title="'.$name.'">&nbsp;' . $name;
                    }
                    $name = '<a href="'.route('admin.users.show', $row->completed_by_id).'">'.$name.'</a>';
                }                
                return $name;
            });

            $table->rawColumns(['actions','massDelete', 'task.name', 'completed_by.name']);

            return $table->make(true);
        }

        return view('admin.time_entries.index', compact('project'));
    }

    /**
     * Show the form for creating new TimeEntry.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $project_id = '' )
    {
        if (! Gate::allows('time_entry_create')) {
            return prepareBlockUserMessage();
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $projects = \App\ClientProject::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $tasks = ProjectTask::where('project_id', $project->id)->where('status', '!=', TASK_STATUS_COMPLETED)->where('billed', '=', 'no')->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $assignees = $project->assigned_to()->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '')->toArray();

        return view('admin.time_entries.create', compact('projects', 'project', 'tasks', 'assignees' ));
    }

    /**
     * Store a newly created TimeEntry in storage.
     *
     * @param  \App\Http\Requests\StoreTimeEntriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimeEntriesRequest $request, $project_id = '')
    {
        if (! Gate::allows('time_entry_create')) {
            return prepareBlockUserMessage();
        }
        
        $date_set = getCurrentDateFormat();

        $additional = array(
            'start_date' => Carbon::createFromFormat($date_set . ' H:i:s', $request->start_date)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::createFromFormat($date_set . ' H:i:s', $request->end_date)->format('Y-m-d H:i:s'),
        );  
        $request->merge($additional);

        $hourly_rate = $request->hourly_rate;
        if ( empty( $hourly_rate ) ) {
            $hourly_rate = Auth::User()->hourly_rate;
            if ( empty( $hourly_rate ) ) {
                $project = \App\ClientProject::find( $project_id );
                if ( $project ) {
                    $hourly_rate = $project->hourly_rate;
                }
            }
        }
        $addtional = array(
            'hourly_rate' => $hourly_rate,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
        
        $time_entry = TimeEntry::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.time_entries.index', $time_entry->project_id);
    }


    /**
     * Show the form for editing TimeEntry.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $id)
    {
        if (! Gate::allows('time_entry_edit')) {
            return prepareBlockUserMessage();
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $projects = \App\ClientProject::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

        $time_entry = TimeEntry::findOrFail($id);

        $date_set = getCurrentDateFormat();
        $time_entry->start_date = Carbon::createFromFormat('Y-m-d H:i:s', $time_entry->start_date)->format($date_set . ' H:i:s');
        if( ! empty ( $time_entry->end_date ) ){
        $time_entry->end_date = Carbon::createFromFormat('Y-m-d H:i:s', $time_entry->end_date)->format($date_set . ' H:i:s');
       }
        
        $tasks = ProjectTask::where('project_id', $project->id)->where('status', '!=', TASK_STATUS_COMPLETED)->where('billed', '=', 'no')->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $assignees = $project->assigned_to()->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '')->toArray();

        return view('admin.time_entries.edit', compact('time_entry', 'project', 'projects', 'tasks', 'assignees'));
    }

    /**
     * Update TimeEntry in storage.
     *
     * @param  \App\Http\Requests\UpdateTimeEntriesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimeEntriesRequest $request, $project_id, $id)
    {
        if (! Gate::allows('time_entry_edit')) {
            return prepareBlockUserMessage();
        }
        $time_entry = TimeEntry::findOrFail($id);

        $date_set = getCurrentDateFormat();

        $additional = array(
            'start_date' => Carbon::createFromFormat($date_set . ' H:i:s', $request->start_date)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::createFromFormat($date_set . ' H:i:s', $request->end_date)->format('Y-m-d H:i:s'),
        );  
        $request->merge($additional);
        $time_entry->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.time_entries.index', $time_entry->project_id);
    }


    /**
     * Display TimeEntry.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($project_id, $id)
    {
        if (! Gate::allows('time_entry_view')) {
            return prepareBlockUserMessage();
        }
        $time_entry = TimeEntry::findOrFail($id);

        return view('admin.time_entries.show', compact('time_entry'));
    }


    /**
     * Remove TimeEntry from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $project_id, $id)
    {
        if (! Gate::allows('time_entry_delete')) {
            return prepareBlockUserMessage();
        }
        $time_entry = TimeEntry::findOrFail($id);
        $project_id = $time_entry->project_id;
        $time_entry->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.time_entries.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
      }
    }

    /**
     * Delete all selected TimeEntry at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('time_entry_delete')) {
            return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = TimeEntry::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore TimeEntry from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($project_id, $id)
    {
        if (! Gate::allows('time_entry_delete')) {
            return prepareBlockUserMessage();
        }
        $time_entry = TimeEntry::onlyTrashed()->findOrFail($id);
        $project_id = $time_entry->project_id;
        $time_entry->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete TimeEntry from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($project_id, $id)
    {
        if (! Gate::allows('time_entry_delete')) {
            return prepareBlockUserMessage();
        }
        $time_entry = TimeEntry::onlyTrashed()->findOrFail($id);
        $project_id = $time_entry->project_id;
        $time_entry->forceDelete();

        return back();
    }
}
