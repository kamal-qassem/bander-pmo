<?php

namespace Modules\InvoiceAdditional\Http\Controllers\Admin;

use App\Invoice;
use Modules\InvoiceAdditional\Entities\InvoiceTask;
use Modules\DynamicOptions\Entities\DynamicOption;
use Modules\RecurringPeriods\Entities\RecurringPeriod;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\InvoiceAdditional\Http\Requests\Admin\StoreInvoiceTasksRequest;
use Modules\InvoiceAdditional\Http\Requests\Admin\UpdateInvoiceTasksRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class InvoiceTasksController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of InvoiceTask.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $invoice_id )
    {

        abort_if (! Gate::allows('invoice_task_access'), 401);

        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        if (request()->ajax()) {
            $query = InvoiceTask::query();
            $query->with("priority");
            $query->with("status");
            $query->with("recurring");
            $query->with("assigned_to");
            $query->with("invoice");
            $query->with("created_by");
            $query->with("mile_stone");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('invoice_task_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'invoice_tasks.id',
                'invoice_tasks.name',
                'invoice_tasks.description',
                'invoice_tasks.priority_id',
                'invoice_tasks.startdate',
                'invoice_tasks.duedate',
                'invoice_tasks.datefinished',
                'invoice_tasks.status_id',
                'invoice_tasks.recurring_id',
                'invoice_tasks.recurring_type',
                'invoice_tasks.recurring_value',
                'invoice_tasks.cycles',
                'invoice_tasks.total_cycles',
                'invoice_tasks.last_recurring_date',
                'invoice_tasks.is_public',
                'invoice_tasks.billable',
                'invoice_tasks.billed',
                'invoice_tasks.invoice_id',
                'invoice_tasks.hourly_rate',
                'invoice_tasks.kanban_order',
                'invoice_tasks.milestone_order',
                'invoice_tasks.visible_to_client',
                'invoice_tasks.deadline_notified',
                'invoice_tasks.created_by_id',
                'invoice_tasks.mile_stone_id',
                
            ]);
            $query->where('invoice_id', '=', $invoice_id );

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'invoice_task_';
                $routeKey = 'admin.invoice_tasks';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('priority.title', function ($row) {
                return $row->priority ? $row->priority->title : '';
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
                return $row->status ? $row->status->title : '';
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
            $table->editColumn('invoice.title', function ($row) {
                return $row->invoice ? $row->invoice->title : '';
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
            $table->editColumn('assigned_to.name', function ($row) {
                return $row->assigned_to ? '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        
                         $row->assigned_to->pluck('name')->toArray()) . '</span>' : '';

                
            });
            $table->editColumn('mile_stone.name', function ($row) {
                return $row->mile_stone ? $row->mile_stone->name : '';
            });
            $table->editColumn('attachments', function ($row) {
                $build  = '';
                foreach ($row->getMedia('attachments') as $media) {
                    $build .= '<p class="form-group"><a href="' . route('admin.home.media-download', $media->id) . '">' . $media->name . '</a></p>';
                }
                
                return $build;
            });

            $table->rawColumns(['actions','massDelete','attachments','assigned_to.name']);

            return $table->make(true);
        }

        return view('invoiceadditional::admin.invoice_tasks.index', compact('invoice'));
    }

    /**
     * Show the form for creating new InvoiceTask.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $invoice_id )
    {

        abort_if (! Gate::allows('invoice_task_create'), 401);

        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $priorities = DynamicOption::where('module', 'invoices')->where('type', 'priorities')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $statuses = DynamicOption::where('module', 'invoices')->where('type', 'taskstatus')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $recurrings = RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $invoices = \App\Invoice::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $mile_stones = \App\MileStone::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_recurring_type = InvoiceTask::$enum_recurring_type;
        $enum_is_public = InvoiceTask::$enum_is_public;
        $enum_billable = InvoiceTask::$enum_billable;
        $enum_billed = InvoiceTask::$enum_billed;
        $enum_visible_to_client = InvoiceTask::$enum_visible_to_client;
        $enum_deadline_notified = InvoiceTask::$enum_deadline_notified;

        $assigned_tos = \App\User::whereHas("role",
                   function ($query) {
                       $query->where('title', ROLE_EMPLOYEE);
                   })->get()->pluck('name', 'id');

        
            
        return view('invoiceadditional::admin.invoice_tasks.create', compact('enum_recurring_type', 'enum_is_public', 'enum_billable', 'enum_billed', 'enum_visible_to_client', 'enum_deadline_notified', 'priorities', 'statuses', 'recurrings', 'invoices', 'created_bies', 'mile_stones', 'invoice', 'assigned_tos'));
    }

    /**
     * Store a newly created InvoiceTask in storage.
     *
     * @param  \App\Http\Requests\StoreInvoiceTasksRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvoiceTasksRequest $request, $invoice_id)
    {

        abort_if (! Gate::allows('invoice_task_create'), 401);
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
       }

        $addtional = array(
            'invoice_id' => $invoice_id,
            'startdate' => ! empty( $request->startdate ) ? Carbon::createFromFormat(config('app.date_format'), $request->startdate)->format('Y-m-d') : NULL,
            'duedate' => ! empty( $request->duedate ) ? Carbon::createFromFormat(config('app.date_format'), $request->duedate)->format('Y-m-d') : NULL,
            'datefinished' => ! empty( $request->datefinished ) ? Carbon::createFromFormat(config('app.date_format'), $request->datefinished)->format('Y-m-d') : NULL,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_task = InvoiceTask::create($request->all());

        $invoice_task->assigned_to()->sync(array_filter((array)$request->input('assigned_to')));



        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $invoice_task->id;
            $file->save();
        }

        flashMessage( 'success', 'create');

        return redirect()->route('admin.invoice_tasks.index', $invoice_id);
    }


    /**
     * Show the form for editing InvoiceTask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($invoice_id, $id)
    {

        abort_if (! Gate::allows('invoice_task_edit'), 401);

        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
                $priorities = DynamicOption::where('module', 'invoices')->where('type', 'priorities')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
                $statuses = DynamicOption::where('module', 'invoices')->where('type', 'taskstatus')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
                $recurrings = RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
                $invoices = \App\Invoice::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
                $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                $mile_stones = \App\MileStone::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                $enum_recurring_type = InvoiceTask::$enum_recurring_type;
                $enum_is_public = InvoiceTask::$enum_is_public;
                $enum_billable = InvoiceTask::$enum_billable;
                $enum_billed = InvoiceTask::$enum_billed;
                $enum_visible_to_client = InvoiceTask::$enum_visible_to_client;
                $enum_deadline_notified = InvoiceTask::$enum_deadline_notified;

                  $assigned_tos = \App\User::whereHas("role",
                   function ($query) {
                       $query->where('title', ROLE_EMPLOYEE);
                   })->get()->pluck('name', 'id');

            
        $invoice_task = InvoiceTask::findOrFail($id);


        return view('invoiceadditional::admin.invoice_tasks.edit', compact('invoice_task', 'enum_recurring_type', 'enum_is_public', 'enum_billable', 'enum_billed', 'enum_visible_to_client', 'enum_deadline_notified', 'priorities', 'statuses', 'recurrings', 'invoices', 'created_bies', 'mile_stones', 'invoice', 'assigned_tos'));
    }

    /**
     * Update InvoiceTask in storage.
     *
     * @param  \App\Http\Requests\UpdateInvoiceTasksRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvoiceTasksRequest $request, $invoice_id, $id)
    {

        abort_if (! Gate::allows('invoice_task_edit'), 401);
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $invoice_task = InvoiceTask::findOrFail($id);
        $addtional = array(

            'startdate' => ! empty( $request->startdate ) ? Carbon::createFromFormat(config('app.date_format'), $request->startdate)->format('Y-m-d') : NULL,
            'duedate' => ! empty( $request->duedate ) ? Carbon::createFromFormat(config('app.date_format'), $request->duedate)->format('Y-m-d') : NULL,
            'datefinished' => ! empty( $request->datefinished ) ? Carbon::createFromFormat(config('app.date_format'), $request->datefinished)->format('Y-m-d') : NULL,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_task->update($request->all());

        $invoice_task->assigned_to()->sync(array_filter((array)$request->input('assigned_to')));


        $media = [];
        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $invoice_task->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $invoice_task->updateMedia($media, 'attachments');

        flashMessage( 'success', 'update');

        return redirect()->route('admin.invoice_tasks.index', $invoice_id);
    }


    /**
     * Display InvoiceTask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_id, $id)
    {

        abort_if (! Gate::allows('invoice_task_view'), 401);

        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $invoice_task = InvoiceTask::findOrFail($id);

        return view('invoiceadditional::admin.invoice_tasks.show', compact('invoice_task', 'invoice'));
    }


    /**
     * Remove InvoiceTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $invoice_id, $id)
    {
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        abort_if (! Gate::allows('invoice_task_delete'), 401);
        $invoice_task = InvoiceTask::findOrFail($id);
        $invoice_task->deletePreservingMedia();

        flashMessage( 'success', 'delete');

        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
    }

    /**
     * Delete all selected InvoiceTask at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        abort_if (! Gate::allows('invoice_task_delete'), 401);
        if ($request->input('ids')) {
            $entries = InvoiceTask::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }
            flashMessage( 'success', 'deletes');
        }
    }


    /**
     * Restore InvoiceTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        abort_if (! Gate::allows('invoice_task_delete'), 401);
        $invoice_task = InvoiceTask::onlyTrashed()->findOrFail($id);
        $invoice_id = $invoice_task->invoice_id;
        $invoice_task->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete InvoiceTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        abort_if (! Gate::allows('invoice_task_delete'), 401);
        $invoice_task = InvoiceTask::onlyTrashed()->findOrFail($id);
        $invoice_id = $invoice_task->invoice_id;
        $invoice_task->forceDelete();

        flashMessage( 'success', 'delete');

        return back();
    }

    public function taskChangestatus( $invoice_id, $id, $status_id ) {
        
        abort_if (! Gate::allows('quote_task_edit'), 401);

        $task = InvoiceTask::where( 'invoice_id', '=', $invoice_id)->where('id', '=', $id)->find();
        if (! $task) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $task->status_id = $status_id;
        $task->save();

        flashMessage( 'success', 'status' );

        return redirect()->route('admin.quote_tasks.index', $invoice_id);
    }
}
