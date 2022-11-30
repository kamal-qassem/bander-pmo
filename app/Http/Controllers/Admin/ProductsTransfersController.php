<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductsTransfersRequest;
use Validator;

class ProductsTransfersController extends Controller
{
    public function index()
    {
        if (! Gate::allows('products_transfer_access')) {
            return prepareBlockUserMessage();
        }

        $categories = \App\ProductCategory::get()->pluck('name', 'id');

        $ware_houses = \App\Warehouse::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $products_collection = \App\Product::get();
        $products = [];
        if ( ! empty( $products_collection ) ) {
            foreach ($products_collection as $product) {
                $warehouse = \App\Warehouse::find( $product->ware_house_id );
                if ( $warehouse ) {
                    $products[ $product->id ] = $product->name . ' ('.$warehouse->name.')';
                } else {
                    $products[ $product->id ] = $product->name;
                }
            }
        }
        return view('admin.products_transfers.index', compact('categories', 'products', 'ware_houses'));
    }

    public function store(Request $request)
    {

		if (! Gate::allows('products_transfer_access')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'ware_house_id_from' => 'required|productinwarehouse|exists:warehouses,id',
            'products.*' => 'required|exists:products,id',
            'ware_house_id_to' => 'required|exists:warehouses,id|different:ware_house_id_from',
        ];
        
        $messages = [
            'ware_house_id_from.productinwarehouse' => 'Few selected products are not in selected ware house',
            'ware_house_id_to.different' => '"Transfer From (Warehouse)" and "Transfer To (Warehouse)" should be different',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ( ! $validator->passes() ) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        // dd('Stop here');     
        $ware_house_id_from = $request->ware_house_id_from;
        $ware_house_id_to = $request->ware_house_id_to;
        $products = $request->product;

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        if ( ! empty( $ware_house_id_from ) && ! empty( $ware_house_id_to ) && ! empty( $products ) ) {
        	foreach ($products as $product_id) {
        		$product = \App\Product::find( $product_id );
        		$product->ware_house_id = $ware_house_id_to;
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        		$product->update();
        	}
        	flashMessage( 'success', 'products_transfered' );
        } else {
        	flashMessage( 'danger', 'create', trans( 'custom.products-transfer.no_products_transfered') );
        }

        return redirect()->route('admin.products.index');
    }
}
