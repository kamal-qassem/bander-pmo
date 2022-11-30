<?php

namespace App\Http\Controllers\Admin;

use App\InternalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInternalNotificationsRequest;
use App\Http\Requests\Admin\UpdateInternalNotificationsRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class InternalNotificationsController extends Controller
{   

    public function __construct() {
        $this->middleware('plugin:quick_notification');
    }
    /**
     * Display a listing of InternalNotification.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('internal_notification_access')) {
            return prepareBlockUserMessage();
        }

        $internal_notifications = InternalNotification::all()->sortByDesc('id');

        return view('admin.internal_notifications.index', compact('internal_notifications'));
    }

    /**
     * Show the form for creating new InternalNotification.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('internal_notification_create')) {
            return prepareBlockUserMessage();
        }
        
        $users = \App\User::get()->pluck('name', 'id');

        return view('admin.internal_notifications.create', compact('users'));
    }

    /**
     * Store a newly created InternalNotification in storage.
     *
     * @param  \App\Http\Requests\StoreInternalNotificationsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInternalNotificationsRequest $request)
    {
        if (! Gate::allows('internal_notification_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $internal_notification = InternalNotification::create($request->all());
        $internal_notification->users()->sync(array_filter((array)$request->input('users')));


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.internal_notifications.index');
    }


    /**
     * Show the form for editing InternalNotification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('internal_notification_edit')) {
            return prepareBlockUserMessage();
        }
        
        $users = \App\User::get()->pluck('name', 'id');


        $internal_notification = InternalNotification::findOrFail($id);

        return view('admin.internal_notifications.edit', compact('internal_notification', 'users'));
    }

    /**
     * Update InternalNotification in storage.
     *
     * @param  \App\Http\Requests\UpdateInternalNotificationsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInternalNotificationsRequest $request, $id)
    {
        if (! Gate::allows('internal_notification_edit')) {
            return prepareBlockUserMessage();
        }
        $internal_notification = InternalNotification::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $internal_notification->update($request->all());
        $internal_notification->users()->sync(array_filter((array)$request->input('users')));


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.internal_notifications.index');
    }


    /**
     * Display InternalNotification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('internal_notification_view')) {
            return prepareBlockUserMessage();
        }
        $internal_notification = InternalNotification::findOrFail($id);

        return view('admin.internal_notifications.show', compact('internal_notification'));
    }


    /**
     * Remove InternalNotification from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('internal_notification_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $internal_notification = InternalNotification::findOrFail($id);
        $internal_notification->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.internal_notifications.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected InternalNotification at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('internal_notification_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = InternalNotification::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }
    /**
     * Set all user notifications as read
     */
    public function read()
    {
        DB::table('internal_notification_user')
            ->where('user_id', Auth::id())
            ->update(['read_at' => Carbon::now()]);
    }
}
