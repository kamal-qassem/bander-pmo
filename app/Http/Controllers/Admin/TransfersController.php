<?php

namespace App\Http\Controllers\Admin;

use App\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTransfersRequest;
use App\Http\Requests\Admin\UpdateTransfersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class TransfersController extends Controller
{
    /**
     * Display a listing of Transfer.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {
        if (! Gate::allows('transfer_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = Transfer::query();
            $query->with("from");
            $query->with("to");
            $query->with("payment_method");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('transfer_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'transfers.id',
                'transfers.from_id',
                'transfers.to_id',
                'transfers.date',
                'transfers.amount',
                'transfers.ref_no',
                'transfers.payment_method_id',
                'transfers.description',
            ]);

            /**
             * when we call invoices display from other pages!
            */
            if ( ! empty( $type ) && 'account' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where(function($orquery) use( $type_id ){
                        $orquery->where('transfers.from_id', $type_id)
                              ->orWhere('transfers.to_id', $type_id);
                    });
                });
            }
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'transfer_';
                $routeKey = 'admin.transfers';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('from.name', function ($row) {
                return $row->from ? $row->from->name : '';
            });
            $table->editColumn('to.name', function ($row) {
                return $row->to ? $row->to->name : '';
            });
            $table->editColumn('date', function ($row) {
                return $row->date ? $row->date : '';
            });
            $table->editColumn('amount', function ($row) {
                return $row->amount ? digiCurrency( $row->amount ) : '';
            });
            $table->editColumn('ref_no', function ($row) {
                return $row->ref_no ? $row->ref_no : '';
            });
            $table->editColumn('payment_method.name', function ($row) {
                return $row->payment_method ? $row->payment_method->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.transfers.index');
    }

    /**
     * Show the form for creating new Transfer.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('transfer_create')) {
            return prepareBlockUserMessage();
        }
        
        $froms = \App\Account::get()->pluck('namebalance', 'id')->prepend(trans('global.app_please_select'), '');
        $tos = \App\Account::get()->pluck('namebalance', 'id')->prepend(trans('global.app_please_select'), '');
        $payment_methods = \App\PaymentGateway::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('admin.transfers.create', compact('froms', 'tos', 'payment_methods'));
    }

    /**
     * Store a newly created Transfer in storage.
     *
     * @param  \App\Http\Requests\StoreTransfersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransfersRequest $request)
    {
        if (! Gate::allows('transfer_create')) {
            return prepareBlockUserMessage();
        }
       
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $transfer = Transfer::create($request->all());

        \App\Account::find($request->from_id)->decrement('initial_balance', $request->amount);
        \App\Account::find( $request->to_id )->increment('initial_balance', $request->amount);

        flashMessage( 'success', 'create', trans('custom.transfers.amount-transfered'));

        return redirect()->route('admin.transfers.index');
    }


    /**
     * Show the form for editing Transfer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('transfer_edit')) {
            return prepareBlockUserMessage();
        }
        
        $froms = \App\Account::get()->pluck('namebalance', 'id')->prepend(trans('global.app_please_select'), '');
        $tos = \App\Account::get()->pluck('namebalance', 'id')->prepend(trans('global.app_please_select'), '');
        $payment_methods = \App\PaymentGateway::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $transfer = Transfer::findOrFail($id);

        return view('admin.transfers.edit', compact('transfer', 'froms', 'tos', 'payment_methods'));
    }

    /**
     * Update Transfer in storage.
     *
     * @param  \App\Http\Requests\UpdateTransfersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransfersRequest $request, $id)
    {
        if (! Gate::allows('transfer_edit')) {
            return prepareBlockUserMessage();
        }
        $transfer = Transfer::findOrFail($id);

        // Restrore the previous values.
        \App\Account::find($transfer->from_id)->increment('initial_balance', $transfer->amount);
        \App\Account::find( $transfer->to_id )->decrement('initial_balance', $transfer->amount);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $transfer->update($request->all());

        // Update new values.
        \App\Account::find($request->from_id)->decrement('initial_balance', $request->amount);
        \App\Account::find( $request->to_id )->increment('initial_balance', $request->amount);

        flashMessage( 'success', 'update' );

        return redirect()->route('admin.transfers.index');
    }


    /**
     * Display Transfer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('transfer_view')) {
            return prepareBlockUserMessage();
        }
        $transfer = Transfer::findOrFail($id);

        return view('admin.transfers.show', compact('transfer'));
    }


    /**
     * Remove Transfer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('transfer_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $transfer = Transfer::findOrFail($id);

        // Restrore the previous values.
        \App\Account::find($transfer->from_id)->increment('initial_balance', $transfer->amount);
        \App\Account::find( $transfer->to_id )->decrement('initial_balance', $transfer->amount);

        $transfer->delete();

        flashMessage( 'success', 'delete' );    
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.transfers.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Transfer at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('transfer_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Transfer::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                
                // Restrore the previous values.
                \App\Account::find($entry->from_id)->increment('initial_balance', $entry->amount);
                \App\Account::find( $entry->to_id )->decrement('initial_balance', $entry->amount);

                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Transfer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('transfer_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $transfer = Transfer::onlyTrashed()->findOrFail($id);

        \App\Account::find($transfer->from_id)->decrement('initial_balance', $transfer->amount);
        \App\Account::find( $transfer->to_id )->increment('initial_balance', $transfer->amount);

        $transfer->restore();

        flashMessage( 'success', 'restore' );

        return back();
    }

    /**
     * Permanently delete Transfer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('transfer_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $transfer = Transfer::onlyTrashed()->findOrFail($id);

        \App\Account::find($transfer->from_id)->increment('initial_balance', $transfer->amount);
        \App\Account::find( $transfer->to_id )->decrement('initial_balance', $transfer->amount);

        $transfer->forceDelete();

        flashMessage( 'success', 'delete' );

        return back();
    }
}
