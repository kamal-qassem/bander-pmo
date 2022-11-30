<?php

namespace Modules\InvoiceAdditional\Http\Controllers\Admin;

use App\Invoice;
use Modules\InvoiceAdditional\Entities\InvoiceNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\InvoiceAdditional\Http\Requests\Admin\StoreInvoiceNotesRequest;
use Modules\InvoiceAdditional\Http\Requests\Admin\UpdateInvoiceNotesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class InvoiceNotesController extends Controller
{
    /**
     * Display a listing of InvoiceNote.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $invoice_id )
    {
        if (! Gate::allows('invoice_note_access')) {
            return abort(401);
        }

$invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        if (request()->ajax()) {
            $query = InvoiceNote::query();
            $query->with("invoice");
            $query->with("created_by");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('invoice_note_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'invoice_notes.id',
                'invoice_notes.description',
                'invoice_notes.date_contacted',
                'invoice_notes.invoice_id',
                'invoice_notes.created_by_id',
            ]);
            $query->where('invoice_id', '=', $invoice_id );
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'invoice_note_';
                $routeKey = 'admin.invoice_notes';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('date_contacted', function ($row) {
                return $row->date_contacted ? $row->date_contacted : '';
            });
            $table->editColumn('invoice.title', function ($row) {
                return $row->invoice ? $row->invoice->title : '';
            });
            $table->editColumn('created_by.name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('invoiceadditional::admin.invoice_notes.index', compact('invoice'));
    }

    /**
     * Show the form for creating new InvoiceNote.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $invoice_id )
    {
        if (! Gate::allows('invoice_note_create')) {
            return abort(401);
        }

        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $invoices = Invoice::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('invoiceadditional::admin.invoice_notes.create', compact('invoices', 'created_bies', 'invoice'));
    }

    /**
     * Store a newly created InvoiceNote in storage.
     *
     * @param  \App\Http\Requests\StoreInvoiceNotesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvoiceNotesRequest $request, $invoice_id)
    {
        if (! Gate::allows('invoice_note_create')) {
            return abort(401);
        }

        $addtional = array(
            'invoice_id' => $invoice_id,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_note = InvoiceNote::create($request->all());

        flashMessage( 'success', 'create');

        return redirect()->route('admin.invoice_notes.index', $invoice_id);
    }


    /**
     * Show the form for editing InvoiceNote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($invoice_id, $id)
    {
        if (! Gate::allows('invoice_note_edit')) {
            return abort(401);
        }

        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        
        $invoices = Invoice::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $invoice_note = InvoiceNote::findOrFail($id);

        return view('invoiceadditional::admin.invoice_notes.edit', compact('invoice_note', 'invoices', 'created_bies', 'invoice'));
    }

    /**
     * Update InvoiceNote in storage.
     *
     * @param  \App\Http\Requests\UpdateInvoiceNotesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvoiceNotesRequest $request, $invoice_id, $id)
    {
        if (! Gate::allows('invoice_note_edit')) {
            return abort(401);
        }
        $invoice_note = InvoiceNote::findOrFail($id);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_note->update($request->all());

        flashMessage( 'success', 'update');

        return redirect()->route('admin.invoice_notes.index', $invoice_id);
    }


    /**
     * Display InvoiceNote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_id, $id)
    {
        if (! Gate::allows('invoice_note_view')) {
            return abort(401);
        }
        $invoice = Invoice::find( $invoice_id );

        if ( ! $invoice ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $invoice_note = InvoiceNote::findOrFail($id);

        return view('invoiceadditional::admin.invoice_notes.show', compact('invoice_note', 'invoice'));
    }


    /**
     * Remove InvoiceNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $invoice_id, $id)
    {
        if (! Gate::allows('invoice_note_delete')) {
            return abort(401);
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_note = InvoiceNote::findOrFail($id);
        $invoice_note->delete();

        flashMessage( 'success', 'delete');

        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
    }

    /**
     * Delete all selected InvoiceNote at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('invoice_note_delete')) {
            return abort(401);
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = InvoiceNote::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes');
        }
    }


    /**
     * Restore InvoiceNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        
        if (! Gate::allows('invoice_note_delete')) {
            return abort(401);
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
       
        $invoice_note = InvoiceNote::onlyTrashed()->findOrFail($id);
        $invoice_id = $invoice_note->invoice_id;
        $invoice_note->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete InvoiceNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('invoice_note_delete')) {
            return abort(401);
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice_note = InvoiceNote::onlyTrashed()->findOrFail($id);
        $invoice_id = $invoice_note->invoice_id;
        $invoice_note->forceDelete();
		flashMessage( 'success', 'delete');
        return back();
    }
}
