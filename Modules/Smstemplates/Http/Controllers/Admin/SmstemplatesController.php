<?php

namespace Modules\Smstemplates\Http\Controllers\Admin;

use Modules\Smstemplates\Entities\Smstemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Smstemplates\Http\Requests\Admin\StoreSmstemplatesRequest;
use Modules\Smstemplates\Http\Requests\Admin\UpdateSmstemplatesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class SmstemplatesController extends Controller
{
    /**
     * Display a listing of Smstemplate.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('smstemplate_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = Smstemplate::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('smstemplate_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'smstemplates.id',
                'smstemplates.title',
                'smstemplates.key',
                'smstemplates.content',
            ]);
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'smstemplate_';
                $routeKey = 'admin.smstemplates';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('key', function ($row) {
                return $row->key ? $row->key : '';
            });
            $table->editColumn('content', function ($row) {
                return $row->content ? $row->content : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('smstemplates::admin.smstemplates.index');
    }

    /**
     * Show the form for creating new Smstemplate.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('smstemplate_create')) {
            return prepareBlockUserMessage();
        }

        return view('smstemplates::admin.smstemplates.create');
    }

    /**
     * Store a newly created Smstemplate in storage.
     *
     * @param  \App\Http\Requests\StoreSmstemplatesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSmstemplatesRequest $request)
    {
        if (! Gate::allows('smstemplate_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $smstemplate = Smstemplate::create($request->all());



        flashMessage( 'success', 'create' );
        return redirect()->route('admin.smstemplates.index');
    }


    /**
     * Show the form for editing Smstemplate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('smstemplate_edit')) {
            return prepareBlockUserMessage();
        }
        $smstemplate = Smstemplate::findOrFail($id);

        return view('smstemplates::admin.smstemplates.edit', compact('smstemplate'));
    }

    /**
     * Update Smstemplate in storage.
     *
     * @param  \App\Http\Requests\UpdateSmstemplatesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSmstemplatesRequest $request, $id)
    {
        if (! Gate::allows('smstemplate_edit')) {
            return prepareBlockUserMessage();
        }
        $smstemplate = Smstemplate::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $smstemplate->update($request->all());


        flashMessage( 'success', 'update' );    
        return redirect()->route('admin.smstemplates.index');
    }


    /**
     * Display Smstemplate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('smstemplate_view')) {
            return prepareBlockUserMessage();
        }
        $smstemplate = Smstemplate::findOrFail($id);

        return view('smstemplates::admin.smstemplates.show', compact('smstemplate'));
    }


    /**
     * Remove Smstemplate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('smstemplate_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $smstemplate = Smstemplate::findOrFail($id);
        $smstemplate->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.smstemplates.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Smstemplate at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('smstemplate_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        if ($request->input('ids')) {
            $entries = Smstemplate::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Smstemplate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('smstemplate_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $smstemplate = Smstemplate::onlyTrashed()->findOrFail($id);
        $smstemplate->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete Smstemplate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('smstemplate_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $smstemplate = Smstemplate::onlyTrashed()->findOrFail($id);
        $smstemplate->forceDelete();

        return back();
    }
}
