<?php

namespace App\Http\Controllers\Admin;

use App\MeasurementUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMeasurementUnitsRequest;
use App\Http\Requests\Admin\UpdateMeasurementUnitsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class MeasurementUnitsController extends Controller
{   
    public function __construct() {
       $this->middleware('plugin:productmeasurementunits');
    }
    /**
     * Display a listing of MeasurementUnit.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('measurement_unit_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = MeasurementUnit::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('measurement_unit_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'measurement_units.id',
                'measurement_units.title',
                'measurement_units.status',
                'measurement_units.description',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'measurement_unit_';
                $routeKey = 'admin.measurement_units';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.measurement_units.index');
    }

    /**
     * Show the form for creating new MeasurementUnit.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('measurement_unit_create')) {
            return prepareBlockUserMessage();
        }        $enum_status = MeasurementUnit::$enum_status;
            
        return view('admin.measurement_units.create', compact('enum_status'));
    }

    /**
     * Store a newly created MeasurementUnit in storage.
     *
     * @param  \App\Http\Requests\StoreMeasurementUnitsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMeasurementUnitsRequest $request)
    {
        if (! Gate::allows('measurement_unit_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $measurement_unit = MeasurementUnit::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.measurement_units.index');
    }


    /**
     * Show the form for editing MeasurementUnit.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('measurement_unit_edit')) {
            return prepareBlockUserMessage();
        }        $enum_status = MeasurementUnit::$enum_status;
            
        $measurement_unit = MeasurementUnit::findOrFail($id);

        return view('admin.measurement_units.edit', compact('measurement_unit', 'enum_status'));
    }

    /**
     * Update MeasurementUnit in storage.
     *
     * @param  \App\Http\Requests\UpdateMeasurementUnitsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMeasurementUnitsRequest $request, $id)
    {
        if (! Gate::allows('measurement_unit_edit')) {
            return prepareBlockUserMessage();
        }
        $measurement_unit = MeasurementUnit::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $measurement_unit->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.measurement_units.index');
    }


    /**
     * Display MeasurementUnit.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('measurement_unit_view')) {
            return prepareBlockUserMessage();
        }
        $measurement_unit = MeasurementUnit::findOrFail($id);

        return view('admin.measurement_units.show', compact('measurement_unit'));
    }


    /**
     * Remove MeasurementUnit from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('measurement_unit_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $measurement_unit = MeasurementUnit::findOrFail($id);
        $measurement_unit->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.measurement_units.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
      }
    }

    /**
     * Delete all selected MeasurementUnit at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('measurement_unit_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = MeasurementUnit::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore MeasurementUnit from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('measurement_unit_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $measurement_unit = MeasurementUnit::onlyTrashed()->findOrFail($id);
        $measurement_unit->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete MeasurementUnit from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('measurement_unit_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $measurement_unit = MeasurementUnit::onlyTrashed()->findOrFail($id);
        $measurement_unit->forceDelete();

        return back();
    }
}
