<?php

namespace App\Http\Controllers\Admin;

use App\ClientProject;
use App\ClientProjectNote;
use App\MileStone;
use App\User;
use App\ProjectTask;
use App\Invoice;
use Modules\DynamicOptions\Entities\DynamicOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientProjectsRequest;
use App\Http\Requests\Admin\UpdateClientProjectsRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Admin\UploadProjectFileRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Input;

use App\Http\Requests\Admin\StoreInvoiceProjectsRequest;
use App\Http\Requests\Admin\StoreInvoiceProjectsSaveRequest;

use Location;
use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Notification;

use App\Expense;
use Illuminate\Support\Arr;
class ClientProjectsController extends Controller
{
    
    use FileUploadTrait;
    public function __construct() {
     $this->middleware('plugin:client_project');
    }
    /**
     * Display a listing of ClientProject.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {
        if (! Gate::allows('client_project_access')) {
            return prepareBlockUserMessage();
        }
        
        if (request()->ajax()) {
            $query = ClientProject::query();
            if( isEmployee() ){
                $query->whereHas("assigned_to",
                   function ($query) {
                       $query->where('id', Auth::id());
                   });
            } 


            $query->with("client");
            $query->with("currency");
            $query->with("billing_type");
            if ( ! isProjectManager() ) {
                $query->with("assigned_to");
            }
            $query->with("status");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('client_project_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'client_projects.id',
                'client_projects.title',
                'client_projects.client_id',
                'client_projects.priority',
                'client_projects.budget',
                'client_projects.billing_type_id',
                'client_projects.phase',
                'client_projects.start_date',
                'client_projects.due_date',
                'client_projects.status_id',
                'client_projects.description',
                'client_projects.demo_url',
                'client_projects.currency_id',
            ]);

             /// Custom Filters Start.
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

            $query->when(request('priority', false), function ($q, $priority) { 
                return $q->where('priority', $priority);
            });

            $query->when(request('projectStatus', false), function ($q, $projectStatus) { 
                return $q->where('status_id', $projectStatus);
            });

             $query->when(request('currency_id', false), function ($q, $currency_id) { 
                return $q->where('currency_id', $currency_id);
            });

            $query->when(request('projectStatus', false), function ($q, $projectStatus) { 
                return $q->where('status_id', $projectStatus);
            });
            /// Custom Filters End.
            
            if( isClient() ){
                $query->whereHas("client",
                   function ($query) {
                       $query->where('id', Auth::id());
                   });
            } 

            /**
             * when we call invoices display from other pages!
            */
            if ( ! empty( $type ) && 'contact' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('client_projects.client_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'project_billing_type' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('client_projects.billing_type_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'project_status' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('client_projects.status_id', $type_id);
                });
            }

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'client_project_';
                $routeKey = 'admin.client_projects';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
               
                return $row->title ? '<a href="'.route('admin.client_projects.show', $row->id ).'">' . $row->title . '</a>' : '';
            });
            $table->editColumn('client.first_name', function ($row) {
                $name = $row->client ? $row->client->name : '';
                if ( empty( $name ) ) {
                    $name = $row->client ? $row->client->first_name : '';
                    if ( ! empty( $row->client->last_name )) {
                        $name .= ' ' . $row->client->last_name;
                    }
                }
                return $name;
            });
            $table->editColumn('priority', function ($row) {
                return $row->priority ? $row->priority : '';
            });
            $table->editColumn('budget', function ($row) {
                $currency_id = getDefaultCurrency('id');
                if ( ! empty( $row->currency_id ) ) {
                    $currency_id = $row->currency_id;
                } elseif ( ! empty( $row->client->currency_id ) ) {
                    $currency_id = $row->client->currency_id;
                }
                return $row->budget ? digiCurrency($row->budget, $currency_id) : '';
            });
            $table->editColumn('billing_type.title', function ($row) {
                return $row->billing_type ? $row->billing_type->title : '';
            });
            $table->editColumn('phase', function ($row) {
                return $row->phase ? $row->phase : '';
            });

            $table->editColumn('assigned_to.name', function ($row) {
                if(count($row->assigned_to) == 0) {
                    return '';
                }
                return $row->assigned_to ? '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        $row->assigned_to->pluck('name')->toArray()) . '</span>' : '';
            });
            $table->editColumn('start_date', function ($row) {
                return $row->start_date ? digiDate( $row->start_date ) : '';
            });
            $table->editColumn('due_date', function ($row) {
                return $row->due_date ? digiDate( $row->due_date ) : '';
            });
            $table->editColumn('status.name', function ($row) {
                return $row->status ? $row->status->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('demo_url', function ($row) {
                return $row->demo_url ? $row->demo_url : '';
            });

             $table->editColumn('currency.name', function ($row) {
                return $row->currency ? $row->currency->name : '';
            });

            $table->rawColumns(['actions','massDelete','assigned_to.name', 'currency.name','title']);

            return $table->make(true);
        }

        return view('admin.client_projects.index');
    }

    /**
     * Show the form for creating new ClientProject.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
     
        if (! Gate::allows('client_project_create')) {
            return prepareBlockUserMessage();
        }
        
        $clients = \App\Contact::whereHas("contact_type",
            function ($query) {
                $query->where('id', CONTACT_CLIENT_TYPE);
            })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
           
        $billing_types = \App\ProjectBillingType::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
      
        $assigned_tos = \App\User::whereHas("role",
            function ($query) {
                $query->where('title', ROLE_EMPLOYEE);
            })->get()->pluck('name', 'id');
            
        $project_tabs = \App\ProjectTab::get()->pluck('title', 'id');

        $statuses = \App\ProjectStatus::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_priority = ClientProject::$enum_priority;
            
        return view('admin.client_projects.create', compact('enum_priority', 'clients', 'billing_types', 'assigned_tos', 'statuses', 'project_tabs'));
    }

    /**
     * Store a newly created ClientProject in storage.
     *
     * @param  \App\Http\Requests\StoreClientProjectsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientProjectsRequest $request)
    {      
     
      
        if (! Gate::allows('client_project_create')) {
            return prepareBlockUserMessage();
        }

        $date_set = getCurrentDateFormat();
        error_log('Some message here.');

        $currency_id = getDefaultCurrency('id');
        if ( ! empty( $request->client_id ) ) {
            $currency_id = getDefaultCurrency('id', $request->client_id);
        }
        $additional = array(           
            'start_date' => ! empty( $request->start_date ) ? Carbon::createFromFormat($date_set, $request->start_date)->format('Y-m-d') : NULL,
            'due_date' => ! empty( $request->due_date ) ? Carbon::createFromFormat($date_set, $request->due_date)->format('Y-m-d') : NULL,
            'currency_id' => $currency_id,
        );
        $request->request->add( $additional ); 
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $client_project = ClientProject::create($request->all());
        dd($client_project);
        $client_project->assigned_to()->sync(array_filter((array)$request->input('assigned_to')));
        $client_project->project_tabs()->sync(array_filter((array)$request->input('project_tabs')));


       flashMessage( 'success', 'create' ); 
       return $client_project;
        // return redirect()->route('admin.client_projects.index');
    }


    /**
     * Show the form for editing ClientProject.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('client_project_edit')) {
            return prepareBlockUserMessage();
        }
        
        $clients = \App\Contact::whereHas("contact_type",
            function ($query) {
                $query->where('id', CONTACT_CLIENT_TYPE);
            })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $billing_types = \App\ProjectBillingType::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        
        $assigned_tos = \App\User::whereHas("role",
            function ($query) {
                $query->where('title', ROLE_EMPLOYEE);
            })->get()->pluck('name', 'id');
        
        $project_tabs = \App\ProjectTab::get()->pluck('title', 'id');

        $statuses = \App\ProjectStatus::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_priority = ClientProject::$enum_priority;
            
        $client_project = ClientProject::findOrFail($id);

        return view('admin.client_projects.edit', compact('client_project', 'enum_priority', 'clients', 'billing_types', 'assigned_tos', 'statuses', 'project_tabs'));
    }

    /**
     * Update ClientProject in storage.
     *
     * @param  \App\Http\Requests\UpdateClientProjectsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientProjectsRequest $request, $id)
    {
        if (! Gate::allows('client_project_edit')) {
            return prepareBlockUserMessage();
        }
        $client_project = ClientProject::findOrFail($id);

        $date_set = getCurrentDateFormat();

        $currency_id = getDefaultCurrency('id');
        if ( ! empty( $request->client_id ) ) {
            $currency_id = getDefaultCurrency('id', $request->client_id);
        }

         $additional = array(           
            'start_date' => ! empty( $request->start_date ) ? Carbon::createFromFormat($date_set, $request->start_date)->format('Y-m-d') : NULL,
            'due_date' => ! empty( $request->due_date ) ? Carbon::createFromFormat($date_set, $request->due_date)->format('Y-m-d') : NULL,
            'currency_id' => $currency_id,
        );  
        $request->request->add( $additional ); 
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $client_project->update($request->all());
        $client_project->assigned_to()->sync(array_filter((array)$request->input('assigned_to')));
        $client_project->project_tabs()->sync(array_filter((array)$request->input('project_tabs')));


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.client_projects.index');
    }


    /**
     * Display ClientProject.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('client_project_view')) {
            return prepareBlockUserMessage();
        }
        
        $clients = \App\Contact::whereHas("contact_type",
            function ($query) {
                $query->where('id', CONTACT_CLIENT_TYPE);
            })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $billing_types = \App\ProjectBillingType::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $assigned_tos = \App\User::get()->pluck('name', 'id');

        $statuses = \App\ProjectStatus::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $time_entries = \App\TimeEntry::where('project_id', $id)->get();
        $project_tabs = \App\ProjectTab::get()->pluck('title', 'id');

        $client_project = $project = ClientProject::findOrFail($id);


        $percent           = ClientProject::calculateProgress( $id );
        $percent_circle        = $percent / 100;

        $tasks_not_completed = \App\ProjectTask::where('project_id', '=', $id)->where('status', '!=', STATUS_COMPLETED)->count();
        $total_project_tasks = \App\ProjectTask::where('project_id', '=', $id)->count();
        $total_finished_tasks = \App\ProjectTask::where('project_id', '=', $id)->where('status', '=', STATUS_COMPLETED)->count();

        $tasks_not_completed_progress = ($total_project_tasks > 0 ? number_format(($tasks_not_completed * 100) / $total_project_tasks, 2) : 0);
        $tasks_not_completed_progress = round($tasks_not_completed_progress, 2);

        $start_date = Carbon::parse( $project->start_date );
        $due_date = ( $project->due_date ) ? Carbon::parse( $project->due_date ) : Carbon::now();
        $project_total_days        = $due_date->diffInDays( $start_date );
        $project_days_left         = $project_total_days;
        $project_time_left_percent = 100;

        if ( $due_date->isPast() ) {
            $project_days_left         = 0;
            $project_time_left_percent = 0;
        }

        if ( $start_date->isPast() && $due_date->isFuture() ) {
            $project_days_left         = $due_date->diffInDays( Carbon::now()->format('Y-m-d') );
            $project_time_left_percent = round( $project_days_left / $project_total_days * 100, 2);
        }

        return view('admin.client_projects.show', compact('client_project', 'time_entries', 'project_tabs', 'percent_circle', 'tasks_not_completed', 'total_project_tasks', 'total_finished_tasks', 'tasks_not_completed_progress', 'project', 'project_total_days', 'project_days_left', 'project_time_left_percent'));
    }


    /**
     * Remove ClientProject from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('client_project_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $client_project = ClientProject::findOrFail($id);
        $client_project->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.client_projects.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        } // We are deleting records from different pages, so let us back to the same page.
     }
    }

    /**
     * Delete all selected ClientProject at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('client_project_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ClientProject::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore ClientProject from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('client_project_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $client_project = ClientProject::onlyTrashed()->findOrFail($id);
        $client_project->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete ClientProject from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('client_project_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $client_project = ClientProject::onlyTrashed()->findOrFail($id);
        $client_project->forceDelete();

        return back();
    }

    public function duplicate( $id ) {
        if (! Gate::allows('client_project_duplicate')) {
            return prepareBlockUserMessage();
        }
        $client_project = ClientProject::findOrFail($id);
        

        $newproject = $client_project->replicate();
        $newproject->title = $client_project->title . '(copied)';
        $newproject->save();

        $assigned_to = $client_project->assigned_to()->get()->pluck('id')->toArray();
        $newproject->assigned_to()->sync( $assigned_to );
        
        $project_tabs = $client_project->project_tabs()->get()->pluck('id')->toArray();
        $newproject->project_tabs()->sync( $project_tabs );

        flashMessage( 'success', 'create', trans('custom.messages.project-duplicated') );

        return redirect()->route('admin.client_projects.index', $newproject->id);
    }

    public function uploadDocuments( $project_id ) {
        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        return view( 'admin.client_projects.operations.uploads', compact('project'));
    }

    public function upload( UploadProjectFileRequest $request, $project_id ) {
        
        
        $project = ClientProject::find( $project_id );

        $request = $this->saveFiles($request);
        
        $media = [];
        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $project->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $project->updateMedia($media, 'attachments');

        flashMessage( 'success', 'create', trans('custom.invoices.upload-success'));
        return redirect()->route('admin.project_files.upload', $project->id);
    }

    public function uploadNotes( $project_id ) {
        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $notes = ClientProjectNote::where('user_id', '=', Auth::id())->where('project_id', '=', $project_id)->first();
        

        return view( 'admin.client_projects.operations.notes', compact('project', 'notes'));
    }

    public function uploadNote( Request $request, $project_id ) {
        
        $validatedData = $request->validate([
            'description' => 'required',
        ]);

        $notes = ClientProjectNote::where('user_id', '=', Auth::id())->where('project_id', '=', $project_id)->first();
        if ( $notes ) {
            $notes->description = $request->description;
            $notes->save();
        } else {
            ClientProjectNote::create(['description' => $request->description, 'project_id' => $project_id, 'user_id' => Auth::id()]);
        }

        flashMessage( 'success', 'update' );
        return redirect()->route('admin.project_files.note', $project_id);
    }

    public function ganttChart( $project_id ) {
        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $gantt_type         = Input::get('type', 'milestones category');
        $taskStatus         = Input::get('status', false);
        
        $db_prefix = env('DB_PREFIX');
        $type_data = [];
        $gantt_data     = [];
        $milestones = DB::select('SELECT *, (SELECT COUNT(id) FROM '.$db_prefix.'project_tasks WHERE milestone='.$db_prefix.'mile_stones.id) AS total_tasks, (SELECT COUNT(id) FROM '.$db_prefix.'project_tasks WHERE milestone='.$db_prefix.'mile_stones.id AND STATUS=5) AS total_finished_tasks FROM '.$db_prefix.'mile_stones WHERE project_id = '.$project_id.' ORDER BY milestone_order ASC');
        foreach ($milestones as $m) {
                $type_data[] = $m;
        }

        foreach ($type_data as $data) {                       
            $tasks = ProjectTask::where('project_id', '=', $project_id)->where('milestone', '=', $data->id)->get()->toArray();
            $name  = $data->name;
        }

        if (count($tasks) > 0) {
            $data         = get_task_array_gantt_data($tasks[0]);
            $data['name'] = $name;

            $gantt_data[] = $data;
            unset($tasks[0]);

            foreach ($tasks as $task) {
                $gantt_data[] = get_task_array_gantt_data($task);
            }
        }

        return view( 'admin.client_projects.operations.gantt-chart', compact('project', 'gantt_type', 'taskStatus', 'gantt_data'));
    }

    public function invoices( $project_id ) {

        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        if (request()->ajax()) {
            $query = Invoice::query();
            if ( isCustomer() ) {
                $query->where( 'customer_id', '=', getContactId())->where('status', '=', 'Published');
            }
            
            $query->with("customer");
            $query->with("currency");
            $query->with("tax");
            $query->with("discount");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {                
                if ( ! Gate::allows('invoice_delete') ) {
                    return prepareBlockUserMessage();
                }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'invoices.id',
                'invoices.customer_id',
                'invoices.currency_id',
                'invoices.title',
                'invoices.address',
                'invoices.invoice_prefix',
                'invoices.show_quantity_as',
                'invoices.invoice_no',
                'invoices.status',
                'invoices.reference',
                'invoices.invoice_date',
                'invoices.invoice_due_date',
                'invoices.invoice_notes',
                'invoices.tax_id',
                'invoices.discount_id',
                'invoices.amount',
                'invoices.paymentstatus',
                'invoices.project_id',
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
            $query->when(request('paymentstatus', false), function ($q, $paymentstatus) { 
                if ( 'unpaid' === $paymentstatus ) {
                    return $q->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->whereRaw('invoice_due_date >= DATE(NOW())');
                } if ( 'overdue' === $paymentstatus ) {
                    return $q->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->whereRaw('invoice_due_date < DATE(NOW())');
                } else {
                    return $q->where('paymentstatus', $paymentstatus );
                }
            });
            $query->when(request('currency_id', false), function ($q, $currency_id) { 
                return $q->where('currency_id', $currency_id);
            });
            $query->when(request('customer', false), function ($q, $customer) { 
                return $q->where('customer_id', $customer);
            });
            /// Custom Filters End.
            
            $query->where('project_id', '=', $project_id);
            $query->orderBy('id', 'desc');
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'invoice_';
                $routeKey = 'admin.invoices';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('customer.first_name', function ($row) {
                return $row->customer_id ? '<a href="'.route('admin.contacts.show', $row->customer_id).'" title="'.$row->customer->name.'">' . $row->customer->name . '</a>' : '';
            });
            $table->editColumn('currency.name', function ($row) {
                return $row->currency ? $row->currency->name : '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('address', function ($row) {
                return $row->address ? $row->address : '';
            });
            $table->editColumn('invoice_prefix', function ($row) {
                return $row->invoice_prefix ? $row->invoice_prefix : '';
            });
            $table->editColumn('show_quantity_as', function ($row) {
                return $row->show_quantity_as ? $row->show_quantity_as : '';
            });
            $table->editColumn('invoice_no', function ($row) {
                return $row->invoice_no ? '<a href="'.route('admin.invoices.show', $row->id).'" title="'.$row->invoice_no.'">' . $row->invoice_no . '</a>' : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });
            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : '';
            });
            $table->editColumn('invoice_date', function ($row) {
                return $row->invoice_date ? digiDate($row->invoice_date) : '';
            });
            $table->editColumn('invoice_due_date', function ($row) {
                return $row->invoice_due_date ? digiDate($row->invoice_due_date) : '';
            });
            $table->editColumn('invoice_notes', function ($row) {
                return $row->invoice_notes ? $row->invoice_notes : '';
            });
            $table->editColumn('tax.name', function ($row) {
                return $row->tax ? $row->tax->name : '';
            });
            $table->editColumn('discount.name', function ($row) {
                return $row->discount ? $row->discount->name : '';
            });
            $table->editColumn('amount', function ($row) {
                return $row->amount ? digiCurrency( $row->amount, $row->currency_id ) : '';
            });
            $table->editColumn('paymentstatus', function ($row) {
                return $row->paymentstatus ? ucfirst( $row->paymentstatus ) : '';
            });

            $table->rawColumns(['actions','massDelete', 'invoice_no', 'customer.first_name']);
            return $table->make(true);
        }

        return view('admin.client_projects.operations.invoices', compact('project'));
    }

    public function invoiceProject( $project_id ) {
        $project = ClientProject::find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $tasks = ProjectTask::where('project_id', '=', $project_id)->where('billable', '=', 'yes')->where('billed', '=', 'no')->get();
        $expenses = \App\Expense::where('project_id', '=', $project_id)->where('billable', '=', 'yes')->where('billed', '=', 'no')->get();

        return view('admin.client_projects.operations.invoice-project', compact('project', 'tasks', 'expenses'));
    }

    public function invoiceProjectPreview( StoreInvoiceProjectsRequest $request, $project_id ) {
        $project = ClientProject::with('client')->find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $type  = $request->invoice_data_type;
        $line_items = [];
        if ( empty( $type ) ) {
            $type = 'single_line';
        }
        $tasks = $request->tasks;
        
        $total_products_amount = 0;
        $sub_total = 0;
        $grand_total = 0;
        if ( ! empty( $tasks ) ) {
            $line_items['project_id'] = $project_id;
            $line_items['total_discount'] = 0;
            $line_items['total_tax'] = 0;
            $line_items['products_amount'] = 0;
            $line_items['sub_total'] = 0;
            $line_items['grand_total'] = 0; 

            if ($type == 'single_line') {       
                $line_items['product_name'][] = $project->title;

                $product_qty = 1;
                $product_price = $project->budget;
                foreach ($tasks as $task_id) {
                    $task = ProjectTask::find( $task_id );
                    $sec  = ProjectTask::taskTotalTime($task_id);
                    $line_items['product_description'][] = $task->project->title . ' - ' . seconds_to_time_format( $sec );
                    $line_items['task_id'][] = $task_id;
                    $line_items['unit'][]    = '';
                    if ($project->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS) {
                        if ($sec < 60) {
                            $sec = 0;
                        }
                        $line_items['product_qty'][] = floatVal(secondsToQuantity($sec));
                        $product_qty = floatVal(secondsToQuantity($sec));
                    }
                }
                if ($project->billing_type_id == PROJECT_BILLING_TYPE_FIXED_PRICE) {
                    $line_items['product_qty'][]  = 1;
                    $line_items['product_price'][] = $project->budget;
                } elseif ($project->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS) {
                    $line_items['product_price'][] = $project->project_rate_per_hour;
                    $product_price = $project->project_rate_per_hour;
                }
                $product_amount = $product_qty * $product_price;
                $line_items['product_amount'][] = $product_amount;
                $line_items['product_subtotal'][] = $product_amount;
                $total_products_amount += $product_amount;
                $grand_total += $product_amount;
                $sub_total +=  $product_amount;

                $line_items['product_tax'][] = 0;
                $line_items['tax_type'][] = 0;
                $line_items['tax_value'][] = 0;
                $line_items['product_discount'][] = 0;
                $line_items['discount_type'][] = 0;
                $line_items['discount_value'][] = 0;
                
                $line_items['pid'][] = $task_id;
                
                $line_items['hsn'][] = 0;
                $line_items['alert'][] = 0;
                $line_items['stock_quantity'][] = 0;
                $line_items['product_ids'][] = $task_id;
                $line_items['product_type'][] = 'task';             
            } elseif ($type == 'task_per_item') {
                $product_qty = 1;
                $product_price = $project->budget;
                foreach ($tasks as $task_id) {
                    $task                     = ProjectTask::find( $task_id );
                    $sec                      = ProjectTask::taskTotalTime($task_id);
                    
                    $product_qty = floatVal(secondsToQuantity($sec));
                    $line_items['product_name'][] = $project->title . ' - ' . $task->name;
                    $line_items['product_qty'][]              = $product_qty;
                    $line_items['product_description'][]      = trans('custom.messages.product_description_hours', [ 'hours' => $product_qty ] );
                    
                    
                    if ($project->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS) {
                        $line_items['product_price'][] = $project->project_rate_per_hour;
                        $product_price = $project->project_rate_per_hour;
                    } elseif ($project->billing_type_id == PROJECT_BILLING_TYPE_TASK_HOURS) {
                        $line_items['product_price'][] = $task->hourly_rate;
                        $product_price = $task->hourly_rate;
                    }
                    $line_items['task_id'][] = $task_id;
                    $line_items['unit'][]    = '';

                    $product_amount = $product_qty * $product_price;
                    
                    $line_items['product_amount'][] = $product_amount;
                    $line_items['product_subtotal'][] = $product_amount;
                    $total_products_amount += $product_amount;
                    $grand_total += $product_amount;
                    $sub_total +=  $product_amount;
                    
                    $line_items['product_tax'][] = 0;
                    $line_items['tax_type'][] = 0;
                    $line_items['tax_value'][] = 0;
                    $line_items['product_discount'][] = 0;
                    $line_items['discount_type'][] = 0;
                    $line_items['discount_value'][] = 0;
                    
                    $line_items['pid'][] = $task_id;
                    
                    $line_items['hsn'][] = 0;
                    $line_items['alert'][] = 0;
                    $line_items['stock_quantity'][] = 0;
                    $line_items['product_ids'][] = $task_id;
                    $line_items['product_type'][] = 'task';
                }
                
            } elseif ($type == 'timesheets_include_notes') {
                $product_qty = 1;
                $product_price = $project->budget;

                $time_entries     = ProjectTask::getTimeEntries($project_id, $tasks);

                $added_task_ids = [];
                foreach ($time_entries as $time_entry) {

                    if ($time_entry->task_data->billed == 'no' && $time_entry->task_data->billable == 'yes') {
                        $line_items['product_name'][] = $project->title . ' - ' . $time_entry->task_data->name;

                        $product_qty = floatVal(secondsToQuantity($time_entry->total_spent));
 

                        $end_date = $time_entry->end_date;
                        if ( empty( $end_date ) ) {
                            $end_date = date('Y-m-d');
                        }
                        $line_items['product_description'][] = trans('global.project-tasks.fields.startdate') . ':' . digiDate($time_entry->start_date) . "\r\n" . trans('global.project-tasks.fields.datefinished') . ':' . digiDate($end_date) . "\r\n" . trans('global.project-tasks.total-logged-time', ['hours' => $product_qty]);
                        if (!in_array($time_entry->task_id, $added_task_ids)) {
                            $line_items['task_id'][] = $time_entry->task_id;
                        }

                        array_push($added_task_ids, $time_entry->task_id);

                        $line_items['product_qty'][]              = $product_qty;
                        
                        
                        if ($project->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS) {
                            $line_items['product_price'][] = $project->project_rate_per_hour;
                            $product_price = $project->project_rate_per_hour;
                        } elseif ($project->billing_type_id == PROJECT_BILLING_TYPE_TASK_HOURS) {
                            $line_items['product_price'][] = $time_entry->task_data->hourly_rate;
                            $product_price = $time_entry->task_data->hourly_rate;
                        }
                        $line_items['unit'][] = '';
                        
                        $product_amount = $product_qty * $product_price;
                        $line_items['product_amount'][] = $product_amount;
                        $line_items['product_subtotal'][] = $product_amount;
                        $total_products_amount += $product_amount;
                        $grand_total += $product_amount;
                        $sub_total +=  $product_amount;
                        
                        $line_items['product_tax'][] = 0;
                        $line_items['tax_type'][] = 0;
                        $line_items['tax_value'][] = 0;
                        $line_items['product_discount'][] = 0;
                        $line_items['discount_type'][] = 0;
                        $line_items['discount_value'][] = 0;
                        
                        $line_items['pid'][] = $time_entry->task_id;
                        
                        $line_items['hsn'][] = 0;
                        $line_items['alert'][] = 0;
                        $line_items['stock_quantity'][] = 0;
                        $line_items['product_ids'][] = $time_entry->task_id;
                        $line_items['product_type'][] = 'task';
                    }
                }
            }

            $line_items['total_discount'] = 0;
            $line_items['total_tax'] = 0;
            $line_items['products_amount'] = $total_products_amount;
            $line_items['sub_total'] = $sub_total;
            $line_items['grand_total'] = $grand_total;
        }

        $expenses = $request->expenses;
        if ( ! empty( $expenses ) ) {
            foreach ($expenses as $expense_id) {
                $expense = \App\Expense::find( $expense_id );
                if ( $expense ) {
                    $line_items['product_name'][]      = $expense->name;
                    $line_items['product_description'][]      = $expense->name;
                    $line_items['product_qty'][]              = 1;
                    $line_items['expense_id'][] = $expense_id;
                    $product_amount = $expense->amount;
                    $total_products_amount += $product_amount;
                    $grand_total += $product_amount;
                    $sub_total +=  $product_amount;

                    $line_items['product_price'][] = $product_amount;
                    $line_items['product_amount'][] = $product_amount;
                    $line_items['product_subtotal'][] = $product_amount;
                    $line_items['unit'][] = '';
                    $line_items['product_tax'][] = 0;
                    $line_items['tax_type'][] = 0;
                    $line_items['tax_value'][] = 0;
                    $line_items['product_discount'][] = 0;
                    $line_items['discount_type'][] = 0;
                    $line_items['discount_value'][] = 0;
                    $line_items['pid'][] = $expense_id;
                    $line_items['hsn'][] = 0;
                    $line_items['alert'][] = 0;
                    $line_items['stock_quantity'][] = 0;
                    $line_items['product_ids'][] = $expense_id;
                    $line_items['product_type'][] = 'expense';
                }
            }

            $line_items['total_discount'] = 0;
            $line_items['total_tax'] = 0;
            $line_items['products_amount'] = $total_products_amount;
            $line_items['sub_total'] = $sub_total;
            $line_items['grand_total'] = $grand_total;
        }


        $customers = \App\Contact::where('id', '=', $project->client_id)->get()->pluck('name', 'id');
        $customer_currency = $project->client->currency_id;
        if ( ! empty( $customer_currency ) ) {
            $currencies = \App\Currency::where('id', '=', $customer_currency)->get()->pluck('name', 'id');
        } else {
            $currencies = \App\Currency::get()->pluck('name', 'id');
        }
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = Invoice::$enum_status;
        $enum_discounts_format = Invoice::$enum_discounts_format;
        $enum_tax_format = Invoice::$enum_tax_format;
        $sale_agents = \App\Contact::whereHas("contact_type",
            function ($query) {
                $query->where('id', CONTACT_SALE_AGENT);
            })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $products_return = (Object)[ 'products' => json_encode($line_items)];
        $products_return->project_id = $project->id;
        $products_return->currency_id = $project->currency_id;
        $project->products = json_encode($line_items);

        if ( empty( $line_items['product_name'] ) ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return back();
        }
        return view('admin.client_projects.operations.invoice-project-preview', compact('project', 'customers', 'currencies', 'taxes', 'discounts', 'enum_status', 'enum_discounts_format', 'enum_tax_format', 'sale_agents', 'products_return'));
    }

    public function invoiceProjectEdit( $project_id, $id ) {
        $project = ClientProject::with('client')->find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $invoice = Invoice::find( $id );

        $customers = \App\Contact::where('id', '=', $project->client_id)->get()->pluck('name', 'id');
        $customer_currency = $project->client->currency_id;
        if ( ! empty( $customer_currency ) ) {
            $currencies = \App\Currency::where('id', '=', $customer_currency)->get()->pluck('name', 'id');
        } else {
            $currencies = \App\Currency::get()->pluck('name', 'id');
        }
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = Invoice::$enum_status;
        $enum_discounts_format = Invoice::$enum_discounts_format;
        $enum_tax_format = Invoice::$enum_tax_format;
        $sale_agents = \App\Contact::whereHas("contact_type",
            function ($query) {
                $query->where('id', CONTACT_SALE_AGENT);
            })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $products_return = $invoice;
        $operation = 'edit';

        return view('admin.client_projects.operations.invoice-project-preview', compact('project', 'customers', 'currencies', 'taxes', 'discounts', 'enum_status', 'enum_discounts_format', 'enum_tax_format', 'sale_agents', 'products_return', 'operation', 'invoice'));
    }

    public function invoiceProjectStore( StoreInvoiceProjectsSaveRequest $request, $project_id, $id = '' ) {
        $products_details = getProductDetails( $request );

        $tax_format = $request->tax_format;
        $discount_format =  $request->discount_format;

        $products_details['project_id'] = $project_id;
        $products_details['discount_format'] = $discount_format;
        $products_details['tax_format'] = $tax_format;

        // These are product values.
        $grand_total = ! empty( $products_details['grand_total'] ) ? $products_details['grand_total'] : 0;
        $products_amount = ! empty( $products_details['products_amount'] ) ? $products_details['products_amount'] : 0;
        $total_tax = ! empty( $products_details['total_tax'] ) ? $products_details['total_tax'] : 0;
        $total_discount = ! empty( $products_details['total_discount'] ) ? $products_details['total_discount'] : 0;

        // Calculation of Cart Tax.
        $tax_id = $request->tax_id;
        $cart_tax = 0;    
        if ( $tax_id > 0 ) {
            
            $invoice->setTaxIdAttribute( $tax_id );
            $tax = $invoice->tax()->first();
            $rate = 0;
            $rate_type = 'percent';
            if ( $tax ) {
                $rate = $tax->rate;
                $rate_type = $tax->rate_type;
            }
            $products_details['cart_tax_rate'] = $rate;
            $products_details['cart_tax_rate_type'] = $rate_type;

            if ( $rate > 0 ) {
                if ( 'before_tax' === $tax_format ) {
                    if ( 'percent' === $rate_type ) {
                        $cart_tax = ( $products_amount * $rate) / 100;
                    } else {
                        $cart_tax = $rate;
                    }                    
                } else {
                    $new_amount = $products_amount + $total_tax;
                    if ( 'percent' === $rate_type ) {
                        $cart_tax = ( $new_amount * $rate) / 100;
                    } else {
                        $cart_tax = $rate;
                    }
                }
            } 
        }

        // Let us calculate Cart Discount
        $cart_discount = 0;
        $discount_id = $request->discount_id;
        if ( $discount_id > 0 ) {
            $invoice->setDiscountIdAttribute( $discount_id );
            $discount = $invoice->discount()->first();

            $rate = 0;
            $rate_type = 'percent';
            if ( $discount ) {
                $rate = $discount->discount;
                $rate_type = $discount->discount_type;
            }
            $products_details['cart_discount_rate'] = $rate;
            $products_details['cart_discount_rate_type'] = $rate_type;
            if ( $rate > 0 ) {
                if ( 'before_tax' === $tax_format ) {
                    if ( 'percent' === $rate_type ) {
                        $cart_discount = ( $products_amount * $rate) / 100;
                    } else {
                        $cart_discount = $rate;
                    }                    
                } else {
                    $new_amount = $products_amount + $total_tax;
                    if ( 'percent' === $rate_type ) {
                        $cart_discount = ( $new_amount * $rate) / 100;
                    } else {
                        $cart_discount = $rate;
                    }
                }
            } 
        }

        $products_details['cart_tax'] = $cart_tax;
        $products_details['cart_discount'] = $cart_discount;
        $amount_payable = $grand_total + $cart_tax - $cart_discount;
        $products_details['amount_payable'] = $amount_payable;

        
        $addtional = array(
            'products' => json_encode( $products_details ),
            'amount' => $amount_payable,
            'project_id' => $project_id,
        );

        $invoice_no = $request->invoice_no;
        if ( empty( $invoice_no ) ) {            
            $invoice_no = getNextNumber();
        }
        $addtional['invoice_no'] = $invoice_no;
        $addtional['slug'] = md5(microtime() . rand());
        $addtional['created_by_id'] = Auth::id();
        // if there are transactions for this customer. Currency selection may disable, so we need to get it from customer profile.
        $currency_id = $request->currency_id;
        if ( empty( $currency_id ) ) {
            $currency_id = getDefaultCurrency( 'id', $request->customer_id );
        }
        $addtional['currency_id'] = $currency_id;
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

        $date_set = getCurrentDateFormat();

        $additional = array(           
            'invoice_date' => ! empty( $request->invoice_date ) ? Carbon::createFromFormat($date_set, $request->invoice_date)->format('Y-m-d') : NULL,
            'invoice_due_date' => ! empty( $request->invoice_due_date ) ? Carbon::createFromFormat($date_set, $request->invoice_due_date)->format('Y-m-d') : NULL,
        );
        $additional['invoice_number_format'] = getSetting( 'invoice-number-format', 'invoice-settings', 'numberbased' );
        $additional['invoice_number_separator'] = getSetting( 'invoice-number-separator', 'invoice-settings', '-' );
        $additional['invoice_number_length'] = getSetting( 'invoice-number-length', 'invoice-settings', '0' );
        $request->request->add( $additional );

        if ( isDemo() ) {
            return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        if ( 'edit' === $request->operation ) {
            $invoice = Invoice::find( $id );

            // Let us change previous task status to unbilled, cause if admin can change or remove them from this invoice!
            $attached_tasks = ! empty( $invoice ) ? $invoice->attached_tasks( $id ) : [];
            if ( ! empty( $attached_tasks ) ) {
               $ids = Arr::pluck($attached_tasks, 'task_id');
                \App\ProjectTask::whereIn('id', $ids)->update(['billed' => 'no']); 
            }
            $attached_expenses = ! empty( $invoice ) ? $invoice->attached_expenses( $id ) : [];
            if ( ! empty( $attached_expenses ) ) {
               $ids = Arr::pluck($attached_expenses, 'expense_id');
                \App\Expense::whereIn('id', $ids)->update(['billed' => 'no']); 
            }
            $invoice->update($request->all());
            $this->insertHistory( array('id' => $invoice->id, 'comments' => 'invoice-updated-from-project', 'operation_type' => 'crud' ) );
        } else {            
            $invoice = Invoice::create($request->all());
            $this->insertHistory( array('id' => $invoice->id, 'comments' => 'invoice-created-from-project', 'operation_type' => 'crud' ) );
        }

        $products_sync = ! empty( $products_details['products_sync'] ) ? $products_details['products_sync'] : array();
        $invoice->invoice_products()->sync( $products_sync );

        $products_sync_tasks = ! empty( $products_details['products_sync_tasks'] ) ? $products_details['products_sync_tasks'] : array();
        $invoice->invoice_products_tasks()->sync( $products_sync_tasks );

        $products_sync_expenses = ! empty( $products_details['products_sync_expenses'] ) ? $products_details['products_sync_expenses'] : array();
        $invoice->invoice_products_expenses()->sync( $products_sync_expenses );

        // Let us change tasks status to complete and they billed.
        if ( ! empty( $products_sync_tasks ) ) {
            $ids = Arr::pluck($products_sync_tasks, 'task_id');
            \App\ProjectTask::whereIn('id', $ids)->update(['billed' => 'yes', 'status' => STATUS_COMPLETED]);
        }

        if ( ! empty( $products_sync_expenses ) ) {
            $ids = Arr::pluck($products_sync_expenses, 'expense_id');
            \App\Expense::whereIn('id', $ids)->update(['billed' => 'yes', 'invoice_id' => $invoice->id]);
        }
        // Let us check if all tasks completed so that we may change project status to completed.
        $total_project_tasks = \App\ProjectTask::where('project_id', '=', $project_id)->count();
        $total_finished_tasks = \App\ProjectTask::where('project_id', '=', $project_id)->where('status', '=', STATUS_COMPLETED)->count();
        if ($total_finished_tasks >= floatval($total_project_tasks)) {
            $project = ClientProject::find( $project_id );
            if ( $project ) {
                $project->status_id = PROJECT_STATUS_COMPLETED;
                $project->date_finished = date('Y-m-d');
                $project->progress = 100;
                $project->save();
            }
        }
        $invoice->allowed_paymodes()->sync(array_filter((array)$request->input('allowed_paymodes')));

        $customer = $invoice->customer()->first();
        if ( ! empty( $request->btnsavesend ) && $customer && 'Published' === $invoice->status ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'client_name' => $customer->name,
                'content' => 'Invoice has been created',
                'invoice_url' => route( 'admin.invoices.preview', [ 'slug' => $invoice->slug ] ),
                'invoice_no' => $invoice->invoice_no,
                'invoice_amount' => $invoice->currencyamount,
                'invoice_date' => digiDate($invoice->invoice_date),
                'invoice_due_date' => digiDate($invoice->invoice_due_date),
                'title' => $invoice->title,
                'address' => $invoice->address,
                'reference' => $invoice->reference,
                'invoice_notes' => $invoice->invoice_notes,
                'customer_id' => $invoice->customer_id,
                'currency_id' => $invoice->currency_id,
                'sale_agent' => $invoice->sale_agent,
                'tax_id' => $invoice->tax_id,
                'discount_id' => $invoice->discount_id,
                'paymentstatus' => $invoice->paymentstatus,
                'created_by_id' => $invoice->created_by_id,


                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),                
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'site_url' => env('APP_URL'),
            );

            if ( $invoice->customer->name ) {
                $templatedata['customer_id'] = $invoice->customer->name;
            }

             if ( $invoice->saleagent->name ) {
                $templatedata['sale_agent'] = $invoice->saleagent->name;
            }
            
            if ( $invoice->currency->name ) {
                $templatedata['currency_id'] = $invoice->currency->name;
            }
            
            if ( $invoice->tax->name ) {
                $templatedata['tax_id'] = $invoice->tax->name;
            }
            
            if ( $invoice->discount->name ) {
                $data['discount_id'] = $invoice->discount->name;
            }
            
            $createduser = \App\User::find( $invoice->created_by_id );
            if ( $createduser ) {
                $data['created_by_id'] = $createduser->name;
            }

            $data = [
                "action" => "Created",
                "crud_name" => "User",
                'template' => 'invoice-created',
                'model' => 'App\Invoices',
                'data' => $templatedata,
            ];
            $customer->notify(new QA_EmailNotification($data));
        }

        if ( 'edit' === $request->operation ) {
            flashMessage( 'success', 'update');
        } else {
            flashMessage( 'success', 'create');
        }

        if ( ! empty( $request->btnsavemanage ) ) {
            return redirect()->route('admin.invoices.show', $invoice->id );
        } else {
            return redirect()->route('admin.client_projects.invoices', $project_id);
        }
        
    }

    private function insertHistory( $data ) {
        $ip_address = GetIP();
        $position = Location::get( $ip_address );

        $id = ! empty( $data['id'] ) ? $data['id'] : 0;
        $comments = ! empty( $data['comments'] ) ? $data['comments'] : 0;
        $operation_type = ! empty( $data['operation_type'] ) ? $data['operation_type'] : 'general';

         $city = ! empty( $position->cityName ) ? $position->cityName : '';
        if ( ! empty( $position->regionName ) ) {
            $city .= ' ' . $position->regionName;
        }
        if ( ! empty( $position->zipCode ) ) {
            $city .= ' ' . $position->zipCode;
        }

        $log = array(
            'ip_address' => $ip_address,
            'country' => ! empty( $position->countryName ) ? $position->countryName : '',
            'city' => $city,
            'browser' => ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'Cron job',
            'invoice_id' => $id,
            'comments' => $comments,
            'operation_type' => $operation_type,
        );
        \App\InvoicesHistory::create( $log );
    }


     public function refreshStats() {
        if (request()->ajax()) {
            $currency = request('currency');

            return view('admin.client_projects.canvas.canvas-panel-body', ['currency_id' => $currency]);
        }
    }

    public function expenses( $project_id ) {
        if (! Gate::allows('expense_access')) {
            return prepareBlockUserMessage();
        }

        $project = ClientProject::with('client')->find( $project_id );

        if (! $project) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        // Total Expense.
        $total_expense = Expense::all()->sum('amount');

        // This month Expense.
        $today = Carbon::today();
        $date_from = $from = date("Y-m-01", strtotime($today));
        $date_to = $to = date("Y-m-t", strtotime($today));
        $total_expense_current_month = Expense::where('created_at', '>=', $date_from)->where('created_at', '<=', $date_to)->sum('amount');

        // This week Expense.
        
        $date_from = Carbon::today()->startOfWeek();
        $date_to = Carbon::today()->endOfWeek();
        
        $total_expense_current_week = Expense::where('created_at', '>=', $date_from)->where('created_at', '<=', $date_to)->sum('amount');

        // This Last 30 Days.
        $today = Carbon::today();
        $date_from = Carbon::today()->subDays(30);
        $total_expense_last_30_days = Expense::whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $today)->sum('amount');

        // Expenses Graph               
        $reportTitle_expense = 'Expenses Report';
        $reportLabel_expense = 'SUM';
        $chartType_expense   = 'bar';

        $results_expense = Expense::whereBetween('entry_date', [$from, $to])->get()->sortBy('entry_date')->groupBy(function ($entry) {
            if ($entry->entry_date instanceof \Carbon\Carbon) {
                return \Carbon\Carbon::parse($entry->entry_date)->format('Y-m-d');
            }
            try {
               return \Carbon\Carbon::createFromFormat(config('app.date_format'), digiDate( $entry->entry_date ) )->format('Y-m-d');
            } catch (\Exception $e) {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', digiDate( $entry->entry_date) )->format('Y-m-d');
            }        })->map(function ($entries, $group) {
            return $entries->sum('amount');
        });

        return view('admin.client_projects.operations.expenses', compact('total_expense', 'total_expense_current_month', 'total_expense_current_week',
            'total_expense_last_30_days',
            'reportTitle_expense', 'results_expense', 'chartType_expense', 'reportLabel_expense', 'project', 'project_id'
        ));
    }
}
