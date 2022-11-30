<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsersRequest;
use App\Http\Requests\Admin\UpdateUsersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Kordy\Ticketit\Models\Ticket;

use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Validator;

class UsersController extends Controller
{   
    public function __construct() {
       $this->middleware('plugin:user');
    }
    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    { 

        if (! Gate::allows('user_access')) {
            return prepareBlockUserMessage();
        }

        if (request()->ajax()) {
            $query = User::query();
            $query->with("role");
            $query->with("department");
            $template = 'actionsTemplate';
            
            $query->select([
                'contacts.id',
                'contacts.name',
                'contacts.email',
                'contacts.password',
                'contacts.remember_token',                
                'contacts.department_id',
                'contacts.status',
                'contacts.is_user',
            ]);

            /**
             * when we call invoices display from other pages!
            */
            if ( ! empty( $type ) && 'contact_type' === $type ) {
                $query->whereHas("contact_type",
                function ($query) use( $type_id ) {
                    $query->where('id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'role' === $type ) {
                $query->whereHas("role",
                function ($query) use( $type_id ) {
                    $query->where('id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'contact_group' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('contacts.group_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'company' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('contacts.company_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'country' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('contacts.country_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'language' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('contacts.language_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'department' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('contacts.department_id', $type_id);
                });
            }
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'user_';
                $routeKey = 'admin.users';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('password', function ($row) {
                return '---';
            });
            $table->editColumn('role.title', function ($row) {
                if(count($row->role) == 0) {
                    return '';
                }

                return '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        $row->role->pluck('title')->toArray()) . '</span>';
            });
            $table->editColumn('remember_token', function ($row) {
                return $row->remember_token ? $row->remember_token : '';
            });
            
            $table->editColumn('department.name', function ($row) {
                return $row->department ? $row->department->name : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : trans('global.users.active');
            });

            $table->rawColumns(['actions','massDelete','role.title']);

            return $table->make(true);
        }

        return view('admin.users.index',compact('type', 'type_id'));
    }

    /**
     * Show the form for creating new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $contact_id )
    {
        
        if (! Gate::allows('user_create')) {
            return prepareBlockUserMessage();
        }
       
        $contact = \App\Contact::find( $contact_id );
        if ( ! $contact ) {
            flashMessage('danger', 'not_found');
            return back();
        }
        $roles = $contact->contact_type()->pluck('title', 'id');
        $role_ids = $contact->contact_type()->pluck('id')->toArray();
        $departments = \App\Department::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('admin.users.create', compact('roles', 'departments', 'contact', 'role_ids', 'contact_id'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreUsersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $contact_id)
    {
        if (! Gate::allows('user_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'name' => 'max:191|required',
            'email' => [
                'email',
                'max:191',
                'required',
                Rule::unique('contacts')->where( function( $query ) {
                    return $query->where('email', request('email'));
                })
                ->where( function( $query ) {
                    return $query->where('is_user', 'yes');
                })
            ],
            'password' => 'required',
            'role' => 'array|required',
            'role.*' => 'integer|exists:roles,id|max:4294967295|required',
            'remember_token' => 'max:191|nullable'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ( ! $validator->passes() ) {            
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $confirmation_code = str_random(30);
        $password = str_random(9);
        if ( empty( $request->password ) ) {
            $request->password = $password;
        }
        $addtional = array(
            'theme' => 'default',
            'portal_language' => 'en',
            'status' => 'Active',
            'confirmation_code' => $confirmation_code,
            'is_user' => 'yes',
        );
        if ( 'yesinactivate' === $request->create_user ) {
            $addtional['status'] = 'Registered';            
        }
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
        
        $id = $contact_id;
        $user = \App\Contact::find($id);
        
        if ( $user ) {

            if ( isDemo() ) {
             return prepareBlockUserMessage( 'info', 'crud_disabled' );
            }
            $user->update($request->all());
            
            $user->role()->sync(array_filter((array)$request->input('role')));

            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'name' => $user->name,
                'user_name' => $user->email,
                'password' => $request->password,
                'activation_link' => route('user.activate', $addtional['confirmation_code']),
                'login_link' => route('login'),
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
                'template' => 'user-created',
                'model' => 'App\User',
                'data' => $templatedata,
            ];
            if ( 'yesactivate' === $request->create_user ) {
                $data['template'] = 'user-created-welcome';
            }
            $user->notify(new QA_EmailNotification($data));
        }

        flashMessage( 'success', 'create' );
        return redirect()->route('admin.users.index');
    }


    /**
     * Show the form for editing User.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('user_edit')) {
            return prepareBlockUserMessage();
        }

        $contact_references = \App\Contact::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $departments = \App\Department::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $user = User::findOrFail($id);

        $roles = $user->contact_type->where('type', 'role')->sortBy('title')->pluck('title', 'id');

        return view('admin.users.edit', compact('user', 'roles', 'contact_references', 'departments'));
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\UpdateUsersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUsersRequest $request, $id)
    {
        if (! Gate::allows('user_edit')) {
            return prepareBlockUserMessage();
        }
        $user = User::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $user->update($request->all());
        $user->role()->sync(array_filter((array)$request->input('role')));

        flashMessage( 'success', 'update' );
        return redirect()->route('admin.users.index');
    }


    /**
     * Display User.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
        
    {
        
        if (! Gate::allows('user_view')) {
            return prepareBlockUserMessage();
        }
        
        $roles = \App\Role::get()->pluck('title', 'id');

        $user = User::findOrFail($id);
        
        $contact_reference_id = $user->id;

        $contact_references = \App\Contact::get()->pluck('first_name', 'id')->prepend(trans('global.app_please_select'), '');
        $departments = \App\Department::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $user_actions = \App\UserAction::where('user_id', $id)->get();
        $internal_notifications = \App\InternalNotification::whereHas('users',
                    function ($query) use ($id) {
                        $query->where('id', $id);
                    })->get();
        $departments = \App\Department::where('created_by_id', $id)->get();
        $assets_histories = \App\AssetsHistory::where('assigned_user_id', $id)->get();
        $tasks = \App\Task::where('user_id', $id)->get();
        if( isEmployee() ) {
        $client_projects = \App\ClientProject::whereHas('assigned_to',
                    function ($query) use ($id) {
                        $query->where('id', $id);
                    })->get();
        } else {
            $client_projects = \App\ClientProject::where('client_id', '=', $contact_reference_id)->get();
        }

     

        $supports_created = Ticket::where('user_id', $id)->get();
        $assets = \App\Asset::where('assigned_user_id', $id)->get();
    
        $supports = Ticket::where('agent_id', $id)->get();

        $invoices = \App\Invoice::where('customer_id', $contact_reference_id)->get();
        $quotes = \Modules\Quotes\Entities\Quote::where('customer_id', $contact_reference_id)->get();
        $recurring_invoices = \Modules\RecurringInvoices\Entities\RecurringInvoice::where('customer_id', $contact_reference_id)->get();

        

        return view('admin.users.show', compact('user', 'user_actions', 'internal_notifications', 'departments', 'assets_histories', 'tasks', 'client_projects', 'supports', 'assets', 'supports', 'invoices', 'quotes', 'recurring_invoices', 'supports_created','list'));
    }


    /**
     * Remove User from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('user_delete')) {
            return prepareBlockUserMessage();
        }
        $user = User::findOrFail($id);
        $user->is_user = 'no';
        $user->email_verified_at = null;
        $user->password = null;
        $user->remember_token = null;

        $user->department_id = null;
        $user->ticketit_admin = 0;
        $user->ticketit_agent = 0;
        $user->status = 'Deleted';
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $user->save();

        flashMessage( 'success', 'delete' );
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
    }

    /**
     * Delete all selected User at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('user_delete')) {
            return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = User::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
              
                $entry->is_user = 'no';
                $entry->email_verified_at = null;
                $entry->password = null;
                $entry->remember_token = null;

                $entry->department_id = null;
                $entry->ticketit_admin = 0;
                $entry->ticketit_agent = 0;
                $entry->status = 'Deleted';
            if ( isDemo() ) {
             return prepareBlockUserMessage( 'info', 'crud_disabled' );
            }
                $entry->save();
            }

            flashMessage( 'success', 'deletes' );
        }
    }

    public function getUser() {
        if (request()->ajax()) {
            $contact_reference_id = request('contact_reference_id');
            $contact = \App\Contact::find( $contact_reference_id );
            
            $status = 'success';
            $message = '';
            $edit_message = '';
            $data = array(
                'contact_reference_id' => $contact_reference_id,
            );
            if ( $contact ) {
                $user = User::where( 'id', '=', $contact_reference_id)->first();
                if ( $user ) {
                    $status = 'danger';
                    $message = $edit_message = trans('custom.messages.already-exists');
                    $data['email'] = $user->email;
                } else {
                    $user = User::where( 'email', '=', $contact->email)->first();
                    
                    if ( empty( $contact->email ) ) {
                        $status = 'danger';
                        $message = trans('custom.messages.contact-dont-have-email');
                        $edit_message = trans('custom.messages.click-here', ['url' => route('admin.contacts.edit', $contact->id)]);
                    } elseif ( $user ) {
                        $status = 'danger';
                        $message = $edit_message = trans('custom.messages.already-exists-email');
                        $data['email'] = $user->email;
                    } else {
                        $data['contact'] = $contact->toArray();
                    }
                }
            } else {
                $status = 'danger';
                $message = trans('custom.messages.not_found');
            }

            $response = array(
                'status' => $status,
                'message' => $message,
                'data' => $data,
                'edit_message' => $edit_message,
            );
            return response()->json( $response );
        }
    }

    /**
     * Remove User from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id)
    {
        if (! Gate::allows('user_change_status')) {
            return prepareBlockUserMessage();
        }
        $user = User::find($id);

        if ( ! $user ) {
            $message = trans('custom.messages.not_found');
            flashMessage( 'danger', 'create', $message );
            return redirect()->route('admin.users.index');
        }

        if ( 'Active' == $user->status ) {
            $user->status = 'Suspended';

            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'name' => $user->name,
                'user_name' => $user->email,
                'login_link' => route('login'),

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
                'template' => 'account-suspended',
                'model' => 'App\User',
                'data' => $templatedata,
            ];
            $user->notify(new QA_EmailNotification($data));

        } else {
            $user->status = 'Active';
            $user->confirmation_code = NULL;
            $user->email_verified_at = Carbon::now();

            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'name' => $user->name,
                'user_name' => $user->email,
                'login_link' => route('login'),

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
                'template' => 'account-activated',
                'model' => 'App\User',
                'data' => $templatedata,
            ];
            $user->notify(new QA_EmailNotification($data));

        }
        $user->save();
        
        flashMessage( 'success', 'update' );
        return redirect()->route('admin.users.index');
    }

}
