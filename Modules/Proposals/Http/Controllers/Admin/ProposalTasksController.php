<?php

namespace Modules\Proposals\Http\Controllers\Admin;
use Modules\Proposals\Entities\Proposal;
use Modules\Proposals\Entities\ProposalTask;
use Modules\RecurringPeriods\Entities\RecurringPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\DynamicOptions\Entities\DynamicOption;
use App\Http\Controllers\Controller;
use Modules\Proposals\Http\Requests\Admin\StoreProposalTasksRequest;
use Modules\Proposals\Http\Requests\Admin\UpdateProposalTasksRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ProposalTasksController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of ProposalTask.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $proposal_id )
    {
        if (! Gate::allows('proposal_task_access')) {
            return prepareBlockUserMessage();
        }
		
		$proposal = Proposal::find( $proposal_id );
       

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
		
        if ($filterBy = Input::get('filter')) {
            if ($filterBy == 'all') {
                Session::put('ProposalTask.filter', 'all');
            } elseif ($filterBy == 'my') {
                Session::put('ProposalTask.filter', 'my');
            }
        }

        
        if (request()->ajax()) {
           
            $query = ProposalTask::query();
            $query->with("recurring");
            $query->with("invoice");
            $query->with("created_by");
            $query->with("mile_stone");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('proposal_task_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'proposal_tasks.id',
                'proposal_tasks.name',
                'proposal_tasks.description',
                'proposal_tasks.priority_id',
                'proposal_tasks.startdate',
                'proposal_tasks.duedate',
                'proposal_tasks.datefinished',
                'proposal_tasks.status_id',
                'proposal_tasks.recurring_id',
                'proposal_tasks.recurring_type',
                'proposal_tasks.recurring_value',
                'proposal_tasks.cycles',
                'proposal_tasks.total_cycles',
                'proposal_tasks.last_recurring_date',
                'proposal_tasks.is_public',
                'proposal_tasks.billable',
                'proposal_tasks.billed',
                'proposal_tasks.proposal_id',
                'proposal_tasks.hourly_rate',
                
                'proposal_tasks.kanban_order',
                'proposal_tasks.milestone_order',
                'proposal_tasks.visible_to_client',
                'proposal_tasks.deadline_notified',
                'proposal_tasks.created_by_id',
                'proposal_tasks.mile_stone_id',
            ]);

            $query->where('proposal_id', '=', $proposal_id );

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'proposal_task_';
                $routeKey = 'admin.proposal_tasks';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                $str = $row->name ? '<a href="'.route('admin.proposal_tasks.show', [ 'proposal_id' => $row->proposal_id, 'id' => $row->id ] ).'">' . $row->name . '</a>' : '';
                if ( ! empty( $row->recurring_type ) ) {
                    $str .= '<br><p class="label label-primary inline-block mtop4">'.trans('global.proposal-tasks.recurring-task').'</p>';
                }
                return $str;
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('priority_id', function ($row) {
                return $row->priority_id ? $row->priority->title : '';
            });
            $table->editColumn('startdate', function ($row) {
                return $row->startdate ? digiDate($row->startdate) : '';
            });
            $table->editColumn('duedate', function ($row) {
                return $row->duedate ? digiDate($row->duedate) : '';
            });
            $table->editColumn('datefinished', function ($row) {
                return $row->datefinished ? digiDate($row->datefinished) : '';
            });
            $table->editColumn('status_id', function ($row) {
                $str = $row->status_id ? $row->status->title : '';
                $str .= '<div class="dropdown more">
                        <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dLabel">';
                        $statuses = \Modules\DynamicOptions\Entities\DynamicOption::where('module', '=', 'proposals')->where('type', '=', 'taskstatus')->get()->pluck('title', 'id');
                        if ( ! empty( $statuses ) ) {
                            foreach ($statuses as $id => $title) {
                                $str .= '<li><a href="'.route('admin.proposal_tasks.changestatus', ['proposal_id' => $row->proposal_id, 'id' => $row->id, 'status' => $id]).'"><i class="fa fa-pencil"></i>'.trans('global.proposal-tasks.mask-as'). $title . '</a></li>';
                            }                           
                        }
                            
                        $str .= '</ul>
                    </div>';
                return $str;
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
            $table->editColumn('invoice.proposal_no', function ($row) {
                return $row->invoice ? $row->invoice->proposal_no : '';
            });
            $table->editColumn('hourly_rate', function ($row) {
                return $row->hourly_rate ? $row->hourly_rate : '';
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
            $table->editColumn('created_by.name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
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

            $table->rawColumns(['actions','massDelete','attachments', 'name', 'status_id']);

            return $table->make(true);
        }

        return view('proposals::admin.proposals.proposal_tasks.index', compact('proposal'));
    }

    /**
     * Show the form for creating new ProposalTask.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $proposal_id )
    {

        if (! Gate::allows('proposal_task_create')) {
            return prepareBlockUserMessage();
        }
		
		$proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        $priorities = DynamicOption::where('module', 'Proposals')->where('type', 'priorities')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $recurrings = RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $invoices = Proposal::get()->pluck('proposal_no', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $mile_stones = \App\MileStone::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_recurring_type = ProposalTask::$enum_recurring_type;
                    $enum_is_public = ProposalTask::$enum_is_public;
                    $enum_billable = ProposalTask::$enum_billable;
                    $enum_billed = ProposalTask::$enum_billed;
                    $enum_visible_to_client = ProposalTask::$enum_visible_to_client;
                    $enum_deadline_notified = ProposalTask::$enum_deadline_notified;
            
        return view('proposals::admin.proposals.proposal_tasks.create', compact('enum_recurring_type', 'enum_is_public', 'enum_billable', 'enum_billed', 'enum_visible_to_client', 'enum_deadline_notified','priorities', 'recurrings', 'invoices', 'created_bies', 'mile_stones', 'proposal'));
    }

    /**
     * Store a newly created ProposalTask in storage.
     *
     * @param  \App\Http\Requests\StoreProposalTasksRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProposalTasksRequest $request, $proposal_id)
    {
        if (! Gate::allows('proposal_task_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }

        $date_set = getCurrentDateFormat();

        $addtional = array(
            'proposal_id' => $proposal_id,
            'startdate' => ! empty( $request->startdate ) ? Carbon::createFromFormat($date_set, $request->startdate)->format('Y-m-d') : NULL,
            'duedate' => ! empty( $request->duedate ) ? Carbon::createFromFormat($date_set, $request->duedate)->format('Y-m-d') : NULL,
             'datefinished' => ! empty( $request->datefinished ) ? Carbon::createFromFormat($date_set, $request->datefinished)->format('Y-m-d') : NULL,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $proposal_task = ProposalTask::create($request->all());


        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $proposal_task->id;
            $file->save();
        }

        flashMessage( 'success', 'create');

        return redirect()->route('admin.proposal_tasks.index', $proposal_id);
    }


    /**
     * Show the form for editing ProposalTask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($proposal_id, $id)
    {
        if (! Gate::allows('proposal_task_edit')) {
            return prepareBlockUserMessage();
        }
		
		$proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $recurrings = RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $invoices = Proposal::get()->pluck('proposal_no', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $mile_stones = \App\MileStone::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_recurring_type = ProposalTask::$enum_recurring_type;
                    $enum_is_public = ProposalTask::$enum_is_public;
                    $enum_billable = ProposalTask::$enum_billable;
                    $enum_billed = ProposalTask::$enum_billed;
                    $enum_visible_to_client = ProposalTask::$enum_visible_to_client;
                    $enum_deadline_notified = ProposalTask::$enum_deadline_notified;
            
        $proposal_task = ProposalTask::findOrFail($id);

        return view('proposals::admin.proposals.proposal_tasks.edit', compact('proposal_task', 'enum_recurring_type', 'enum_is_public', 'enum_billable', 'enum_billed', 'enum_visible_to_client', 'enum_deadline_notified', 'recurrings', 'invoices', 'created_bies', 'mile_stones', 'proposal'));
    }

    /**
     * Update ProposalTask in storage.
     *
     * @param  \App\Http\Requests\UpdateProposalTasksRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProposalTasksRequest $request, $proposal_id, $id)
    {

        if (! Gate::allows('proposal_task_edit')) {
            return prepareBlockUserMessage();
        }
        
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $proposal_task = ProposalTask::findOrFail($id);

        $date_set = getCurrentDateFormat();

        $addtional = array(
            'startdate' => ! empty( $request->startdate ) ? Carbon::createFromFormat($date_set, $request->startdate)->format('Y-m-d') : NULL,
            'duedate' => ! empty( $request->duedate ) ? Carbon::createFromFormat($date_set, $request->duedate)->format('Y-m-d') : NULL,
            'datefinished' => ! empty( $request->datefinished ) ? Carbon::createFromFormat($date_set, $request->datefinished)->format('Y-m-d') : NULL,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $proposal_task->update($request->all());


       $media = [];
        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $proposal_task->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $proposal_task->updateMedia($media, 'attachments');

        flashMessage( 'success', 'update');

        return redirect()->route('admin.proposal_tasks.index', $proposal_id);
    }


    /**
     * Display ProposalTask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($proposal_id, $id)
    {
        if (! Gate::allows('proposal_task_view')) {
            return prepareBlockUserMessage();
        }
        
        $proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $proposal_task = ProposalTask::findOrFail($id);

        return view('proposals::admin.proposals.proposal_tasks.show', compact('proposal_task', 'proposal','proposal_id','id'));
    }


    /**
     * Remove ProposalTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($proposal_id, $id)
    {

        if (! Gate::allows('proposal_task_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $proposal_task = ProposalTask::findOrFail($id);
        $proposal_task->deletePreservingMedia();

        return redirect()->route('admin.proposal_tasks.index', $proposal_id);
    }

    /**
     * Delete all selected ProposalTask at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {

        if (! Gate::allows('proposal_task_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ProposalTask::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }
        }
    }


    /**
     * Restore ProposalTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {

       if (! Gate::allows('proposal_task_delete')) {
            return prepareBlockUserMessage();
        }
        
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $proposal_task = ProposalTask::onlyTrashed()->findOrFail($id);
        $proposal_id = $proposal_task->proposal_id;

        $proposal_task->restore();

        return redirect()->route('admin.proposal_tasks.index', $proposal_id);
    }

    /**
     * Permanently delete ProposalTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('proposal_task_delete')) {
            return prepareBlockUserMessage();
        }

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        
        $proposal_task = ProposalTask::onlyTrashed()->findOrFail($id);

        $proposal_id = $proposal_task->proposal_id;

        $proposal_task->forceDelete();

        return redirect()->route('admin.proposal_tasks.index', $proposal_id);
    }

    public function taskChangestatus( $proposal_id, $id, $status_id ) {
        if (! Gate::allows('proposal_task_edit')) {
            return prepareBlockUserMessage();
        }

        $task = ProposalTask::where( 'proposal_id', '=', $proposal_id)->where('id', '=', $id)->find();
        if (! $task) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $task->status_id = $status_id;
        $task->save();

        flashMessage( 'success', 'status' );

        return redirect()->route('admin.proposal_tasks.index', $proposal_id);
    }
}
