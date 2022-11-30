<?php

namespace App\Http\Controllers\Admin;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductsRequest;
use App\Http\Requests\Admin\UpdateProductsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Validator;
class ProductsController extends Controller
{
    use FileUploadTrait;
    
    public function __construct() {
        $this->middleware('plugin:product');
    }   
    /**
     * Display a listing of Product.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type = '', $type_id = '')
    {
        if (! Gate::allows('product_access')) {
            return prepareBlockUserMessage();
        }

        if (request()->ajax()) {
            $query = Product::query();
            $query->with("category");
            $query->with("ware_house");
            $query->with("brand");
            $template = 'actionsTemplate';

              if(request('show_deleted') == 1) {                
                if (! Gate::allows('invoice_delete')) {
                    return prepareBlockUserMessage();
                }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            
            $query->select([
                'products.id',
                'products.name',
                'products.product_code',
                'products.actual_price',
                'products.sale_price',
                'products.ware_house_id',
                'products.description',
                'products.stock_quantity',
                'products.alert_quantity',
                'products.thumbnail',
                'products.hsn_sac_code',
                'products.product_size',
                'products.product_weight',
                'products.brand_id',
            ]);

            /**
             * when we call purchase orders display from other pages!
            */
            if ( ! empty( $type ) && 'ware_house' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('products.ware_house_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'brand' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('products.brand_id', $type_id);
                });
            }

            if ( ! empty( $type ) && 'product_category' === $type ) {
                $query->whereHas("category",
                function ($query) use( $type_id ) {
                    $query->where('id', $type_id);
                });
            }

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'product_';
                $routeKey = 'admin.products';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('products.name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('product_code', function ($row) {
                return $row->product_code ? $row->product_code : '';
            });
            $table->editColumn('actual_price', function ($row) {
                return $row->actual_price ? digiCurrency($row->actual_price) : '';
            });
            $table->editColumn('sale_price', function ($row) {
                return $row->sale_price ? digiCurrency($row->sale_price): '';
            });
            $table->editColumn('category.name', function ($row) {
                if(count($row->category) == 0) {
                    return '';
                }
                return '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        $row->category->pluck('name')->toArray()) . '</span>';
            });
            
            $table->editColumn('ware_house.name', function ($row) {
                return $row->ware_house ? $row->ware_house->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('stock_quantity', function ($row) {
                if ( ! empty( $row->stock_quantity ) && ! empty( $row->alert_quantity ) && $row->stock_quantity < $row->alert_quantity ) {
                    $help_text = trans('global.products.low_quantity');
                    return '<b style="color:red;font-size: x-large;">' . $row->stock_quantity . '</b>' . digi_get_help( $help_text, 'fa fa-spinner fa-lg');
                } else {
                    return $row->stock_quantity ? $row->stock_quantity : '';
                }
            });
            $table->editColumn('alert_quantity', function ($row) {
                return $row->alert_quantity ? $row->alert_quantity : '';
            });
            $table->editColumn('image_gallery', function ($row) {
                $build  = '';
                foreach ($row->getMedia('image_gallery') as $media) {
                    $build .= '<p class="form-group"><a href="' . route('admin.home.media-download', $media->id) . '">' . $media->name . '</a></p>';
                }
                
                return $build;
            });
            $table->editColumn('thumbnail', function ($row) {
                if($row->thumbnail && file_exists(public_path() . '/thumb/' . $row->thumbnail)) { 
                    return '<a href="'. route('admin.home.media-file-download', [ 'model' => 'Product', 'field' => 'thumbnail', 'record_id' => $row->id ]) .'"><img src="'. asset(env('UPLOAD_PATH').'/thumb/' . $row->thumbnail) .'" title="'.$row->name.'"/></a>';
                } else {
                    return '<img src="'. asset('images/product-50x50.jpg') .'" title="'.$row->name.'" width="50" height="50"/>';
                }
            });
            $table->editColumn('other_files', function ($row) {
                $build  = '';
                foreach ($row->getMedia('other_files') as $media) {
                    $build .= '<p class="form-group"><a href="' . route('admin.home.media-download', $media->id) . '">' . $media->name . '</a></p>';
                }
                
                return $build;
            });
            $table->editColumn('hsn_sac_code', function ($row) {
                return $row->hsn_sac_code ? $row->hsn_sac_code : '';
            });
            $table->editColumn('product_size', function ($row) {
                return $row->product_size ? $row->product_size : '';
            });
            $table->editColumn('product_weight', function ($row) {
                return $row->product_weight ? $row->product_weight : '';
            });
            $table->editColumn('brand.title', function ($row) {
                return $row->brand ? $row->brand->title : '';
            });

            $table->rawColumns(['actions','massDelete','category.name','image_gallery','thumbnail','other_files', 'stock_quantity']);

            return $table->make(true);
        }

        $csvtemplatepath = asset( 'csvtemplates/products.csv');
        return view('admin.products.index', compact('csvtemplatepath','type','type_id'));
    }

    /**
     * Show the form for creating new Product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('product_create')) {
            return prepareBlockUserMessage();
        }
        
        $categories = \App\ProductCategory::get()->pluck('name', 'id');

        
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $ware_houses = \App\Warehouse::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $brands = \App\Brand::where('status', '=', 'Active')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_product_status = Product::$enum_product_status;

        return view('admin.products.create', compact('categories', 'ware_houses', 'brands', 'taxes', 'discounts', 'enum_product_status'));
    }

    /**
     * Store a newly created Product in storage.
     *
     * @param  \App\Http\Requests\StoreProductsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('product_create')) {
            return prepareBlockUserMessage();
        }

        config(['app.date_format' => env('DATE_FORMAT')]);



        $rules = [
            'name'  => 'required|unique:products,name',
            
            'actual_price' => 'required|regex:/^\d+(\.\d{1,4})?$/',
            'sale_price' => 'nullable|regex:/^\d+(\.\d{1,4})?$/',
            'category.*' => 'exists:product_categories,id',            
            
            'stock_quantity' => 'max:2147483647|nullable|numeric',
            'alert_quantity' => 'max:2147483647|nullable|numeric',
            'thumbnail' => 'nullable|mimes:png,jpg,jpeg,gif',
            
        ];

        $messages = [
           'sale_price.lte' => trans( 'global.products.sale-price-lessthan-actual-price' ),
           
        ];

        /**
         * If the price either price is null then "lte" validation will give error "comparision must be same type", so to avoid that error we need to validate price if they are same type!
         */
        $prices = $request->prices;
        if ( ! empty( $prices ) ) {
            foreach ($prices as $key => $value) {
                if ( is_array( $value ) && ! empty( $value ) ) {
                    foreach ($value as $cur => $price) {
                        if ( ! empty( $prices['sale'][ $cur ] ) && ! empty( $prices['actual'][ $cur ] ) ) {
                            $rules['prices.sale.' . $cur] = 'nullable|regex:/^\d+(\.\d{1,4})?$/|lte:prices.actual.' . $cur;
                            $messages['prices.sale.'.$cur.'.lte'] = trans( 'global.products.sale-price-variation-lessthan-actual-price-variation', [ 'cur' => $cur, 'cur2' => $cur ] );
                        }
                    }
                }
            }
        }

        if ( ! empty( $request->actual_price ) ) {
            $rules['sale_price'] = 'nullable|lte:actual_price|regex:/^\d+(\.\d{1,4})?$/';
        }
        
        $validator = Validator::make($request->all(), $rules, $messages);
        if ( ! $validator->passes() ) {
            if ( $request->ajax() ) {
                return response()->json(['error'=>$validator->errors()->all()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $prices = $request->prices;
        $prices['actual'][getDefaultCurrency('code')] = $request->actual_price;
        $prices['sale'][getDefaultCurrency('code')] = $request->sale_price;     

        

        $additional = array(
            'prices' => json_encode( $prices ),
            
        );
        $request->merge( $additional ); //Replacing the old input string with new string.
        
        $product = Product::create($request->all());
        $product->category()->sync(array_filter((array)$request->input('category')));
        
        $prices_attach = [];
        $prices_available = '';
        if ( ! empty( $prices['actual'] ) ) {
            foreach ($prices['actual'] as $key => $value) {
                $currency_sale_price = ! empty( $prices['sale'][ $key ] ) ? $prices['sale'][ $key ] : 0;
                $currency_id = null;
                $details = \App\Currency::where('code', $key)->first();
                if ( $details ) {
                    $currency_id = $details->id;
                }
                $prices_attach[] = [
                    'product_id' => $product->id,
                    'currency_id' => $currency_id,
                    'currency_code' => $key,
                    'currency_actual_price' => $value,
                    'currency_sale_price' => $currency_sale_price,
                ];
                if ( ! empty( $currency_sale_price ) ) { // If the sale price is available then considered it is available currency price.
                    $prices_available .= $key . ','; 
                }
            }
        }

        $product->prices_available = $prices_available;

         if ( isDemo() ) {
            return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $product->save();

        DB::select("DELETE FROM " . env('DB_PREFIX') . 'product_currency WHERE product_id = ' . $product->id);
        if ( count( $prices_attach ) > 0 ) {
            $product->product_currency()->sync( $prices_attach );
        }

        foreach ($request->input('image_gallery_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $product->id;
            $file->save();
        }

        foreach ($request->input('other_files_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $product->id;
            $file->save();
        }
        if ( $request->ajax() ) {
            $product->row_id = $request->row_id;
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $product]);
        } else {
            flashMessage();
            return redirect()->route('admin.products.index');
        }
    }


    /**
     * Show the form for editing Product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('product_edit')) {
            return prepareBlockUserMessage();
        }
        
        $categories = \App\ProductCategory::get()->pluck('name', 'id');

        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $ware_houses = \App\Warehouse::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $brands = \App\Brand::where('status', '=', 'Active')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_product_status = Product::$enum_product_status;

        $product = Product::findOrFail($id);

        return view('admin.products.edit', compact('product', 'categories', 'ware_houses', 'brands', 'taxes', 'discounts', 'enum_product_status'));
    }

    /**
     * Update Product in storage.
     *
     * @param  \App\Http\Requests\UpdateProductsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! Gate::allows('product_edit')) {
            return prepareBlockUserMessage();
        }

        config(['app.date_format' => env('DATE_FORMAT')]);

        $rules = [
            'name'  => 'bail|required|unique:products,id,' . $id,
            
            'actual_price' => 'required|regex:/^\d+(\.\d{1,4})?$/',
            'sale_price' => 'nullable|regex:/^\d+(\.\d{1,4})?$/',
            'category.*' => 'exists:product_categories,id',
            
            'stock_quantity' => 'max:2147483647|nullable|numeric',
            'alert_quantity' => 'max:2147483647|nullable|numeric',
            'thumbnail' => 'nullable|mimes:png,jpg,jpeg,gif',
            
        ];
        if ( ! empty( $request->actual_price ) ) {
            $rules['sale_price'] = 'nullable|lte:actual_price|regex:/^\d+(\.\d{1,4})?$/';
        }

        $messages = [
           'sale_price.lte' => trans( 'global.products.sale-price-lessthan-actual-price' ),
           
        ];

        /**
         * If the price either price is null then "lte" validation will give error "comparision must be same type", so to avoid that error we need to validate price if they are same type!
         */
        $prices = $request->prices;
        if ( ! empty( $prices ) ) {
            foreach ($prices as $key => $value) {
                if ( is_array( $value ) && ! empty( $value ) ) {
                    foreach ($value as $cur => $price) {
                        if ( ! empty( $prices['sale'][ $cur ] ) && ! empty( $prices['actual'][ $cur ] ) ) {
                            $rules['prices.sale.' . $cur] = 'nullable|regex:/^\d+(\.\d{1,4})?$/|lte:prices.actual.' . $cur;
                            $messages['prices.sale.'.$cur.'.lte'] = trans( 'global.products.sale-price-variation-lessthan-actual-price-variation', [ 'cur' => $cur, 'cur2' => $cur ] );
                        }
                    }
                }
            }
        }

                
        $validator = Validator::make($request->all(), $rules, $messages);
        if ( ! $validator->passes() ) {
            if ( $request->ajax() ) {
                return response()->json(['error'=>$validator->errors()->all()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        if ( ! isDemo() ) {
            $request = $this->saveFiles($request);
        }

        $product = Product::findOrFail($id);
        

        $prices = $request->prices;
        $prices['actual'][getDefaultCurrency('code')] = $request->actual_price;
        $prices['sale'][getDefaultCurrency('code')] = $request->sale_price;
        
        $prices_attach = [];
        $prices_available = '';
        if ( ! empty( $prices['actual'] ) ) {
            foreach ($prices['actual'] as $key => $value) {
                $currency_sale_price = ! empty( $prices['sale'][ $key ] ) ? $prices['sale'][ $key ] : 0;
                $currency_id = null;
                $details = \App\Currency::where('code', $key)->first();
                if ( $details ) {
                    $currency_id = $details->id;
                }
                $prices_attach[] = [
                    'product_id' => $product->id,
                    'currency_id' => $currency_id,
                    'currency_code' => $key,
                    'currency_actual_price' => $value,
                    'currency_sale_price' => $currency_sale_price,
                ];
                if ( ! empty( $currency_sale_price ) ) { // If the sale price is available then considered it is available currency price.
                    $prices_available .= $key . ','; 
                }

            }
        }

        $additional = array(
            'prices' => json_encode( $prices ),
            'prices_available' => $prices_available,
        );
        $request->merge( $additional ); //Replacing the old input string with new string.

        if ( isDemo() ) {
            return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $product->update($request->all());

        $product->category()->sync(array_filter((array)$request->input('category')));                

        DB::select("DELETE FROM " . env('DB_PREFIX') . 'product_currency WHERE product_id = ' . $product->id);        
        if ( count( $prices_attach ) > 0 ) {
            $product->product_currency()->sync( $prices_attach );
        }

        $media = [];
        foreach ($request->input('image_gallery_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $product->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $product->updateMedia($media, 'image_gallery');

        $media = [];
        foreach ($request->input('other_files_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $product->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $product->updateMedia($media, 'other_files');

        flashMessage( 'success', 'update' );

        return redirect()->route('admin.products.index');
    }


    /**
     * Display Product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id , $list ='' )
    {
        if (! Gate::allows('product_view')) {
            return prepareBlockUserMessage();
        }
        $product = Product::findOrFail($id);

        return view('admin.products.show', compact('product','list'));
    }


    /**
     * Remove Product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('product_delete')) {
            return prepareBlockUserMessage();
        }
          if ( isDemo() ) {
            return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $product = Product::findOrFail($id);
        $product->deletePreservingMedia();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.products.index');
        } else {    
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected Product at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('product_delete')) {
            return prepareBlockUserMessage();
        }
          if ( isDemo() ) {
            return prepareBlockUserMessage( 'info', 'crud_disabled' );
          }
        if ($request->input('ids')) {
            $entries = Product::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }
        }

        flashMessage( 'success', 'deletes' );
    }

    /**
     * Permanently delete product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('product_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $product = Product::onlyTrashed()->findOrFail($id);
        $product->forceDelete();

        flashMessage( 'success', 'delete' );

        return back();
    }

    /**
     * Restore Product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('product_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    
    public function fillPrices() {
        if (request()->ajax()) {
            
            $prices = array();
            
            $actual_price = request('actual_price');
            $sale_price = request('sale_price');
            
            $basecurrency = \App\Currency::where('is_default', 'yes')->first();
            
            $currencies = \App\Currency::where('status', '=', 'Active')->get();         
            foreach ($currencies as $currency) {
                

                $prices['prices_actual_' . $currency->code] = number_format( $actual_price * $currency->rate, 2, '.', '' );
                $prices['prices_sale_' . $currency->code] = number_format( $sale_price * $currency->rate, 2, '.', '' );
            }
            return json_encode( $prices );
           
        }
    }

}
