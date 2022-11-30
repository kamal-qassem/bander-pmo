<?php

namespace App\Http\Controllers\Admin;

use App\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWarehousesRequest;
use App\Http\Requests\Admin\UpdateWarehousesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
class WarehousesController extends Controller
{   
    public function __construct() {
       $this->middleware('plugin:productwarehouse');
    }
    /**
     * Display a listing of Warehouse.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('warehouse_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = Warehouse::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('warehouse_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'warehouses.id',
                'warehouses.name',
                'warehouses.address',
                'warehouses.description',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'warehouse_';
                $routeKey = 'admin.warehouses';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('address', function ($row) {
                return $row->address ? $row->address : '';
            });
            $table->editColumn('warehouse_description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.warehouses.index');
    }

    /**
     * Show the form for creating new Warehouse.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('warehouse_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.warehouses.create');
    }

    /**
     * Store a newly created Warehouse in storage.
     *
     * @param  \App\Http\Requests\StoreWarehousesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('warehouse_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'name' => 'required|unique:warehouses,name',
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
        $warehouse = Warehouse::create($request->all());

        if ( $request->ajax() ) {
            $warehouse->selectedid = 'ware_house_id';
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $warehouse]);
        } else {
            flashMessage( 'success', 'create' );
            return redirect()->route('admin.warehouses.index');
        }
    }


    /**
     * Show the form for editing Warehouse.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('warehouse_edit')) {
            return prepareBlockUserMessage();
        }
        $warehouse = Warehouse::findOrFail($id);

        return view('admin.warehouses.edit', compact('warehouse'));
    }

    /**
     * Update Warehouse in storage.
     *
     * @param  \App\Http\Requests\UpdateWarehousesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWarehousesRequest $request, $id)
    {
        if (! Gate::allows('warehouse_edit')) {
            return prepareBlockUserMessage();
        }
        $warehouse = Warehouse::findOrFail($id);

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $warehouse->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.warehouses.index');
    }


    /**
     * Display Warehouse.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('warehouse_view')) {
            return prepareBlockUserMessage();
        }
        
        $warehouse = Warehouse::findOrFail($id);

        return view('admin.warehouses.show', compact('warehouse',
        'list'));
    }


    /**
     * Remove Warehouse from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('warehouse_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.warehouses.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Warehouse at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('warehouse_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Warehouse::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Warehouse from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('warehouse_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $warehouse = Warehouse::onlyTrashed()->findOrFail($id);
        $warehouse->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete Warehouse from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('warehouse_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $warehouse = Warehouse::onlyTrashed()->findOrFail($id);
        $warehouse->forceDelete();

        return back();
    }
}
