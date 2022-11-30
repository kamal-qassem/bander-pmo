<?php

namespace App\Http\Controllers\Admin;

use App\ContactGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContactGroupsRequest;
use App\Http\Requests\Admin\UpdateContactGroupsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
class ContactGroupsController extends Controller
{
    /**
     * Display a listing of ContactGroup.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('contact_group_access')) {
            return prepareBlockUserMessage();
        }

        if (request()->ajax()) {
            $query = ContactGroup::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
            if (! Gate::allows('contact_group_delete')) {
                return prepareBlockUserMessage();
            }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'contact_groups.id',
                'contact_groups.name',
                'contact_groups.description',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'contact_group_';
                $routeKey = 'admin.contact_groups';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.contact_groups.index');
    }

    /**
     * Show the form for creating new ContactGroup.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('contact_group_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.contact_groups.create');
    }
    
    /**
     * Store a newly created ContactGroup in storage.
     *
     * @param  \App\Http\Requests\StoreContactGroupsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('contact_group_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'name' => 'required|unique:contact_groups,name',
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

        $contact_group = ContactGroup::create($request->all());

        if ( $request->ajax() ) {
            $contact_group->selectedid = $request->selectedid;
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $contact_group]);
        } else {
            flashMessage( 'success', 'create' );
            return redirect()->route('admin.contact_groups.index');
        }
    }


    /**
     * Show the form for editing ContactGroup.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('contact_group_edit')) {
            return prepareBlockUserMessage();
        }
        $contact_group = ContactGroup::findOrFail($id);

        return view('admin.contact_groups.edit', compact('contact_group'));
    }

    /**
     * Update ContactGroup in storage.
     *
     * @param  \App\Http\Requests\UpdateContactGroupsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContactGroupsRequest $request, $id)
    {
        if (! Gate::allows('contact_group_edit')) {
            return prepareBlockUserMessage();
        }
        $contact_group = ContactGroup::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_group->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.contact_groups.index');
    }


    /**
     * Display ContactGroup.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('contact_group_view')) {
            return prepareBlockUserMessage();
        }
        $contacts = \App\Contact::where('group_id', $id)->get();

        $contact_group = ContactGroup::findOrFail($id);

        return view('admin.contact_groups.show', compact('contact_group', 'contacts', 'list'));
    }


    /**
     * Remove ContactGroup from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('contact_group_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_group = ContactGroup::findOrFail($id);
        $contact_group->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.contact_groups.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected ContactGroup at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('contact_group_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ContactGroup::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore ContactGroup from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('contact_group_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_group = ContactGroup::onlyTrashed()->findOrFail($id);
        $contact_group->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete ContactGroup from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('contact_group_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contact_group = ContactGroup::onlyTrashed()->findOrFail($id);
        $contact_group->forceDelete();

        return back();
    }
}
