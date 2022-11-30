<?php

namespace App\Http\Controllers\Admin;

use App\ContactCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContactCompaniesRequest;
use App\Http\Requests\Admin\UpdateContactCompaniesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
class ContactCompaniesController extends Controller
{
    /**
     * Display a listing of ContactCompany.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {
        if (! Gate::allows('contact_company_access')) {
            return prepareBlockUserMessage();
        }

        if (request()->ajax()) {
            $query = ContactCompany::query();
            $template = 'actionsTemplate';
            $query->with('country');
            $query->select([
                'contact_companies.id',
                'contact_companies.name',
                'contact_companies.email',
                'contact_companies.address',
                'contact_companies.country_id',
                'contact_companies.website',
            ]);

            /**
             * when we call invoices display from other pages!
            */
            if ( ! empty( $type ) && 'country' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('contact_companies.country_id', $type_id);
                });
            }
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'contact_company_';
                $routeKey = 'admin.contact_companies';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : '';
            });
            $table->editColumn('address', function ($row) {
                return $row->address ? $row->address : '';
            });

             $table->editColumn('country.title', function ($row) {
                return $row->country ? $row->country->title : '';
            });


            $table->editColumn('website', function ($row) {
                return $row->website ? '<a href="'. $row->website.'" target="_blank">' . $row->website . '</a>' : '';
            });

            $table->rawColumns(['actions','massDelete','website']);

            return $table->make(true);
        }

        $csvtemplatepath = asset( 'csvtemplates/contact-companies.csv');
        return view('admin.contact_companies.index', compact('csvtemplatepath'));
    }

    /**
     * Show the form for creating new ContactCompany.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('contact_company_create')) {
            return prepareBlockUserMessage();
        }
		$countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        return view('admin.contact_companies.create', compact('countries'));
    }

    /**
     * Store a newly created ContactCompany in storage.
     *
     * @param  \App\Http\Requests\StoreContactCompaniesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request  $request)
    {
        if (! Gate::allows('contact_company_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'name' => 'required|unique:contact_companies,name',
            'email' => 'nullable|email|unique:contact_companies,email',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if ( ! $validator->passes() ) {
            if ( $request->ajax() ) {
                return response()->json(['error'=>$validator->errors()->all()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_company = ContactCompany::create($request->all());

        
        if ( $request->ajax() ) {
            $contact_company->selectedid = $request->selectedid;
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $contact_company]);
        } else {
            flashMessage( 'success', 'create' );
            return redirect()->route('admin.contact_companies.index');
        }
    }


    /**
     * Show the form for editing ContactCompany.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('contact_company_edit')) {
            return prepareBlockUserMessage();
        }
        $contact_company = ContactCompany::findOrFail($id);
		
		$countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

        return view('admin.contact_companies.edit', compact('contact_company', 'countries'));
    }

    /**
     * Update ContactCompany in storage.
     *
     * @param  \App\Http\Requests\UpdateContactCompaniesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContactCompaniesRequest $request, $id)
    {
        if (! Gate::allows('contact_company_edit')) {
            return prepareBlockUserMessage();
        }
        $contact_company = ContactCompany::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_company->update($request->all());

        flashMessage( 'success', 'update' );

        return redirect()->route('admin.contact_companies.index');
    }


    /**
     * Display ContactCompany.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('contact_company_view')) {
            return prepareBlockUserMessage();
        }
        
        $contact_company = ContactCompany::findOrFail($id);

        return view('admin.contact_companies.show', compact('contact_company', 'list'));
    }


    /**
     * Remove ContactCompany from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('contact_company_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_company = ContactCompany::findOrFail($id);
        $contact_company->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.contact_companies.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected ContactCompany at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('contact_company_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ContactCompany::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }

}
