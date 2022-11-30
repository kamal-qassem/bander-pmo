<?php

namespace App\Http\Controllers\Admin;

use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContactsRequest;
use App\Http\Requests\Admin\UpdateContactsRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Requests\Admin\UpdateDeliveryAddressRequest;
use Yajra\DataTables\DataTables;

use App\Http\Controllers\Traits\FileUploadTrait;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Notification;
use Validator;
use Illuminate\Support\Collection;
use \DrewM\MailChimp\MailChimp as DrewMailChimp;
use App\Jobs\SendBulkEmail;
use Log;

class ContactsController extends Controller
{
     use FileUploadTrait;

    /**
     * Display a listing of Contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {
        if (! Gate::allows('contact_access')) {
            return prepareBlockUserMessage();
        }


        if (request()->ajax()) {

            $query = Contact::query();

            $query->with("contact_type");

            $template = 'actionsTemplate';

            if(request('show_deleted') == 1) {
                if (! Gate::allows('contact_delete')) {
                    return prepareBlockUserMessage();
                }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }

            $query->select([
                'contacts.id',
                'contacts.company_id',
                'contacts.group_id',
                'contacts.first_name',
                'contacts.last_name',
                'contacts.phone1_code',
                'contacts.phone1',
                'contacts.phone2_code',
                'contacts.phone2',
                'contacts.email',
                'contacts.skype',
                'contacts.address',
                'contacts.city',
                'contacts.state_region',
                'contacts.zip_postal_code',
                'contacts.tax_id',
                'contacts.country_id',
                'contacts.thumbnail',

                'contacts.name',
                'contacts.fulladdress',
                'contacts.is_user'
            ]);

            if ( empty( $type ) ) {
                $query->whereHas("contact_type",
                function ($query) {
                    $query->where('id', '!=', LEADS_TYPE);
                });
            }

            /**
             * when we call invoices display from other pages!
            */
            if ( ! empty( $type ) && 'contact_type' === $type ) {
                $query->whereHas("contact_type",
                function ($query) use( $type_id ) {
                    $query->where('roles.id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'contact_group' === $type ) {
                $query->when($type_id, function ($q, $type_id) {
                    return $q->where('contacts.group_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'contact_company' === $type ) {
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
                $gateKey  = 'contact_';
                $routeKey = 'admin.contacts';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });



            $table->editColumn('thumbnail', function ($row) {
                if($row->thumbnail) { return '<a href="'. asset(env('UPLOAD_PATH').'/' . $row->thumbnail) .'" target="_blank"><img src="'. asset(env('UPLOAD_PATH').'/thumb/' . $row->thumbnail) .'"/>'; };
            });

            $table->editColumn('contact_type.title', function ($row) {
                if(count($row->contact_type) == 0) {
                    return '';
                }
                return '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        $row->contact_type->pluck('title')->toArray()) . '</span>';
            });
            $table->editColumn('first_name', function ($row) {
                return $row->name ? $row->first_name : '';
            });
            $table->editColumn('last_name', function ($row) {
                return $row->last_name ? $row->last_name : '';
            });
            $table->editColumn('contacts.name', function ($row) {
                $name = $row->name ? $row->name : '';
                return $name;
            });
            $table->editColumn('fulladdress', function ($row) {
                $fulladdress = $row->fulladdress ? $row->fulladdress : '';
                if ( empty( $fulladdress ) ) {
                    $fulladdress = $row->address ? $row->address : '';

                    if ( ! empty( $row->city ) ) {
                        $fulladdress .= ', ' . $row->city;
                    }
                    if ( ! empty( $row->state_region ) ) {
                        $fulladdress .= ', ' . $row->state_region;
                    }
                    if ( ! empty( $row->country_id ) ) {
                        $fulladdress .= ', ' . getCountryname( $row->country_id );
                    }
                    if ( ! empty( $row->zip_postal_code ) ) {
                        $fulladdress .= ' - ' . $row->zip_postal_code;
                    }
                } else {
                    $fulladdress = str_replace("\n", ', ', $fulladdress);
                }
                return $fulladdress;
            });

            $table->editColumn('phone1', function ($row) {
                $code = $row->phone1_code ? '+' . $row->phone1_code . ' - ' : '';
                return $row->phone1 ? $code . $row->phone1 : '';
            });
            $table->editColumn('phone2', function ($row) {
                $code = $row->phone2_code ? '+' . $row->phone2_code . ' - ' : '';
                return $row->phone2 ? $code . $row->phone2 : '';
            });
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : '';
            });
            $table->editColumn('skype', function ($row) {
                return $row->skype ? $row->skype : '';
            });
            $table->editColumn('address', function ($row) {
                return $row->address ? $row->address : '';
            });
            $table->editColumn('city', function ($row) {
                return $row->city ? $row->city : '';
            });
            $table->editColumn('state_region', function ($row) {
                return $row->state_region ? $row->state_region : '';
            });
            $table->editColumn('zip_postal_code', function ($row) {
                return $row->zip_postal_code ? $row->zip_postal_code : '';
            });
            $table->editColumn('tax_id', function ($row) {
                return $row->tax_id ? $row->tax_id : '';
            });


            $table->rawColumns(['actions','massDelete','contact_type.title','language.language']);

            return $table->make(true);
        }
        return view('admin.contacts.index', compact('type', 'type_id'));
    }



    /**
     * Show the form for creating new Contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $type = '' )
    {
        if (! Gate::allows('contact_create')) {
            return prepareBlockUserMessage();
        }

        $companies = \App\ContactCompany::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $groups = \App\ContactGroup::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        if ( ! empty( $type ) && $type == LEADS_TYPE ) {
            $contact_types = \App\ContactType::where('id', LEADS_TYPE)->orderBy('title')->get()->pluck('title', 'id');
        } else {
            $contact_types = \App\ContactType::where('id', '!=', LEADS_TYPE)->orderBy('title')->get()->pluck('title', 'id');
        }

        $languages = \App\Language::get()->pluck('language', 'id');

        $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

        $countries_code = \App\Country::get()->pluck('title', 'dialcode')->prepend(trans('global.app_please_select'), '');

        $redirect = route( 'admin.contacts.index' );

        if ( ! empty( $type ) ) {
            $redirect = route( 'admin.list_contacts.index', ['type' => 'contact_type', 'type_id' => $type ] );
        }
        $contact = '';
        return view('admin.contacts.create', compact('companies', 'groups', 'contact_types', 'languages', 'countries', 'countries_code', 'type', 'redirect','contact'));
    }

    /**
     * Store a newly created Contact in storage.
     *
     * @param  \App\Http\Requests\StoreContactsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('contact_create')) {
            return prepareBlockUserMessage();
        }


        $rules = [

            'email' => 'required|email|unique:contacts,email',
            'contact_type' => 'required',
            'first_name' => 'required',
            'contact_type.*' => 'exists:roles,id',
            'language.*' => 'exists:languages,id',
            'currency_id' => 'required',
            'currency_id.*' => 'exists:currencies,id',
            'phone1' => 'nullable|phone_number',
            'phone2' => 'nullable|phone_number',
            'thumbnail' => 'nullable|image',
        ];

        $contact_types = $request->contact_type;
        if ( in_array( LEADS_TYPE, $contact_types ) ) {
            $rules['email'] = 'nullable|email|unique:contacts,email';
        }
        $validator = Validator::make($request->all(), $rules);

        if ( ! $validator->passes() ) {
            if ( $request->ajax() ) {
                return response()->json(['error'=>$validator->errors()->all()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }

        $delivery_address = array();
        $delivery_address['first_name'] = ( $request->first_name_delivery ) ? $request->first_name_delivery : '';
        $delivery_address['last_name'] = ( $request->last_name_delivery ) ? $request->last_name_delivery : '';
        $delivery_address['address'] = ( $request->address_delivery ) ? $request->address_delivery : '';
        $delivery_address['city'] = ( $request->city_delivery ) ? $request->city_delivery : '';
        $delivery_address['state_region'] = ( $request->state_region_delivery ) ? $request->state_region_delivery : '';
        $delivery_address['zip_postal_code'] = ( $request->zip_postal_code_delivery ) ? $request->zip_postal_code_delivery : '';
        $delivery_address['country_id'] = ( $request->country_id_delivery ) ? $request->country_id_delivery : '';


        $name = $request->first_name;
        if ( ! empty( $request->last_name ) ) {
            $name .= ' ' . $request->last_name;
        }

        $fulladdress = $request->address;
        if ( ! empty( $request->city ) ) {
            if ( empty( $fulladdress ) ) {
                $fulladdress .= $request->city;
            } else {
                $fulladdress .= "\n" . $request->city;
            }
        }
        if ( ! empty( $request->state_region ) ) {
            if ( empty( $fulladdress ) ) {
                $fulladdress .= $request->state_region;
            } else {
                $fulladdress .= "\n" . $request->state_region;
            }
        }
        if ( ! empty( $request->country_id ) ) {
            if ( empty( $fulladdress ) ) {
                $fulladdress .= getCountryname( $request->country_id );
            } else {
                $fulladdress .= "\n" . getCountryname( $request->country_id );
            }
        }
        if ( ! empty( $request->zip_postal_code ) ) {
            $fulladdress .= ' - ' . $request->zip_postal_code;
        }
        $addtional = array(
            'delivery_address' => json_encode( $delivery_address ),
            'name' => $name,
            'fulladdress' => $fulladdress,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact = Contact::create($request->all());
        $contact->contact_type()->sync(array_filter((array)$request->input('contact_type')));
        $contact->language()->sync(array_filter((array)$request->input('language')));


        if ( ! empty($request->create_user) && 'no' != $request->create_user ) {

            $user = $contact;

            $password_raw = Str::random(8);
            $password = Hash::make( $password_raw );
            $confirmation_code = str_random(30);
            $user->is_user = 'yes';
            $user->status = 'Active'; // yesactivate
            $user->password = $password;
            $user->theme = 'default';
            $user->portal_language = 'en';
            if ( 'yesinactivate' === $request->create_user ) {
                $user->status = 'Registered';
                $user->confirmation_code = $confirmation_code;
            }
            $user->save();

            $roles = array_filter((array)$request->input('contact_type'));
            $user->role()->sync( $roles );

            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'name' => $user->name,
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'site_url' => env('APP_URL'),
                'user_name' => $user->email,
                'password' => $password_raw,
                'activation_link' => route('user.activate', $confirmation_code),
                'login_link' => route('login'),
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

        if ( $request->ajax() ) {
            $contact->fetchaddress = $request->fetchaddress;
            $contact->selectedid = $request->selectedid;
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $contact]);
        } else {
            flashMessage( 'success', 'create' );
            if ( ! empty( $request->redirect ) ) {
                return redirect( $request->redirect );
            } else {
                return redirect()->route('admin.contacts.index');
            }
        }
    }


    /**
     * Show the form for editing Contact.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('contact_edit')) {
            return prepareBlockUserMessage();
        }

        $companies = \App\ContactCompany::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $groups = \App\ContactGroup::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $contact_types = \App\ContactType::orderBy('title')->get()->pluck('title', 'id');

        $languages = \App\Language::get()->pluck('language', 'id');

        $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

        $contact = Contact::findOrFail($id);

        $countries_code = \App\Country::get()->pluck('title', 'dialcode')->prepend(trans('global.app_please_select'), '');

        $redirect = route( 'admin.contacts.index' );
        $type = Arr::first( $contact->contact_type->pluck('id')->toArray() );
        if ( ! empty( $type ) ) {
            $redirect = route( 'admin.list_contacts.index', ['type' => 'contact_type', 'type_id' => $type ] );
        }

        return view('admin.contacts.edit', compact('contact', 'companies', 'groups', 'contact_types', 'languages', 'countries', 'countries_code', 'redirect'));
    }

    /**
     * Update Contact in storage.
     *
     * @param  \App\Http\Requests\UpdateContactsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContactsRequest $request, $id)
    {
        if (! Gate::allows('contact_edit')) {
            return prepareBlockUserMessage();
        }

        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }

        $delivery_address = array();
        $delivery_address['first_name'] = ( $request->first_name_delivery ) ? $request->first_name_delivery : '';
        $delivery_address['last_name'] = ( $request->last_name_delivery ) ? $request->last_name_delivery : '';
        $delivery_address['address'] = ( $request->address_delivery ) ? $request->address_delivery : '';
        $delivery_address['city'] = ( $request->city_delivery ) ? $request->city_delivery : '';
        $delivery_address['state_region'] = ( $request->state_region_delivery ) ? $request->state_region_delivery : '';
        $delivery_address['zip_postal_code'] = ( $request->zip_postal_code_delivery ) ? $request->zip_postal_code_delivery : '';
        $delivery_address['country_id'] = ( $request->country_id_delivery ) ? $request->country_id_delivery : '';
        $delivery_address['first_name'] = ( $request->first_name_delivery ) ? $request->first_name_delivery : '';

        $name = $request->first_name;
        if ( ! empty( $request->last_name ) ) {
            $name .= ' ' . $request->last_name;
        }

        $fulladdress = $request->address;
        if ( ! empty( $request->city ) ) {
            if ( empty( $fulladdress ) ) {
                $fulladdress .= $request->city;
            } else {
                $fulladdress .= "\n" . $request->city;
            }
        }
        if ( ! empty( $request->state_region ) ) {
            if ( empty( $fulladdress ) ) {
                $fulladdress .= $request->state_region;
            } else {
                $fulladdress .= "\n" . $request->state_region;
            }
        }
        if ( ! empty( $request->country_id ) ) {
            if ( empty( $fulladdress ) ) {
                $fulladdress .= getCountryname( $request->country_id );
            } else {
                $fulladdress .= "\n" . getCountryname( $request->country_id );
            }
        }
        if ( ! empty( $request->zip_postal_code ) ) {
            $fulladdress .= ' - ' . $request->zip_postal_code;
        }

        $addtional = array(
            'delivery_address' => json_encode( $delivery_address ),
            'name' => $name,
            'fulladdress' => $fulladdress,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

        $contact = Contact::findOrFail($id);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact->update($request->all());
        $contact->contact_type()->sync(array_filter((array)$request->input('contact_type')));

        if ( 'yes' === $contact->is_user ) {
            $user = \App\User::find( $id );
            $contact->role()->sync(array_filter((array)$request->input('contact_type')));
        }
        $contact->language()->sync(array_filter((array)$request->input('language')));

        if ( ! empty($request->create_user) && 'no' != $request->create_user ) {
            $password_raw = Str::random(8);
            $password = Hash::make( $password_raw );
            $confirmation_code = str_random(30);

            $user = $contact;

            $user->is_user = 'yes';
            $user->status = 'Active'; // yesactivate
            $user->password = $password;
            $user->theme = 'default';
            $user->portal_language = 'en';
            if ( 'yesinactivate' === $request->create_user ) {
                $user->status = 'Registered';
                $user->confirmation_code = $confirmation_code;
            }
            $user->save();

            $roles = array_filter((array)$request->input('contact_type'));
            $user->role()->sync(array_filter((array)$roles));

            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'name' => $user->name,
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'site_url' => env('APP_URL'),
                'user_name' => $user->email,
                'password' => $password_raw,
                'activation_link' => route('user.activate', $confirmation_code),
                'login_link' => route('login'),
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
            //$user->notify(new QA_EmailNotification($data));
        }

        flashMessage( 'success', 'update' );

        $redirect = $request->redirect;
        if ( ! empty( $redirect ) ) {
            return \Redirect::to( $redirect );
        } else {
            return redirect()->route('admin.contacts.index');
        }
    }


    /**
     * Display Contact.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('contact_view')) {
            return prepareBlockUserMessage();
        }

        $companies = \App\ContactCompany::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $groups = \App\ContactGroup::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $contact_types = \App\ContactType::get()->pluck('title', 'id');

        $languages = \App\Language::get()->pluck('language', 'id');

        $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

        $contact = Contact::findOrFail($id);
        // dd($contact);

        $countries_code = \App\Country::get()->pluck('title', 'dialcode')->prepend(trans('global.app_please_select'), '');

        return view('admin.contacts.show', compact('contact', 'countries_code', 'list'));
    }


    /**
     * Remove Contact from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('contact_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        try{
            $contact = Contact::findOrFail($id);
            $contact->delete();
            flashMessage( 'success', 'delete' );
        } catch (\Illuminate\Database\QueryException $e) {
            flashMessage( 'info', 'exception' );
        }


         if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.contacts.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
            return redirect( $request->redirect_url );
        } else {
            return back();
        }
     }
    }

    public function destroyInfo( $id )
    {
        if (! Gate::allows('contact_delete')) {
            return prepareBlockUserMessage();
        }

        //All the invoices and recurring invoices related to the customer(contact) gets deleted.
        $invoices = DB::table('invoices')->where('customer_id', $id)->count('id');

        //All the credit notes  related to the customer(contact) gets deleted.
        $quotes = DB::table('quotes')->where('customer_id', '=', $id)->count('id');

        //All the credit notes  related to the customer(contact) gets deleted.
        $credit_notes = DB::table('credit_notes')->where('customer_id', '=', $id)->count('id');

        //All the orders related to the customer(contact) gets deleted.
        $orders = DB::table('orders')->where('customer_id', '=', $id)->count('id');

        $contact = Contact::findOrFail($id);


    return view('admin.contacts.info', compact('contact', 'invoices', 'quotes', 'credit_notes', 'orders'));
    }

    /**
     * Delete all selected Contact at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('contact_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Contact::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                try{
                    $entry->delete();
                } catch (\Illuminate\Database\QueryException $e) {
                    flashMessage( 'info', 'exception' );
                }
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Invoice from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('contact_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact = Contact::onlyTrashed()->findOrFail($id);
        $contact->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete Contact from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function del_permanent(Request $request, $id)
    {
        if (! Gate::allows('contact_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $contact = Contact::findOrFail($id);
        $contact->forceDelete();

        flashMessage( 'success', 'delete' );

        // return redirect()->route('admin.contacts.index');
        if ( ! empty( $request->redirect_url ) ) {
            return redirect( $request->redirect_url );
        } else {
            return back();
        }
    }

    /**
     * Permanently delete Contact from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('contact_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact = Contact::onlyTrashed()->findOrFail($id);
        $contact->forceDelete();

        flashMessage( 'success', 'delete' );

        return back();
    }

    public function profileEdit() {
        if (! Gate::allows('profile_edit')) {
            flashMessage( 'danger', 'not_allowed' );
            return back();
        }
        $id = Auth()->id();

        $companies = \App\ContactCompany::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $groups = \App\ContactGroup::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $contact_types = \App\ContactType::get()->pluck('title', 'id');

        $languages = \App\Language::get()->pluck('language', 'id');

        $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

        $contact = Contact::find($id);

        $countries_code = \App\Country::get()->pluck('title', 'dialcode')->prepend(trans('global.app_please_select'), '');

        return view('admin.contacts.profile', compact('contact', 'companies', 'groups', 'contact_types', 'languages', 'countries', 'countries_code'));
    }

    public function profileUpdate( UpdateProfileRequest $request ) {

        if (! Gate::allows('profile_edit')) {
            flashMessage( 'danger', 'not_allowed' );
            return back();
        }

        $id = Auth()->id();

        $theme = $request->input('theme');
        $direction = 'ltr';
        $lang = 'en';
        $color_theme = $request->input('color_theme');
        $color_skin = $request->input('color_skin');

        if ( $id > 0 ) {
            if ( ! isDemo() ) {
            $request = $this->saveFiles($request);
            }
            $contact = Contact::findOrFail($id);
            $contact->update($request->all());
            $contact->language()->sync(array_filter((array)$request->input('language')));

            $user = Auth()->user();
            $user->theme = $theme;
            $user->color_theme = $color_theme;
            $user->color_skin = $color_skin;
            $user->save();

            flashMessage('success', 'update', trans('custom.messages.profile_updated'));
        } else {
            flashMessage('success', 'update', trans('custom.messages.profile_updated_failed'));
        }

        return redirect('admin/profile/edit')->withCookie(cookie()->forever('language', $lang))
            ->withCookie(cookie()->forever('direction', $direction))
            ->withCookie(cookie()->forever('color_theme', $color_theme))
            ->withCookie(cookie()->forever('color_skin', $color_skin))
            ->withCookie(cookie()->forever('theme', $theme));
    }

    public function deliveryAddressEdit( $id = '' ) {
        if (! Gate::allows('delivery_address_edit')) {
            flashMessage( 'danger', 'not_allowed' );
            return back();
        }

        if( empty( $id ) ) {
            $id = Auth()->id();
        }
        $contact = Contact::find($id);

        $delivery_address = ( $contact->delivery_address ) ? json_decode( $contact->delivery_address, true ) : array();
        $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        return view('admin.contacts.delivery-address', compact('delivery_address', 'contact', 'countries'));
    }

    public function deliveryAddressUpdate( UpdateDeliveryAddressRequest $request, $id = '' ) {
        if (! Gate::allows('delivery_address_edit')) {
            flashMessage( 'danger', 'not_allowed' );
            return back();
        }

        if( empty( $id ) ) {
            $id = Auth()->id();
        }
        $contact = Contact::find($id);

        $delivery_address = array();
        $delivery_address['first_name'] = $request->first_name;
        $delivery_address['last_name'] = $request->last_name;
        $delivery_address['address'] = $request->address;
        $delivery_address['city'] = $request->city;
        $delivery_address['state_region'] = $request->state_region;
        $delivery_address['zip_postal_code'] = $request->zip_postal_code;
        $delivery_address['country_id'] = $request->country_id;
        $contact->delivery_address = json_encode( $delivery_address );
        $contact->save();

        flashMessage('success', 'update', trans('global.contacts.delivery_address_updated'));

        if ( isCustomer() ) {
            return redirect()->route('admin.contacts.delivery-address.edit');
        } else {
            return redirect()->route('admin.contacts.show', $contact->id);
        }
    }

    public function shippingAddressEdit( $id = '' ) {
        if (! Gate::allows('shipping_address_edit')) {
            flashMessage( 'danger', 'not_allowed' );
            return back();
        }

        if( empty( $id ) ) {
            $id = Auth()->id();
        }
        $contact = Contact::find($id);

        $shipping_address = ( $contact->shipping_address ) ? json_decode( $contact->shipping_address, true ) : array();
        $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        return view('admin.contacts.shipping-address', compact('shipping_address', 'contact', 'countries'));
    }

    public function shippingAddressUpdate( UpdateDeliveryAddressRequest $request, $id = '' ) {
        if (! Gate::allows('shipping_address_edit')) {
            flashMessage( 'danger', 'not_allowed' );
            return back();
        }

        if( empty( $id ) ) {
            $id = Auth()->id();
        }
        $contact = Contact::find($id);

        $shipping_address = array();
        $shipping_address['first_name'] = $request->first_name;
        $shipping_address['last_name'] = $request->last_name;
        $shipping_address['address'] = $request->address;
        $shipping_address['city'] = $request->city;
        $shipping_address['state_region'] = $request->state_region;
        $shipping_address['zip_postal_code'] = $request->zip_postal_code;
        $shipping_address['country_id'] = $request->country_id;
        $contact->shipping_address = json_encode( $shipping_address );
        $contact->save();

        flashMessage('success', 'update', trans('global.contacts.delivery_address_updated'));

        if ( isCustomer() ) {
            return redirect()->route('admin.contacts.shipping-address.edit');
        } else {
            return redirect()->route('admin.contacts.show', $contact->id);
        }
    }

    public function sendEmail()
    {
        if (request()->ajax()) {
            $action = request('action');
            $post = request('data');

            $id = $post['contact_id'];

            $response = array('status' => 'danger', 'message' => trans('custom.messages.somethiswentwrong') );

            $contact = Contact::find( $id );
            if ( $contact ) {
                $data = array();
                $data['name'] = $contact->name;
                if ( empty( $data['name'] ) ) {
                    $data['name'] = $contact->first_name . ' ' . $contact->last_name;
                }
                $toemail = ! empty( $post['toemail'] ) ? $post['toemail'] : '';
                if ( ! empty( $toemail ) ) {
                    $data['to_email'] = $toemail;
                } else {
                    $data['to_email'] = $contact->email;
                }
                $data['ccemail'] = ! empty( $post['ccemail'] ) ? $post['ccemail'] : '';
                $data['bccemail'] = ! empty( $post['bccemail'] ) ? $post['bccemail'] : '';
                $data['bcc_admin'] = ! empty( $post['bcc_admin'] ) ? $post['bcc_admin'] : '';

                $data['content'] = $post['message'];

                $data['site_title'] = getSetting( 'site_title', 'site_settings');
                $logo = getSetting( 'site_logo', 'site_settings' );
                $data['logo'] = asset( 'uploads/settings/' . $logo );
                $data['date'] = digiTodayDateAdd();

                $data['site_address'] = getSetting( 'site_address', 'site_settings');
                $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
                $data['site_email'] = getSetting( 'contact_email', 'site_settings');

                $response['status'] = 'success';
                $response['message'] = trans('custom.messages.mailsent');

                $res = sendEmail( 'contact-email', $data );

                $contact_notes = [
                    'title' => trans('custom.messages.mailsent'),
                    'notes' => render( \Blade::compileString(getTemplate('contact-email', $data)), $data ),
                    'contact_id' => $contact->id,
                ];
                \App\ContactNote::create( $contact_notes );
            }

            return json_encode( $response );
        }
    }

    public function mailchimpEmailCampaigns( $list_id = '' )
    {

        if (! Gate::allows('contact_mailchimp_email_campaigns')) {
            return prepareBlockUserMessage();
        }

        $api_key = getSetting('mailchimp_api_key', 'mailchimp-settings', '');
        $MailChimp = new DrewMailChimp( trim( $api_key ) );

        if (request()->ajax()) {
            if ( ! empty( $list_id ) ) {
                $mailchimp_lists = $MailChimp->get('lists/' . $list_id . '/members' . '?offset=0&count=200');
            } else {
                $mailchimp_lists = $MailChimp->get('lists');
            }

            $lists = new Collection;
            $date_set = getCurrentDateFormat();
            if ( ! empty( $list_id ) ) {
                foreach($mailchimp_lists['members'] as $list) {
                    $latest_run = \App\ContactEmailCampaigns::orderBy('id', 'desc')->first();
                    if ( ! $latest_run ) {
                        $latest_run = (Object) [
                            'is_schedule' => '',
                            'schedule_date' => '',
                            'created_at' => '',
                        ];
                    }
                    $lists->push([
                        'id' => $list['id'],
                        'email_address' => $list['email_address'],
                        'status' => $list['status'],
                        'is_schedule' => $latest_run->is_schedule,
                        'schedule_date' => $latest_run->schedule_date,
                        'last_run' => ($latest_run->created_at) ? Carbon::createFromFormat('Y-m-d H:i:s', $latest_run->created_at)
                      ->format($date_set . ' H:i:s') : '',
                    ]
                    );
                }
            } else {
                foreach($mailchimp_lists['lists'] as $list) {
                    $count = ! empty( $list['stats']['member_count'] ) ? $list['stats']['member_count'] : 0;

                    $latest_run = \App\ContactEmailCampaigns::orderBy('id', 'desc')->first();


                    if ( ! $latest_run ) {
                        $latest_run = (Object) [
                            'is_schedule' => '',
                            'schedule_date' => '',
                            'created_at' => '',
                        ];
                    } else {
                        if ( ! empty( $latest_run->campaign_id ) ) {
                            $email_activity = $MailChimp->get('campaigns/'.$latest_run->campaign_id.'/email-activity');

                            if ( ! empty( $email_activity['total_items'] ) ) {

                            }
                        }
                    }
                    $lists->push([
                        'id' => $list['id'],
                        'name' => $list['name'],
                        'member_count' => $count,

                        'is_schedule' => $latest_run->is_schedule,
                        'schedule_date' => $latest_run->schedule_date,
                        'last_run' => ($latest_run->created_at) ? Carbon::createFromFormat('Y-m-d H:i:s', $latest_run->created_at)
                      ->format($date_set . ' H:i:s') : '',
                    ]
                    );
                }
            }


            $table = app('datatables')->collection($lists);


            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use( $list_id ) {
                $str = '<a href="'.route('admin.contacts.mailchimp-email-campaigns', $row['id']).'" class="btn btn-xs btn-primary">View</a>';
                $str .= '&nbsp;<a href="'.route('admin.contacts.run-mailchimp-email-campaigns', $row['id']).'" class="btn btn-xs btn-info">Run campaign</a>';
                return $str;
            });

            $table->rawColumns(['actions']);


            return $table->toJson();
        }
        $list_deails = '';
        if ( $list_id ) {
            $list_deails = $MailChimp->get('lists/' . $list_id);
        }
        return view('admin.contacts.mailchimp-email-campaigns', compact('list_id', 'list_deails'));
    }

    public function RunMailchimpEmailCampaigns( Request $request, $list_id )
    {
        if (! Gate::allows('contact_mailchimp_email_campaigns')) {
            return prepareBlockUserMessage();
        }

        $api_key = getSetting('mailchimp_api_key', 'mailchimp-settings', '');

        $MailChimp = new DrewMailChimp( trim( $api_key ) );

        if ( ! empty( $MailChimp->getLastError() ) ) {
            return prepareBlockUserMessage('danger', 'create', $MailChimp->getLastError() );
        }
        $members = $MailChimp->get('lists/' . $list_id . '/members' . '?offset=0&count=200');

        $total_items = ! empty( $members['total_items'] ) ? $members['total_items'] : 0;
        if ( empty( $total_items ) ) {
            return prepareBlockUserMessage('danger', 'create', trans('custom.messages.no-members') );
        }

        if ( $request->isMethod('post') )
        {
            $rules = [
                'subject' => 'required',
                'from_name' => 'required',
                'from_email' => 'required|email',
                'content' => 'required',
            ];
            if ( 'yes' === $request->is_schedule ) {
                $rules['schedule_date'] = 'required';
            }
            $newsletter_subject = $request->subject;

            $addtional = [];

            $schedule_date = $request->schedule_date;
            if ( ! empty( $schedule_date ) && count( explode(' ', $schedule_date) ) == 1 ) {
                $schedule_date = $schedule_date . ' ' . date('H:i:s');
            }
            $addtional['schedule_date'] = $schedule_date;

            $request->request->add( $addtional ); //add additonal / Changed values to the request object.

            $data = [
               'content' => $request->content,
            ];
            $data['site_title'] = getSetting( 'site_title', 'site_settings');
            $logo = getSetting( 'site_logo', 'site_settings' );
            $data['logo'] = asset( 'uploads/settings/' . $logo );
            $data['date'] = digiTodayDateAdd();

            $data['site_address'] = getSetting( 'site_address', 'site_settings');
            $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
            $data['site_email'] = getSetting( 'contact_email', 'site_settings');

            $newsletter_html = render( \Blade::compileString(getTemplate('mailchimp-contact-email', $data)), $data );

            $MailChimp = new \Mailchimp(trim( $api_key )); // Its a different package.

            $request->from_email = 'adiyya@gmail.com';
            //Create a Campaign
	        $campaign = $MailChimp->campaigns->create('regular', [
	            'list_id' => $list_id,
	            'subject' => $newsletter_subject,
	            'from_email' => $request->from_email,
	            'from_name' => $request->from_name,

	        ], [
	            'html' => $newsletter_html,
	            'text' => strip_tags($newsletter_html)
	        ]);

            $date_set = getCurrentDateFormat();
            if ( empty( $campaign['error'] ) )
            {
                if ( 'yes' === $request->is_schedule ) {

                    $dateTime = Carbon::createFromFormat($date_set . ' H:i:s', $request->schedule_date)
                      ->format('Y-m-d H:i:s');

                    $result = $MailChimp->campaigns->schedule($campaign['id'], $dateTime);
                } else {
                    if ( env('APP_DEV') ) {
                    	$emails = ['adiyya@gmail.com', 'adiyya@conquerorstech.net'];
                    	try {
                            $result = $MailChimp->campaigns->sendTest($campaign['id'], $emails );
                        } catch( Exception $ex ) {
                            die( 'ddddd');
                        }
                    } else {
                    	$result = $MailChimp->campaigns->send($campaign['id']);
                	}
                }
            } else {
                $result['errors'] = $campaign['error'];
            }


            if ( ! empty( $result['error']) ) {
                $result['errors'] = $campaign['error'];
            }
            if ( ! empty( $result['errors'] ) ) {
                return back()->withErrors($result['errors']);
            } else {
                $data = array(
                    'list_id' => $list_id,
                    'list_name' => $request->list_name,
                    'subject' => $request->subject,
                    'from_name' => $request->from_name,
                    'from_email' => $request->from_email,
                    'is_schedule' => $request->is_schedule,
                    'schedule_date' => ($request->schedule_date) ? Carbon::createFromFormat($date_set . ' H:i:s', $request->schedule_date)->format('Y-m-d H:i:s') : null,
                    'content' => $newsletter_html,
                    'campaign_id' => $campaign['id'],
                );
                \App\ContactEmailCampaigns::create( $data );
                flashMessage( 'success', 'create', trans('custom.messages.mailsent') );
                return redirect()->route('admin.contacts.mailchimp-email-campaigns');
            }
        }

        $list_deails = '';
        if ( $list_id ) {
            $list_deails = $MailChimp->get('lists/' . $list_id);
        }

        $template = \Modules\Templates\Entities\Template::where('key', 'mailchimp-contact-email')->first();
        return view('admin.contacts.run-mailchimp-email-campaigns', compact('list_id', 'list_deails', 'members', 'template'));
    }

    public function sendBulkEmailQueue( $contact_type = '' )
    {
        if (! Gate::allows('contact_mailchimp_email_campaigns')) {
            return prepareBlockUserMessage();
        }


        if (request()->ajax()) {
            $query = \App\ContactType::query();
            if ( ! empty( $contact_type ) ) {

            }
            $table = Datatables::of($query);


            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use( $contact_type ) {
                $str = '<a href="'.route('admin.contact_types.show', $row->id).'" class="btn btn-xs btn-primary" target="_blank">View</a>';
                $str .= '&nbsp;<a href="'.route('admin.contacts.send-bulk-emails', $row->id).'" class="btn btn-xs btn-info">Send email</a>';
                return $str;
            });

            $table->editColumn('member_count', function ($row) use( $contact_type ) {
                return \App\Contact::whereHas("contact_type",
                function ($query) use( $row ) {
                    $query->where('id', $row->id);
                })->count();
            });

            $table->rawColumns(['actions']);
            return $table->make(true);
        }
        $list_deails = '';
        if ( $contact_type ) {
            $list_deails = \App\ContactType::find( $contact_type );
            $template = \Modules\Templates\Entities\Template::where('key', 'bulk-contact-email')->first();

            $emails = \App\Contact::whereHas("contact_type",
                function ($query) use( $contact_type ) {
                    $query->where('id', $contact_type);
                })->get()->pluck('email', 'email')->toArray();
            return view('admin.contacts.send-bulk-emails-form', compact('contact_type', 'list_deails', 'template', 'emails'));
        }
        return view('admin.contacts.send-bulk-emails', compact('contact_type', 'list_deails'));
    }

    public function runBulkEmailQueue( Request $request )
    {
        $rules = [
            'subject' => 'required',
            'from_name' => 'required',
            'from_email' => 'required|email',
            'emails.*' => 'required|email',
        ];
       Validator::make($request->all(), $rules);

       $emails = $request->emails;
       $delay = $request->send_after;

       $data = [
        'emails' => $emails,
        'delay' => $delay,
        'subject' => $request->subject,
        'from_name' => $request->from_name,
        'from_email' => $request->from_email,
        'content' => $request->content,
       ];

        $data['site_title'] = getSetting( 'site_title', 'site_settings');
        $logo = getSetting( 'site_logo', 'site_settings' );
        $data['logo'] = asset( 'uploads/settings/' . $logo );
        $data['date'] = digiTodayDateAdd();
        $data['site_address'] = getSetting( 'site_address', 'site_settings');
        $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
        $data['site_email'] = getSetting( 'contact_email', 'site_settings');


        Log::info("Request Cycle with Queues Begins");
            if ( $delay == 0 ) {
                $this->dispatch(new SendBulkEmail( $data ));
            } else {
                $this->dispatch(new SendBulkEmail( $data ))->delay(60 * $delay);
            }
        Log::info("Request Cycle with Queues Ends");

        flashMessage( 'success', 'create', trans('custom.messages.mailsent') );
        return redirect()->route('admin.contacts.send-bulk-emails');
    }

    public function leadConvert( $contact_id, $contact_type_id )
    {
        $contact = Contact::find( $contact_id );
        if ( ! $contact ) {
            flashMessage('danger', 'not-found');
            return back();
        }

        DB::table('contact_contact_type')->where('contact_id', $contact_id)->delete();
        $contact_types = $contact->contact_type()->sync( [ $contact_type_id ] );

        $contact_type = \App\ContactType::find( $contact_type_id );
        $title = $contact_type_id;
        if ( $contact_type ) {
            $title = $contact_type->title;
        }

        flashMessage( 'success', 'create', trans('custom.messages.lead-convert', ['type' => $title]) );
        return redirect()->route('admin.contacts.edit', $contact_id);
    }
}
