<?php

namespace App\Http\Controllers\Admin;

use App\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaxesRequest;
use App\Http\Requests\Admin\UpdateTaxesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
class TaxesController extends Controller
{
    /**
     * Display a listing of Tax.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('tax_access')) {
            return prepareBlockUserMessage();
        }
        $currency = getSettings('local_settings', 'currency');
        
        if (request()->ajax()) {
            $query = Tax::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('tax_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'taxes.id',
                'taxes.name',
                'taxes.rate',
                'taxes.rate_type',
                'taxes.description',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'tax_';
                $routeKey = 'admin.taxes';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('rate', function ($row) {
                return $row->rate ? $row->rate : '';
            });
            $table->editColumn('rate_type', function ($row) {
                return $row->rate_type ? $row->rate_type : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.taxes.index');
    }

    /**
     * Show the form for creating new Tax.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('tax_create')) {
            return prepareBlockUserMessage();
        }        $enum_rate_type = Tax::$enum_rate_type;
            
        return view('admin.taxes.create', compact('enum_rate_type'));
    }

    /**
     * Store a newly created Tax in storage.
     *
     * @param  \App\Http\Requests\StoreTaxesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('tax_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'name' => 'required|unique:taxes,name',
            'rate' => 'numeric|required',
        ];
        if ( 'percent' === $request->rate_type ) {
            $rules['rate'] = 'required|numeric|max:100';
        }
        
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
        $tax = Tax::create($request->all());
        
        if ( $request->ajax() ) {
            $tax->selectedid = 'tax_id';
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $tax]);
        } else {
            flashMessage( 'success', 'create' );
            return redirect()->route('admin.taxes.index');
        }
    }


    /**
     * Show the form for editing Tax.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('tax_edit')) {
            return prepareBlockUserMessage();
        }        $enum_rate_type = Tax::$enum_rate_type;
            
        $tax = Tax::findOrFail($id);

        return view('admin.taxes.edit', compact('tax', 'enum_rate_type'));
    }

    /**
     * Update Tax in storage.
     *
     * @param  \App\Http\Requests\UpdateTaxesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaxesRequest $request, $id)
    {
        if (! Gate::allows('tax_edit')) {
            return prepareBlockUserMessage();
        }
        $tax = Tax::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $tax->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.taxes.index');
    }


    /**
     * Display Tax.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('tax_view')) {
            return prepareBlockUserMessage();
        }
        
        $tax = Tax::findOrFail($id);

        return view('admin.taxes.show', compact('tax', 'list'));
    }


    /**
     * Remove Tax from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('tax_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $tax = Tax::findOrFail($id);
        $tax->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.taxes.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Tax at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('tax_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Tax::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Tax from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('tax_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $tax = Tax::onlyTrashed()->findOrFail($id);
        $tax->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete Tax from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('tax_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $tax = Tax::onlyTrashed()->findOrFail($id);
        $tax->forceDelete();

        return back();
    }
}
