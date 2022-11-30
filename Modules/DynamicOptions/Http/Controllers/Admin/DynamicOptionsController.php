<?php

namespace Modules\DynamicOptions\Http\Controllers\Admin;

use Modules\DynamicOptions\Entities\DynamicOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\DynamicOptions\Http\Requests\Admin\StoreDynamicOptionsRequest;
use Modules\DynamicOptions\Http\Requests\Admin\UpdateDynamicOptionsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class DynamicOptionsController extends Controller
{   

    public function __construct() {
        $this->middleware('plugin:dynamicoptions');
    }
    /**
     * Display a listing of DynamicOption.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('dynamic_option_access')) {
            return prepareBlockUserMessage();
        }
        
        if (request()->ajax()) {
            $query = DynamicOption::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('dynamic_option_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'dynamic_options.id',
                'dynamic_options.title',
                'dynamic_options.module',
                'dynamic_options.type',
                'dynamic_options.color',
                'dynamic_options.description',
            ]);
			
			$query->orderBy('module', 'asc');
            $query->orderBy('type', 'asc');
			
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'dynamic_option_';
                $routeKey = 'admin.dynamic_options';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
                $title = $row->title ? $row->title : '';
                if ( ! empty( $title ) && ! empty( $row->color ) ) {
                    $title = '<span style="color:'.$row->color.'">'.$title.'</span>';
                }
                return $title;
            });
            $table->editColumn('module', function ($row) {
                return $row->module ? ucfirst( $row->module ) . ' ('. ucfirst( Str::plural( $row->type, $row->module ) ) . ')' : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete', 'title']);

            return $table->make(true);
        }
        return view('dynamicoptions::admin.dynamic_options.index');
    }

    /**
     * Show the form for creating new DynamicOption.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('dynamic_option_create')) {
            return prepareBlockUserMessage();
        }

        
        return view('dynamicoptions::admin.dynamic_options.create');
    }

    /**
     * Store a newly created DynamicOption in storage.
     *
     * @param  \App\Http\Requests\StoreDynamicOptionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDynamicOptionsRequest $request)
    {
        if (! Gate::allows('dynamic_option_create')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $dynamic_option = DynamicOption::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.dynamic_options.index');
    }


    /**
     * Show the form for editing DynamicOption.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('dynamic_option_edit')) {
            return prepareBlockUserMessage();
        }
        $dynamic_option = DynamicOption::findOrFail($id);

        return view('dynamicoptions::admin.dynamic_options.edit', compact('dynamic_option'));
    }

    /**
     * Update DynamicOption in storage.
     *
     * @param  \App\Http\Requests\UpdateDynamicOptionsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDynamicOptionsRequest $request, $id)
    {
        if (! Gate::allows('dynamic_option_edit')) {
            return prepareBlockUserMessage();
        }
        $recurring_period = DynamicOption::findOrFail($id);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_period->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.dynamic_options.index');
    }


    /**
     * Display DynamicOption.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('dynamic_option_view')) {
            return prepareBlockUserMessage();
        }

        $invoice_priorities = \Modules\InvoiceAdditional\Entities\InvoiceTask::where('priority_id', $id)->get();
        $invoice_tasks = \Modules\InvoiceAdditional\Entities\InvoiceTask::where('status_id', $id)->get();

        $quote_priorities = \Modules\Quotes\Entities\QuoteTask::where('priority_id', $id)->get();
        $quote_tasks = \Modules\Quotes\Entities\QuoteTask::where('status_id', $id)->get();

        $dynamic_option = DynamicOption::findOrFail($id);

        return view('dynamicoptions::admin.dynamic_options.show', compact('dynamic_option', 'invoice_priorities', 'invoice_tasks','quote_priorities','quote_tasks'));
    }


    /**
     * Remove DynamicOption from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('dynamic_option_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $dynamic_option = DynamicOption::findOrFail($id);
        $dynamic_option->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.dynamic_options.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected DynamicOption at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('dynamic_option_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        flashMessage( 'success', 'deletes' );

        if ($request->input('ids')) {
            $entries = DynamicOption::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore DynamicOption from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('dynamic_option_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $dynamic_option = DynamicOption::onlyTrashed()->findOrFail($id);
        $dynamic_option->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete DynamicOption from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('dynamic_option_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $dynamic_option = DynamicOption::onlyTrashed()->findOrFail($id);
        $dynamic_option->forceDelete();

        return back();
    }
}
