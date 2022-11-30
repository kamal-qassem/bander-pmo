<?php

namespace App\Http\Controllers\Admin;

use App\ContactType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContactTypesRequest;
use App\Http\Requests\Admin\UpdateContactTypesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ContactTypesController extends Controller
{
    /**
     * Display a listing of ContactType.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('contact_type_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = ContactType::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('contact_type_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'roles.id',
                'roles.title',
                'roles.description',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'contact_type_';
                $routeKey = 'admin.contact_types';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.contact_types.index');
    }

    /**
     * Show the form for creating new ContactType.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('contact_type_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.contact_types.create');
    }

    /**
     * Store a newly created ContactType in storage.
     *
     * @param  \App\Http\Requests\StoreContactTypesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContactTypesRequest $request)
    {
        if (! Gate::allows('contact_type_create')) {
            return prepareBlockUserMessage();
        }

        $addtional = array(
            'name' => $request->title,        
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_type = ContactType::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.contact_types.index');
    }


    /**
     * Show the form for editing ContactType.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('contact_type_edit')) {
            return prepareBlockUserMessage();
        }
        $contact_type = ContactType::findOrFail($id);

        return view('admin.contact_types.edit', compact('contact_type'));
    }

    /**
     * Update ContactType in storage.
     *
     * @param  \App\Http\Requests\UpdateContactTypesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContactTypesRequest $request, $id)
    {
        if (! Gate::allows('contact_type_edit')) {
            return prepareBlockUserMessage();
        }
        $contact_type = ContactType::findOrFail($id);

        $addtional = array(
            'name' => $request->title,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_type->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.contact_types.index');
    }


    /**
     * Display ContactType.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$list='')
    {
        if (! Gate::allows('contact_type_view')) {
            return prepareBlockUserMessage();
        }
        

        $contact_type = ContactType::findOrFail($id);

        return view('admin.contact_types.show', compact('contact_type', 'list'));
    }


    /**
     * Remove ContactType from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('contact_type_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_type = ContactType::findOrFail($id);
        $contact_type->delete();

        flashMessage( 'success', 'delete' );
        return redirect()->route('admin.contact_types.index');
    }

    /**
     * Delete all selected ContactType at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('contact_type_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ContactType::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore ContactType from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('contact_type_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_type = ContactType::onlyTrashed()->findOrFail($id);
        $contact_type->restore();

        flashMessage( 'success', 'restore' );
        return redirect()->route('admin.contact_types.index');
    }

    /**
     * Permanently delete ContactType from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('contact_type_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_type = ContactType::onlyTrashed()->findOrFail($id);
        $contact_type->forceDelete();

        return redirect()->route('admin.contact_types.index', ['show_deleted' => 1]);
    }
}
