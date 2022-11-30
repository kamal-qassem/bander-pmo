<?php

namespace Modules\Quotes\Http\Controllers\Admin;

use Modules\Quotes\Entities\Quote;
use Modules\Quotes\Entities\QuotesReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Quotes\Http\Requests\Admin\StoreQuotesRemindersRequest;
use Modules\Quotes\Http\Requests\Admin\UpdateQuotesRemindersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Str;
class QuotesRemindersController extends Controller
{
    /**
     * Display a listing of QuotesReminder.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $quote_id )
    {
        
        if (! Gate::allows('quote_reminder_access')) {
        return prepareBlockUserMessage();
        }

        $quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }


        
        if (request()->ajax()) {
            $query = QuotesReminder::query();
            $query->with("quote");
            $query->with("reminder_to");
            $query->with("created_by");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('quote_reminder_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'quotes_reminders.id',
                'quotes_reminders.description',
                'quotes_reminders.date',
                'quotes_reminders.isnotified',
                'quotes_reminders.quote_id',
                'quotes_reminders.reminder_to_id',
                'quotes_reminders.notify_by_email',
                'quotes_reminders.created_by_id',
            ]);
            $query->where('quote_id', '=', $quote_id );
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'quote_reminder_';
                $routeKey = 'admin.quote_reminders';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('description', function ($row) {
                $description = $row->description ? $row->description : '';
                if ( ! empty( $description ) ) {
                    $description = Str::limit($description, 40, '<a href="'.route('admin.quote_reminders.show', ['quote_id' => $row->quote_id, 'id' => $row->id]).'">...</a>');
                }
                return $description;
            });
            $table->editColumn('date', function ($row) {
                return $row->date ? $row->date : '';
            });
            $table->editColumn('isnotified', function ($row) {
                return $row->isnotified ? ucfirst( $row->isnotified ) : '';
            });
            $table->editColumn('quote.title', function ($row) {
                return $row->quote ? $row->quote->title : '';
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

        return view('quotes::admin.quotes.quotes_reminders.index', compact('quote'));
    }

    /**
     * Show the form for creating new QuotesReminder.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $quote_id )
    {
        
       if (! Gate::allows('quote_reminder_create')) {
            return prepareBlockUserMessage();
        }

        $quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $quotes = Quote::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $reminder_tos = \App\User::whereHas("role",
                   function ($query) {
                       $query->where('title', ROLE_EMPLOYEE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_isnotified = QuotesReminder::$enum_isnotified;
        $enum_notify_by_email = QuotesReminder::$enum_notify_by_email;
            
        return view('quotes::admin.quotes.quotes_reminders.create', compact('enum_isnotified', 'enum_notify_by_email', 'quotes', 'reminder_tos', 'created_bies', 'quote'));
    }

    /**
     * Store a newly created QuotesReminder in storage.
     *
     * @param  \App\Http\Requests\StoreQuotesRemindersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuotesRemindersRequest $request, $quote_id)
    {

        if (! Gate::allows('quote_reminder_create')) {
        return prepareBlockUserMessage();
        }

        $addtional = array(
            'quote_id' => $quote_id,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $quotes_reminder = QuotesReminder::create( $request->all() );

        flashMessage( 'success', 'create');

        $employee = $quotes_reminder->reminder_to()->first();

        if (  'yes' === $request->notify_by_email ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'reminder_to' => $quotes_reminder->reminder_to->name,
                'date' => digiDate($quotes_reminder->date),
                'content' => 'Reminder',
                'description' => $quotes_reminder->description,

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
                'template' => 'quote-reminder-employee',
                'model' => 'Modules\Quotes\Entities\QuotesReminder',
                'data' => $templatedata,
            ];
          
            $employee->notify(new QA_EmailNotification($data));
        }

        return redirect()->route('admin.quote_reminders.index', $quote_id);
    }


    /**
     * Show the form for editing QuotesReminder.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($quote_id, $id)
    {
        
        if (! Gate::allows('quote_reminder_edit')) {
            return prepareBlockUserMessage();
        }

        $quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $quotes = Quote::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $reminder_tos = \App\User::whereHas("role",
                   function ($query) {
                       $query->where('title', ROLE_EMPLOYEE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_isnotified = QuotesReminder::$enum_isnotified;
        $enum_notify_by_email = QuotesReminder::$enum_notify_by_email;
            
        $quotes_reminder = QuotesReminder::findOrFail($id);

        return view('quotes::admin.quotes.quotes_reminders.edit', compact('quotes_reminder', 'enum_isnotified', 'enum_notify_by_email', 'quotes', 'reminder_tos', 'created_bies', 'quote'));
    }

    /**
     * Update QuotesReminder in storage.
     *
     * @param  \App\Http\Requests\UpdateQuotesRemindersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuotesRemindersRequest $request, $quote_id, $id)
    {
        if (! Gate::allows('quote_reminder_edit')) {
            return prepareBlockUserMessage();
        }
        $quotes_reminder = QuotesReminder::findOrFail($id);

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $quotes_reminder->update($request->all());

        flashMessage( 'success', 'update');

        $employee = $quotes_reminder->reminder_to()->first();

        if (  'yes' === $request->notify_by_email ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'reminder_to' => $quotes_reminder->reminder_to->name,
                'date' => digiDate($quotes_reminder->date),
                'content' => 'Reminder',
                'description' => $quotes_reminder->description,

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
                'template' => 'quote-reminder-employee',
                'model' => 'Modules\Quotes\Entities\QuotesReminder',
                'data' => $templatedata,
            ];
           
            $employee->notify(new QA_EmailNotification($data));
        }

        return redirect()->route('admin.quote_reminders.index', $quote_id);
    }


    /**
     * Display QuotesReminder.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($quote_id, $id)
    {

        if (! Gate::allows('quote_reminder_view')) {
            return prepareBlockUserMessage();
        }

        $quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $quotes_reminder = QuotesReminder::findOrFail($id);

        return view('quotes::admin.quotes.quotes_reminders.show', compact('quotes_reminder', 'quote'));
    }


    /**
     * Remove QuotesReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $quote_id, $id)
    {

        if (! Gate::allows('quote_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $quotes_reminder = QuotesReminder::findOrFail($id);
        $quotes_reminder->delete();

        flashMessage( 'success', 'delete');

        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
    }

    /**
     * Delete all selected QuotesReminder at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {

        if (! Gate::allows('quote_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = QuotesReminder::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes');
        }
    }


    /**
     * Restore QuotesReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('quote_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $quotes_reminder = QuotesReminder::onlyTrashed()->findOrFail($id);

        $quote_id = $quotes_reminder->quote_id;
        
        $quotes_reminder->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete QuotesReminder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('quote_reminder_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $quotes_reminder = QuotesReminder::onlyTrashed()->findOrFail($id);
        $quote_id = $quotes_reminder->quote_id;
        $quotes_reminder->forceDelete();

        flashMessage( 'success', 'delete');

        return back();
    }
}
