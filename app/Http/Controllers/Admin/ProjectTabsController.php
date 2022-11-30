<?php

namespace App\Http\Controllers\Admin;

use App\ProjectTab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectTabsRequest;
use App\Http\Requests\Admin\UpdateProjectTabsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ProjectTabsController extends Controller
{
    /**
     * Display a listing of ProjectTab.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('project_tab_access')) {
            return abort(401);
        }


        
        if (request()->ajax()) {
            $query = ProjectTab::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('project_tab_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'project_tabs.id',
                'project_tabs.title',
                'project_tabs.description',
            ]);
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'project_tab_';
                $routeKey = 'admin.project_tabs';

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

        return view('admin.project_tabs.index');
    }

    /**
     * Show the form for creating new ProjectTab.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('project_tab_create')) {
            return abort(401);
        }
        return view('admin.project_tabs.create');
    }

    /**
     * Store a newly created ProjectTab in storage.
     *
     * @param  \App\Http\Requests\StoreProjectTabsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectTabsRequest $request)
    {
        if (! Gate::allows('project_tab_create')) {
            return abort(401);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_tab = ProjectTab::create($request->all());

        return redirect()->route('admin.project_tabs.index');
    }


    /**
     * Show the form for editing ProjectTab.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('project_tab_edit')) {
            return abort(401);
        }
        $project_tab = ProjectTab::findOrFail($id);

        return view('admin.project_tabs.edit', compact('project_tab'));
    }

    /**
     * Update ProjectTab in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectTabsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectTabsRequest $request, $id)
    {
        if (! Gate::allows('project_tab_edit')) {
            return abort(401);
        }
        $project_tab = ProjectTab::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_tab->update($request->all());



        return redirect()->route('admin.project_tabs.index');
    }


    /**
     * Display ProjectTab.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('project_tab_view')) {
            return abort(401);
        }
        $project_tab = ProjectTab::findOrFail($id);

        return view('admin.project_tabs.show', compact('project_tab'));
    }


    /**
     * Remove ProjectTab from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('project_tab_delete')) {
            return abort(401);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_tab = ProjectTab::findOrFail($id);
        $project_tab->delete();
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.project_tabs.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected ProjectTab at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('project_tab_delete')) {
            return abort(401);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ProjectTab::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore ProjectTab from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('project_tab_delete')) {
            return abort(401);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_tab = ProjectTab::onlyTrashed()->findOrFail($id);
        $project_tab->restore();

        return back();
    }

    /**
     * Permanently delete ProjectTab from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('project_tab_delete')) {
            return abort(401);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_tab = ProjectTab::onlyTrashed()->findOrFail($id);
        $project_tab->forceDelete();

        return back();
    }
}
