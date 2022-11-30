<?php

namespace App\Http\Controllers\Admin;

use App\ClientProject;
use App\ProjectTask;
use App\MileStone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMileStonesRequest;
use App\Http\Requests\Admin\UpdateMileStonesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class MileStonesController extends Controller
{
    /**
     * Display a listing of MileStone.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $project_id = '' )
    {
        if (! Gate::allows('mile_stone_access')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        if (request()->ajax()) {
            $query = MileStone::query();
            $query->with("project");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('mile_stone_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'mile_stones.id',
                'mile_stones.name',
                'mile_stones.description',
                'mile_stones.description_visible_to_customer',
                'mile_stones.due_date',
                'mile_stones.project_id',
                'mile_stones.color',
                'mile_stones.milestone_order',
            ]);
            $query->where('project_id', '=', $project_id );
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'mile_stone_';
                $routeKey = 'admin.mile_stones';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                $name =  $row->name ? '<a href="'.route('admin.mile_stones.tasks', ['project_id' => $row->project_id, 'mile_stone_id' => $row->id]).'">' . $row->name . '</a>' : '';
                if ( ! empty( $row->color ) ) {
                    $name = '<span style="color:'.$row->color.'">'.$name.'</span>';
                }
                return $name;
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('description_visible_to_customer', function ($row) {
                return $row->description_visible_to_customer ? $row->description_visible_to_customer : '';
            });
            $table->editColumn('due_date', function ($row) {
                return $row->due_date ? digiDate($row->due_date) : '';
            });
            $table->editColumn('project.title', function ($row) {
                return $row->project ? $row->project->title : '';
            });
            $table->editColumn('color', function ($row) {
                return $row->color ? $row->color : '';
            });
            $table->editColumn('milestone_order', function ($row) {
                return $row->milestone_order ? $row->milestone_order : '';
            });

            $table->rawColumns(['actions','massDelete', 'name']);

            return $table->make(true);
        }

        return view('admin.mile_stones.index', compact('project'));
    }

    public function mileStoneTasks( $project_id = '', $mile_stone_id = '' ,$project_task_id = '')
    {
        if (! Gate::allows('project_task_access')) {
            return abort(401);
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
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('project_task_delete')) {
            return abort(401);
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
            if ( ! empty( $mile_stone_id ) ) {
                $query->where('milestone', '=', $mile_stone_id );
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
                return $row->name ? $row->name : '';
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
                return $row->datefinished ? digiDate( $row->datefinished ) : '';
            });
            $table->editColumn('status.title', function ($row) {
                return $row->status ? $row->task_status->title : '';
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
                    $build .= '<p class="form-group"><a href="' . $media->getUrl() . '" target="_blank">' . $media->name . '</a></p>';
                }
                
                return $build;
            });
            $table->editColumn('created_by.name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions','massDelete','attachments']);

            return $table->make(true);
        }


    return view('admin.mile_stones.mile-stone-tasks', compact('project', 'mile_stone_id','project_task'));
    }

    /**
     * Show the form for creating new MileStone.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $project_id = '' )
    {
        if (! Gate::allows('mile_stone_create')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $projects = \App\ClientProject::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_description_visible_to_customer = MileStone::$enum_description_visible_to_customer;
            
        return view('admin.mile_stones.create', compact('enum_description_visible_to_customer', 'projects', 'project'));
    }

    /**
     * Store a newly created MileStone in storage.
     *
     * @param  \App\Http\Requests\StoreMileStonesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMileStonesRequest $request, $project_id = '')
    {
        if (! Gate::allows('mile_stone_create')) {
            return abort(401);
        }

         $date_set = getCurrentDateFormat();

         $additional = array(     
           
            'due_date' => ! empty( $request->due_date ) ? Carbon::createFromFormat($date_set, $request->due_date)->format('Y-m-d') : NULL,
           
        );  
        $request->request->add( $additional ); 

        $mile_stone = MileStone::create($request->all());

        flashMessage( 'success', 'create');

        return redirect()->route('admin.mile_stones.index', $project_id);
    }


    /**
     * Show the form for editing MileStone.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $id)
    {
        if (! Gate::allows('mile_stone_edit')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $projects = \App\ClientProject::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_description_visible_to_customer = MileStone::$enum_description_visible_to_customer;
            
        $mile_stone = MileStone::findOrFail($id);

        $date_set = getCurrentDateFormat();
        $mile_stone->due_date = ! empty( $mile_stone->due_date ) ? Carbon::createFromFormat('Y-m-d', $mile_stone->due_date)->format($date_set) : NULL;

        return view('admin.mile_stones.edit', compact('mile_stone', 'enum_description_visible_to_customer', 'projects', 'project'));
    }

    /**
     * Update MileStone in storage.
     *
     * @param  \App\Http\Requests\UpdateMileStonesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMileStonesRequest $request, $project_id, $id)
    {
        if (! Gate::allows('mile_stone_edit')) {
            return abort(401);
        }
        $mile_stone = MileStone::findOrFail($id);

         $date_set = getCurrentDateFormat();

        $additional = array(           
           
            'due_date' => ! empty( $request->due_date ) ? Carbon::createFromFormat($date_set, $request->due_date)->format('Y-m-d') : NULL,
           
        );  
        $request->request->add( $additional ); 

        $mile_stone->update($request->all());

        flashMessage( 'success', 'update');

        return redirect()->route('admin.mile_stones.index', $project_id);
    }


    /**
     * Display MileStone.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($project_id, $id)
    {
        if (! Gate::allows('mile_stone_view')) {
            return abort(401);
        }

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $mile_stone = MileStone::findOrFail($id);
        $date_set = getCurrentDateFormat();
        $mile_stone->due_date = ! empty( $mile_stone->due_date ) ? Carbon::createFromFormat('Y-m-d', $mile_stone->due_date)->format($date_set) : NULL;

        return view('admin.mile_stones.show', compact('mile_stone', 'project'));
    }


    /**
     * Remove MileStone from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $project_id, $id)
    {
        if (! Gate::allows('mile_stone_delete')) {
            return abort(401);
        }
        $mile_stone = MileStone::findOrFail($id);
        $project_id = $mile_stone->project_id;
        $mile_stone->delete();

        flashMessage( 'success', 'delete');
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.mile_stones.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected MileStone at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('mile_stone_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = MileStone::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore MileStone from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($project_id, $id)
    {
        if (! Gate::allows('mile_stone_delete')) {
            return abort(401);
        }
        $mile_stone = MileStone::onlyTrashed()->findOrFail($id);
        $project_id = $mile_stone->project_id;
        $mile_stone->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete MileStone from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($project_id, $id)
    {
        if (! Gate::allows('mile_stone_delete')) {
            return abort(401);
        }
        $mile_stone = MileStone::onlyTrashed()->findOrFail($id);
        $project_id = $mile_stone->project_id;
        $mile_stone->forceDelete();

        flashMessage( 'success', 'delete');

        return back();
    }
}