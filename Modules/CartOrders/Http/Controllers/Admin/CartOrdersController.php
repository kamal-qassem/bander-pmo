<?php

namespace Modules\CartOrders\Http\Controllers\Admin;

use Modules\CartOrders\Entities\CartOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\CartOrders\Http\Requests\Admin\StoreCartOrdersRequest;
use Modules\CartOrders\Http\Requests\Admin\UpdateCartOrdersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class CartOrdersController extends Controller
{   
    public function __construct() {
     $this->middleware('plugin:order');
    }
    /**
     * Display a listing of Order.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('order_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = CartOrder::query();
            $query->with("customer");
            $query->with("billing_cycle");
            if ( isCustomer() ) {
                $query->where( 'customer_id', '=', getContactId());
            }
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
                if (! Gate::allows('order_delete')) {
                    flashMessage('danger', 'not_allowed');
                    return back();
                }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'orders.id',
                'orders.customer_id',
                'orders.status',
                'orders.price',
                'orders.billing_cycle_id',
                'orders.slug',
            ]);
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'order_';
                $routeKey = 'admin.orders';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('customer.first_name', function ($row) {
                return $row->customer ? $row->customer->first_name : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? digiCurrency( $row->price ) : '';
            });
            $table->editColumn('billing_cycle.title', function ($row) {
                return $row->billing_cycle ? $row->billing_cycle->title : trans('orders::global.orders.onetime');
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('orders::admin.orders.index');
    }

    /**
     * Show the form for creating new Order.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('order_create')) {
            return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::get()->pluck('first_name', 'id')->prepend(trans('global.app_please_select'), '');
        $billing_cycles = \App\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('orders::global.orders.onetime'), '');
        $enum_status = CartOrder::$enum_status;
    
        if ( isCustomer() ) {
            return view('orders::admin.orders.cart', compact('enum_status', 'customers', 'billing_cycles'));
        } else {
            return view('orders::admin.orders.create', compact('enum_status', 'customers', 'billing_cycles'));
        }
    }

    /**
     * Store a newly created Order in storage.
     *
     * @param  \App\Http\Requests\StoreOrdersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCartOrdersRequest $request)
    {
        if (! Gate::allows('order_create')) {
            return prepareBlockUserMessage();
        }

        $products_details = getProductDetails( $request );
        
        // These are product values.
        $grand_total = ! empty( $products_details['grand_total'] ) ? $products_details['grand_total'] : 0;
        $products_amount = ! empty( $products_details['products_amount'] ) ? $products_details['products_amount'] : 0;
        $total_tax = ! empty( $products_details['total_tax'] ) ? $products_details['total_tax'] : 0;
        $total_discount = ! empty( $products_details['total_discount'] ) ? $products_details['total_discount'] : 0;


        $cart_tax = $cart_discount = 0;
        $products_details['cart_tax'] = $cart_tax;
        $products_details['cart_discount'] = $cart_discount;
        $amount_payable = $grand_total + $cart_tax - $cart_discount;
        $products_details['amount_payable'] = $amount_payable;

        $addtional = array(
            'products' => json_encode( $products_details ),
            'price' => $amount_payable,
        );

        $addtional['slug'] = md5(microtime() . rand());

        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

        $order = CartOrder::create($request->all());

        flashMessage( 'success', 'create', trans('orders::global.orders.created'));

        return redirect()->route('admin.orders.index');
    }


    /**
     * Show the form for editing Order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('order_edit')) {
            return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::get()->pluck('first_name', 'id')->prepend(trans('global.app_please_select'), '');
        $billing_cycles = \App\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = Order::$enum_status;
        
        if ( is_numeric( $id ) ) {
            $order = CartOrder::findOrFail($id);
        } else {
            $order = CartOrder::where('slug', '=', $id)->firstOrFail($id);
        }

        return view('orders::admin.orders.edit', compact('order', 'enum_status', 'customers', 'billing_cycles'));
    }

    /**
     * Update Order in storage.
     *
     * @param  \App\Http\Requests\UpdateOrdersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrdersRequest $request, $id)
    {
        if (! Gate::allows('order_edit')) {
            return prepareBlockUserMessage();
        }
        if ( is_numeric( $id ) ) {
            $order = CartOrder::findOrFail($id);
        } else {
            $order = CartOrder::where('slug', '=', $id)->firstOrFail($id);
        }

        $products_details = getProductDetails( $request );
        
        // These are product values.
        $grand_total = ! empty( $products_details['grand_total'] ) ? $products_details['grand_total'] : 0;
        $products_amount = ! empty( $products_details['products_amount'] ) ? $products_details['products_amount'] : 0;
        $total_tax = ! empty( $products_details['total_tax'] ) ? $products_details['total_tax'] : 0;
        $total_discount = ! empty( $products_details['total_discount'] ) ? $products_details['total_discount'] : 0;


        $cart_tax = $cart_discount = 0;
        $products_details['cart_tax'] = $cart_tax;
        $products_details['cart_discount'] = $cart_discount;
        $amount_payable = $grand_total + $cart_tax - $cart_discount;
        $products_details['amount_payable'] = $amount_payable;

        $addtional = array(
            'products' => json_encode( $products_details ),
            'price' => $amount_payable,
        );

       
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

        $order->update($request->all());

        flashMessage( 'success', 'update', trans('orders::global.orders.updated'));

        return redirect()->route('admin.orders.index');
    }


    /**
     * Display Order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('order_view')) {
            return prepareBlockUserMessage();
        }

        if ( is_numeric( $id ) ) {
            $order = CartOrder::findOrFail($id);
        } else {
             $order = CartOrder::where('slug', '=', $id)->firstOrFail();
        }

        return view('orders::admin.orders.show', compact('order'));
    }


    /**
     * Remove Order from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('order_delete')) {
            return prepareBlockUserMessage();
        }
        if ( is_numeric( $id ) ) {
            $order = CartOrder::findOrFail($id);
        } else {
            $order = CartOrder::where('slug', '=', $id)->firstOrFail();
        }

        $order->delete();

        flashMessage( 'success', 'delete');

        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
    }

    /**
     * Delete all selected Order at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('order_delete')) {
            return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = CartOrder::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Order from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('order_delete')) {
            return prepareBlockUserMessage();
        }
        
        if ( is_numeric( $id ) ) {
            $order = CartOrder::onlyTrashed()->findOrFail($id);
        } else {
            $order = CartOrder::where('slug', '=', $id)->onlyTrashed()->firstOrFail();
        }
        $order->restore();

        flashMessage( 'success', 'restore' );

        return back();
    }

    /**
     * Permanently delete Order from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('order_delete')) {
            return prepareBlockUserMessage();
        }
        
        if ( is_numeric( $id ) ) {
            $order = CartOrder::onlyTrashed()->findOrFail($id);
        } else {
            $order = CartOrder::where('slug', '=', $id)->onlyTrashed()->firstOrFail();
        }

        $order->forceDelete();

        flashMessage( 'success', 'delete' );

        return back();
    }

    public function cancelOrder( $id ) {
        if (! Gate::allows('order_cancel')) {
            return prepareBlockUserMessage();
        }
        
        if ( is_numeric( $id ) ) {
            $order = CartOrder::findOrFail($id);
        } else {
            $order = CartOrder::where('slug', '=', $id)->firstOrFail();
        }

        $order->update( array('status' => 'Cancelled') );

        flashMessage( 'success', 'update' );

        return redirect()->route('admin.orders.index');
    }

    public function searchProduct( Request $request ) {
        if (request()->ajax()) {
            $product = \App\Product::findOrFail( $request->product_id );
            return view('orders::admin.orders.product-details', compact('product'));
        }
    }

    public function addToCart( Request $request ) {
        if ( request()->ajax() ) {
            $product_id = $request->product_id;
            $quantity = $request->quantity;
        }
    }
}
