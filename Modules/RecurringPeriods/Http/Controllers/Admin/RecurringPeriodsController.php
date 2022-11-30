<?php

namespace Modules\RecurringPeriods\Http\Controllers\Admin;

use Modules\RecurringPeriods\Entities\RecurringPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\RecurringPeriods\Http\Requests\Admin\StoreRecurringPeriodsRequest;
use Modules\RecurringPeriods\Http\Requests\Admin\UpdateRecurringPeriodsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Validator;
class RecurringPeriodsController extends Controller
{
    /**
     * Display a listing of RecurringPeriod.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('recurring_period_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = RecurringPeriod::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('recurring_period_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'recurring_periods.id',
                'recurring_periods.title',
                'recurring_periods.value',
                'recurring_periods.type',
                'recurring_periods.description',
            ]);
			
			$query->orderBy('id', 'desc');
			
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'recurring_period_';
                $routeKey = 'admin.recurring_periods';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('value', function ($row) {
                return $row->value ? $row->value . ' '. ucfirst( Str::plural( $row->type, $row->value ) ) : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('recurringperiods::admin.recurring_periods.index');
    }

    /**
     * Show the form for creating new RecurringPeriod.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('recurring_period_create')) {
            return prepareBlockUserMessage();
        }

        
        return view('recurringperiods::admin.recurring_periods.create');
    }

    /**
     * Store a newly created RecurringPeriod in storage.
     *
     * @param  \App\Http\Requests\StoreRecurringPeriodsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('recurring_period_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'title' => 'required|unique:recurring_periods,title',
            'value' => 'required',
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
        $recurring_period = RecurringPeriod::create($request->all());
        
        if ( $request->ajax() ) {
            $recurring_period->selectedid = $request->selectedid;
            $recurring_period->fetchaddress = $request->fetchaddress;            
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $recurring_period]);
        } else {
            flashMessage( 'success', 'create' );
            return redirect()->route('admin.recurring_periods.index');
        }
    }


    /**
     * Show the form for editing RecurringPeriod.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('recurring_period_edit')) {
            return prepareBlockUserMessage();
        }
        $recurring_period = RecurringPeriod::findOrFail($id);

        return view('recurringperiods::admin.recurring_periods.edit', compact('recurring_period'));
    }

    /**
     * Update RecurringPeriod in storage.
     *
     * @param  \App\Http\Requests\UpdateRecurringPeriodsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRecurringPeriodsRequest $request, $id)
    {
        if (! Gate::allows('recurring_period_edit')) {
            return prepareBlockUserMessage();
        }
        $recurring_period = RecurringPeriod::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_period->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.recurring_periods.index');
    }


    /**
     * Display RecurringPeriod.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('recurring_period_view')) {
            return prepareBlockUserMessage();
        }
        $recurring_invoices = \Modules\RecurringInvoices\Entities\RecurringInvoice::where('recurring_period_id', $id)->get();

        $recurring_period = RecurringPeriod::findOrFail($id);

        return view('recurringperiods::admin.recurring_periods.show', compact('recurring_period', 'recurring_invoices'));
    }


    /**
     * Remove RecurringPeriod from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('recurring_period_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_period = RecurringPeriod::findOrFail($id);
        $recurring_period->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.recurring_periods.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
       }
    }

    /**
     * Delete all selected RecurringPeriod at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('recurring_period_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        flashMessage( 'success', 'deletes' );

        if ($request->input('ids')) {
            $entries = RecurringPeriod::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore RecurringPeriod from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('recurring_period_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_period = RecurringPeriod::onlyTrashed()->findOrFail($id);
        $recurring_period->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete RecurringPeriod from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('recurring_period_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_period = RecurringPeriod::onlyTrashed()->findOrFail($id);
        $recurring_period->forceDelete();

        return back();
    }
}
