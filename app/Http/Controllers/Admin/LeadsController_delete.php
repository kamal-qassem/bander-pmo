<?php

namespace App\Http\Controllers\Admin;

use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContactsRequest;
use App\Http\Requests\Admin\UpdateContactsRequest;
use Yajra\DataTables\DataTables;

use App\Http\Controllers\Traits\FileUploadTrait;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class LeadsController extends Controller
{
     use FileUploadTrait;

    /**
     * Display a listing of Contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '' )
    {
        if (! Gate::allows('lead_access')) {
           return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = Contact::query();
            $query->with("company");
            $query->with("group");
            $query->with("contact_type");
            $query->with("language");
            $query->with("country");
            $query->whereHas("contact_type",
            function ($query) {
                $query->where('id', LEADS_TYPE);
            });
            $template = 'actionsTemplate';
            
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
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'lead_';
                $routeKey = 'admin.leads';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('company.name', function ($row) {
                return $row->company ? $row->company->name : '';
            });
            $table->editColumn('thumbnail', function ($row) {
                if($row->thumbnail) { return '<a href="'. asset(env('UPLOAD_PATH').'/' . $row->thumbnail) .'" target="_blank"><img src="'. asset(env('UPLOAD_PATH').'/thumb/' . $row->thumbnail) .'"/>'; };
            });
            $table->editColumn('group.name', function ($row) {
                return $row->group ? $row->group->name : '';
            });
            $table->editColumn('contact_type.name', function ($row) {
                if(count($row->contact_type) == 0) {
                    return '';
                }

                return '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        $row->contact_type->pluck('name')->toArray()) . '</span>';
            });
            $table->editColumn('first_name', function ($row) {
                return $row->first_name ? $row->first_name : '';
            });
            $table->editColumn('last_name', function ($row) {
                return $row->last_name ? $row->last_name : '';
            });
            $table->editColumn('name', function ($row) {
                $name = $row->name ? $row->name : '';
                if ( empty( $name ) ) {
                    $name = $row->first_name ? $row->first_name : '';
                    if ( empty( $row->last_name ) ) {
                        $name .= ' ' . $row->last_name;
                    }
                }
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
                }
                return $fulladdress;
            });
            $table->editColumn('language.language', function ($row) {
                if(count($row->language) == 0) {
                    return '';
                }
                return '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        $row->language->pluck('language')->toArray()) . '</span>';
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
            $table->editColumn('country.title', function ($row) {
                return $row->country ? $row->country->title : '';
            });

            $table->rawColumns(['actions','massDelete','contact_type.name','language.language']);

            return $table->make(true);
        }

        return view('admin.contacts.leads.index');
    }

    

    /**
     * Show the form for creating new Contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('lead_create')) {
           return prepareBlockUserMessage();
        }
        
        $companies = \App\ContactCompany::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $groups = \App\ContactGroup::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $contact_types = \App\ContactType::get()->pluck('name', 'id');

        $languages = \App\Language::get()->pluck('language', 'id');

        $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

        $countries_code = \App\Country::get()->pluck('title', 'dialcode')->prepend(trans('global.app_please_select'), '');

        $redirect = route('admin.leads.index');

        $default_contact_type = LEADS_TYPE;

        return view('admin.contacts.leads.create', compact('companies', 'groups', 'contact_types', 'languages', 'countries', 'countries_code', 'redirect','default_contact_type'));
    }

    /**
     * Store a newly created Contact in storage.
     *
     * @param  \App\Http\Requests\StoreContactsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContactsRequest $request)
    {
        if (! Gate::allows('lead_create')) {
           return prepareBlockUserMessage();
        }

        $request = $this->saveFiles($request);

        $contact = Contact::create($request->all());
        $contact->contact_type()->sync(array_filter((array)$request->input('contact_type')));
        $contact->language()->sync(array_filter((array)$request->input('language')));

        flashMessage();

        return redirect()->route('admin.leads.index');
    }


    /**
     * Show the form for editing Contact.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('lead_edit')) {
           return prepareBlockUserMessage();
        }
        
        $companies = \App\ContactCompany::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $groups = \App\ContactGroup::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $contact_types = \App\ContactType::get()->pluck('name', 'id');

        $languages = \App\Language::get()->pluck('language', 'id');

        $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

        $contact = Contact::findOrFail($id);

        $countries_code = \App\Country::get()->pluck('title', 'dialcode')->prepend(trans('global.app_please_select'), '');

        $redirect = route('admin.leads.index');

        return view('admin.contacts.leads.edit', compact('contact', 'companies', 'groups', 'contact_types', 'languages', 'countries', 'countries_code', 'redirect'));
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
        if (! Gate::allows('lead_edit')) {
           return prepareBlockUserMessage();
        }

        $request = $this->saveFiles($request);

        $contact = Contact::findOrFail($id);
        $contact->update($request->all());
        $contact->contact_type()->sync(array_filter((array)$request->input('contact_type')));
        $contact->language()->sync(array_filter((array)$request->input('language')));

        flashMessage( 'success', 'update');

        return redirect()->route('admin.leads.index');
    }


    /**
     * Display Contact.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('lead_view')) {
           return prepareBlockUserMessage();
        }
        
        $companies = \App\ContactCompany::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $groups = \App\ContactGroup::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $contact_types = \App\ContactType::get()->pluck('name', 'id');

        $languages = \App\Language::get()->pluck('language', 'id');

        $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $invoices = \App\Invoice::where('customer_id', $id)->get();
        $quotes = \Modules\Quotes\Entities\Quote::where('customer_id', $id)->get();
        $recurring_invoices = \Modules\RecurringInvoices\Entities\RecurringInvoice::where('customer_id', $id)->get();
        $purchase_orders = \App\PurchaseOrder::where('customer_id', $id)->get();
        $contact_notes = \App\ContactNote::where('contact_id', $id)->get();
        $products_returns = \App\ProductsReturn::where('customer_id', $id)->get();
        $client_projects = \App\ClientProject::where('client_id', $id)->get();
        $contact_documents = \App\ContactDocument::where('contact_id', $id)->get();
        $users = \App\User::where('contact_reference_id', $id)->get();
        $incomes = \App\Income::where('payer_id', $id)->get();
        $expenses = \App\Expense::where('payee_id', $id)->get();

        $contact = Contact::findOrFail($id);

        $countries_code = \App\Country::get()->pluck('title', 'dialcode')->prepend(trans('global.app_please_select'), '');

        return view('admin.contacts.show', compact('contact', 'invoices', 'quotes', 'recurring_invoices', 'purchase_orders', 'contact_notes', 'products_returns', 'client_projects', 'contact_documents', 'users', 'incomes', 'expenses', 'countries_code'));
    }


    /**
     * Remove Contact from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('lead_delete')) {
           return prepareBlockUserMessage();
        }
        $contact = Contact::findOrFail($id);
        $contact->delete();

        flashMessage( 'success', 'delete' );

        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
    }

    /**
     * Delete all selected Contact at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('lead_delete')) {
           return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = Contact::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }
}
