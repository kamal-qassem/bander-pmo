<?php

namespace App\Http\Controllers\Admin;

use App\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductCategoriesRequest;
use App\Http\Requests\Admin\UpdateProductCategoriesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ProductCategoriesController extends Controller
{
    use FileUploadTrait;
    
    public function __construct() {
       $this->middleware('plugin:productcategory');
    }
    /**
     * Display a listing of ProductCategory.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('product_category_access')) {
            return prepareBlockUserMessage();
        }
        $product_categories = ProductCategory::all()->sortByDesc('id');

        return view('admin.product_categories.index', compact('product_categories'));
    }

    /**
     * Show the form for creating new ProductCategory.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('product_category_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.product_categories.create');
    }

    /**
     * Store a newly created ProductCategory in storage.
     *
     * @param  \App\Http\Requests\StoreProductCategoriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductCategoriesRequest $request)
    {
        if (! Gate::allows('product_category_create')) {
            return prepareBlockUserMessage();
        }
         if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
         }

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $product_category = ProductCategory::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.product_categories.index')->with(['message' => trans( 'custom.messages.record_saved'), 'status' => 'success']);
    }


    /**
     * Show the form for editing ProductCategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('product_category_edit')) {
            return prepareBlockUserMessage();
        }
        $product_category = ProductCategory::findOrFail($id);

        return view('admin.product_categories.edit', compact('product_category'));
    }

    /**
     * Update ProductCategory in storage.
     *
     * @param  \App\Http\Requests\UpdateProductCategoriesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductCategoriesRequest $request, $id)
    {
        if (! Gate::allows('product_category_edit')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
         }
        $product_category = ProductCategory::findOrFail($id);

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $product_category->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.product_categories.index')->with(['message' => trans( 'custom.messages.record_updated'), 'status' => 'success']);
    }


    /**
     * Display ProductCategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('product_category_view')) {
            return prepareBlockUserMessage();
        }
       

        $product_category = ProductCategory::findOrFail($id);

        return view('admin.product_categories.show', compact('product_category', 'list'));
    }


    /**
     * Remove ProductCategory from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('product_category_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $product_category = ProductCategory::findOrFail($id);
        $product_category->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.product_categories.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected ProductCategory at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('product_category_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ProductCategory::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
            session()->flash('status', 'success' );
            session()->flash('message', trans( 'custom.messages.records_deleted' ) );
        }
    }

}
