<?php
namespace Modules\Quotes\Http\Controllers\Admin;

use Modules\Quotes\Entities\Quote;
use Modules\Quotes\Entities\QuotesNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Quotes\Http\Requests\Admin\StoreQuotesNotesRequest;
use Modules\Quotes\Http\Requests\Admin\UpdateQuotesNotesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class QuotesNotesController extends Controller
{
    /**
     * Display a listing of QuotesNote.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $quote_id )
    {
        
        if (! Gate::allows('quote_note_access')) {
            return prepareBlockUserMessage();
        }

        $quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
    
        if (request()->ajax()) {
            $query = QuotesNote::query();
            $query->with("quote");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        
            abort_if (! Gate::allows('quotes_note_delete'), 401);


                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'quotes_notes.id',
                'quotes_notes.description',
                'quotes_notes.date_contacted',
                'quotes_notes.quote_id',
                'quotes_notes.created_by_id',
            ]);
            $query->where('quote_id', '=', $quote_id );
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'quotes_note_';
                $routeKey = 'admin.quotes_notes';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('description', function ($row) {
                $description = $row->description ? $row->description : '';
                if ( ! empty( $description ) ) {
                    $description = Str::limit($description, 40, '<a href="'.route('admin.quotes_notes.show', ['quote_id' => $row->quote_id, 'id' => $row->id]).'">...</a>');
                }
                return $description;
            });

            
            $table->editColumn('date_contacted', function ($row) {
                return $row->date_contacted ? digiDate( $row->date_contacted ) : '';
            });
            $table->editColumn('quote.status', function ($row) {
                return $row->quote ? $row->quote->status : '';
            });

               $table->editColumn('created_by.name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions','massDelete', 'description']);

            return $table->make(true);
        }

        return view('quotes::admin.quotes.quotes_notes.index', compact('quote'));
    }

    /**
     * Show the form for creating new QuotesNote.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $quote_id )
    {
        
        if (! Gate::allows('quote_note_create')) {
            return prepareBlockUserMessage();
        }

        $quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $quotes = Quote::get()->pluck('status', 'id')->prepend(trans('global.app_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('quotes::admin.quotes.quotes_notes.create', compact('quotes','created_bies','quote'));
    }

    /**
     * Store a newly created QuotesNote in storage.
     *
     * @param  \App\Http\Requests\StoreQuotesNotesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuotesNotesRequest $request, $quote_id)
    {
        
        if (! Gate::allows('quote_note_create')) {
            return prepareBlockUserMessage();
        }
        $addtional = array(
            'quote_id' => $quote_id,
            'created_by_id' => Auth::id(),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $quotes_note = QuotesNote::create($request->all());

        flashMessage( 'success', 'create');

        return redirect()->route('admin.quotes_notes.index', $quote_id);
    }


    /**
     * Show the form for editing QuotesNote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($quote_id, $id)
    {
        
        if (! Gate::allows('quote_note_edit')) {
            return prepareBlockUserMessage();
        }

        $quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $quotes = Quote::get()->pluck('status', 'id')->prepend(trans('global.app_please_select'), '');

        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $quotes_note = QuotesNote::findOrFail($id);

        return view('quotes::admin.quotes.quotes_notes.edit', compact('quotes_note', 'created_bies','quotes', 'quote'));
    }

    /**
     * Update QuotesNote in storage.
     *
     * @param  \App\Http\Requests\UpdateQuotesNotesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuotesNotesRequest $request, $quote_id, $id)
    {
        if (! Gate::allows('quote_note_edit')) {
            return prepareBlockUserMessage();
        }

        $quotes_note = QuotesNote::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $quotes_note->update($request->all());

        flashMessage( 'success', 'update');

        return redirect()->route('admin.quotes_notes.index', $quote_id);
    }


    /**
     * Display QuotesNote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($quote_id, $id)
    {

        if (! Gate::allows('quote_note_view')) {
            return prepareBlockUserMessage();
        }

        $quote = Quote::find( $quote_id );

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $quotes_note = QuotesNote::findOrFail($id);

        return view('quotes::admin.quotes.quotes_notes.show', compact('quotes_note', 'quote'));
    }


    /**
     * Remove QuotesNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $quote_id, $id)
    {
        if (! Gate::allows('quote_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $quotes_note = QuotesNote::findOrFail($id);
        $quotes_note->delete();

        flashMessage( 'success', 'delete');

        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
    }

    /**
     * Delete all selected QuotesNote at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('quote_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = QuotesNote::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
            flashMessage( 'success', 'deletes');
        }
    }


    /**
     * Restore QuotesNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('quote_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $quotes_note = QuotesNote::onlyTrashed()->findOrFail($id);
        $quotes_note->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete QuotesNote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('quote_note_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $quotes_note = QuotesNote::onlyTrashed()->findOrFail($id);

        $quote_id = $quotes_note->quote_id;
        
        $quotes_note->forceDelete();

        flashMessage( 'success', 'delete');

        return back();
    }
}
