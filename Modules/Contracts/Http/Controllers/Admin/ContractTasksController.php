<?php

namespace Modules\Contracts\Http\Controllers\Admin;
use Modules\Contracts\Entities\Contract;
use Modules\Contracts\Entities\ContractTask;
use Modules\RecurringPeriods\Entities\RecurringPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Contracts\Http\Requests\Admin\StoreContractTasksRequest;
use Modules\Contracts\Http\Requests\Admin\UpdateContractTasksRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ContractTasksController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of ContractTask.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $contract_id )
    {
        if (! Gate::allows('contract_task_access')) {
            return prepareBlockUserMessage();
        }
		
		$contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
		
        if ($filterBy = Input::get('filter')) {
            if ($filterBy == 'all') {
                Session::put('ContractTask.filter', 'all');
            } elseif ($filterBy == 'my') {
                Session::put('ContractTask.filter', 'my');
            }
        }

        
        if (request()->ajax()) {
            $query = ContractTask::query();
            $query->with("recurring");
            $query->with("invoice");
            $query->with("created_by");
            $query->with("mile_stone");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('contract_task_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'contract_tasks.id',
                'contract_tasks.name',
                'contract_tasks.description',
                'contract_tasks.priority_id',
                'contract_tasks.startdate',
                'contract_tasks.duedate',
                'contract_tasks.datefinished',
                'contract_tasks.status_id',
                'contract_tasks.recurring_id',
                'contract_tasks.recurring_type',
                'contract_tasks.recurring_value',
                'contract_tasks.cycles',
                'contract_tasks.total_cycles',
                'contract_tasks.last_recurring_date',
                'contract_tasks.is_public',
                'contract_tasks.billable',
                'contract_tasks.billed',
                'contract_tasks.contract_id',
                'contract_tasks.hourly_rate',
                
                'contract_tasks.kanban_order',
                'contract_tasks.milestone_order',
                'contract_tasks.visible_to_client',
                'contract_tasks.deadline_notified',
                'contract_tasks.created_by_id',
                'contract_tasks.mile_stone_id',
            ]);

            $query->where('contract_id', '=', $contract_id );

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'contract_task_';
                $routeKey = 'admin.contract_tasks';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                $str = $row->name ? '<a href="'.route('admin.contract_tasks.show', [ 'contract_id' => $row->contract_id, 'id' => $row->id ] ).'">' . $row->name . '</a>' : '';
                if ( ! empty( $row->recurring_type ) ) {
                    $str .= '<br><p class="label label-primary inline-block mtop4">'.trans('global.contract-tasks.recurring-task').'</p>';
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
                        $statuses = \Modules\DynamicOptions\Entities\DynamicOption::where('module', '=', 'contracts')->where('type', '=', 'taskstatus')->get()->pluck('title', 'id');
                        if ( ! empty( $statuses ) ) {
                            foreach ($statuses as $id => $title) {
                                $str .= '<li><a href="'.route('admin.contract_tasks.changestatus', ['contract_id' => $row->contract_id, 'id' => $row->id, 'status' => $id]).'"><i class="fa fa-pencil"></i>'.trans('global.contract-tasks.mask-as'). $title . '</a></li>';
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
            $table->editColumn('invoice.contract_no', function ($row) {
                return $row->invoice ? $row->invoice->contract_no : '';
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

        return view('contracts::admin.contracts.contract_tasks.index', compact('contract'));
    }

    /**
     * Show the form for creating new ContractTask.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $contract_id )
    {

        if (! Gate::allows('contract_task_create')) {
            return prepareBlockUserMessage();
        }
		
		$contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $recurrings = RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $invoices = Contract::get()->pluck('contract_no', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $mile_stones = \App\MileStone::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_recurring_type = ContractTask::$enum_recurring_type;
                    $enum_is_public = ContractTask::$enum_is_public;
                    $enum_billable = ContractTask::$enum_billable;
                    $enum_billed = ContractTask::$enum_billed;
                    $enum_visible_to_client = ContractTask::$enum_visible_to_client;
                    $enum_deadline_notified = ContractTask::$enum_deadline_notified;
        $enum_visible_to_client = ContractTask::$enum_visible_to_client; 
           
        return view('contracts::admin.contracts.contract_tasks.create', compact('enum_recurring_type', 'enum_is_public', 'enum_billable', 'enum_billed', 'enum_visible_to_client','enum_deadline_notified', 'recurrings', 'invoices', 'created_bies', 'mile_stones', 'contract'));
    }

    /**
     * Store a newly created ContractTask in storage.
     *
     * @param  \App\Http\Requests\StoreContractTasksRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContractTasksRequest $request, $contract_id)
    {
        if (! Gate::allows('contract_task_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }

        $date_set = getCurrentDateFormat();

        $addtional = array(
            'contract_id' => $contract_id,
            'startdate' => ! empty( $request->startdate ) ? Carbon::createFromFormat($date_set, $request->startdate)->format('Y-m-d') : NULL,
            'duedate' => ! empty( $request->duedate ) ? Carbon::createFromFormat($date_set, $request->duedate)->format('Y-m-d') : NULL,
             'datefinished' => ! empty( $request->datefinished ) ? Carbon::createFromFormat($date_set, $request->datefinished)->format('Y-m-d') : NULL,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contract_task = ContractTask::create($request->all());


        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $contract_task->id;
            $file->save();
        }

        flashMessage( 'success', 'create');

        return redirect()->route('admin.contract_tasks.index', $contract_id);
    }


    /**
     * Show the form for editing ContractTask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($contract_id, $id)
    {
        if (! Gate::allows('contract_task_edit')) {
            return prepareBlockUserMessage();
        }
		
		$contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $recurrings = RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $invoices = Contract::get()->pluck('contract_no', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $mile_stones = \App\MileStone::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_recurring_type = ContractTask::$enum_recurring_type;
                    $enum_is_public = ContractTask::$enum_is_public;
                    $enum_billable = ContractTask::$enum_billable;
                    $enum_billed = ContractTask::$enum_billed;
                    $enum_visible_to_client = ContractTask::$enum_visible_to_client;
                    $enum_deadline_notified = ContractTask::$enum_deadline_notified;
            
        $contract_task = ContractTask::findOrFail($id);

        return view('contracts::admin.contracts.contract_tasks.edit', compact('contract_task', 'enum_recurring_type', 'enum_is_public', 'enum_billable', 'enum_billed', 'enum_visible_to_client','enum_deadline_notified', 'recurrings', 'invoices', 'created_bies', 'mile_stones', 'contract'));
    }

    /**
     * Update ContractTask in storage.
     *
     * @param  \App\Http\Requests\UpdateContractTasksRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContractTasksRequest $request, $contract_id, $id)
    {

        if (! Gate::allows('contract_task_edit')) {
            return prepareBlockUserMessage();
        }
        
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $contract_task = ContractTask::findOrFail($id);

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
        $contract_task->update($request->all());


       $media = [];
        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $contract_task->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $contract_task->updateMedia($media, 'attachments');

        flashMessage( 'success', 'update');

        return redirect()->route('admin.contract_tasks.index', $contract_id);
    }


    /**
     * Display ContractTask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($contract_id, $id)
    {
        if (! Gate::allows('contract_task_view')) {
            return prepareBlockUserMessage();
        }
        
        $contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $contract_task = ContractTask::findOrFail($id);

        return view('contracts::admin.contracts.contract_tasks.show', compact('contract_task', 'contract'));
    }


    /**
     * Remove ContractTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($contract_id, $id)
    {

        if (! Gate::allows('contract_task_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $contract_task = ContractTask::findOrFail($id);
        $contract_task->deletePreservingMedia();

        return redirect()->route('admin.contract_tasks.index', $contract_id);
    }
    /**
     * Delete all selected ContractTask at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {

        if (! Gate::allows('contract_task_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ContractTask::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }
        }
    }


    /**
     * Restore ContractTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {

       if (! Gate::allows('contract_task_delete')) {
            return prepareBlockUserMessage();
        }
        
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contract_task = ContractTask::onlyTrashed()->findOrFail($id);
        $contract_id = $contract_task->contract_id;

        $contract_task->restore();

        return redirect()->route('admin.contract_tasks.index', $contract_id);
    }

    /**
     * Permanently delete ContractTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('contract_task_delete')) {
            return prepareBlockUserMessage();
        }

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        
        $contract_task = ContractTask::onlyTrashed()->findOrFail($id);

        $contract_id = $contract_task->contract_id;

        $contract_task->forceDelete();

        return redirect()->route('admin.contract_tasks.index', $contract_id);
    }

    public function taskChangestatus( $contract_id, $id, $status_id ) {
        if (! Gate::allows('contract_task_edit')) {
            return prepareBlockUserMessage();
        }

        $task = ContractTask::where( 'contract_id', '=', $contract_id)->where('id', '=', $id)->find();
        if (! $task) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $task->status_id = $status_id;
        $task->save();

        flashMessage( 'success', 'status' );

        return redirect()->route('admin.contract_tasks.index', $contract_id);
    }
}
