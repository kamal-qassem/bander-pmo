<?php

namespace App\Http\Controllers\Admin;

use App\ProjectBillingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectBillingTypesRequest;
use App\Http\Requests\Admin\UpdateProjectBillingTypesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ProjectBillingTypesController extends Controller
{
    /**
     * Display a listing of ProjectBillingType.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('project_billing_type_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = ProjectBillingType::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('project_billing_type_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'project_billing_types.id',
                'project_billing_types.title',
                'project_billing_types.description',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'project_billing_type_';
                $routeKey = 'admin.project_billing_types';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.project_billing_types.index');
    }

    /**
     * Show the form for creating new ProjectBillingType.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('project_billing_type_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.project_billing_types.create');
    }

    /**
     * Store a newly created ProjectBillingType in storage.
     *
     * @param  \App\Http\Requests\StoreProjectBillingTypesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectBillingTypesRequest $request)
    {
        if (! Gate::allows('project_billing_type_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_billing_type = ProjectBillingType::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.project_billing_types.index');
    }


    /**
     * Show the form for editing ProjectBillingType.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('project_billing_type_edit')) {
            return prepareBlockUserMessage();
        }
        $project_billing_type = ProjectBillingType::findOrFail($id);

        return view('admin.project_billing_types.edit', compact('project_billing_type'));
    }

    /**
     * Update ProjectBillingType in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectBillingTypesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectBillingTypesRequest $request, $id)
    {
        if (! Gate::allows('project_billing_type_edit')) {
            return prepareBlockUserMessage();
        }

        $project_billing_type = ProjectBillingType::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_billing_type->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.project_billing_types.index');
    }


    /**
     * Display ProjectBillingType.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('project_billing_type_view')) {
            return prepareBlockUserMessage();
        }
        $client_projects = \App\ClientProject::where('billing_type_id', $id)->get();

        $project_billing_type = ProjectBillingType::findOrFail($id);

        return view('admin.project_billing_types.show', compact('project_billing_type', 'client_projects', 'list'));
    }


    /**
     * Remove ProjectBillingType from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('project_billing_type_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_billing_type = ProjectBillingType::findOrFail($id);
        $project_billing_type->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.project_billing_types.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected ProjectBillingType at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('project_billing_type_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ProjectBillingType::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'delete' );
        }
    }


    /**
     * Restore ProjectBillingType from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('project_billing_type_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_billing_type = ProjectBillingType::onlyTrashed()->findOrFail($id);
        $project_billing_type->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete ProjectBillingType from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('project_billing_type_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_billing_type = ProjectBillingType::onlyTrashed()->findOrFail($id);
        $project_billing_type->forceDelete();

        return back();
    }
}
