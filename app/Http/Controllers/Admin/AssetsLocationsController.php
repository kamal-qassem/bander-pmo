<?php

namespace App\Http\Controllers\Admin;

use App\AssetsLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssetsLocationsRequest;
use App\Http\Requests\Admin\UpdateAssetsLocationsRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class AssetsLocationsController extends Controller
{
    /**
     * Display a listing of AssetsLocation.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('assets_location_access')) {
            return prepareBlockUserMessage();
        }


                $assets_locations = AssetsLocation::all()->sortByDesc('id');

        return view('admin.assets_locations.index', compact('assets_locations'));
    }

    /**
     * Show the form for creating new AssetsLocation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('assets_location_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.assets_locations.create');
    }

    /**
     * Store a newly created AssetsLocation in storage.
     *
     * @param  \App\Http\Requests\StoreAssetsLocationsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetsLocationsRequest $request)
    {
        if (! Gate::allows('assets_location_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $assets_location = AssetsLocation::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.assets_locations.index');
    }


    /**
     * Show the form for editing AssetsLocation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('assets_location_edit')) {
            return prepareBlockUserMessage();
        }
        $assets_location = AssetsLocation::findOrFail($id);

        return view('admin.assets_locations.edit', compact('assets_location'));
    }

    /**
     * Update AssetsLocation in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetsLocationsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetsLocationsRequest $request, $id)
    {
        if (! Gate::allows('assets_location_edit')) {
            return prepareBlockUserMessage();
        }
        $assets_location = AssetsLocation::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $assets_location->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.assets_locations.index');
    }


    /**
     * Display AssetsLocation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list='')
    {
        if (! Gate::allows('assets_location_view')) {
            return prepareBlockUserMessage();
        }
        $assets_histories = \App\AssetsHistory::where('location_id', $id)->get();
        $assets = \App\Asset::where('location_id', $id)->get();

        $assets_location = AssetsLocation::findOrFail($id);

        return view('admin.assets_locations.show', compact('assets_location', 'assets_histories', 'assets','list'));
    }


    /**
     * Remove AssetsLocation from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('assets_location_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $assets_location = AssetsLocation::findOrFail($id);
        $assets_location->delete();

        flashMessage( 'success', 'delete' );
         if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.assets_locations.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
      }
    }

    /**
     * Delete all selected AssetsLocation at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('assets_location_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = AssetsLocation::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
