<?php

namespace App\Http\Controllers\Admin;

use App\AssetsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssetsCategoriesRequest;
use App\Http\Requests\Admin\UpdateAssetsCategoriesRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class AssetsCategoriesController extends Controller
{
    /**
     * Display a listing of AssetsCategory.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('assets_category_access')) {
            return prepareBlockUserMessage();
        }


                $assets_categories = AssetsCategory::all()->sortByDesc('id');

        return view('admin.assets_categories.index', compact('assets_categories'));
    }

    /**
     * Show the form for creating new AssetsCategory.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('assets_category_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.assets_categories.create');
    }

    /**
     * Store a newly created AssetsCategory in storage.
     *
     * @param  \App\Http\Requests\StoreAssetsCategoriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetsCategoriesRequest $request)
    {
        if (! Gate::allows('assets_category_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $assets_category = AssetsCategory::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.assets_categories.index');
    }


    /**
     * Show the form for editing AssetsCategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('assets_category_edit')) {
            return prepareBlockUserMessage();
        }
        $assets_category = AssetsCategory::findOrFail($id);

        return view('admin.assets_categories.edit', compact('assets_category'));
    }

    /**
     * Update AssetsCategory in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetsCategoriesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetsCategoriesRequest $request, $id)
    {
        if (! Gate::allows('assets_category_edit')) {
            return prepareBlockUserMessage();
        }
        $assets_category = AssetsCategory::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $assets_category->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.assets_categories.index');
    }


    /**
     * Display AssetsCategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list ='')
    {
        if (! Gate::allows('assets_category_view')) {
            return prepareBlockUserMessage();
        }

        $assets = \App\Asset::where('category_id', $id)->get();

        $assets_category = AssetsCategory::findOrFail($id);

        return view('admin.assets_categories.show', compact('assets_category', 'list','assets'));
    }


    /**
     * Remove AssetsCategory from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('assets_category_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $assets_category = AssetsCategory::findOrFail($id);
        $assets_category->delete();

        flashMessage( 'success', 'delete' );
         if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.assets_categories.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
      }
    }

    /**
     * Delete all selected AssetsCategory at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('assets_category_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = AssetsCategory::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

}
