<?php

namespace Modules\Quotes\Http\Controllers\Admin;
use Modules\Quotes\Entities\Quote;
use Modules\Quotes\Entities\QuoteTask;
use Modules\RecurringPeriods\Entities\RecurringPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Quotes\Http\Requests\Admin\StoreQuoteTasksRequest;
use Modules\Quotes\Http\Requests\Admin\UpdateQuoteTasksRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class QuoteTasksController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of QuoteTask.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $quote_id )
    {
        if (! Gate::allows('quote_task_access')) {
            return prepareBlockUserMessage();
        }
		
		$quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
		
        if ($filterBy = Input::get('filter')) {
            if ($filterBy == 'all') {
                Session::put('QuoteTask.filter', 'all');
            } elseif ($filterBy == 'my') {
                Session::put('QuoteTask.filter', 'my');
            }
        }

        
        if (request()->ajax()) {
            $query = QuoteTask::query();
            $query->with("recurring");
            $query->with("invoice");
            $query->with("created_by");
            $query->with("mile_stone");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('quote_task_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'quote_tasks.id',
                'quote_tasks.name',
                'quote_tasks.description',
                'quote_tasks.priority_id',
                'quote_tasks.startdate',
                'quote_tasks.duedate',
                'quote_tasks.datefinished',
                'quote_tasks.status_id',
                'quote_tasks.recurring_id',
                'quote_tasks.recurring_type',
                'quote_tasks.recurring_value',
                'quote_tasks.cycles',
                'quote_tasks.total_cycles',
                'quote_tasks.last_recurring_date',
                'quote_tasks.is_public',
                'quote_tasks.billable',
                'quote_tasks.billed',
                'quote_tasks.quote_id',
                'quote_tasks.hourly_rate',
                
                'quote_tasks.kanban_order',
                'quote_tasks.milestone_order',
                'quote_tasks.visible_to_client',
                'quote_tasks.deadline_notified',
                'quote_tasks.created_by_id',
                'quote_tasks.mile_stone_id',
            ]);

            $query->where('quote_id', '=', $quote_id );

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'quote_task_';
                $routeKey = 'admin.quote_tasks';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                $str = $row->name ? '<a href="'.route('admin.quote_tasks.show', [ 'quote_id' => $row->quote_id, 'id' => $row->id ] ).'">' . $row->name . '</a>' : '';
                if ( ! empty( $row->recurring_type ) ) {
                    $str .= '<br><p class="label label-primary inline-block mtop4">'.trans('global.quote-tasks.recurring-task').'</p>';
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
                        $statuses = \Modules\DynamicOptions\Entities\DynamicOption::where('module', '=', 'quotes')->where('type', '=', 'taskstatus')->get()->pluck('title', 'id');
                        if ( ! empty( $statuses ) ) {
                            foreach ($statuses as $id => $title) {
                                $str .= '<li><a href="'.route('admin.quote_tasks.changestatus', ['quote_id' => $row->quote_id, 'id' => $row->id, 'status' => $id]).'"><i class="fa fa-pencil"></i>'.trans('global.quote-tasks.mask-as'). $title . '</a></li>';
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
            $table->editColumn('invoice.quote_no', function ($row) {
                return $row->invoice ? $row->invoice->quote_no : '';
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

        return view('quotes::admin.quotes.quote_tasks.index', compact('quote'));
    }

    /**
     * Show the form for creating new QuoteTask.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $quote_id )
    {

        if (! Gate::allows('quote_task_create')) {
            return prepareBlockUserMessage();
        }
		
		$quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $recurrings = RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $invoices = Quote::get()->pluck('quote_no', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $mile_stones = \App\MileStone::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_recurring_type = QuoteTask::$enum_recurring_type;
                    $enum_is_public = QuoteTask::$enum_is_public;
                    $enum_billable = QuoteTask::$enum_billable;
                    $enum_billed = QuoteTask::$enum_billed;
                    $enum_visible_to_client = QuoteTask::$enum_visible_to_client;
                    $enum_deadline_notified = QuoteTask::$enum_deadline_notified;
            
        return view('quotes::admin.quotes.quote_tasks.create', compact('enum_recurring_type', 'enum_is_public', 'enum_billable', 'enum_billed', 'enum_visible_to_client', 'enum_deadline_notified', 'recurrings', 'invoices', 'created_bies', 'mile_stones', 'quote'));
    }

    /**
     * Store a newly created QuoteTask in storage.
     *
     * @param  \App\Http\Requests\StoreQuoteTasksRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuoteTasksRequest $request, $quote_id)
    {
        if (! Gate::allows('quote_task_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }

        $date_set = getCurrentDateFormat();

        $addtional = array(
            'quote_id' => $quote_id,
            'startdate' => ! empty( $request->startdate ) ? Carbon::createFromFormat($date_set, $request->startdate)->format('Y-m-d') : NULL,
            'duedate' => ! empty( $request->duedate ) ? Carbon::createFromFormat($date_set, $request->duedate)->format('Y-m-d') : NULL,
             'datefinished' => ! empty( $request->datefinished ) ? Carbon::createFromFormat($date_set, $request->datefinished)->format('Y-m-d') : NULL,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $quote_task = QuoteTask::create($request->all());


        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $quote_task->id;
            $file->save();
        }

        flashMessage( 'success', 'create');

        return redirect()->route('admin.quote_tasks.index', $quote_id);
    }


    /**
     * Show the form for editing QuoteTask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($quote_id, $id)
    {
        if (! Gate::allows('quote_task_edit')) {
            return prepareBlockUserMessage();
        }
		
		$quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $recurrings = RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $invoices = Quote::get()->pluck('quote_no', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $mile_stones = \App\MileStone::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_recurring_type = QuoteTask::$enum_recurring_type;
                    $enum_is_public = QuoteTask::$enum_is_public;
                    $enum_billable = QuoteTask::$enum_billable;
                    $enum_billed = QuoteTask::$enum_billed;
                    $enum_visible_to_client = QuoteTask::$enum_visible_to_client;
                    $enum_deadline_notified = QuoteTask::$enum_deadline_notified;
            
        $quote_task = QuoteTask::findOrFail($id);

        return view('quotes::admin.quotes.quote_tasks.edit', compact('quote_task', 'enum_recurring_type', 'enum_is_public', 'enum_billable', 'enum_billed', 'enum_visible_to_client', 'enum_deadline_notified', 'recurrings', 'invoices', 'created_bies', 'mile_stones', 'quote'));
    }

    /**
     * Update QuoteTask in storage.
     *
     * @param  \App\Http\Requests\UpdateQuoteTasksRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuoteTasksRequest $request, $quote_id, $id)
    {

        if (! Gate::allows('quote_task_edit')) {
            return prepareBlockUserMessage();
        }
        
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $quote_task = QuoteTask::findOrFail($id);

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
        $quote_task->update($request->all());


       $media = [];
        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $quote_task->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $quote_task->updateMedia($media, 'attachments');

        flashMessage( 'success', 'update');

        return redirect()->route('admin.quote_tasks.index', $quote_id);
    }


    /**
     * Display QuoteTask.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($quote_id, $id)
    {
        if (! Gate::allows('quote_task_view')) {
            return prepareBlockUserMessage();
        }
        
        $quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $quote_task = QuoteTask::findOrFail($id);

        return view('quotes::admin.quotes.quote_tasks.show', compact('quote_task', 'quote'));
    }


    /**
     * Remove QuoteTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $quote_id, $id)
    {

        if (! Gate::allows('quote_task_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $quote_task = QuoteTask::findOrFail($id);
        $quote_task->deletePreservingMedia();

        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
    }

    /**
     * Delete all selected QuoteTask at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {

        if (! Gate::allows('quote_task_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = QuoteTask::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }
        }
    }


    /**
     * Restore QuoteTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {

       if (! Gate::allows('quote_task_delete')) {
            return prepareBlockUserMessage();
        }
        
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $quote_task = QuoteTask::onlyTrashed()->findOrFail($id);
        $quote_id = $quote_task->quote_id;

        $quote_task->restore();

        return back();
    }

    /**
     * Permanently delete QuoteTask from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('quote_task_delete')) {
            return prepareBlockUserMessage();
        }

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        
        $quote_task = QuoteTask::onlyTrashed()->findOrFail($id);

        $quote_id = $quote_task->quote_id;

        $quote_task->forceDelete();

        return back();
    }

    public function taskChangestatus( $quote_id, $id, $status_id ) {
        if (! Gate::allows('quote_task_edit')) {
            return prepareBlockUserMessage();
        }

        $task = QuoteTask::where( 'quote_id', '=', $quote_id)->where('id', '=', $id)->find();
        if (! $task) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $task->status_id = $status_id;
        $task->save();

        flashMessage( 'success', 'status' );

        return redirect()->route('admin.quote_tasks.index', $quote_id);
    }
}
