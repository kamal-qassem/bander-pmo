<?php

namespace Modules\Contracts\Http\Controllers\Admin;

use Modules\Contracts\Entities\Contract;
use Modules\Contracts\Entities\ContractsReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Contracts\Http\Requests\Admin\StoreContractsRemindersRequest;
use Modules\Contracts\Http\Requests\Admin\UpdateContractsRemindersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Str;
class ContractsRemindersController extends Controller
{
    /**
     * Display a listing of ContractsReminder.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $contract_id )
    {
        
        if (! Gate::allows('contract_reminder_access')) {
        return prepareBlockUserMessage();
        }

        $contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }


        
        if (request()->ajax()) {
            $query = ContractsReminder::query();
            $query->with("contract");
            $query->with("reminder_to");
            $query->with("created_by");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('contract_reminder_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'contracts_reminders.id',
                'contracts_reminders.description',
                'contracts_reminders.date',
                'contracts_reminders.isnotified',
                'contracts_reminders.contract_id',
                'contracts_reminders.reminder_to_id',
                'contracts_reminders.notify_by_email',
                'contracts_reminders.created_by_id',
            ]);
            $query->where('contract_id', '=', $contract_id );
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'contract_reminder_';
                $routeKey = 'admin.contract_reminders';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('description', function ($row) {
                $description = $row->description ? $row->description : '';
                if ( ! empty( $description ) ) {
                    $description = Str::limit($description, 40, '<a href="'.route('admin.contract_reminders.show', ['contract_id' => $row->contract_id, 'id' => $row->id]).'">...</a>');
                }
                return $description;
            });
            $table->editColumn('date', function ($row) {
                return $row->date ? $row->date : '';
            });
            $table->editColumn('isnotified', function ($row) {
                return $row->isnotified ? ucfirst( $row->isnotified ) : '';
            });
            $table->editColumn('contract.title', function ($row) {
                return $row->contract ? $row->contract->title : '';
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
                return $row->notify_by_email ? ucfirst( $row->notify_by_email ) : '';
            });
            $table->editColumn('created_by.name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions','massDelete', 'reminder_to.name', 'description']);

            return $table->make(true);
        }

        return view('contracts::admin.contracts.contracts_reminders.index', compact('contract'));
    }

    /**
     * Show the form for creating new ContractsReminder.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $contract_id )
    {
        
       if (! Gate::allows('contract_reminder_create')) {
            return prepareBlockUserMessage();
        }

        $contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $contracts = Contract::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $reminder_tos = \App\User::whereHas("role",
                   function ($query) {
                       $query->where('title', ROLE_EMPLOYEE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_isnotified = ContractsReminder::$enum_isnotified;
        $enum_notify_by_email = ContractsReminder::$enum_notify_by_email;
            
        return view('contracts::admin.contracts.contracts_reminders.create', compact('enum_isnotified', 'enum_notify_by_email', 'contracts', 'reminder_tos', 'created_bies', 'contract'));
    }

    /**
     * Store a newly created ContractsReminder in storage.
     *
     * @param  \App\Http\Requests\StoreContractsRemindersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContractsRemindersRequest $request, $contract_id)
    {

        if (! Gate::allows('contract_reminder_create')) {
        return prepareBlockUserMessage();
        }

        $addtional = array(
            'contract_id' => $contract_id,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $contracts_reminder = ContractsReminder::create( $request->all() );

        flashMessage( 'success', 'create');

        $employee = $contracts_reminder->reminder_to()->first();

        if (  'yes' === $request->notify_by_email ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'reminder_to' => $contracts_reminder->reminder_to->name,
                'date' => digiDate($contracts_reminder->date),
                'content' => 'Reminder',
                'description' => $contracts_reminder->description,

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
                'template' => 'contract-reminder-employee',
                'model' => 'Modules\Contracts\Entities\ContractsReminder',
                'data' => $templatedata,
            ];
            
            $employee->notify(new QA_EmailNotification($data));
        }

        return redirect()->route('admin.contract_reminders.index', $contract_id);
    }


    /**
     * Show the form for editing ContractsReminder.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($contract_id, $id)
    {
        
        if (! Gate::allows('contract_reminder_edit')) {
            return prepareBlockUserMessage();
        }

        $contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $contracts = Contract::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $reminder_tos = \App\User::whereHas("role",
                   function ($query) {
                       $query->where('title', ROLE_EMPLOYEE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_isnotified = ContractsReminder::$enum_isnotified;
        $enum_notify_by_email = ContractsReminder::$enum_notify_by_email;
            
        $contracts_reminder = ContractsReminder::findOrFail($id);

        return view('contracts::admin.contracts.contracts_reminders.edit', compact('contracts_reminder', 'enum_isnotified', 'enum_notify_by_email', 'contracts', 'reminder_tos', 'created_bies', 'contract'));
    }

    /**
     * Update ContractsReminder in storage.
     *
     * @param  \App\Http\Requests\UpdateContractsRemindersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContractsRemindersRequest $request, $contract_id, $id)
    {
        if (! Gate::allows('contract_reminder_edit')) {
            return prepareBlockUserMessage();
        }
        $contracts_reminder = ContractsReminder::findOrFail($id);

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $contracts_reminder->update($request->all());

        flashMessage( 'success', 'update');

        $employee = $contracts_reminder->reminder_to()->first();

        if (  'yes' === $request->notify_by_email ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'reminder_to' => $contracts_reminder->reminder_to->name,
                'date' => digiDate($contracts_reminder->date),
                'content' => 'Reminder',
                'description' => $contracts_reminder->description,

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
                'template' => 'contract-reminder-employee',
                'model' => 'Modules\Contracts\Entities\ContractsReminder',
                'data' => $templatedata,
            ];
            $employee->notify(new QA_EmailNotification($data));
        }

        return redirect()->route('admin.contract_reminders.index', $contract_id);
    }


    /**
     * Display ContractsReminder.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($contract_id, $id)
    {

        if (! Gate::allows('contract_reminder_view')) {
            return prepareBlockUserMessage();
        }

        $contract = Contract::find( $contract_id );

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $contracts_reminder = ContractsReminder::findOrFail($id);

        return view('contracts::admin.contracts.contracts_reminders.show', compact('contracts_reminder', 'contract'));
    }


    /**
     * Remove ContractsReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($contract_id, $id)
    {

        if (! Gate::allows('contract_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $contracts_reminder = ContractsReminder::findOrFail($id);
        $contracts_reminder->delete();

        flashMessage( 'success', 'delete');

        return redirect()->route('admin.contract_reminders.index', $contract_id);
    }

    /**
     * Delete all selected ContractsReminder at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {

        if (! Gate::allows('contract_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ContractsReminder::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes');
        }
    }


    /**
     * Restore ContractsReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('contract_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contracts_reminder = ContractsReminder::onlyTrashed()->findOrFail($id);

        $contract_id = $contracts_reminder->contract_id;
        
        $contracts_reminder->restore();

        flashMessage( 'success', 'restore');

        return redirect()->route('admin.contract_reminders.index', $contract_id);
    }

    /**
     * Permanently delete ContractsReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('contract_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $contracts_reminder = ContractsReminder::onlyTrashed()->findOrFail($id);
        $contract_id = $contracts_reminder->contract_id;
        $contracts_reminder->forceDelete();

        flashMessage( 'success', 'delete');

        return redirect()->route('admin.contract_reminders.index', $contract_id);
    }
}
