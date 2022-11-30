<?php

namespace Modules\Proposals\Http\Controllers\Admin;

use Modules\Proposals\Entities\Proposal;
use Modules\Proposals\Entities\ProposalsReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Proposals\Http\Requests\Admin\StoreProposalsRemindersRequest;
use Modules\Proposals\Http\Requests\Admin\UpdateProposalsRemindersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Str;
class ProposalsRemindersController extends Controller
{
    /**
     * Display a listing of ProposalsReminder.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $proposal_id )
    {
        
        if (! Gate::allows('proposal_reminder_access')) {
        return prepareBlockUserMessage();
        }


        $proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
           
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }


       
        if (request()->ajax()) {
        
            $query = ProposalsReminder::query();
            $query->with("proposal");
            $query->with("reminder_to");
            $query->with("created_by");
            $template = 'actionsTemplate';
     
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('proposal_reminder_delete')) {
            return abort(401);
        }

                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }

            $query->select([
                'proposals_reminders.id',
                'proposals_reminders.description',
                'proposals_reminders.date',
                'proposals_reminders.isnotified',
                'proposals_reminders.proposal_id',
                'proposals_reminders.reminder_to_id',
                'proposals_reminders.notify_by_email',
                'proposals_reminders.created_by_id',
            ]);
            $query->where('proposal_id', '=', $proposal_id );
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'proposal_reminder_';
                $routeKey = 'admin.proposal_reminders';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('description', function ($row) {
                $description = $row->description ? $row->description : '';
                if ( ! empty( $description ) ) {
                    $description = Str::limit($description, 40, '<a href="'.route('admin.proposal_reminders.show', ['proposal_id' => $row->proposal_id, 'id' => $row->id]).'">...</a>');
                }
                return $description;
            });
            $table->editColumn('date', function ($row) {
                return $row->date ? $row->date : '';
            });
            $table->editColumn('isnotified', function ($row) {
                return $row->isnotified ? ucfirst( $row->isnotified ) : '';
            });
            $table->editColumn('proposal.title', function ($row) {
                return $row->proposal ? $row->proposal->title : '';
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

        return view('proposals::admin.proposals.proposals_reminders.index', compact('proposal'));
    }

    /**
     * Show the form for creating new ProposalsReminder.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $proposal_id )
    {
        
       if (! Gate::allows('proposal_reminder_create')) {
            return prepareBlockUserMessage();
        }

        $proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $proposals = Proposal::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $reminder_tos = \App\User::whereHas("role",
                   function ($query) {
                       $query->where('title', ROLE_EMPLOYEE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_isnotified = ProposalsReminder::$enum_isnotified;
        $enum_notify_by_email = ProposalsReminder::$enum_notify_by_email;
            
        return view('proposals::admin.proposals.proposals_reminders.create', compact('enum_isnotified', 'enum_notify_by_email', 'proposals', 'reminder_tos', 'created_bies', 'proposal'));
    }

    /**
     * Store a newly created ProposalsReminder in storage.
     *
     * @param  \App\Http\Requests\StoreProposalsRemindersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProposalsRemindersRequest $request, $proposal_id)
    {

        if (! Gate::allows('proposal_reminder_create')) {
        return prepareBlockUserMessage();
        }

        $addtional = array(
            'proposal_id' => $proposal_id,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $proposals_reminder = ProposalsReminder::create( $request->all() );

        flashMessage( 'success', 'create');

        $employee = $proposals_reminder->reminder_to()->first();

        if (  'yes' === $request->notify_by_email ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'reminder_to' => $proposals_reminder->reminder_to->name,
                'date' => digiDate($proposals_reminder->date),
                'content' => 'Reminder',
                'description' => $proposals_reminder->description,

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
                'template' => 'proposal-reminder-employee',
                'model' => 'Modules\Proposals\Entities\ProposalsReminder',
                'data' => $templatedata,
            ];
          
            $employee->notify(new QA_EmailNotification($data));
        }

        return redirect()->route('admin.proposal_reminders.index', $proposal_id);
    }


    /**
     * Show the form for editing ProposalsReminder.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($proposal_id, $id)
    {
        
        if (! Gate::allows('proposal_reminder_edit')) {
            return prepareBlockUserMessage();
        }

        $proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $proposals = Proposal::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $reminder_tos = \App\User::whereHas("role",
                   function ($query) {
                       $query->where('title', ROLE_EMPLOYEE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_isnotified = ProposalsReminder::$enum_isnotified;
        $enum_notify_by_email = ProposalsReminder::$enum_notify_by_email;
            
        $proposals_reminder = ProposalsReminder::findOrFail($id);

        return view('proposals::admin.proposals.proposals_reminders.edit', compact('proposals_reminder', 'enum_isnotified', 'enum_notify_by_email', 'proposals', 'reminder_tos', 'created_bies', 'proposal'));
    }

    /**
     * Update ProposalsReminder in storage.
     *
     * @param  \App\Http\Requests\UpdateProposalsRemindersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProposalsRemindersRequest $request, $proposal_id, $id)
    {
        if (! Gate::allows('proposal_reminder_edit')) {
            return prepareBlockUserMessage();
        }
        $proposals_reminder = ProposalsReminder::findOrFail($id);

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $proposals_reminder->update($request->all());

        flashMessage( 'success', 'update');

        $employee = $proposals_reminder->reminder_to()->first();

        if (  'yes' === $request->notify_by_email ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'reminder_to' => $proposals_reminder->reminder_to->name,
                'date' => digiDate($proposals_reminder->date),
                'content' => 'Reminder',
                'description' => $proposals_reminder->description,

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
                'template' => 'proposal-reminder-employee',
                'model' => 'Modules\Proposals\Entities\ProposalsReminder',
                'data' => $templatedata,
            ];
           
            $employee->notify(new QA_EmailNotification($data));
        }

        return redirect()->route('admin.proposal_reminders.index', $proposal_id);
    }


    /**
     * Display ProposalsReminder.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($proposal_id, $id)
    {

       
        if (! Gate::allows('proposal_reminder_view')) {
            return prepareBlockUserMessage();
        }

        $proposal = Proposal::find( $proposal_id );

        if (! $proposal) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $proposals_reminder = ProposalsReminder::findOrFail($id);

        return view('proposals::admin.proposals.proposals_reminders.show', compact('proposals_reminder', 'proposal'));
    }


    /**
     * Remove ProposalsReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($proposal_id, $id)
    {

        if (! Gate::allows('proposal_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $proposals_reminder = ProposalsReminder::findOrFail($id);
        $proposals_reminder->delete();

        flashMessage( 'success', 'delete');

        return redirect()->route('admin.proposal_reminders.index', $proposal_id);
    }

    /**
     * Delete all selected ProposalsReminder at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {

        if (! Gate::allows('proposal_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ProposalsReminder::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes');
        }
    }


    /**
     * Restore ProposalsReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('proposal_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $proposals_reminder = ProposalsReminder::onlyTrashed()->findOrFail($id);

        $proposal_id = $proposals_reminder->proposal_id;
        
        $proposals_reminder->restore();

        flashMessage( 'success', 'restore');

        return redirect()->route('admin.proposal_reminders.index', $proposal_id);
    }

    /**
     * Permanently delete ProposalsReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('proposal_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $proposals_reminder = ProposalsReminder::onlyTrashed()->findOrFail($id);
        $proposal_id = $proposals_reminder->proposal_id;
        $proposals_reminder->forceDelete();

        flashMessage( 'success', 'delete');

        return redirect()->route('admin.proposal_reminders.index', $proposal_id);
    }
}
