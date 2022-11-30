<?php

namespace App\Http\Controllers\Admin;

use App\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssetsRequest;
use App\Http\Requests\Admin\UpdateAssetsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class AssetsController extends Controller
{
    use FileUploadTrait;
    
    public function __construct() {
       $this->middleware('plugin:asset');
    }
    /**
     * Display a listing of Asset.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('asset_access')) {
            return prepareBlockUserMessage();
        }


         $assets = Asset::all()->sortByDesc('id');

        return view('admin.assets.index', compact('assets'));
    }

    /**
     * Show the form for creating new Asset.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('asset_create')) {
            return prepareBlockUserMessage();
        }
        
        $categories = \App\AssetsCategory::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $statuses = \App\AssetsStatus::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $locations = \App\AssetsLocation::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $assigned_users = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('admin.assets.create', compact('categories', 'statuses', 'locations', 'assigned_users'));
    }

    /**
     * Store a newly created Asset in storage.
     *
     * @param  \App\Http\Requests\StoreAssetsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetsRequest $request)
    {
        if (! Gate::allows('asset_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $asset = Asset::create($request->all());


        foreach ($request->input('photo2_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $asset->id;
            $file->save();
        }

        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $asset->id;
            $file->save();
        }

        flashMessage( 'success', 'create' );
        return redirect()->route('admin.assets.index');
    }


    /**
     * Show the form for editing Asset.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('asset_edit')) {
            return prepareBlockUserMessage();
        }
        
        $categories = \App\AssetsCategory::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $statuses = \App\AssetsStatus::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $locations = \App\AssetsLocation::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $assigned_users = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $asset = Asset::findOrFail($id);

        return view('admin.assets.edit', compact('asset', 'categories', 'statuses', 'locations', 'assigned_users'));
    }

    /**
     * Update Asset in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetsRequest $request, $id)
    {
        if (! Gate::allows('asset_edit')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $asset = Asset::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $asset->update($request->all());


        $media = [];
        foreach ($request->input('photo2_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $asset->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $asset->updateMedia($media, 'photo2');

        $media = [];
        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $asset->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $asset->updateMedia($media, 'attachments');

        flashMessage( 'success', 'update' );
        return redirect()->route('admin.assets.index');
    }


    /**
     * Display Asset.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('asset_view')) {
            return prepareBlockUserMessage();
        }
        
        $categories = \App\AssetsCategory::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $statuses = \App\AssetsStatus::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $locations = \App\AssetsLocation::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $assigned_users = \App\User::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $assets_histories = \App\AssetsHistory::where('asset_id', $id)->get();

        $asset = Asset::findOrFail($id);

        return view('admin.assets.show', compact('asset', 'list','assets_histories'));
    }


    /**
     * Remove Asset from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('asset_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $asset = Asset::findOrFail($id);
        $asset->deletePreservingMedia();

        flashMessage( 'success', 'delete' );
         if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.assets.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
      }
    }

    /**
     * Delete all selected Asset at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('asset_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Asset::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
