<?php

namespace Modules\InvoiceAdditional\Http\Controllers\Admin;

use Modules\InvoiceAdditional\Entities\InvoiceReminder;
use App\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\InvoiceAdditional\Http\Requests\Admin\StoreInvoiceRemindersRequest;
use Modules\InvoiceAdditional\Http\Requests\Admin\UpdateInvoiceRemindersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Auth;
class InvoiceRemindersController extends Controller
{
    /**
     * Display a listing of InvoiceReminder.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $invoice_id )
    {
        if (! Gate::allows('invoice_reminder_access')) {
            return abort(401);
        }

        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }


        
        if (request()->ajax()) {
            $query = InvoiceReminder::query();
            $query->with("invoice");
            $query->with("reminder_to");
            $query->with("created_by");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('invoice_reminder_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'invoice_reminders.id',
                'invoice_reminders.description',
                'invoice_reminders.date',
                'invoice_reminders.isnotified',
                'invoice_reminders.invoice_id',
                'invoice_reminders.reminder_to_id',
                'invoice_reminders.notify_by_email',
                'invoice_reminders.created_by_id',
            ]);
            $query->where('invoice_id', '=', $invoice_id );
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'invoice_reminder_';
                $routeKey = 'admin.invoice_reminders';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('date', function ($row) {
                return $row->date ? $row->date : '';
            });
            $table->editColumn('isnotified', function ($row) {
                return $row->isnotified ? $row->isnotified : '';
            });
            $table->editColumn('invoice.title', function ($row) {
                return $row->invoice ? $row->invoice->title : '';
            });
               $table->editColumn('reminder_to.name', function ($row) {
                $name = $row->reminder_to ? $row->reminder_to->name : '';
              
                $img_url = asset('images/avatar-32x32.jpg');
                if ( ! empty( $row->reminder_to->thumbnail ) && file_exists(public_path() . 'thumb/' . $row->reminder_to->thumbnail) ) {
                    $img_url = asset(env('UPLOAD_PATH').'/thumb/'.$row->reminder_to->thumbnail);
                }
                $img = '<img src="'.$img_url.'" class="profile-image-small" alt="'.$name.'">';
                $name = '<a href="'.route('admin.users.show', $row->reminder_to ).'">'.$img . $name.'</a>';
                return $name;
            });
            $table->editColumn('notify_by_email', function ($row) {
                return $row->notify_by_email ? $row->notify_by_email : '';
            });
            $table->editColumn('created_by.name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions','massDelete','reminder_to.name']);

            return $table->make(true);
        }

        return view('invoiceadditional::admin.invoice_reminders.index', compact('invoice'));
    }

    /**
     * Show the form for creating new InvoiceReminder.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $invoice_id )
    {
        if (! Gate::allows('invoice_reminder_create')) {
            return abort(401);
        }

        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $invoices = \App\Invoice::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

    

        $reminder_tos = \App\User::whereHas("role",
                   function ($query) {
                       $query->where('title', ROLE_EMPLOYEE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_isnotified = InvoiceReminder::$enum_isnotified;
        $enum_notify_by_email = InvoiceReminder::$enum_notify_by_email;
            
        return view('invoiceadditional::admin.invoice_reminders.create', compact('enum_isnotified', 'enum_notify_by_email', 'invoices', 'reminder_tos', 'created_bies', 'invoice'));
    }

    /**
     * Store a newly created InvoiceReminder in storage.
     *
     * @param  \App\Http\Requests\StoreInvoiceRemindersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvoiceRemindersRequest $request, $invoice_id)
    {
        if (! Gate::allows('invoice_reminder_create')) {
            return abort(401);
        }
        $addtional = array(
            'invoice_id' => $invoice_id,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_reminder = InvoiceReminder::create($request->all());

        flashMessage( 'success', 'update');

        $employee = $invoice_reminder->reminder_to()->first();

        if (  'yes' === $request->notify_by_email ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'reminder_to' => $invoice_reminder->reminder_to->name,
                'date' => digiDate($invoice_reminder->date),
                'content' => 'Reminder',
                'description' => $invoice_reminder->description,

                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),                
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'site_url' => env('APP_URL'),
            
            );
            
            $data = [
                "action" => "Created",
                "crud_name" => "User",
                'template' => 'invoice-reminder-employee',
                'model' => 'Modules\InvoiceAdditional\Entities\InvoiceReminder',
                'data' => $templatedata,
            ];
            
            $employee->notify(new QA_EmailNotification($data));
        }

        return redirect()->route('admin.invoice_reminders.index', $invoice_id);
    }


    /**
     * Show the form for editing InvoiceReminder.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($invoice_id, $id)
    {
        if (! Gate::allows('invoice_reminder_edit')) {
            return abort(401);
        }

        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $invoices = \App\Invoice::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');


        $reminder_tos = \App\User::whereHas("role",
                   function ($query) {
                       $query->where('title', ROLE_EMPLOYEE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_isnotified = InvoiceReminder::$enum_isnotified;
                    $enum_notify_by_email = InvoiceReminder::$enum_notify_by_email;
            
        $invoice_reminder = InvoiceReminder::findOrFail($id);

        return view('invoiceadditional::admin.invoice_reminders.edit', compact('invoice_reminder', 'enum_isnotified', 'enum_notify_by_email', 'invoices', 'reminder_tos', 'created_bies', 'invoice'));
    }

    /**
     * Update InvoiceReminder in storage.
     *
     * @param  \App\Http\Requests\UpdateInvoiceRemindersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvoiceRemindersRequest $request, $invoice_id, $id)
    {
        if (! Gate::allows('invoice_reminder_edit')) {
            return abort(401);
        }
        $invoice_reminder = InvoiceReminder::findOrFail($id);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_reminder->update($request->all());

        flashMessage( 'success', 'update');

        
        $employee = $invoice_reminder->reminder_to()->first();

        if (  'yes' === $request->notify_by_email ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'reminder_to' => $invoice_reminder->reminder_to->name,
                'date' => digiDate($invoice_reminder->date),
                'content' => 'Reminder',
                'description' => $invoice_reminder->description,

                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),                
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'site_url' => env('APP_URL'),
            
            );
            
            $data = [
                "action" => "Created",
                "crud_name" => "User",
                'template' => 'invoice-reminder-employee',
                'model' => 'Modules\InvoiceAdditional\Entities\InvoiceReminder',
                'data' => $templatedata,
            ];
          
            $employee->notify(new QA_EmailNotification($data));
        }


        return redirect()->route('admin.invoice_reminders.index', $invoice_id);
    }


    /**
     * Display InvoiceReminder.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_id, $id)
    {
        if (! Gate::allows('invoice_reminder_view')) {
            return abort(401);
        }

        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $invoice_reminder = InvoiceReminder::with('created_by')->findOrFail($id);

        return view('invoiceadditional::admin.invoice_reminders.show', compact('invoice_reminder', 'invoice'));
    }


    /**
     * Remove InvoiceReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $invoice_id, $id)
    {
        if (! Gate::allows('invoice_reminder_delete')) {
            return abort(401);
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_reminder = InvoiceReminder::findOrFail($id);
        $invoice_id = $invoice_reminder->invoice_id;
        $invoice_reminder->delete();

        flashMessage( 'success', 'delete');

        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
    }

    /**
     * Delete all selected InvoiceReminder at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('invoice_reminder_delete')) {
            return abort(401);
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = InvoiceReminder::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes');
        }
    }


    /**
     * Restore InvoiceReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('invoice_reminder_delete')) {
            return abort(401);
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_reminder = InvoiceReminder::onlyTrashed()->findOrFail($id);
        $invoice_id = $invoice_reminder->invoice_id;
        $invoice_reminder->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete InvoiceReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('invoice_reminder_delete')) {
            return abort(401);
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_reminder = InvoiceReminder::onlyTrashed()->findOrFail($id);
        $invoice_id = $invoice_reminder->invoice_id;
        $invoice_reminder->forceDelete();

        flashMessage( 'success', 'delete');

        return back();
    }
}
