<?php

namespace App\Http\Controllers\Admin;

use App\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePurchaseOrdersRequest;
use App\Http\Requests\Admin\UpdatePurchaseOrdersRequest;
use App\Http\Requests\Admin\UploadPurchaseOrdersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Traits\FileUploadTrait;

use PDF;
use Location;
use Validator;
class PurchaseOrdersController extends Controller
{
    use FileUploadTrait;
    
    public function __construct() {
     $this->middleware('plugin:purchase_order');
    }
    /**
     * Display a listing of PurchaseOrder.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {
        if (! Gate::allows('purchase_order_access')) {
            return prepareBlockUserMessage();
        }
        
        if (request()->ajax()) {
            $query = PurchaseOrder::query();
            $query->with("customer");
            $query->with("currency");
            $query->with("warehouse");
            $query->with("tax");
            $query->with("discount");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('purchase_order_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'purchase_orders.id',
                'purchase_orders.customer_id',
                'purchase_orders.subject',
                'purchase_orders.status',
                'purchase_orders.address',
                'purchase_orders.invoice_prefix',
                'purchase_orders.show_quantity_as',
                'purchase_orders.invoice_no',
                'purchase_orders.reference',
                'purchase_orders.order_date',
                'purchase_orders.order_due_date',
                'purchase_orders.update_stock',
                'purchase_orders.notes',
                'purchase_orders.currency_id',
                'purchase_orders.warehouse_id',
                'purchase_orders.tax_id',
                'purchase_orders.discount_id',
                'purchase_orders.paymentstatus',
                'purchase_orders.amount',
            ]);

            // Custom Filters Start.
            $query->when(request('date_filter', false), function ($q, $date_filter) {
                $parts = explode(' - ' , $date_filter);
                $date_type = request('date_type');
                $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
                $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');
                if ( ! empty( $date_type ) ) {
                    if ( in_array($date_type, array( 'created_at') ) ) {
                        return $q->where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to);
                    } else {
                        return $q->whereBetween($date_type, [$date_from, $date_to]);
                    }
                }
            });
            $query->when(request('paymentstatus', false), function ($q, $paymentstatus) { 
                if ( 'unpaid' === $paymentstatus ) {
                    return $q->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->whereRaw('order_due_date >= DATE(NOW())');
                } if ( 'overdue' === $paymentstatus ) {
                    return $q->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->whereRaw('order_due_date < DATE(NOW())');
                } else {
                    return $q->where('paymentstatus', '=', $paymentstatus );
                }
            });
            $query->when(request('currency_id', false), function ($q, $currency_id) { 
                return $q->where('currency_id', $currency_id);
            });
            $query->when(request('supplier', false), function ($q, $supplier) { 
                return $q->where('customer_id', $supplier);
            });
            /// Custom Filters End.

            /**
             * when we call purchase orders display from other pages!
            */
            if ( ! empty( $type ) && 'contact' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('purchase_orders.customer_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'currency' === $type ) { // If the type is "currency" then id we are getting in "customer_id" is "currency_id"
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('purchase_orders.currency_id', $type_id);
                });
            }
            /**
             * Tax is additional tax applied to the PO, not to the products in the PO.
             */
            if ( ! empty( $type ) && 'tax' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('purchase_orders.tax_id', $type_id);
                });
            }
            /**
             * Tax is additional discount applied to the PO, not to the products in the PO.
             */
            if ( ! empty( $type ) && 'discount' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('purchase_orders.discount_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'warehouse' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('purchase_orders.warehouse_id', $type_id);
                });
            }

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'purchase_order_';
                $routeKey = 'admin.purchase_orders';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });            
            $table->editColumn('customer.first_name', function ($row) {
                return $row->customer ? '<a href="'.route('admin.contacts.show', $row->customer->id).'" title="'.$row->customer->first_name.'">' . $row->customer->first_name . '</a>' : '';
            });
            $table->editColumn('subject', function ($row) {
                return $row->subject ? $row->subject : '';
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
                return $row->invoice_no ? '<a href="'.route('admin.purchase_orders.show', $row->id).'" title="'.$row->invoice_no.'">' . $row->invoice_no . '</a>' : '';
            });
            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : '';
            });
            $table->editColumn('order_date', function ($row) {
                return $row->order_date ? digiDate( $row->order_date ) : '';
            });
            $table->editColumn('order_due_date', function ($row) {
                return $row->order_due_date ? digiDate( $row->order_due_date ) : '';
            });
            $table->editColumn('update_stock', function ($row) {
                return $row->update_stock ? $row->update_stock : '';
            });
            $table->editColumn('notes', function ($row) {
                return $row->notes ? $row->notes : '';
            });
            $table->editColumn('currency.name', function ($row) {
                return $row->currency ? $row->currency->name : '';
            });
            $table->editColumn('warehouse.name', function ($row) {
                return $row->warehouse ? $row->warehouse->name : '';
            });
            $table->editColumn('tax.name', function ($row) {
                return $row->tax ? $row->tax->name : '';
            });
            $table->editColumn('discount.name', function ($row) {
                return $row->discount ? $row->discount->name : '';
            });
            $table->editColumn('amount', function ($row) {

                $total_paid =  \App\PurchaseOrderPayment::where('purchase_order_id', '=', $row->id)->where('payment_status', 'Success')->sum('amount');
                $amount_due = $row->amount - $total_paid;
                $paymentstatus = $row->paymentstatus;

                if($paymentstatus == 'partial'){

                    return  $row->amount ? digiCurrency( $row->amount, $row->currency_id ).'<br/>'.'Amount due :'.'<span class="badge">'.digiCurrency($amount_due,$row->currency_id).'</span>' : '';
                }else{

                return $row->amount ? digiCurrency( $row->amount, $row->currency_id ) : '';
            }
            });

             $table->editColumn('paymentstatus', function ($row) {
                return $row->paymentstatus ? ucfirst( $row->paymentstatus ) : '';
            });

            $table->rawColumns(['actions','massDelete', 'invoice_no', 'customer.first_name','amount']);

            return $table->make(true);
        }

        return view('admin.purchase_orders.index');
    }

    /**
     * Show the form for creating new PurchaseOrder.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('purchase_order_create')) {
            return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::whereHas("contact_type",
            function ($query) {
                $query->where('id', SUPPLIERS_TYPE);
            })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $warehouses = \App\Warehouse::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = PurchaseOrder::$enum_status;
		
		$enum_discounts_format = \App\Invoice::$enum_discounts_format;
        $enum_tax_format = \App\Invoice::$enum_tax_format;
            
        return view('admin.purchase_orders.create', compact('enum_status', 'customers', 'currencies', 'warehouses', 'taxes', 'discounts', 'enum_discounts_format', 'enum_tax_format'));
    }

    /**
     * Store a newly created PurchaseOrder in storage.
     *
     * @param  \App\Http\Requests\StorePurchaseOrdersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseOrdersRequest $request)
    {
        if (! Gate::allows('purchase_order_create')) {
            return prepareBlockUserMessage();
        }

        $products_details = getProductDetails( $request );

        $tax_format = $request->tax_format;
        $discount_format =  $request->discount_format;

        $products_details['discount_format'] = $discount_format;
        $products_details['tax_format'] = $tax_format;
        
        // These are product values.
        $grand_total = ! empty( $products_details['grand_total'] ) ? $products_details['grand_total'] : 0;
        $products_amount = ! empty( $products_details['products_amount'] ) ? $products_details['products_amount'] : 0;
        $total_tax = ! empty( $products_details['total_tax'] ) ? $products_details['total_tax'] : 0;
        $total_discount = ! empty( $products_details['total_discount'] ) ? $products_details['total_discount'] : 0;

        // Calculation of Cart Tax.
        $tax_id = $request->tax_id;
        $cart_tax = 0;    
        if ( $tax_id > 0 ) {
            $invoice = new PurchaseOrder();
            $invoice->setTaxIdAttribute( $tax_id );
            $tax = $invoice->tax()->first();
            $rate = 0;
            $rate_type = 'percent';
			if ( $tax ) {
				$rate = $tax->rate;
				$rate_type = $tax->rate_type;
			}

            if ( $rate > 0 ) {
                if ( 'before_tax' === $tax_format ) {
                    if ( 'percent' === $rate_type ) {
                        $cart_tax = ( $products_amount * $rate) / 100;
                    } else {
                        $cart_tax = $rate;
                    }                    
                } else {
                    $new_amount = $products_amount + $total_tax;
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
            $invoice = new PurchaseOrder();
            $invoice->setDiscountIdAttribute( $discount_id );
            $discount = $invoice->discount()->first();
			
			$rate = 0;
            $rate_type = 'percent';
			if ( $discount ) {
				$rate = $discount->discount;
				$rate_type = $discount->discount_type;
			}
            if ( $rate > 0 ) {
                if ( 'before_tax' === $discount_format ) {
                    if ( 'percent' === $rate_type ) {
                        $cart_discount = ( $products_amount * $rate) / 100;
                    } else {
                        $cart_discount = $rate;
                    }                    
                } else {
                    $new_amount = $products_amount + $total_tax;
                    if ( 'percent' === $rate_type ) {
                        $cart_discount = ( $new_amount * $rate) / 100;
                    } else {
                        $cart_discount = $rate;
                    }
                }
            } 
        }
        $products_details['cart_tax'] = $cart_tax;
        $products_details['cart_discount'] = $cart_discount;
        $amount_payable = $grand_total + $cart_tax - $cart_discount;
        $products_details['amount_payable'] = $amount_payable;
		
        // if there are transactions for this customer. Currency selection may disable, so we need to get it from customer profile.
        $currency_id = $request->currency_id;
        if ( empty( $currency_id ) ) {
            $currency_id = getDefaultCurrency( 'id', $request->customer_id );
        }
        
        $addtional = array(
            'products' => json_encode( $products_details ),
            'amount' => $amount_payable,
            'currency_id' => $currency_id,
        );

        $invoice_no = $request->invoice_no;
        if ( empty( $invoice_no ) ) {
            $invoice_no = getNextNumber('PO');
        }
        $addtional['invoice_no'] = $invoice_no;

        $addtional['slug'] = md5(microtime() . rand());

       

        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

        $date_set = getCurrentDateFormat();

        $additional = array(           
            'order_date' => ! empty( $request->order_date ) ? Carbon::createFromFormat($date_set, $request->order_date)->format('Y-m-d') : NULL,
            'order_due_date' => ! empty( $request->order_due_date ) ? Carbon::createFromFormat($date_set, $request->order_due_date)->format('Y-m-d') : NULL,
        );
        $additional['invoice_number_format'] = getSetting( 'po-number-format', 'purchase-orders-settings', 'numberbased' );
        $additional['invoice_number_separator'] = getSetting( 'po-number-separator', 'purchase-orders-settings', '-' );
        $additional['invoice_number_length'] = getSetting( 'po-number-length', 'purchase-orders-settings', '0' ); 

        $request->request->add( $additional );

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $invoice = PurchaseOrder::create($request->all());

        $products_sync = ! empty( $products_details['products_sync'] ) ? $products_details['products_sync'] : array();
        $invoice->purchase_order_products()->sync( $products_sync );

        $this->insertHistory( array('id' => $invoice->id, 'comments' => 'purchase-order-created', 'operation_type' => 'crud' ) );

        $id = $invoice->id;

        if ( 'Yes' === $request->update_stock && ! empty( $products_sync ) ) {
            foreach ($products_sync as $item) {
                digiUpdateProduct( $item['product_id'], $item['product_qty'], 'inc' );
            }
            $invoice->update_stock = 'Yes';
            $invoice->save(); 
        }

        flashMessage();
        if ( ! empty( $request->btnsavemanage ) ) {
            return redirect( 'admin/purchase_orders/' . $id );
        } else {
            return redirect()->route('admin.purchase_orders.index');
        }
    }


    /**
     * Show the form for editing PurchaseOrder.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('purchase_order_edit')) {
            return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::whereHas("contact_type",
            function ($query) {
                $query->where('id', SUPPLIERS_TYPE);
            })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $warehouses = \App\Warehouse::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = PurchaseOrder::$enum_status;
		
		$enum_discounts_format = \App\Invoice::$enum_discounts_format;
        $enum_tax_format = \App\Invoice::$enum_tax_format;
            
        $invoice = PurchaseOrder::findOrFail($id);

        return view('admin.purchase_orders.edit', compact('invoice', 'enum_status', 'customers', 'currencies', 'warehouses', 'taxes', 'discounts', 'enum_discounts_format', 'enum_tax_format'));
    }

    /**
     * Update PurchaseOrder in storage.
     *
     * @param  \App\Http\Requests\UpdatePurchaseOrdersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePurchaseOrdersRequest $request, $id)
    {
        if (! Gate::allows('purchase_order_edit')) {
            return prepareBlockUserMessage();
        }
        $invoice = PurchaseOrder::findOrFail($id);

        $old_products_details = $invoice->products;

        $products_details = getProductDetails( $request );
     
        $tax_format = $request->tax_format;
        $discount_format =  $request->discount_format;

        $products_details['discount_format'] = $discount_format;
        $products_details['tax_format'] = $tax_format;
        
        // These are product values.
        $grand_total = ! empty( $products_details['grand_total'] ) ? $products_details['grand_total'] : 0;
        $products_amount = ! empty( $products_details['products_amount'] ) ? $products_details['products_amount'] : 0;
        $total_tax = ! empty( $products_details['total_tax'] ) ? $products_details['total_tax'] : 0;
        $total_discount = ! empty( $products_details['total_discount'] ) ? $products_details['total_discount'] : 0;

        // Calculation of Cart Tax.
        $tax_id = $request->tax_id;
        $cart_tax = 0;    
        if ( $tax_id > 0 ) {
            
            $invoice->setTaxIdAttribute( $tax_id );
            $tax = $invoice->tax()->first();
            
            $rate = $tax->rate;
            $rate_type = $tax->rate_type;
            $products_details['cart_tax_rate'] = $rate;
            $products_details['cart_tax_rate_type'] = $rate_type;

            if ( $rate > 0 ) {
                if ( 'before_tax' === $tax_format ) {
                    if ( 'percent' === $rate_type ) {
                        $cart_tax = ( $products_amount * $rate) / 100;
                    } else {
                        $cart_tax = $rate;
                    }                    
                } else {
                    $new_amount = $products_amount + $total_tax;
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
            $invoice->setDiscountIdAttribute( $discount_id );
            $discount = $invoice->discount()->first();

            $rate = $discount->discount;
            $rate_type = $discount->discount_type;
            $products_details['cart_discount_rate'] = $rate;
            $products_details['cart_discount_rate_type'] = $rate_type;
            if ( $rate > 0 ) {
                if ( 'before_tax' === $discount_format ) {
                    if ( 'percent' === $rate_type ) {
                        $cart_discount = ( $products_amount * $rate) / 100;
                    } else {
                        $cart_discount = $rate;
                    }                    
                } else {
                    $new_amount = $products_amount + $total_tax;
                    if ( 'percent' === $rate_type ) {
                        $cart_discount = ( $new_amount * $rate) / 100;
                    } else {
                        $cart_discount = $rate;
                    }
                }
            } 
        }

        $products_details['cart_tax'] = $cart_tax;
        $products_details['cart_discount'] = $cart_discount;
        $amount_payable = $grand_total + $cart_tax - $cart_discount;
        $products_details['amount_payable'] = $amount_payable;

        // if there are transactions for this customer. Currency selection may disable, so we need to get it from customer profile.
        $currency_id = $request->currency_id;
        if ( empty( $currency_id ) ) {
            $currency_id = getDefaultCurrency( 'id', $request->customer_id );
        }

        $addtional = array(
            'products' => json_encode( $products_details ),
            'amount' => $amount_payable,
            'currency_id' => $currency_id,
        );

        $invoice_no = $request->invoice_no;
        if ( empty( $invoice_no ) ) {
            $invoice_no = getNextNumber('PO');
        }
        
        $addtional['invoice_no'] = $invoice_no;

        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

        $date_set = getCurrentDateFormat();

        $additional = array(           
            'order_date' => ! empty( $request->order_date ) ? Carbon::createFromFormat($date_set, $request->order_date)->format('Y-m-d') : NULL,
            'order_due_date' => ! empty( $request->order_due_date ) ? Carbon::createFromFormat($date_set, $request->order_due_date)->format('Y-m-d') : NULL,
        );    

        $request->request->add( $additional );

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $invoice->update($request->all());

        $products_sync = ! empty( $products_details['products_sync'] ) ? $products_details['products_sync'] : array();
        $invoice->purchase_order_products()->sync( $products_sync );

        $this->insertHistory( array('id' => $invoice->id, 'comments' => 'purchase-order-updated', 'operation_type' => 'crud' ) );     

        if ( 'Yes' === $request->update_stock && ! empty( $products_sync ) ) {
            foreach ($products_sync as $item) {
                digiUpdateProduct( $item['product_id'], $item['product_qty'], 'inc' );
            }
            $invoice->update_stock = 'Yes';
            $invoice->save();
        }

        flashMessage( 'success', 'update' );
        if ( ! empty( $request->btnsavemanage ) ) {
            return redirect( 'admin/purchase_orders/' . $id );
        } else {
            return redirect()->route('admin.purchase_orders.index');
        }
    }


    /**
     * Display PurchaseOrder.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('purchase_order_view')) {
            return prepareBlockUserMessage();
        }
        $invoice = PurchaseOrder::findOrFail($id);

        return view('admin.purchase_orders.show', compact('purchase_order', 'invoice'));
    }


    /**
     * Remove PurchaseOrder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('purchase_order_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice = PurchaseOrder::findOrFail($id);
        $invoice->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.purchase_orders.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

   
  
    /**
     * Delete all selected PurchaseOrder at once.
     *
     * @param Request $request
     */

    public function massDestroy(Request $request)
    {
        if (! Gate::allows('purchase_order_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = PurchaseOrder::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore PurchaseOrder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('purchase_order_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice = PurchaseOrder::onlyTrashed()->findOrFail($id);
        $invoice->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete PurchaseOrder from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('purchase_order_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice = PurchaseOrder::onlyTrashed()->findOrFail($id);
        $invoice->forceDelete();

        return back();
    }

    // Mailing functions
    public function mailInvoice() {
            if (request()->ajax()) {
                $action = request('action');
                $id = request('invoice_id');

                $invoice = PurchaseOrder::findOrFail($id);
                $customer = $invoice->customer()->first();

                $sub = substr($action, -3);
                $template = '';
                
                if ( 'sms' === $sub ) {
                    $action = substr($action, 0, -4);
                    $template = \Modules\Smstemplates\Entities\Smstemplate::where('key', '=', $action)->first();
                } elseif( 'ema' === $sub ) {
                    $action = substr($action, 0, -4);
                    $template = \Modules\Templates\Entities\Template::where('key', '=', $action)->first();

                    $file_name = $id . '_' . $invoice->invoice_no . '.pdf';                
                    PDF::loadView('admin.purchase_orders.invoice.invoice-content', compact('invoice'))->save(  public_path() . '/uploads/purchase_orders/' . $file_name, true );
                }              
                if ( 'sms' === $sub ) {
                    return view( 'admin.purchase_orders.sms.sms-form', compact('invoice', 'customer', 'template', 'action', 'sub'));
                } elseif( 'ema' === $sub ) {
                    return view( 'admin.purchase_orders.mail.mail-form', compact('invoice', 'customer', 'template', 'action', 'sub'));
                } elseif( 'pay' === $sub ) {
                    $accounts = \App\Account::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                    $payment_gateways = \App\Settings::where('moduletype', 'payment')->where('status', '=', 'Active')->get()->pluck('module', 'key')->prepend(trans('global.app_please_select'), '');
                    $categories = \App\ExpenseCategory::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

                    return view( 'admin.purchase_orders.invoice.payment-form', compact('invoice', 'customer', 'template', 'action', 'sub', 'accounts', 'payment_gateways', 'categories'));
                }
            }
    }
    
    public function invoiceSend( Request $request ) {
        if (request()->ajax()) {
            $action = request('action');
            
            $post = request('data');
            $sub = $post['sub'];
           
            $id = $post['invoice_id'];

            $response = array('status' => 'danger', 'message' => trans('custom.messages.somethiswentwrong') );

            if ( 'sms' === $sub ) {
                $tonumber  = $post['tonumber'];
                $toname  = $post['toname'];
                $message  = $post['message'];
                $rules = [
                    'tonumber' => 'required|numeric|min:12',
                    'toname' => 'required',
                    'message' => 'required',
                ];
                $messages = [
                    'tonumber.required' => trans('custom.invoices.messages.tonumber'),
                    'tonumber.numeric' => trans('custom.messages.numeric-only'),
                    'toname.required' => trans('custom.invoices.messages.toname'),
                 
                ];
                $additional = [
                    'tonumber' => $tonumber,
                    'toname' => $toname,
                    'message' => $message,
                ];
                $validator = Validator::make(array_merge($request->all(), $additional ), $rules, $messages);
                if ( ! $validator->passes()) {
                    return response()->json(['status' => 'danger', 'error'=>$validator->errors()->all()]);
                }
            } else if ( 'ema' === $sub ) {
                $toemail  = $post['toemail'];
                $toname  = $post['toname'];
                $ccemail  = $post['ccemail'];

                $bccemail  = $post['bccemail'];
                $subject  = $post['subject'];
                $message  = $post['message'];
                $rules = [
                    'toemail' => 'required|email',
                    'toname' => 'required',
                    'ccemail' => 'nullable|email',
                    'bccemail' => 'nullable|email',
                    'subject' => 'required',
                    'message' => 'required',
                ];
                $additional = [
                    'toemail' => $toemail,
                    'toname' => $toname,
                    'ccemail' => $ccemail,

                    'bccemail' => $bccemail,
                    'subject' => $subject,
                    'message' => $message,
                ];
                $validator = Validator::make(array_merge($request->all(), $additional ), $rules);
                if ( ! $validator->passes()) {
                    return response()->json(['status' => 'danger', 'error'=>$validator->errors()->all()]);
                }
            }

            $invoice = PurchaseOrder::findOrFail($id);
            $customer = $invoice->customer()->first();

            $data = array();

            $toname = ! empty( $post['bcc_admin'] ) ? $post['toname'] : '';
            if ( ! empty( $toname ) ) {
                $data['client_name'] = $toname;
            } else {
                $data['client_name'] = $customer->first_name . ' ' . $customer->last_name;
            }

            $toemail = ! empty( $post['bcc_admin'] ) ? $post['toemail'] : '';
            if ( ! empty( $toemail ) ) {
                $data['to_email'] = $toemail;
            } else {
                $data['to_email'] = $customer->email;
            }


            $data['attachments'] = array();
            if ( ! empty( $post['attach_pdf'] ) && 'Yes' === $post['attach_pdf'] ) {
                $file = public_path() . '/uploads/purchase_orders/' . $invoice->id . '_' . $invoice->invoice_no . '.pdf';
                if ( file_exists( $file ) ) {
                    $data['attachments'][] = $file;
                }
            }

            $data['ccemail'] = ! empty( $post['bcc_admin'] ) ? $post['ccemail'] : '';
            $data['bccemail'] = ! empty( $post['bcc_admin'] ) ? $post['bccemail'] : '';
            $data['bcc_admin'] = ! empty( $post['bcc_admin'] ) ? $post['bcc_admin'] : '';

            $admin_email = getSetting('contact_email', 'site_settings');
            if ( ! empty($data['bcc_admin']) && $data['bcc_admin'] == 'yes' && ! empty( $admin_email )) {
                if ( ! empty($data['bccemail'])) {
                    $data['bccemail'] = $data['bccemail'] . ',' . $admin_email;
                } else {
                    $data['bccemail'] = $admin_email;
                }
            }

            $data['content'] = $post['message'];

            $data['site_title'] = getSetting( 'site_title', 'site_settings');
            $logo = getSetting( 'site_logo', 'site_settings' );
            $data['logo'] = asset( 'uploads/settings/' . $logo );
            $data['date'] = digiTodayDateAdd();
            $data['invoice_url'] = route( 'admin.quotes.preview', [ 'slug' => $invoice->slug ] );
            $data['invoice_no'] = $invoice->invoicenumberdisplay;
            $data['amount'] = digiCurrency($invoice->amount,$invoice->currency_id);
            $data['products'] = productshtml($invoice->id, 'purchaseorder');
            $data['order_created_date'] = digiDate( $invoice->created_at );
            $data['order_date'] = digiDate( $invoice->order_date );
            $data['order_due_date'] = digiDate( $invoice->order_due_date );
            $data['site_address'] = getSetting( 'site_address', 'site_settings');
            $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
            $data['site_email'] = getSetting( 'contact_email', 'site_settings');
            $data['address'] = $invoice->address;
            $data['track_link'] = '';

            $response['status'] = 'success';
            $response['message'] = trans('custom.messages.mailsent');
            $operation_type = 'email';
            if ( 'sms' === $sub ) {
                $operation_type = 'sms';
                $data['tonumber']  = $post['tonumber'];
                if ( ! empty( $customer->phone1_code ) ) {
                  
                }

                $res = sendSms( $action, $data );
                if ( ! empty( $res['status'] ) && 'failed' === $res['status'] ) {
                    $response['status'] = 'danger';
                    $response['message'] = $res['message'];
                    $action .= '-failed';
                } else {
                    $response['message'] = trans('custom.messages.smssent');
                    
                }
            } elseif( 'ema' === $sub ) {
                $res = sendEmail( $action, $data );
            }

            $this->insertHistory( array('id' => $id, 'comments' => $action, 'operation_type' => $operation_type ) );
    
            flashMessage( 'success', 'restore', $response['message']);
            return json_encode( $response );
        }
    }

    private function insertHistory( $data ) {
        $ip_address = GetIP();
        $position = Location::get( $ip_address );

        $id = ! empty( $data['id'] ) ? $data['id'] : 0;
        $comments = ! empty( $data['comments'] ) ? $data['comments'] : 0;
        $operation_type = ! empty( $data['operation_type'] ) ? $data['operation_type'] : 'general';

         $city = ! empty( $position->cityName ) ? $position->cityName : '';
        if ( ! empty( $position->regionName ) ) {
            $city .= ' ' . $position->regionName;
        }
        if ( ! empty( $position->zipCode ) ) {
            $city .= ' ' . $position->zipCode;
        }

        $log = array(
            'ip_address' => $ip_address,
            'country' => ! empty( $position->countryName ) ? $position->countryName : '',
            'city' => $city,
            'browser' => $_SERVER['HTTP_USER_AGENT'],
            'purchase_order_id' => $id,
            'comments' => $comments,
            'operation_type' => $operation_type,
        );
        \App\PurchaseOrderHistory::create( $log );
    }

    public function savePayment() {
        if (request()->ajax()) {
            $post = request('data');
            $sub = $post['sub'];
          
            $id = $post['invoice_id'];

            $response = array('status' => 'danger', 'message' => trans('custom.messages.somethiswentwrong') );

             $rules = [
                    'date' => 'required',
                    'amount' => 'required|numeric|min:0.01',
                    'category' => 'required|exists:expense_categories,id',
                    'paymethod' => 'required',
                ];
            
            if ( isPluginActive('account') ) {
                $rules['account'] = 'required|exists:accounts,id';
                
                if ( ! empty( $post['account'] ) ) {
                    $account = \App\Account::find( $post['account'] );
                    if ( $account ) {
                        $rules['amount'] = 'required|numeric|lte:' . $account->initial_balance;
                    }
                }
            }
         

             $messages = [
                'account.required' => trans('custom.invoices.messages.account'),
                'account.exists' => trans('custom.invoices.messages.account-exists'),

                'category.required' => trans('custom.invoices.messages.category'),
                'category.exists' => trans('custom.invoices.messages.category-exists'),

                'date.required' => trans('custom.invoices.messages.date'),
                'amount.required' => trans('custom.invoices.messages.amount'),
                'amount.numeric' => trans('custom.messages.numeric-only'),
                'amount.min' => trans('custom.invoices.messages.amount-positive-number'),
                'paymethod.required' => trans('custom.invoices.messages.paymethod'),                
            ];

            $validator = Validator::make($post, $rules, $messages);

            if ( ! $validator->passes()) {
                return response()->json(['status' => 'danger', 'error'=>$validator->errors()->all()]);
            }

            $data = array();

            $data['date'] = $post['date'];
            $data['amount'] = $post['amount'];
            $data['transaction_id'] = $post['transaction_id'];
            $data['account_id'] = ! empty( $post['account'] ) ? $post['account'] : null;
            $data['purchase_order_id'] = $id;
            $data['paymentmethod'] = $post['paymethod'];
            $data['description'] = $post['description'];
            $data['payment_status'] = 'Success';

            $record = \App\PurchaseOrderPayment::create( $data );

            $invoice = PurchaseOrder::find($id);
            $total_paid = \App\PurchaseOrderPayment::where('purchase_order_id', $id)->where('payment_status', 'Success')->sum('amount');
            if( $total_paid >= $invoice->amount ) {
                $invoice->paymentstatus = 'paid';
                $invoice->save();
            }else if( $total_paid < $invoice->amount ){
                $invoice->paymentstatus = 'partial';
                $invoice->save();
            }

            //PO payment  
            $add_to_expense_po = getSetting( 'add-to-expense-purchase-order', 'purchase-orders-settings', 'No' );

             $account_details = '';
            if ( ! empty( $data['account_id'] ) ) {
                $account_details = \App\Account::find( $data['account_id'] );
            }
            if ( ! $account_details ) {
                $account_id = getSetting('default-account', 'purchase-orders-settings', 0);
                $account_details = \App\Account::find( $account_id ); 
            }

            $amount = $data['amount'];
            if ( ! empty( $account_details ) && 'Yes' === $add_to_expense_po ){
               
                // As this is the Purchase Order payment, so it was Expense, lets add it in expense.
                $pay_method = \App\PaymentGateway::where('key', '=', $post['paymethod'])->first();
                $pay_method_id = null;
                if ( $pay_method ) {
                    $pay_method_id = $pay_method->id;
                }
                $expense = array(
                    'name' => trans('custom.invoices.payment-for-po') . $invoice->invoice_no,
                    'slug' => md5(microtime() . rand()),
                    'entry_date' => Carbon::createFromFormat(config('app.date_format'), $post['date'])->format('Y-m-d'),
                    'amount' => $amount, // Let is save amount in  currency.
                    'currency_id' => $invoice->currency_id,
                    'description' => $data['description'],
                    'ref_no' => $data['transaction_id'],
                    'account_id' => ( $account_details ) ? $account_details->id : null,
                    'payee_id' => $invoice->customer_id,
                    'pay_method_id' => $pay_method_id,
                    'expense_category_id' => $post['category'],
                    'credit_notes_id' => $invoice->id,
                    'payment_method_id' => $pay_method_id,
                );
                \App\Expense::create( $expense );

                // Let us deduct the amount from the account. This amount is in base currency.
              
               //Let us convert amount to base currency  
                $basecurrency = \App\Currency::where('is_default', 'yes')->first();                
                if ( $invoice && $basecurrency && ! empty( $invoice->currency_id ) ){
                    $amount = ( $amount / $invoice->currency->rate ) * $basecurrency->rate;
                }

                // Let us add thhis account to the specified account.
                /**
                
                 */

                digiUpdateAccount( $data['account_id'], $amount, 'desc' );

            }


            $this->insertHistory( array('id' => $id, 'comments' => trans('custom.purchase_orders.payment-inserted'), 'operation_type' => 'payment' ) );

            $response['status'] = 'success';
            $response['message'] = trans('custom.invoices.messages.save-success');

            flashMessage( 'success', 'restore', $response['message']);
            return json_encode( $response );
        }
    }

    public function changeStatus( $id, $status ) {
        if (! Gate::allows('invoice_edit')) {
            return prepareBlockUserMessage();
        }

        $invoice = PurchaseOrder::findOrFail($id);
        $invoice->paymentstatus = $status;
        $invoice->save();

        $this->insertHistory( array('id' => $id, 'comments' => trans('custom.purchase_orders.status-changed-' . $status) ) );

        flashMessage( 'success', 'status' );

        return redirect()->route('admin.purchase_orders.show', $id);
    }

    public function showPreview( $slug ) {
        if (! Gate::allows('invoice_edit')) {
            return prepareBlockUserMessage();
        }

        $invoice = PurchaseOrder::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $topbar = 'no';
        $sidebar = 'no';
        return view( 'admin.purchase_orders.preview', compact('invoice', 'sidebar', 'topbar'));
    }

    public function invoicePDF( $slug, $operation = '') {

        if (! Gate::allows('invoice_edit')) {
            return prepareBlockUserMessage();
        }

        $invoice = PurchaseOrder::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }



        $file_name = $invoice->id . '_' . $invoice->invoice_no . '.pdf';
        $path = public_path() . '/uploads/purchase_orders/' . $file_name;
        PDF::loadView('admin.purchase_orders.invoice.invoice-content', compact('invoice'))->save( $path , true );
        
        if ( 'view' === $operation ) {
            return response()->file($path);
        } elseif ( 'print' === $operation ) {
            \Debugbar::disable();
            return view('admin.purchase_orders.invoice.invoice-print', compact('invoice'));
        } else {
            return response()->download($path);
        }
    }

    public function uploadDocuments( $slug ) {
        if (! Gate::allows('invoice_edit')) {
            return prepareBlockUserMessage();
        }

        $invoice = PurchaseOrder::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        return view( 'admin.purchase_orders.invoice.uploads', compact('invoice'));
    }

    public function upload( UploadPurchaseOrdersRequest $request, $slug ) {
        if (! Gate::allows('invoice_edit')) {
            return prepareBlockUserMessage();
        }

        $invoice = PurchaseOrder::where('slug', '=', $slug)->first();
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $media = [];
        foreach ($request->input('attachments_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $invoice->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $invoice->updateMedia($media, 'attachments');

        $this->insertHistory( array('id' => $invoice->id, 'comments' => trans('custom.purchase_orders.documents-uploaded') ) );

        flashMessage( 'success', 'create', trans('custom.purchase_orders.upload-success'));
        return redirect()->route('admin.purchase_orders.show', [$invoice->id]);
    }

    public function duplicate( $slug ) {
        if (! Gate::allows('quote_edit')) {
            return prepareBlockUserMessage();
        }

        $invoice = PurchaseOrder::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $newinvoice = $invoice->replicate();

        $invoice_no = getNextNumber('PO');
        $newinvoice->invoice_no = $invoice_no;
        $newinvoice->paymentstatus = 'unpaid';
        $newinvoice->save();

        $products_sync = \App\PurchaseOrder::select(['pop.*'])
        ->join('purchase_order_products as pop', 'pop.purchase_order_id', '=', 'purchase_orders.id')
        ->join('products', 'products.id', '=', 'pop.product_id')
        ->where('purchase_orders.id', $invoice->id)->get()->makeHidden(['purchase_order_id'])->toArray();
        $newinvoice->purchase_order_products()->sync( $products_sync );

        $this->insertHistory( array('id' => $invoice->id, 'comments' => 'purchase-order-created', 'operation_type' => 'duplicated' ) );

        flashMessage( 'success', 'create', trans('custom.purchase_orders.purchase-order-duplicated'));
        return redirect()->route('admin.purchase_orders.show', [$newinvoice->id]);
    }


     public function refreshStats() {
        if (request()->ajax()) {
            $currency = request('currency');

            return view('admin.purchase_orders.canvas.canvas-panel-body', ['currency_id' => $currency]);
        }
    }
}
