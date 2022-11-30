<?php

namespace App\Http\Controllers\Admin;

use App\ProductsReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductsReturnsRequest;
use App\Http\Requests\Admin\UpdateProductsReturnsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ProductsReturnsController extends Controller
{
    /**
     * Display a listing of ProductsReturn.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('products_return_access')) {
            return prepareBlockUserMessage();
        }
        
        if (request()->ajax()) {
            $query = ProductsReturn::query();
            $query->with("customer");
            $query->with("currency");
            $query->with("ware_house");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {                
                if (! Gate::allows('products_return_delete')) {
                    return prepareBlockUserMessage();
                }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'products_returns.id',
                'products_returns.customer_id',
                'products_returns.currency_id',
                'products_returns.status',
                'products_returns.address',
                'products_returns.invoice_prefix',
                'products_returns.show_quantity_as',
                'products_returns.invoice_no',
                'products_returns.reference',
                'products_returns.order_date',
                'products_returns.order_due_date',
                'products_returns.update_stock',
                'products_returns.notes',
                'products_returns.ware_house_id',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'products_return_';
                $routeKey = 'admin.products_returns';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('subject', function ($row) {
                return $row->subject ? $row->subject : '';
            });
            $table->editColumn('customer.first_name', function ($row) {
                return $row->customer ? $row->customer->first_name : '';
            });
            $table->editColumn('currency.name', function ($row) {
                return $row->currency ? $row->currency->name : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });
            $table->editColumn('address', function ($row) {
                return $row->address ? $row->address : '';
            });
            $table->editColumn('invoice_prefix', function ($row) {
                return $row->invoice_prefix ? $row->invoice_prefix : '';
            });
            $table->editColumn('show_quantity_as', function ($row) {
                return $row->show_quantity_as ? $row->show_quantity_as : '';
            });
            $table->editColumn('invoice_no', function ($row) {
                return $row->invoice_no ? $row->invoice_no : '';
            });
            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : '';
            });
            $table->editColumn('order_date', function ($row) {
                return $row->order_date ? $row->order_date : '';
            });
            $table->editColumn('order_due_date', function ($row) {
                return $row->order_due_date ? $row->order_due_date : '';
            });
            $table->editColumn('update_stock', function ($row) {
                return $row->update_stock ? $row->update_stock : '';
            });
            $table->editColumn('notes', function ($row) {
                return $row->notes ? $row->notes : '';
            });
            $table->editColumn('tax.name', function ($row) {
                return $row->tax ? $row->tax->name : '';
            });
            $table->editColumn('discount.name', function ($row) {
                return $row->discount ? $row->discount->name : '';
            });
            $table->editColumn('ware_house.name', function ($row) {
                return $row->ware_house ? $row->ware_house->name : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.products_returns.index');
    }

    /**
     * Show the form for creating new ProductsReturn.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('products_return_create')) {
            return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::get()->pluck('first_name', 'id')->prepend(trans('global.app_please_select'), '');
        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $ware_houses = \App\Warehouse::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = ProductsReturn::$enum_status;
            
        return view('admin.products_returns.create', compact('enum_status', 'customers', 'currencies', 'taxes', 'discounts', 'ware_houses'));
    }

    /**
     * Store a newly created ProductsReturn in storage.
     *
     * @param  \App\Http\Requests\StoreProductsReturnsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductsReturnsRequest $request)
    {
        if (! Gate::allows('products_return_create')) {
            return prepareBlockUserMessage();
        }


        $products_details = array(
            'product_name' => $request->product_name,
            'product_qty' => $request->product_qty,
            'product_price' => $request->product_price,
            'product_amount' => $request->product_amount, 

            'product_tax' => $request->product_tax, // Rate
            'tax_type' => $request->tax_type,
            'tax_value' => $request->tax_value,

            'product_discount' => $request->product_discount, // Rate
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,

            'product_subtotal' => $request->product_subtotal, 
            'pid' => $request->pid, // Row ID
            'unit' => $request->unit,
            'hsn' => $request->hsn,
            'alert' => $request->alert,
            'stock_quantity' => $request->stock_quantity,
            'product_ids' => $request->product_ids,
            'product_description' => $request->product_description,

            'total_tax' => $request->total_tax,
            'total_discount' => $request->total_discount,
            'products_amount' => $request->products_amount, 
            'sub_total' => $request->sub_total,  
            'grand_total' => $request->grand_total,
        );

        // Let us calculate tax, discount, grandtotal at server side. If user cahgned through JS. we can prevent here!!
        $products = ( Object ) $products_details;
        $product_names = $products->product_name;

		$amount = 0;
        if ( ! empty( $product_names ) ) {
            
            $product_qtys = $products->product_qty;
            $product_prices = $products->product_price;
            $product_amounts = $products->product_amount;

            $product_taxs = $products->product_tax;
            $tax_types = $products->tax_type;
            $tax_values = $products->tax_value;

            $product_discounts = $products->product_discount;
            $discount_types = $products->discount_type;
            $discount_values = $products->discount_value;

            $product_subtotals = $products->product_subtotal;
            $pids = $products->pid;
            $units = $products->unit;
            $hsns = $products->hsn;
            $alerts = $products->alert;
            $stock_quantitys = $products->stock_quantity;
            $product_ids = $products->product_ids;
            $product_descriptions = $products->product_description;

            $total_tax = 0;
            $total_discount = 0;
            $total_products_amount = 0;
            $sub_total = 0;
            $grand_total = 0;

            for( $i = 0; $i < count( $product_names ); $i++ ) {
                
                $product_qty = ! empty( $product_qtys[ $i ] ) ? $product_qtys[ $i ] : '1';
                $product_price = ! empty( $product_prices[ $i ] ) ? $product_prices[ $i ] : '0';
                $product_amount = $product_qty * $product_price;
                $product_amounts[ $i ] = $product_amount; // Changed here.
                $total_products_amount += $product_amount;

                $product_tax = ! empty( $product_taxs[ $i ] ) ? $product_taxs[ $i ] : '0'; // Rate.
                $tax_type = ! empty( $tax_types[ $i ] ) ? $tax_types[ $i ] : 'percent';
                $tax_value = ! empty( $tax_values[ $i ] ) ? $tax_values[ $i ] : '0';
                if ( 'percent' === $tax_type && $product_tax > 0 && $product_amount > 0 ) {
                    $tax_value = ( $product_amount * $product_tax) / 100;
                } else {
                    $tax_value = $product_tax;
                }
                $tax_values[ $i ] = $tax_value; // Changed Here.
                $total_tax += $tax_value;

                $product_discount = ! empty( $product_discounts[ $i ] ) ? $product_discounts[ $i ] : '0'; // Rate.
                $discount_type = ! empty( $discount_types[ $i ] ) ? $discount_types[ $i ] : 'percent';
                $discount_value = ! empty( $discount_values[ $i ] ) ? $discount_values[ $i ] : '0';
                if ( 'percent' === $discount_type && $product_discount > 0 && $product_amount > 0 ) {
                    $discount_value = ( $product_amount * $product_discount) / 100;
                } else {
                    $discount_value = $product_discount;
                }
                $discount_values[ $i ] = $product_discount; // Changed Here.
                $total_discount += $discount_value;


                $amount = ($product_qty * $product_price) + $tax_value - $discount_value;
                $product_subtotals[ $i ] = $amount;
                $grand_total += $amount;
                $sub_total +=  $amount + $discount_value;

                $pid = ! empty( $pids[ $i ] ) ? $pids[ $i ] : '';
                $unit = ! empty( $units[ $i ] ) ? $units[ $i ] : '';
                $hsn = ! empty( $hsns[ $i ] ) ? $hsns[ $i ] : '';
                $alert = ! empty( $alerts[ $i ] ) ? $alerts[ $i ] : '';
                $stock_quantity = ! empty( $stock_quantitys[ $i ] ) ? $stock_quantitys[ $i ] : '';
                $product_id = ! empty( $product_ids[ $i ] ) ? $product_ids[ $i ] : '';
                $product_description = ! empty( $product_descriptions[ $i ] ) ? $product_descriptions[ $i ] : '';
            }
			
			// Calculation of Cart Tax.
            $tax_id = $request->tax_id;
			$tax_format = $request->tax_format;
			$cart_tax = 0;    
			if ( $tax_id > 0 ) {
				$invoice = new ProductsReturn();
				$invoice->setTaxIdAttribute( $tax_id );
				$tax = $invoice->tax()->first();
				
				$rate = $tax->rate;
				$rate_type = $tax->rate_type;

				if ( $rate > 0 ) {
					if ( 'before_tax' === $tax_format ) {
						if ( 'percent' === $rate_type ) {
							$cart_tax = ( $total_products_amount * $rate) / 100;
						} else {
							$cart_tax = $rate;
						}                    
					} else {
						$new_amount = $total_products_amount + $total_tax;
						if ( 'percent' === $rate_type ) {
							$cart_tax = ( $new_amount * $rate) / 100;
						} else {
							$cart_tax = $rate;
						}
					}
				} 
			}
			
			// Let us calculate Cart Discount
			$cart_discount = 0;
			$discount_id = $request->discount_id;
			if ( $discount_id > 0 ) {
				$invoice = new ProductsReturn();
				$invoice->setDiscountIdAttribute( $discount_id );
				$discount = $invoice->discount()->first();
				$rate = $discount->rate;
				$rate_type = $discount->rate_type;
				if ( $rate > 0 ) {
					if ( 'before_tax' === $discount_format ) {
						if ( 'percent' === $rate_type ) {
							$cart_discount = ( $total_products_amount * $rate) / 100;
						} else {
							$cart_discount = $rate;
						}                    
					} else {
						$new_amount = $total_products_amount + $total_tax;
						if ( 'percent' === $rate_type ) {
							$cart_discount = ( $new_amount * $rate) / 100;
						} else {
							$cart_discount = $rate;
						}
					}
				} 
			}

            $products_details['tax_value'] = $tax_values;
            $products_details['discount_value'] = $discount_values;
            $products_details['product_amount'] = $product_amounts;
            $products_details['product_subtotal'] = $product_subtotals;

            $products_details['total_discount'] = $total_discount;
            $products_details['total_tax'] = $total_tax;
            $products_details['products_amount'] = $total_products_amount;
            $products_details['sub_total'] = $sub_total;
            $products_details['grand_total'] = $grand_total;
			
			$products_details['cart_tax'] = $cart_tax;
			$products_details['cart_discount'] = $cart_discount;
			$amount_payable = $grand_total + $cart_tax - $cart_discount;
			$products_details['amount_payable'] = $amount_payable;
        }

        $addtional = array(
            'products' => json_encode( $products_details ),
			'amount' => $amount_payable,
        );

        $invoice_no = $request->invoice_no;
        if ( empty( $invoice_no ) ) {
            $invoice_start = getSetting( 'invoice_start', 'currency_settings' );
            $max_id = ProductsReturn::max('id');
            $invoice_no = $max_id;
            if ( is_numeric( $invoice_start ) ) {
                $invoice_no = $invoice_start + $max_id;
            }
        }

        if ( ! empty( $request->invoice_prefix ) ) {
            $invoice_no = $request->invoice_prefix . $invoice_no;
        }
        $addtional['invoice_no'] = $invoice_no;

        $request->request->add( $addtional ); //add request

        ProductsReturn::create($request->all());

        $update_stock = $request->update_stock;
        if ( 'Yes' === $update_stock ) {
            // Products are returned, so we need to increase products quantity.
            $product_ids = $request->product_ids;
            $product_qty = $request->product_qty;
            if ( ! empty( $product_ids ) ) {
                foreach ($product_ids as $key => $id ) {
                    $product = \App\Product::findOrFail( $id );
                    if ( $product ) {
                        $qty = ! empty( $product_qty[ $key ] ) ? $product_qty[ $key ] : 0;
                        $quantity = $product->stock_quantity + $qty;
                        $product->update( [ 'stock_quantity' => $quantity ] );
                    }
                }
            }
        }        

        flashMessage();
        return redirect()->route('admin.products_returns.index');
    }


    /**
     * Show the form for editing ProductsReturn.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('products_return_edit')) {
            return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::get()->pluck('first_name', 'id')->prepend(trans('global.app_please_select'), '');
        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $ware_houses = \App\Warehouse::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = ProductsReturn::$enum_status;
        $enum_update_stock = ProductsReturn::$enum_update_stock;
            
        $products_return = ProductsReturn::findOrFail($id);

        return view('admin.products_returns.edit', compact('products_return', 'enum_status', 'customers', 'currencies', 'taxes', 'discounts', 'ware_houses', 'enum_update_stock'));
    }

    /**
     * Update ProductsReturn in storage.
     *
     * @param  \App\Http\Requests\UpdateProductsReturnsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductsReturnsRequest $request, $id)
    {
        if (! Gate::allows('products_return_edit')) {
            return prepareBlockUserMessage();
        }

        $products_details = array(
            'product_name' => $request->product_name,
            'product_qty' => $request->product_qty,
            'product_price' => $request->product_price,
            'product_amount' => $request->product_amount, 

            'product_tax' => $request->product_tax, // Rate
            'tax_type' => $request->tax_type,
            'tax_value' => $request->tax_value,

            'product_discount' => $request->product_discount, // Rate
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,

            'product_subtotal' => $request->product_subtotal, 
            'pid' => $request->pid, // Row ID
            'unit' => $request->unit,
            'hsn' => $request->hsn,
            'alert' => $request->alert,
            'stock_quantity' => $request->stock_quantity,
            'product_ids' => $request->product_ids,
            'product_description' => $request->product_description,

            'total_tax' => $request->total_tax,
            'total_discount' => $request->total_discount,
            'products_amount' => $request->products_amount, // Amount without Tax and Discount
            'sub_total' => $request->sub_total,          
            'grand_total' => $request->grand_total, 
        );
        // Let us calculate tax, discount, grandtotal at server side. If user cahgned through JS. we can prevent here!!
        $products = ( Object ) $products_details;
        $product_names = $products->product_name;

        if ( ! empty( $product_names ) ) {
            
            $product_qtys = $products->product_qty;
            $product_prices = $products->product_price;
            $product_amounts = $products->product_amount; 

            $product_taxs = $products->product_tax;
            $tax_types = $products->tax_type;
            $tax_values = $products->tax_value;

            $product_discounts = $products->product_discount;
            $discount_types = $products->discount_type;
            $discount_values = $products->discount_value;

            $product_subtotals = $products->product_subtotal;
            $pids = $products->pid;
            $units = $products->unit;
            $hsns = $products->hsn;
            $alerts = $products->alert;
            $stock_quantitys = $products->stock_quantity;
            $product_ids = $products->product_ids;
            $product_descriptions = $products->product_description;

            $total_tax = 0;
            $total_discount = 0;
            $total_products_amount = 0;
            $sub_total = 0;
            $grand_total = 0;

            for( $i = 0; $i < count( $product_names ); $i++ ) {
                
                $product_qty = ! empty( $product_qtys[ $i ] ) ? $product_qtys[ $i ] : '1';
                $product_price = ! empty( $product_prices[ $i ] ) ? $product_prices[ $i ] : '0';
                $product_amount = $product_qty * $product_price;
                $product_amounts[ $i ] = $product_amount; // Changed here.
                $total_products_amount += $product_amount;

                $product_tax = ! empty( $product_taxs[ $i ] ) ? $product_taxs[ $i ] : '0'; // Rate.
                $tax_type = ! empty( $tax_types[ $i ] ) ? $tax_types[ $i ] : 'percent';
                $tax_value = ! empty( $tax_values[ $i ] ) ? $tax_values[ $i ] : '0';
                if ( 'percent' === $tax_type && $product_tax > 0 && $product_amount > 0 ) {
                    $tax_value = ( $product_amount * $product_tax) / 100;
                } else {
                    $tax_value = $product_tax;
                }
                $tax_values[ $i ] = $tax_value; // Changed Here.
                $total_tax += $tax_value;

                $product_discount = ! empty( $product_discounts[ $i ] ) ? $product_discounts[ $i ] : '0'; // Rate.
                $discount_type = ! empty( $discount_types[ $i ] ) ? $discount_types[ $i ] : 'percent';
                $discount_value = ! empty( $discount_values[ $i ] ) ? $discount_values[ $i ] : '0';
                if ( 'percent' === $discount_type && $product_discount > 0 && $product_amount > 0 ) {
                    $discount_value = ( $product_amount * $product_discount) / 100;
                } else {
                    $discount_value = $product_discount;
                }
                $discount_values[ $i ] = $product_discount; // Changed Here.
                $total_discount += $discount_value;


                $amount = ($product_qty * $product_price) + $tax_value - $discount_value;
                $product_subtotals[ $i ] = $amount;
                $grand_total += $amount;
                $sub_total +=  $amount + $discount_value;

                $pid = ! empty( $pids[ $i ] ) ? $pids[ $i ] : '';
                $unit = ! empty( $units[ $i ] ) ? $units[ $i ] : '';
                $hsn = ! empty( $hsns[ $i ] ) ? $hsns[ $i ] : '';
                $alert = ! empty( $alerts[ $i ] ) ? $alerts[ $i ] : '';
                $stock_quantity = ! empty( $stock_quantitys[ $i ] ) ? $stock_quantitys[ $i ] : '';
                $product_id = ! empty( $product_ids[ $i ] ) ? $product_ids[ $i ] : '';
                $product_description = ! empty( $product_descriptions[ $i ] ) ? $product_descriptions[ $i ] : '';
            }

            $products_details['tax_value'] = $tax_values;
            $products_details['discount_value'] = $discount_values;
            $products_details['product_amount'] = $product_amounts;
            $products_details['product_subtotal'] = $product_subtotals;

            $products_details['total_discount'] = $total_discount;
            $products_details['total_tax'] = $total_tax;
            $products_details['sub_total'] = $sub_total;
            $products_details['grand_total'] = $grand_total;
        }

        $addtional = array(
            'products' => json_encode( $products_details ),
        );

        $invoice_no = $request->invoice_no;
        if ( empty( $invoice_no ) ) {
            $invoice_start = getSetting( 'invoice_start', 'currency_settings' );
            $max_id = ProductsReturn::max('id');
            $invoice_no = $max_id;
            if ( is_numeric( $invoice_start ) ) {
                $invoice_no = $invoice_start + $max_id;
            }
        }

        if ( ! empty( $request->invoice_prefix ) ) {
            $invoice_no = $request->invoice_prefix . $invoice_no;
        }
        $addtional['invoice_no'] = $invoice_no;

        $request->request->add( $addtional ); //add request


        $products_return = ProductsReturn::findOrFail($id);
        $products_return->update($request->all());

        $update_stock = $request->update_stock;
        if ( 'Yes' === $update_stock ) {
            // Products are returned, so we need to increase products quantity.
            $product_ids = $request->product_ids;
            $product_qty = $request->product_qty;
            if ( ! empty( $product_ids ) ) {
                foreach ($product_ids as $key => $id ) {
                    $product = \App\Product::findOrFail( $id );
                    if ( $product ) {
                        $qty = ! empty( $product_qty[ $key ] ) ? $product_qty[ $key ] : 0;
                        $quantity = $product->stock_quantity + $qty;
                        $product->update( [ 'stock_quantity' => $quantity ] );
                    }
                }
            }
        }

        flashMessage( 'success', 'update' );

        return redirect()->route('admin.products_returns.index');
    }


    /**
     * Display ProductsReturn.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('products_return_view')) {
            return prepareBlockUserMessage();
        }
        $products_return = ProductsReturn::findOrFail($id);

        return view('admin.products_returns.show', compact('products_return'));
    }


    /**
     * Remove ProductsReturn from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('products_return_delete')) {
            return prepareBlockUserMessage();
        }
        $products_return = ProductsReturn::findOrFail($id);
        $products_return->delete();

        flashMessage( 'success', 'delete');
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.products_returns.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected ProductsReturn at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('products_return_delete')) {
            return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = ProductsReturn::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore ProductsReturn from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('products_return_delete')) {
            return prepareBlockUserMessage();
        }
        $products_return = ProductsReturn::onlyTrashed()->findOrFail($id);
        $products_return->restore();

        flashMessage( 'success', 'restore');

        return back();
    }

    /**
     * Permanently delete ProductsReturn from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('products_return_delete')) {
            return prepareBlockUserMessage();
        }
        $products_return = ProductsReturn::onlyTrashed()->findOrFail($id);
        $products_return->forceDelete();

        flashMessage( 'success', 'delete');

        return back();
    }

    /**
     * Delete all selected ProductsReturn at once.
     *
     * @param Request $request
     */
    public function searchProducts(Request $request)
    {      
        $type = $request->type;
        if ( empty( $type ) ) {
            $type = 'products';
        }
        switch ( $type ) {
            case 'leads':
                $leads = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', LEADS_TYPE);
                   })->get()->pluck('name', 'id')->toArray();
                echo json_encode( $leads );
            break;
            case 'customers':
                $customers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->toArray();
                echo json_encode( $customers );
            break;
            case 'customer':
                    $customer_id = $request->customer_id;
                    $customer = \App\Contact::where( 'id', $customer_id)->first();
                    if ( ! empty( $customer ) ) {
                        $customer->havetransactions = 'no';
                        if ( haveTransactions( $customer_id ) ) {
                            $customer->havetransactions = 'yes';
                        }
                        if ( ! $customer->currency_id ) {
                            $customer->currency_id = getDefaultCurrency('id');
                        }
						
						$display_currency = \App\Settings::getSetting('display_currency', 'currency_settings');
						if ( 'code' === $display_currency ) {
							$customer->currency_code = getCurrency($customer->currency_id, 'code');
						} else {
							$customer->currency_code = getCurrency($customer->currency_id, 'symbol');
						}

                        $fulladdress = $customer->fulladdress;
                        if ( empty( $fulladdress ) ) {
                            $fulladdress = $customer->address;
                            if ( ! empty( $customer->city ) ) {
                                $fulladdress .= "\n" . $customer->city;
                            }
                            if ( ! empty( $customer->state_region ) ) {
                                $fulladdress .= "\n" . $customer->state_region;
                            }
                            if ( ! empty( $customer->country_id ) ) {
                                $fulladdress .= "\n" . getCountryname( $customer->country_id );
                            }
                            if ( ! empty( $customer->zip_postal_code ) ) {
                                $fulladdress .= ' - ' . $customer->zip_postal_code;
                            }
                        } else {
                            $fulladdress = str_replace(", ", "\n", $fulladdress);
                        }
                        $customer->address = $fulladdress;
                        
                        if ( $customer->delivery_address ) {
                            

                            $delivery_address = ! empty( $customer->delivery_address ) ? json_decode( $customer->delivery_address, true ) : array();
                            $delivery_address_str = '';
                            if ( ! empty( $delivery_address['first_name'] ) ) {
                                $delivery_address_str .=  $delivery_address['first_name'];
                            }

                            if ( ! empty( $delivery_address['last_name'] ) ) {
                                $delivery_address_str .= ' ' . $delivery_address['last_name'];
                            }
                            if ( ! empty( $delivery_address_str ) ) {
                                $delivery_address_str .= "\n";
                            }
                            if ( ! empty( $delivery_address['address'] ) ) {
                                $delivery_address_str .= $delivery_address['address'];
                            }
                            if ( ! empty( $delivery_address_str ) ) {
                                $delivery_address_str .= "\n";
                            }

                            if ( ! empty( $delivery_address['city'] ) ) {
                                $delivery_address_str .= $delivery_address['city'];
                            }
                            if ( ! empty( $delivery_address_str ) ) {
                                $delivery_address_str .= "\n";
                            }

                            if ( ! empty( $delivery_address['state_region'] ) ) {
                            $delivery_address_str .= $delivery_address['state_region'];
                            }
                            if ( ! empty( $delivery_address['cicountry_idty'] ) ) {
                            $delivery_address_str .= "\n" . getCountryname( $delivery_address['country_id'] );
                            }
                            if ( ! empty( $delivery_address['zip_postal_code'] ) ) {
                            $delivery_address_str .= ' - ' . $delivery_address['zip_postal_code'];
                            }
                            
                            $customer->delivery_address = $delivery_address_str;
                        }
                        
                        echo json_encode( $customer );
                    }
                break;
            case 'reload':
				$currency_id = $request->input('currency_id');
				
				$display_currency = \App\Settings::getSetting('display_currency', 'currency_settings');
				if ( 'code' === $display_currency ) {
					$currency_code = getCurrency($currency_id, 'code');
				} else {
					$currency_code = getCurrency($currency_id, 'symbol');
				}
				
				$currency_short_code = getCurrency($currency_id, 'code');
				$view = view("admin.common.add-products", compact('currency_id') )->render();
				
				
				return response()->json(['html' => $view, 'currency_code' => $currency_code, 'currency_id' => $currency_id, 'currency_short_code' => $currency_short_code]);
				
				break;
            case 'task_details':
                if ($request->input('name_startsWith') || $request->input('product_id')) {
                    $query = \App\ProjectTask::query();
                    $query->select([
                        'project_tasks.id',
                        'project_tasks.name',
                        'project_tasks.description',
                        'project_tasks.startdate',
                        'project_tasks.duedate',
                        'project_tasks.datefinished',
                        'project_tasks.billable',
                        'project_tasks.billed',
                        'project_tasks.hourly_rate',
                        'project_tasks.project_id',
                    ]);
                    if ( $request->input('name_startsWith') ) {
                        $query->where( 'name', 'like', $request->input('name_startsWith') . '%' );
                    } elseif ( $request->input('product_id') ) {
                        $query->where( 'id', '=', $request->input('product_id') );
                    }

                    $product_ids = $request->product_ids;
                    if ( ! empty( $product_ids ) ) {
                        $query->whereNotIn( 'id', explode( ',', $product_ids) );
                    }

                    $currency_id = $request->input('currency_id');
                    $currency_code = getCurrency($currency_id, 'code');
                    
                    $display_currency = \App\Settings::getSetting('display_currency', 'currency_settings');
                    
                    $records = array();
                    foreach ($query->get() as $record ) {                       
                        
                        $task_id = $record->id;
                        $sec     = \App\ProjectTask::taskTotalTime($task_id);
                        $record->quantity = floatVal(secondsToQuantity($sec));
                        $record->currency_code = $currency_code;

                        $hourly_rate = $record->hourly_rate;
                        if ( empty( $hourly_rate ) ) {
                            $hourly_rate = $record->project->hourly_rate;
                        }

                        if ( $record->project->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS ) {
                            $hourly_rate = $record->project->project_rate_per_hour;
                        }
                        if ($record->project->billing_type_id == PROJECT_BILLING_TYPE_FIXED_PRICE) {
                            $hourly_rate = $record->project->budget;
                            $record->quantity = 1;
                        }
                        $record->sale_price = $hourly_rate;

                        $record->tax_rate = 0;
                        $record->rate_type = 'value';
                        $record->discount_rate = 0;
                        $record->discount_type = 'value';

                        $record->excerpt = $record->description;
                        $record->measurement_unit = 0;
                        $record->hsn_sac_code = 0;
                        $record->alert_quantity = 0;
                        $record->stock_quantity = 0;

                        $records[] = $record;
                        if ( $request->input('product_id') ) {
                            $records = $record;
                        }
                    }
                    echo json_encode( $records );
                }
                break;
            case 'warehouse_products':
                $products = [];

                if ($request->input('wid')) {
                    $products_collection = \App\Product::where('ware_house_id', $request->input('wid'))->get();
                    
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
                } else {
                    $products_collection = \App\Product::get();
                    
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
                }
                echo json_encode( $products );
            default:
                if ($request->input('name_startsWith') || $request->input('product_id')) {
                    
                    $product_id = $type = '';
                    $parts = explode( '_', $request->input('product_id') );
                    if ( ! empty( $parts[0] ) ) {
                        $product_id = $parts[0];
                    }
                    if ( ! empty( $parts[1] ) ) {
                        $type = $parts[1];
                    }

                    $currency_id = $request->input('currency_id');
                    $currency_code = getCurrency($currency_id, 'code');
                    
                    $display_currency = \App\Settings::getSetting('display_currency', 'currency_settings');

                    $records = array();
                    if ( 'task' === $type ) {
                        $query = \App\ProjectTask::query();
                        if ( $request->input('name_startsWith') ) {
                            $query->where( 'name', 'like', $request->input('name_startsWith') . '%' );
                        } elseif ( ! empty( $product_id ) ) {
                            $query->where( 'id', $product_id );
                        }

                        foreach ($query->get() as $record ) {
                            


                            $record->stock_quantity = 1;
                            $record->sale_price = $record->hourly_rate;
                            
                            $record->tax_rate = 0;
                            $record->rate_type = 'percent';
                            $record->discount_rate = 0;
                            $record->discount_type = 'percent';
                            $record->measurement_unit = 'hours';
                            $record->hsn_sac_code = '';
                            $record->alert_quantity = 0;
                            $record->record_type = 'task';
                            
                            if ( 'code' === $display_currency ) {
                                $record->currency_code = getCurrency($currency_id, 'code');
                            } else {
                                $record->currency_code = getCurrency($currency_id, 'symbol');
                            }

                            $records[] = $record;
                            if ( $request->input('product_id') ) {
                                $records = $record;
                            }
                        }
                    } elseif( 'expense' === $type ) {
                        $query = \App\Expense::query();
                        if ( $request->input('name_startsWith') ) {
                            $query->where( 'name', 'like', $request->input('name_startsWith') . '%' );
                        } elseif ( ! empty( $product_id ) ) {
                            $query->where( 'id', $product_id );
                        }

                        foreach ($query->get() as $record ) {
                            
                            $record->sale_price = $record->amount;

                            $record->tax_rate = 0;
                            $record->rate_type = 'percent';
                            $record->discount_rate = 0;
                            $record->discount_type = 'percent';
                            $record->measurement_unit = 'hours';
                            $record->hsn_sac_code = '';
                            $record->alert_quantity = 0;
                            $record->record_type = 'expense';
                            
                            if ( 'code' === $display_currency ) {
                                $record->currency_code = getCurrency($currency_id, 'code');
                            } else {
                                $record->currency_code = getCurrency($currency_id, 'symbol');
                            }

                            $records[] = $record;
                            if ( $request->input('product_id') ) {
                                $records = $record;
                            }
                        }
                    } else {
                        $query = \App\Product::query();
                        $query->select([
                            'products.id',
                            'products.name',
                            'products.product_code',
                            'products.actual_price',
                            'products.sale_price',
                            'products.stock_quantity',
                            'products.hsn_sac_code',
                            'products.alert_quantity',
                            'products.description',
                            'products.excerpt',
                            'products.tax_id',
                            'products.discount_id',
                            'products.measurement_unit',
                            'products.prices',
                        ]);
                        if ( $request->input('name_startsWith') ) {
                            $query->where( 'name', 'like', $request->input('name_startsWith') . '%' );
                            if ( $request->input('wid') && is_int( $request->input('wid') ) ) {
                                $query->where( 'ware_house_id', '=', $request->input('wid') );
                            }
                        } elseif ( $request->input('product_id') ) {
                            $query->where( 'id', '=', $request->input('product_id') );
                        }

                        $product_ids = $request->product_ids;
                        if ( ! empty( $product_ids ) ) {
                            $query->whereNotIn( 'id', explode( ',', $product_ids) );
                        }
                        
                        foreach ($query->get() as $record ) {
                            $stock_quantity = 0;
                            if ( ! empty( $record->stock_quantity ) ) {
                                $stock_quantity = $record->stock_quantity;
                            }
                            $record->namequantity = $record->name . ' ('.$stock_quantity.')';
                            
                            $prices = ! empty( $record->prices ) ? json_decode( $record->prices, true ) : array();

                            $actual_price = ! empty( $prices['actual'][ $currency_code ] ) ? $prices['actual'][ $currency_code ] : '0';
                            $sale_price = ! empty( $prices['sale'][ $currency_code ] ) ? $prices['sale'][ $currency_code ] : '0';
                            
                            $record->sale_price = $sale_price; // Let us take the selected currency value as sale price.
                            
                            if ( 'code' === $display_currency ) {
                                $record->currency_code = getCurrency($currency_id, 'code');
                            } else {
                                $record->currency_code = getCurrency($currency_id, 'symbol');
                            }
        
                            $tax = $record->tax;
                            if ( $tax ) {
                                $record->tax_rate = $tax->rate;
                                $record->tax_value = $tax->rate;
                                $record->rate_type = $tax->rate_type;
                                if ( $tax->rate > 0 && 'percent' === $tax->rate_type ) {
                                    $record->tax_value = ($record->sale_price * $tax->rate) / 100;
                                }
                            } else {
                                $record->tax_rate = 0;
                                $record->tax_value = 0;
                                $record->rate_type = 'percent';
                            }

                            $discount = $record->discount;
                            if ( $discount ) {
                                $record->discount_rate = $discount->discount;
                                $record->discount_value = $record->discount;
                                $record->discount_type = $discount->discount_type;
                                if ( $discount->discount > 0 && 'percent' === $discount->discount_type ) {
                                    $record->discount_value = ($record->sale_price * $discount->discount) / 100;
                                }
                            } else {
                                $record->discount_rate = 0;
                                $record->discount_value = 0;
                                $record->discount_type = 'percent';
                            }
                            $record->record_type = 'product';

                            $records[] = $record;
                            if ( $request->input('product_id') ) {
                                $records = $record;
                            }
                        }
                    }
                    echo json_encode( $records );
                }
                break;
        }
    
    }
}
