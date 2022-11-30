<?php

namespace App\Http\Controllers\Admin;

use App\CreditNotePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;

use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class CreditNotePaymentsController extends Controller
{
    /**
     * Display a listing of InvoicePayment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('invoice_payment_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = CreditNotePayment::query();
            $query->with("invoice");
            $query->with("account");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('invoice_payment_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'credit_note_payments.id',
                'credit_note_payments.credit_note_id',
                'credit_note_payments.date',
                'credit_note_payments.account_id',
                'credit_note_payments.amount',
                'credit_note_payments.transaction_id',
            ]);
			
			$query->orderBy('id', 'desc');
			
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'credit_note_payment_';
                $routeKey = 'admin.credit_note_payments';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('invoice.invoice_no', function ($row) {
                return $row->invoice ? $row->invoice->invoice_no : '';
            });
            $table->editColumn('date', function ($row) {
                return $row->date ? $row->date : '';
            });
            $table->editColumn('account.name', function ($row) {
                return $row->account ? $row->account->name : '';
            });
            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });
            $table->editColumn('transaction_id', function ($row) {
                return $row->transaction_id ? $row->transaction_id : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.credit_note_payments.index');
    }

    /**
     * Show the form for creating new InvoicePayment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('invoice_payment_create')) {
            return prepareBlockUserMessage();
        }
        
        $invoices = \App\Invoice::get()->pluck('invoice_no', 'id')->prepend(trans('global.app_please_select'), '');
        $accounts = \App\Account::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('admin.credit_note_payments.create', compact('invoices', 'accounts'));
    }

    /**
     * Store a newly created InvoicePayment in storage.
     *
     * @param  \App\Http\Requests\StoreInvoicePaymentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('invoice_payment_create')) {
            return prepareBlockUserMessage();
        }
        $invoice_payment = CreditNotePayment::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.credit_note_payments.index');
    }


    /**
     * Show the form for editing InvoicePayment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('invoice_payment_edit')) {
            return prepareBlockUserMessage();
        }
        
        $invoices = \App\CreditNote::get()->pluck('invoice_no', 'id')->prepend(trans('global.app_please_select'), '');
        $accounts = \App\Account::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $invoice_payment = CreditNotePayment::findOrFail($id);

        return view('admin.credit_note_payments.edit', compact('invoice_payment', 'invoices', 'accounts'));
    }

    /**
     * Update InvoicePayment in storage.
     *
     * @param  \App\Http\Requests\UpdateInvoicePaymentsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! Gate::allows('invoice_payment_edit')) {
            return prepareBlockUserMessage();
        }
        $invoice_payment = CreditNotePayment::findOrFail($id);
        $invoice_payment->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.credit_note_payments.index');
    }


    /**
     * Display InvoicePayment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('invoice_payment_view')) {
            return prepareBlockUserMessage();
        }
        $invoice_payment = CreditNotePayment::findOrFail($id);

        return view('admin.credit_note_payments.show', compact('invoice_payment'));
    }


    /**
     * Remove InvoicePayment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('invoice_payment_delete')) {
            return prepareBlockUserMessage();
        }
        $invoice_payment = CreditNotePayment::findOrFail($id);
        $invoice_payment->delete();

        flashMessage( 'success', 'delete' );
         if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.credit_note_payments.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected InvoicePayment at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('invoice_payment_delete')) {
            return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = CreditNotePayment::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore InvoicePayment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('invoice_payment_delete')) {
            return prepareBlockUserMessage();
        }
        $invoice_payment = CreditNotePayment::onlyTrashed()->findOrFail($id);
        $invoice_payment->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete InvoicePayment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('invoice_payment_delete')) {
            return prepareBlockUserMessage();
        }
        $invoice_payment = CreditNotePayment::onlyTrashed()->findOrFail($id);
        $invoice_payment->forceDelete();

        return back();
    }
}
