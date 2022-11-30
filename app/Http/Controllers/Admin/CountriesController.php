<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCountriesRequest;
use App\Http\Requests\Admin\UpdateCountriesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
class CountriesController extends Controller
{
    /**
     * Display a listing of Country.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('country_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = Country::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('country_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'countries.id',
                'countries.shortcode',
                'countries.title',
                'countries.dialcode',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'country_';
                $routeKey = 'admin.countries';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('shortcode', function ($row) {
                return $row->shortcode ? $row->shortcode : '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('dialcode', function ($row) {
                return $row->dialcode ? $row->dialcode : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.countries.index');
    }

    /**
     * Show the form for creating new Country.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('country_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.countries.create');
    }

    /**
     * Store a newly created Country in storage.
     *
     * @param  \App\Http\Requests\StoreCountriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('country_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'shortcode' => 'required|unique:countries,shortcode',
            'title' => 'required|unique:countries,title',
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

        $country = Country::create($request->all());

        if ( $request->ajax() ) {
            $country->selectedid = $request->selectedid;
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $country]);
        } else {
            flashMessage( 'success', 'create' );
            return redirect()->route('admin.countries.index');
        }
    }


    /**
     * Show the form for editing Country.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('country_edit')) {
            return prepareBlockUserMessage();
        }
        $country = Country::findOrFail($id);

        return view('admin.countries.edit', compact('country'));
    }

    /**
     * Update Country in storage.
     *
     * @param  \App\Http\Requests\UpdateCountriesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCountriesRequest $request, $id)
    {
        if (! Gate::allows('country_edit')) {
            return prepareBlockUserMessage();
        }
        $country = Country::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $country->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.countries.index');
    }


    /**
     * Display Country.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('country_view')) {
            return prepareBlockUserMessage();
        }
        

        $country = Country::findOrFail($id);

        return view('admin.countries.show', compact('country','list'));
    }


    /**
     * Remove Country from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('country_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $country = Country::findOrFail($id);
        $country->delete();

        flashMessage( 'success', 'delete' );
         if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.countries.index');
        } else {    
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Country at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('country_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Country::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Country from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('country_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $country = Country::onlyTrashed()->findOrFail($id);
        $country->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete Country from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('country_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $country = Country::onlyTrashed()->findOrFail($id);
        $country->forceDelete();

        return back();
    }
}
