<?php

namespace App\Http\Controllers\Admin;

use App\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectStatusesRequest;
use App\Http\Requests\Admin\UpdateProjectStatusesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ProjectStatusesController extends Controller
{
    /**
     * Display a listing of ProjectStatus.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('project_status_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = ProjectStatus::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('project_status_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'project_statuses.id',
                'project_statuses.name',
                'project_statuses.description',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'project_status_';
                $routeKey = 'admin.project_statuses';

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

        return view('admin.project_statuses.index');
    }

    /**
     * Show the form for creating new ProjectStatus.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('project_status_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.project_statuses.create');
    }

    /**
     * Store a newly created ProjectStatus in storage.
     *
     * @param  \App\Http\Requests\StoreProjectStatusesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectStatusesRequest $request)
    {
        if (! Gate::allows('project_status_create')) {
            return prepareBlockUserMessage();
        }

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $project_status = ProjectStatus::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.project_statuses.index');
    }


    /**
     * Show the form for editing ProjectStatus.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('project_status_edit')) {
            return prepareBlockUserMessage();
        }
        $project_status = ProjectStatus::findOrFail($id);

        return view('admin.project_statuses.edit', compact('project_status'));
    }

    /**
     * Update ProjectStatus in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectStatusesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectStatusesRequest $request, $id)
    {
        if (! Gate::allows('project_status_edit')) {
            return prepareBlockUserMessage();
        }
        $project_status = ProjectStatus::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_status->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.project_statuses.index');
    }


    /**
     * Display ProjectStatus.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('project_status_view')) {
            return prepareBlockUserMessage();
        }
        

        $project_status = ProjectStatus::findOrFail($id);

        return view('admin.project_statuses.show', compact('project_status', 'list'));
    }


    /**
     * Remove ProjectStatus from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('project_status_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_status = ProjectStatus::findOrFail($id);
        $project_status->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.project_statuses.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected ProjectStatus at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('project_status_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ProjectStatus::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore ProjectStatus from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('project_status_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_status = ProjectStatus::onlyTrashed()->findOrFail($id);
        $project_status->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete ProjectStatus from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('project_status_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $project_status = ProjectStatus::onlyTrashed()->findOrFail($id);
        $project_status->forceDelete();

        return back();
    }
}
