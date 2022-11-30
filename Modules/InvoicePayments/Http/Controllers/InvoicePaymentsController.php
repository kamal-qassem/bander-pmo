<?php

namespace Modules\InvoicePayments\Http\Controllers;

use Modules\InvoicePayments\Entities\InvoicePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\InvoicePayments\Http\Requests\Admin\StoreInvoicePaymentsRequest;
use Modules\InvoicePayments\Http\Requests\Admin\UpdateInvoicePaymentsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class InvoicePaymentsController extends Controller
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
            $query = InvoicePayment::query();
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
                'invoice_payments.id',
                'invoice_payments.invoice_id',
                'invoice_payments.date',
                'invoice_payments.account_id',
                'invoice_payments.amount',
                'invoice_payments.transaction_id',
            ]);
			
			$query->orderBy('id', 'desc');
			
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'invoice_payment_';
                $routeKey = 'admin.invoice_payments';

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

        return view('admin.invoice_payments.index');
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

        return view('admin.invoice_payments.create', compact('invoices', 'accounts'));
    }

    /**
     * Store a newly created InvoicePayment in storage.
     *
     * @param  \App\Http\Requests\StoreInvoicePaymentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvoicePaymentsRequest $request)
    {
        if (! Gate::allows('invoice_payment_create')) {
            return prepareBlockUserMessage();
        }
        $invoice_payment = InvoicePayment::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.invoice_payments.index');
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
        
        $invoices = \App\Invoice::get()->pluck('invoice_no', 'id')->prepend(trans('global.app_please_select'), '');
        $accounts = \App\Account::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $invoice_payment = InvoicePayment::findOrFail($id);

        return view('admin.invoice_payments.edit', compact('invoice_payment', 'invoices', 'accounts'));
    }

    /**
     * Update InvoicePayment in storage.
     *
     * @param  \App\Http\Requests\UpdateInvoicePaymentsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvoicePaymentsRequest $request, $id)
    {
        if (! Gate::allows('invoice_payment_edit')) {
            return prepareBlockUserMessage();
        }
        $invoice_payment = InvoicePayment::findOrFail($id);
        $invoice_payment->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.invoice_payments.index');
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
        $invoice_payment = InvoicePayment::findOrFail($id);

        return view('admin.invoice_payments.show', compact('invoice_payment'));
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
        $invoice_payment = InvoicePayment::findOrFail($id);
        $invoice_payment->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.invoice_payments.index');
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
            $entries = InvoicePayment::whereIn('id', $request->input('ids'))->get();

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
        $invoice_payment = InvoicePayment::onlyTrashed()->findOrFail($id);
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
        $invoice_payment = InvoicePayment::onlyTrashed()->findOrFail($id);
        $invoice_payment->forceDelete();

        return back();
    }
}
