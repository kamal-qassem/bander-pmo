<?php

namespace App\Http\Controllers\Admin;

use App\AssetsStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssetsStatusesRequest;
use App\Http\Requests\Admin\UpdateAssetsStatusesRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class AssetsStatusesController extends Controller
{
    /**
     * Display a listing of AssetsStatus.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('assets_status_access')) {
            return prepareBlockUserMessage();
        }


                $assets_statuses = AssetsStatus::all()->sortByDesc('id');

        return view('admin.assets_statuses.index', compact('assets_statuses'));
    }

    /**
     * Show the form for creating new AssetsStatus.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('assets_status_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.assets_statuses.create');
    }

    /**
     * Store a newly created AssetsStatus in storage.
     *
     * @param  \App\Http\Requests\StoreAssetsStatusesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetsStatusesRequest $request)
    {
        if (! Gate::allows('assets_status_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $assets_status = AssetsStatus::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.assets_statuses.index');
    }


    /**
     * Show the form for editing AssetsStatus.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('assets_status_edit')) {
            return prepareBlockUserMessage();
        }
        $assets_status = AssetsStatus::findOrFail($id);

        return view('admin.assets_statuses.edit', compact('assets_status'));
    }

    /**
     * Update AssetsStatus in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetsStatusesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetsStatusesRequest $request, $id)
    {
        if (! Gate::allows('assets_status_edit')) {
            return prepareBlockUserMessage();
        }
        $assets_status = AssetsStatus::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $assets_status->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.assets_statuses.index');
    }


    /**
     * Display AssetsStatus.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$list ='')
    {
        if (! Gate::allows('assets_status_view')) {
            return prepareBlockUserMessage();
        }
        $assets_histories = \App\AssetsHistory::where('status_id', $id)->get();$assets = \App\Asset::where('status_id', $id)->get();

        $assets_status = AssetsStatus::findOrFail($id);

        return view('admin.assets_statuses.show', compact('assets_status', 'assets_histories', 'assets','list'));
    }


    /**
     * Remove AssetsStatus from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('assets_status_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $assets_status = AssetsStatus::findOrFail($id);
        $assets_status->delete();

        flashMessage( 'success', 'delete' );
         if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.assets_statuses.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected AssetsStatus at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('assets_status_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = AssetsStatus::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
