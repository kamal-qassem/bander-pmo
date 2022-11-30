<?php

namespace Modules\Orders\Http\Controllers\Admin;

use Modules\Orders\Entities\Order;
use Modules\Orders\Entities\OrdersPayments;
use Modules\Orders\Entities\OrdersProducts;

use Modules\CartOrders\Entities\CartOrder;
use Modules\CartOrders\Entities\CartOrdersProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Orders\Http\Requests\Admin\StoreOrdersRequest;
use Modules\Orders\Http\Requests\Admin\UpdateOrdersRequest;
use Yajra\DataTables\DataTables;
use App\Http\Requests\Admin\UpdatePaynowRequest;
use App\Paypal;
use Tzsk\Payu\Facade\Payment;
use Cartalyst\Stripe\Stripe;

use Illuminate\Support\Facades\DB;
use Carbon;
use Illuminate\Support\Facades\Auth;
use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Notification;
use Validator;
class OrdersController extends Controller
{
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
            $query = Order::query();
            $query->with("customer");
            $query->with("billing_cycle");
            if ( isCustomer() ) {
                $query->where( 'customer_id', '=', getContactId());
            }
          
            $template = 'actionsTemplate';
            if ( request('show_deleted') == 1 ) {                
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
                'orders.created_at',
                'orders.currency_id',
            ]);

            // Custom Filters Start.
            $query->when(request('status', false), function ($q, $status) { 
                return $q->where('status', $status);
            });
            $query->when(request('customer', false), function ($q, $customer) { 
                return $q->where('customer_id', $customer);
            });
               $query->when(request('currency_id', false), function ($q, $currency_id) { 
                return $q->where('currency_id', $currency_id);
            });
            /// Custom Filters End.

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
            $table->editColumn('id', function ($row) {
                $id = $row->id ? '<a href="'.route('admin.orders.show', $row->id).'">' . $row->id . '</a>' : '';
                $id .= '<p>'.digiDate( $row->created_at, true ).'</p>';
                return $id;
            });
            $table->editColumn('customer.first_name', function ($row) {
                $name = $row->customer->name;
                if ( empty( $name ) ) {
                    $name = $row->customer->first_name;
                    if ( $row->customer->last_name ) {
                        $name .= ' ' . $row->customer->last_name;
                    }
                }
                if( isCustomer() ){
                    return $row->customer ? $row->customer->name : '';
                }else{

                  return $row->customer ? '<a href="'.route('admin.contacts.show', [$row->customer_id]).'">' . $name . '</a>' : '';      
                }
            });
            $table->editColumn('status', function ($row) {
                $title =$row->status;
                $class = 'danger';
                if ( 'Active' === $title ) {
                    $title = 'Success';
                    $class = 'success';
                }
                $status = $row->status ? '<span class="label label-'.$class.' label-many">'.$title.'</span>' : '';
                return $status;
            });
            $table->editColumn('price', function ($row) {
                $currency_id = getDefaultCurrency('id');
                if ( ! empty( $row->currency_id ) ) {
                    $currency_id = $row->currency_id;
                }
                return $row->price ? digiCurrency( $row->price, $currency_id ) : '';
            });
            $table->editColumn('billing_cycle.title', function ($row) {
                return $row->billing_cycle_id ? $row->billing_cycle->title : trans('orders::global.orders.onetime');
            });

            $table->rawColumns(['actions','massDelete', 'id', 'customer.first_name', 'status']);

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
        
        $customers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $billing_cycles = \App\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('orders::global.orders.onetime'), '');
        $enum_status = Order::$enum_status;
        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
    
        if ( isCustomer() ) {
            return view('orders::admin.orders.cart', compact('enum_status', 'customers', 'billing_cycles'));
        } else {
            return view('orders::admin.orders.create', compact('enum_status', 'customers', 'billing_cycles', 'currencies'));
        }
    }

    /**
     * Store a newly created Order in storage.
     *
     * @param  \App\Http\Requests\StoreOrdersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrdersRequest $request)
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

        $customer_details = \App\Contact::find( $request->customer_id );
        $currency_id = getDefaultCurrency('id');
        if ( $customer_details && ! empty( $customer_details->currency_id ) ) {
            $currency_id = $customer_details->currency_id;
        }
        $addtional = array(
            'products' => json_encode( $products_details ),
            'price' => $amount_payable,
            'currency_id' => $currency_id,
            'invoice_date' => date('Y-m-d'), // Order created date.
        );
        $addtional['slug'] = md5(microtime() . rand());

        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $order = Order::create($request->all());

        $products_sync = ! empty( $products_details['products_sync'] ) ? $products_details['products_sync'] : [];
        if ( count( $products_sync ) > 0 ) {
            $order->order_products()->sync( $products_sync );

            if ( ( 'yes' === $request->update_stock ) && ! empty( $products_sync ) ) {
                foreach ($products_sync as $item) {
                    digiUpdateProduct( $item['product_id'], $item['product_qty'], 'desc' );
                }
                $order->stock_updated = 'yes';
                $order->save();
            }
        }

        $customer = $order->customer()->first();
        $delivery_address = '';
        if ( $customer ) {
            $delivery_address_raw = ( $customer->delivery_address ) ? json_decode( $customer->delivery_address, true ) : array();
            
            $delivery_address = '';
            if ( ! empty( $delivery_address_raw['first_name'] ) ) {
                $delivery_address .= $delivery_address_raw['first_name'];
                
                if ( ! empty( $delivery_address_raw['last_name'] ) ) {
                    $delivery_address .= ' ' . $delivery_address_raw['last_name'];
                }
            } else {
                $delivery_address .= $customer->first_name;
            }
            
            if ( ! empty( $delivery_address_raw['address'] ) ) {
                $delivery_address .= "\n" . $delivery_address_raw['address'];
            } else {
                $delivery_address .= "\n" . $customer->address;
            }
            
            if ( ! empty( $delivery_address_raw['city'] ) ) {
                $delivery_address .= "\n" . $delivery_address_raw['city'];
            } else {
                $delivery_address .= "\n" . $customer->city;
            }
            
            if ( ! empty( $delivery_address_raw['state_region'] ) ) {
                $delivery_address .= "\n" . $delivery_address_raw['state_region'];
            } else {
                $delivery_address .= "\n" . $customer->state_region;
            }
            
            if ( ! empty( $delivery_address_raw['country'] ) ) {
                $country = \App\Country::find( $delivery_address_raw['country'] );
                $country_name = '';
                if ( $country ) {
                    $country_name = $country->title;
                }
                if ( ! empty( $country_name ) ) {
                    $delivery_address .= "\n" . $delivery_address_raw['country'];
                }
            } else {
                $country = \App\Country::find( $customer->country_id );
                $country_name = '';
                if ( $country ) {
                    $country_name = $country->title;
                }
                if ( ! empty( $country_name ) ) {
                    $delivery_address .= "\n" . $customer->country_name;
                }
            }
            if ( ! empty( $delivery_address_raw['zip_postal_code'] ) ) {
                $delivery_address .= '-' . $delivery_address_raw['zip_postal_code'];
            } else {
                $delivery_address .= '-' . $customer->zip_postal_code;
            }
        }

        $order->delivery_address = $delivery_address;

        //start generate invoice
        if ( $customer && 'yes' === $request->generate_invoice ) {
            $paymentstatus = 'unpaid';
            if ( 'Active' === $request->status ) {
                $paymentstatus = 'paid';
            }
            $data = [
                'slug' => md5(microtime() . rand()),
               
                'invoice_no' => getNextNumber(),
                'address' => $customer->fulladdress,
                'invoice_prefix' => getSetting( 'invoice-prefix', 'invoice-settings' ),
                'show_quantity_as' => getSetting( 'show_quantity_as', 'invoice-settings' ),
                'status' => 'Published',
                'invoice_date' => date('Y-m-d'),
                'invoice_due_date' => date('Y-m-d'),
                'customer_id' => $customer->id,
                'currency_id' => $currency_id,
                'amount' => $amount_payable,
                'products' => json_encode( $products_details ),
                'paymentstatus' => $paymentstatus,
                'created_by_id' => Auth::id(),
                'delivery_address' => $delivery_address,
                'terms_conditions' => getSetting('predefined_terms_invoice', 'invoice-settings'),
                'admin_notes' => getSetting('predefined_adminnote_invoice', 'invoice-settings'),
                'invoice_notes' => getSetting('predefined_clientnote_invoice', 'invoice-settings'),
                'order_id' => $order->id,
            ];
            $data['invoice_number_format'] = getSetting( 'invoice-number-format', 'invoice-settings', 'numberbased' );
            $data['invoice_number_separator'] = getSetting( 'invoice-number-separator', 'invoice-settings', '-' );
            $data['invoice_number_length'] = getSetting( 'invoice-number-length', 'invoice-settings', '0' );

            $invoice = \App\Invoice::create( $data );

            if ( 'Active' === $request->status ) {
                $data = array();
                $data['date'] = date('Y-m-d');
                $data['amount'] = $amount_payable;
                $data['transaction_id'] = null;
                $data['account_id'] = getSetting('default-account', 'invoice-settings', PAYMENT_METHOD_OFFLINE);
                $data['invoice_id'] = $invoice->id;
                $data['paymentmethod'] = PAYMENT_METHOD_OFFLINE;
                $data['description'] = trans('custom.invoices.payment-for') . ' #' . $invoice->invoice_no;

                \Modules\InvoicePayments\Entities\InvoicePayment::create( $data );
            }
        }

        if ( 'Active' === $request->status ) {
            $default_orders_account = getSetting('default-account', 'order-settings', '');
            if ( ! empty( $default_orders_account ) ) {
                $details = \App\Account::find( $default_orders_account );
                if ( ! $details ) {
                    $default_orders_account = '';
                }
            }
            $data = array(
                'date' => digiTodayDateDB(),
                'amount' => $amount_payable,
                'transaction_id' => '',
                'account_id' => ( $default_orders_account ) ? $default_orders_account : NULL,
                'order_id' => $order->id,
                'paymentmethod' => PAYMENT_METHOD_OFFLINE,
                'description' => trans('orders::global.orders.payment-description') . $order->id,
                'slug' => md5(microtime() . $order->id . rand()),
                'payment_status' => PAYMENT_STATUS_SUCCESS,
            );
            OrdersPayments::create( $data );
        }
        //end generate Invoice


        //start send email
        if ( $customer && 'yes' === $request->send_email ) {            
            
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
            'client_name' => $customer->name,
            'content' => 'Order has been created',
            'status' => $order->status,
            'amount' => digiCurrency($order->price,$order->currency_id),
            'products' => $order->products,
            'customer_id' => $order->customer_id,
            'billing_cycle_id' => $order->billing_cycle_id,
            'currency_id' => $order->currency_id,

            'track_link' => route('admin.orders.show', $order->id),
            'delivery_address' => $delivery_address,

            'site_address' => getSetting( 'site_address', 'site_settings'),
            'site_phone' => getSetting( 'site_phone', 'site_settings'),
            'site_email' => getSetting( 'contact_email', 'site_settings'),                
            'site_title' => getSetting( 'site_title', 'site_settings'),
            'logo' => asset( 'uploads/settings/' . $logo ),
            'date' => digiTodayDate(),
            'order_created_date' => digiDate($order->created_at),
            'site_url' => url('/'),

            'invoice_no' => $order->id,
            'invoice_url' => route('admin.orders.show', $order->id),
        );

            $order_created_date = $order->created_at;

            if($order_created_date){
                $templatedata['created_at'] = digiDate($order->created_at);
            }

            if ( $order->customer->name ) {
                $templatedata['customer_id'] = $order->customer->name;
            }
            
            if ( $order->currency->name ) {
                $templatedata['currency_id'] = $order->currency->name;
            }

            if ( $order->billing_cycle->title ) {
                $templatedata['billing_cycle_id'] = $order->billing_cycle->title;
            }

            $products = json_decode( $order->products, true );

            if ( ! empty( $products ) ) {
                $templatedata['products'] = view('orders::admin.orders.show-products', array('products_return' => $order));
            }


            
            $data = [
                "action" => "Created",
                "crud_name" => "User",
                'template' => 'order-created',
                'model' => 'Modules\Orders\Entities\Order',
                'data' => $templatedata,
            ];
            
            $customer->notify(new QA_EmailNotification($data));
        }
        //end send email

        //start send sms
        if ( $customer && 'yes' === $request->send_sms ) {
            $logo = getSetting( 'site_logo', 'site_settings' );
            $tonumber = ! empty($customer->phone1) ? $customer->phone1 : '';
            if ( ! empty( $tonumber ) && ! empty( $customer->phone1_code ) ) {
                $tonumber = $customer->phone1_code . $tonumber;
            }
            $smsdata = array(
                'client_name' => $customer->name,
                'content' => 'Order has been created',
                'status' => $order->status,
                'amount' => digiCurrency($order->price),
                'products' => $order->products,
                'customer_id' => $order->customer_id,
                'billing_cycle_id' => $order->billing_cycle_id,
                'tonumber' => $tonumber,

                'track_link' => route('admin.orders.show', $order->id),
                'delivery_address' => '',

                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),                
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'order_created_date' => digiDate($order->created_at),
                'date' => digiTodayDate(),
                'site_url' => url('/'),

                'invoice_no' => $order->id,
                'invoice_url' => route('admin.orders.show', $order->id),
            );

              $order_created_date = $order->created_at;

            if($order_created_date){
                $templatedata['created_at'] = digiDate($order->created_at);
            }
            if ( $order->customer->name ) {
                $templatedata['customer_id'] = $order->customer->name;
            }
            
            if ( $order->billing_cycle->title ) {
                $templatedata['billing_cycle_id'] = $order->billing_cycle->title;
            }
            sendSms( 'order-created', $smsdata );
        }

        //end send sms

        //start add to income
        
        $amount = $order->price;
        $account_id = getSetting('default-account', 'order-settings', 0);
        $account_details = \App\Account::find( $account_id );       

        if ( ! empty( $account_details ) && 'yes' === $request->add_to_income ) {

            $basecurrency = \App\Currency::where('is_default', 'yes')->first();      
            if ( $order && $basecurrency ) {
                $amount = ( $amount / $order->currency->rate ) * $basecurrency->rate;
            }            
            
            if ( $account_details && ! empty( $account_id ) ) {
                // Let us add thhis account to the specified account.               
                \App\Account::find( $account_id )->increment('initial_balance', $amount);
            }                       
            
            // As this is the Invoice payment, so it was Income, lets add it in income.
            $pay_method = getSetting('default_payment_gateway', 'site_settings', PAYMENT_METHOD_OFFLINE);
            $pay_method_id = null;
            if ( $pay_method ) {
                $pay_method_id = \App\PaymentGateway::where('key', '=', $pay_method )->first()->id;
            }
            $income = array(
                'slug' => md5(microtime() . rand()),
                'entry_date' => date('Y-m-d', time()),
                'amount' =>  $amount,
                'original_amount' =>  $amount,
                'original_currency_id' => getDefaultCurrency('id'),
                'description' => trans('others.orders.payment-for') . $order->id,
                'ref_no' => $order->id,
                'account_id' => ( $account_details ) ? $account_id : null,
                'payer_id' => $order->customer_id,
                'pay_method_id' => $pay_method_id,
            );
           
            \App\Income::create( $income );         
        }
        //end add to income


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
        
        $customers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $billing_cycles = \App\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('orders::global.orders.onetime'), '');
        
        $enum_status = Order::$enum_status;
        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        
        if ( is_numeric( $id ) ) {
            $order = Order::findOrFail($id);
        } else {
            $order = Order::where('slug', '=', $id)->firstOrFail($id);
        }

        return view('orders::admin.orders.edit', compact('order', 'enum_status', 'customers', 'billing_cycles', 'currencies'));
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
            $order = Order::findOrFail($id);
        } else {
            $order = Order::where('slug', '=', $id)->firstOrFail($id);
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

        $customer_details = \App\Contact::find( $request->customer_id );
        $currency_id = getDefaultCurrency('id');
        if ( $customer_details && ! empty( $customer_details->currency_id ) ) {
            $currency_id = $customer_details->currency_id;
        }
        $addtional = array(
            'products' => json_encode( $products_details ),
            'price' => $amount_payable,
            'currency_id' => $currency_id,
        );

       

        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $order->update($request->all());

        $products_sync = ! empty( $products_details['products_sync'] ) ? $products_details['products_sync'] : [];
        $order->order_products()->sync( $products_sync );
        
        if ( ( 'yes' === $request->update_stock ) && ! empty( $products_sync ) ) {
            foreach ($products_sync as $item) {
                digiUpdateProduct( $item['product_id'], $item['product_qty'], 'desc' );
            }
            $order->stock_updated = 'yes';
            $order->save();
        }

        $customer = $order->customer()->first();
        if ( $customer && 'yes' === $request->send_email ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'client_name' => $customer->name,
                'content' => trans( 'custom.orders.order-created' ),
                'status' => $order->status,
                'amount' => digiCurrency($order->price,$order->currency_id),
                'products' => $order->products,
                'customer_id' => $order->customer_id,
                'billing_cycle_id' => $order->billing_cycle_id,

                'track_link' => '',
                'delivery_address' => '',

                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),                
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'order_created_date' => digiDate($order->created_at),
                'site_url' => url('/'),
            );

            $order_created_date = $order->created_at;

            if($order_created_date){
                $templatedata['created_at'] = digiDate($order->created_at);
            }

            if ( $order->customer->name ) {
                $templatedata['customer_id'] = $order->customer->name;
            }
            
            if ( $order->billing_cycle->title ) {
                $templatedata['billing_cycle_id'] = $order->billing_cycle->title;
            }

            $products = json_decode( $order->products, true );

            if ( ! empty( $products ) ) {
                $templatedata['products'] = view('orders::admin.orders.show-products', array('products_return' => $order));
            }
            
            $data = [
                "action" => "Created",
                "crud_name" => "User",
                'template' => 'order-created',
                'model' => 'Modules\Orders\Entities\Order',
                'data' => $templatedata,
            ];
            
            $customer->notify(new QA_EmailNotification($data));
        }

        //start send sms

        if ( $customer && 'yes' === $request->send_sms ) {
            $logo = getSetting( 'site_logo', 'site_settings' );
            $tonumber = ! empty($customer->phone1) ? $customer->phone1 : '';
            if ( ! empty( $tonumber ) && ! empty( $customer->phone1_code ) ) {
                $tonumber = $customer->phone1_code . $tonumber;
            }
            $smsdata = array(
                'client_name' => $customer->name,
                'content' => 'Order has been created',
                'status' => $order->status,
                'amount' => digiCurrency($order->price),
                'products' => $order->products,
                'customer_id' => $order->customer_id,
                'billing_cycle_id' => $order->billing_cycle_id,
                'tonumber' => $tonumber,

                'track_link' => route('admin.orders.show', $order->id),
                'delivery_address' => '',

                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),                
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'order_created_date' => digiDate($order->created_at),
                'site_url' => url('/'),

                'invoice_no' => $order->id,
                'invoice_url' => route('admin.orders.show', $order->id),
            );

            $order_created_date = $order->created_at;

            if($order_created_date){
                $templatedata['created_at'] = digiDate($order->created_at);
            }


            if ( $order->customer->name ) {
                $templatedata['customer_id'] = $order->customer->name;
            }
            
            if ( $order->billing_cycle->title ) {
                $templatedata['billing_cycle_id'] = $order->billing_cycle->title;
            }
            sendSms( 'order-created', $smsdata );
        }
        
        $amount = $order->price;
        if ( 'yes' === $request->add_to_income ) {
            
            $account_id = getSetting('default-account', 'order-settings', 0);

            $account_details = \App\Account::find( $account_id );  
            
            $pay_method = getSetting('default_payment_gateway', 'site_settings', 0);
            
            $paymentmethod = null;
            if ( ! empty( $pay_method ) ) {             
                $paymentmethod = $pay_method;
            }
            
            $payment = array(
                'date' => date('Y-m-d', time()),
                'amount' =>   $amount,
                'transaction_id' => rand(),
                'account_id' => ( $account_details ) ? $account_id : null,
                'order_id' => $order->id,
                'paymentmethod' => $paymentmethod,
                'description' => trans('others.orders.payment-for') . $order->id,
                'slug' => md5(microtime() . rand() . $order->id),
                'payment_status' => 'success',
            );
               
            
            
            if ( $account_details && ! empty( $account_id ) ) {
                // Let us add thhis account to the specified account.               
                \App\Account::find( $account_id )->increment('initial_balance', $amount);
            }                       
            
            // As this is the Invoice payment, so it was Income, lets add it in income.
            $pay_method = getSetting('default_payment_gateway', 'site_settings', PAYMENT_METHOD_OFFLINE);
            $pay_method_id = null;
            if ( $pay_method ) {
                $pay_method_id = \App\PaymentGateway::where('key', '=', $pay_method )->first()->id;
            }
            $income = array(
                'slug' => md5(microtime() . rand()),
                'entry_date' => date('Y-m-d', time()),
                'amount' => $amount,
                'original_amount' => $amount,
                'original_currency_id' => getDefaultCurrency('id'),
                'description' => trans('others.orders.payment-for') . $order->id,
                'ref_no' => $order->id,
                'account_id' => ( $account_details ) ? $account_id : null,
                'payer_id' => $order->customer_id,
                'pay_method_id' => $pay_method_id,
            );

            \App\Income::create( $income );         
        }

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
            $order = Order::findOrFail($id);
        } else {
             $order = Order::where('slug', '=', $id)->firstOrFail();
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
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ( is_numeric( $id ) ) {
            $order = Order::findOrFail($id);
        } else {
            $order = Order::where('slug', '=', $id)->firstOrFail();
        }

        $order->delete();

        flashMessage( 'success', 'delete');
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.orders.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
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
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Order::whereIn('id', $request->input('ids'))->get();

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
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
     
        if ( is_numeric( $id ) ) {
            $order = Order::onlyTrashed()->findOrFail($id);
        } else {
            $order = Order::where('slug', '=', $id)->onlyTrashed()->firstOrFail();
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
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
    
        if ( is_numeric( $id ) ) {
            $order = Order::onlyTrashed()->findOrFail($id);
        } else {
            $order = Order::where('slug', '=', $id)->onlyTrashed()->firstOrFail();
        }

        $order->forceDelete();

        flashMessage( 'success', 'delete' );

        return back();
    }

    public function cancelOrder( $id, $status = 'Cancelled' ) {
        if (! Gate::allows('order_cancel')) {
            return prepareBlockUserMessage();
        }
        
        if ( is_numeric( $id ) ) {
            $order = Order::findOrFail($id);
        } else {
            $order = Order::where('slug', '=', $id)->firstOrFail();
        }

        $order->update( array('status' => $status) );

        flashMessage( 'success', 'update' );

        return redirect()->route('admin.orders.index');
    }

    public function searchProduct( Request $request ) {
        if (request()->ajax()) {
            $product = \App\Product::findOrFail( $request->product_id );
            
            $product->selected_quantity = 1;
            $cartorderproduct = '';

            $order = CartOrder::where('customer_id', '=', getContactId())->first();
            if ( $order ) {
                $cartorderproduct = CartOrdersProduct::where('cart_order_id', '=', $order->id)->where('product_id', '=', $request->product_id )->first();
                if ( $cartorderproduct ) {
                    $product->selected_quantity = $cartorderproduct->quantity;
                }
            }
            return view('orders::admin.orders.cart.product-details', compact('product'));
        }
    }

    public function getProduct( Request $request ) {
        if (request()->ajax()) {
            $product = \App\Product::findOrFail( $request->product_id );
            
            $product->selected_quantity = 1;
            $cartorderproduct = '';

            $order = CartOrder::where('customer_id', '=', getContactId())->first();
            if ( $order ) {
                $cartorderproduct = CartOrdersProduct::where('cart_order_id', '=', $order->id)->where('product_id', '=', $request->product_id )->first();
                if ( $cartorderproduct ) {
                    $product->selected_quantity = $cartorderproduct->quantity;
                }
            }
            return view('orders::admin.orders.cart.show-product-details', compact('product'));
        }
    }

    public function addToCart( Request $request ) {
        if (request()->ajax()) {
            $product_id = $request->product_id;
            $quantity = $request->quantity;

            $product = \App\Product::findOrFail( $product_id );
            $order = CartOrder::where('customer_id', '=', getContactId())->first();
            if ( ! $order ) {
                $data = array(
                    'customer_id' => getContactId(),
                    'price' => 0,
                    'slug' => md5(microtime() . rand()),
                );
                CartOrder::create( $data );
                $order = CartOrder::where('customer_id', '=', getContactId())->first();
            }

            $cartorderproduct = CartOrdersProduct::where('cart_order_id', '=', $order->id)->where('product_id', '=', $product_id )->first();
            if ( $cartorderproduct ) {
                $cartorderproduct->update( [ 'quantity' => $quantity ] );
            } else {
                $data = array(
                    'quantity' => $quantity,
                    'cart_order_id' => $order->id,
                    'product_id' => $product_id,
                    'slug' => md5(microtime() . rand()),
                );
                CartOrdersProduct::create( $data );
            }

            return response()->json(['success'=>'Data is successfully added']);
        }
    }

    public function updateCart() {
        $order = CartOrder::where('customer_id', '=', getContactId())->first();
        $cartorderproducts = '';
       
        if ( $order ) {
            $cartorderproducts = CartOrdersProduct::where('cart_order_id', '=', $order->id)->get();
        }
        $source = request()->source;
        
        $order = CartOrder::where('customer_id', '=', getContactId())->first();
        $ischeckout = 'no';
        if ( 'checkout' === $source ) {
            $ischeckout = 'yes';
        }
        return view('orders::admin.orders.cart.cart-products', compact('cartorderproducts', 'ischeckout', 'order'));
    }

    public function removeFromCart( Request $request ) {
        if (request()->ajax()) {
            $slug = $request->record_slug;

            $operation = $request->operation;
            $product_id = 0;
            if ( $operation == 'removeitem' ) {
                $quantity = 0;
            } else {
                 $quantity = $request->quantity - 1;
                 $cartorderproduct = CartOrdersProduct::where('slug', '=', $slug)->first();
                $cartorderproduct->update( [ 'quantity' => $quantity ] );
            }           
            
            if ( $quantity <= 0 ) {
                $cartorderproduct = CartOrdersProduct::where('slug', '=', $slug)->first();
                $product_id = $cartorderproduct->product_id;
                CartOrdersProduct::where('slug', '=', $slug)->forceDelete();
            }
            return response()->json([ 'message' => trans('orders::global.orders.removed'), 'status' => 'success', 'product_id' => $product_id, 'operation' => $operation, 'quantity' => $quantity ]);
        }
    }

    public function updateCartProduct( Request $request ) {
        if (request()->ajax()) {
            $slug = $request->record_slug;
            
            $increse = $request->increse;

            $quantity = $request->quantity;
            if ( 'yes' === $increse ) {
                $quantity += 1;
            }

            $cartorderproduct = CartOrdersProduct::where('slug', '=', $slug)->first();

            $product_details = \App\Product::find( $cartorderproduct->product_id );
            if ( $product_details ) {
                if ( $quantity <= $product_details->stock_quantity ) {
                    if ( $quantity <= 0 ) {
                        CartOrdersProduct::where('slug', '=', $slug)->forceDelete();
                    } else {
                        $cartorderproduct->update( [ 'quantity' => $quantity ] );
                    }
                    return response()->json([ 'status' => 'success', 'message' => trans('orders::global.orders.updated') ]);
                } else {
                    return response()->json(['status' => 'danger', 'message' => trans('orders::global.orders.quantity-not-available') ]);
                }
            } else {
                return response()->json(['status' => 'danger', 'message' => trans('orders::global.orders.product-not-found') ]);
            }            
        }
    }

    public function clearCart() {
        $cartorder = CartOrder::where('customer_id', '=', getContactId())->first();

        if ( ! $cartorder ) {
            flashMessage('danger', 'not_found' );
            return back();
        }
        
        CartOrdersProduct::where('cart_order_id', '=', $cartorder->id)->forceDelete();
        $cartorder->forceDelete();

        flashMessage('success', 'create', trans('orders::global.orders.cart-cleared') );

        return redirect()->route('admin.orders.create');
    }

    public function checkOut() {
        $order = CartOrder::where('customer_id', '=', getContactId())->first();

        if ( ! $order ) {
            flashMessage('danger', 'not_found' );
            return back();
        }

        $cartorderproducts = CartOrdersProduct::where('cart_order_id', '=', $order->id)->get();
        $ischeckout = 'yes';
        return view('orders::admin.orders.cart.checkout', compact('order', 'cartorderproducts', 'ischeckout'));
    }

    public function notifyCustomer( Order $order ) {
        $customer = $order->customer()->first();
        if ( $customer ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            
            $templatedata = array(
                'client_name' => $customer->name,
                'content' => 'Order has been created',
                'status' => $order->status,
                'amount' => digiCurrency($order->price),
                'products' => $order->products,
                'customer_id' => $order->customer_id,
                'billing_cycle_id' => $order->billing_cycle_id,

                'track_link' => '',
                'delivery_address' => $customer->fullbillingaddress,

                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),                
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'site_url' => url('/'),
            );

            if ( $order->customer->name ) {
                $templatedata['customer_id'] = $order->customer->name;
            }
            
            if ( $order->billing_cycle->title ) {
                $templatedata['billing_cycle_id'] = $order->billing_cycle->title;
            }

            $products = json_decode( $order->products, true );

            if ( ! empty( $products ) ) {
                $templatedata['products'] = view('orders::admin.orders.show-products', array('products_return' => $order));
            }

            $data = [
                "action" => "Created",
                "crud_name" => "User",
                'template' => 'order-payment-success',
                'model' => 'Modules\Orders\Entities\Order',
                'data' => $templatedata,
            ];
            
            $customer->notify(new QA_EmailNotification($data));
        }
    }

    // Payment.
    public function payNow( UpdatePaynowRequest $request, $slug, $module ) {
        
       
        $cartorder = CartOrder::where('slug', '=', $slug)->where('customer_id', '=', getContactId())->first();

        if ( ! $cartorder ) {
            flashMessage('danger', 'not_found' );
            return back();
        }

        $cartproducts = CartOrdersProduct::where('cart_order_id', '=', $cartorder->id)->get();
        if ( ! $cartproducts ) {
            flashMessage('danger', 'not_found', 'trans("orders::global.orders.no-products-in-cart")' );
            return back();
        }

        $currency_code = getDefaultCurrency( 'code' );

        $products_details = array();
        $pid = $total_tax = $total_discount = $products_amount = $sub_total = $grand_total = 0;
        foreach ($cartproducts as $cartproduct ) {
            $product = $cartproduct->product;
            $products_details['product_name'][] = $product->name;
            
            $price = $product->sale_price;
            $prices = ! empty($product->prices) ? json_decode( $product->prices, true ) : array();
            if ( isCustomer() && ! empty( $prices['sale'][ $currency_code ] ) ) {
                $price = $prices['sale'][ $currency_code ]; // If customer is on orders page we need to display prices in his own currency.
            }
            $quantity = $cartproduct->quantity;
            $amount = $quantity * $price;
            $products_details['product_qty'][] = $quantity;
            $products_details['product_price'][] = $price;
            $products_details['product_amount'][] = $amount; // Product Quantity * Product Price
            $products_amount += $amount;

            $tax_value = $tax_rate = 0;
            $rate_type = 'percent';
            $tax = $product->tax;
            if ( $tax ) {
              $tax_rate = $tax->rate;
              $tax_value = $tax_rate * $quantity;
              $rate_type = $tax->rate_type;
              if ( $tax_rate > 0 && 'percent' === $rate_type ) {
                  $tax_value = ($amount * $tax_rate) / 100;
              }
            }
            $total_tax += $tax_value;
            $products_details['product_tax'][] = $tax_rate;
            $products_details['tax_type'][] = $rate_type;
            $products_details['tax_value'][] = $tax_value;

            $discount_value = $discount_rate = 0;
            $discount_type = 'percent';
            $discount = $product->discount;
            if ( $discount ) {
              $discount_rate = $discount->discount;
              $discount_value = $discount_rate * $quantity;
              $discount_type = $discount->discount_type;
              if ( $discount_rate > 0 && 'percent' === $discount_type ) {
                  $discount_value = ($amount * $discount_rate) / 100;
              }
            }
            $total_discount += $discount_value;
            $products_details['product_discount'][] = $discount_rate;
            $products_details['discount_type'][] = $discount_type;
            $products_details['discount_value'][] = $discount_value;

            $amount = $amount + $tax_value - $discount_value;
            $grand_total += $amount;
            $sub_total +=  $amount + $discount_value;
            $products_details['product_subtotal'][] = $amount; 
            $products_details['pid'][] = $pid++;
            $products_details['unit'][] = $product->measurement_unit;
            $products_details['hsn'][] = $product->hsn_sac_code;
            $products_details['alert'][] = $product->alert_quantity;
            $products_details['stock_quantity'][] = $product->stock_quantity;
            $products_details['product_ids'][] = $product->id;            
        }

        $products_details['total_tax'] = $total_tax;
        $products_details['total_discount'] = $total_discount;
        $products_details['products_amount'] = $products_amount;
        $products_details['sub_total'] = $sub_total;
        $products_details['grand_total'] = $grand_total;

        $cart_tax = $cart_discount = 0;
        $products_details['cart_tax'] = $cart_tax;
        $products_details['cart_discount'] = $cart_discount;
        $amount_payable = $grand_total + $cart_tax - $cart_discount;
        $products_details['amount_payable'] = $amount_payable;
        

        $customer = \App\Contact::find( getContactId() );
        $delivery_address = '';
        if ( $customer ) {
            $delivery_address_raw = ( $customer->delivery_address ) ? json_decode( $customer->delivery_address, true ) : array();
            
            $delivery_address = '';
            if ( ! empty( $delivery_address_raw['first_name'] ) ) {
                $delivery_address .= $delivery_address_raw['first_name'];
                
                if ( ! empty( $delivery_address_raw['last_name'] ) ) {
                    $delivery_address .= ' ' . $delivery_address_raw['last_name'];
                }
            } else {
                $delivery_address .= $customer->first_name;
            }
            
            if ( ! empty( $delivery_address_raw['address'] ) ) {
                $delivery_address .= "\n" . $delivery_address_raw['address'];
            } else {
                $delivery_address .= "\n" . $customer->address;
            }
            
            if ( ! empty( $delivery_address_raw['city'] ) ) {
                $delivery_address .= "\n" . $delivery_address_raw['city'];
            } else {
                $delivery_address .= "\n" . $customer->city;
            }
            
            if ( ! empty( $delivery_address_raw['state_region'] ) ) {
                $delivery_address .= "\n" . $delivery_address_raw['state_region'];
            } else {
                $delivery_address .= "\n" . $customer->state_region;
            }
            
            if ( ! empty( $delivery_address_raw['country'] ) ) {
                $country = \App\Country::find( $delivery_address_raw['country'] );
                $country_name = '';
                if ( $country ) {
                    $country_name = $country->title;
                }
                if ( ! empty( $country_name ) ) {
                    $delivery_address .= "\n" . $delivery_address_raw['country'];
                }
            } else {
                $country = \App\Country::find( $customer->country_id );
                $country_name = '';
                if ( $country ) {
                    $country_name = $country->title;
                }
                if ( ! empty( $country_name ) ) {
                    $delivery_address .= "\n" . $customer->country_name;
                }
            }
            if ( ! empty( $delivery_address_raw['zip_postal_code'] ) ) {
                $delivery_address .= '-' . $delivery_address_raw['zip_postal_code'];
            } else {
                $delivery_address .= '-' . $customer->zip_postal_code;
            }
        }

        $customer_id = getContactId();
        $customer_details = \App\Contact::find( $customer_id );
        $currency_id = getDefaultCurrency('id');
        if ( $customer_details && ! empty( $customer_details->currency_id ) ) {
            $currency_id = $customer_details->currency_id;
        }
        $data = array(
            'status' => 'Pending',
            'price' => 0,
            'customer_id' => getContactId(),
            'currency_id' => $currency_id,
            'slug' => md5(microtime() . rand()),
            'products' => json_encode($products_details),
            'price' => $amount_payable,
            'invoice_date' => date('Y-m-d'), // Order created date.
            'delivery_address' => $delivery_address,
        );
        $record = Order::create( $data );
        $id = $record->id; // Last Inserted id.
        
        $payment_gateway = $request->payment_gateway;
        $data = array(
            'paymentmethod' => $payment_gateway,
            'amount_payable' => $amount_payable,
        );

        if ( 'paypal' === $payment_gateway ) {
            if ( ! in_array( strtoupper( $currency_code ), paypalCurrencies() ) ) {
                flashMessage('danger', 'create', trans('orders::global.orders.paypal-currency-not-supported'));
                return back();
            }
        }

        if ( 'stripe' === $payment_gateway ) {
            if ( ! in_array( strtolower( $currency_code ), stripeCurrencies() ) ) {
                flashMessage('danger', 'create', trans('orders::global.orders.stripe-currency-not-supported'));
                return back();
            }
        }

        if ( 'payu' === $payment_gateway ) {
            if ( ! in_array( strtolower( $currency_code ), ['inr'] ) ) {
                flashMessage('danger', 'create', trans('orders::global.orders.payu-currency-not-supported'));
                return back();
            }
        }

        if ( $id > 0 ) {
            $user = \App\Contact::where('id', '=', getContactId())->first();
            $token = $this->preserveBeforeSave( $id, $data, $module );

            if(  'paypal' === $payment_gateway ){                
                $paypal = new Paypal();
                $paypal->config['return']        = route('admin.shop.process-payment', [ $token, $module ] );
                $paypal->config['cancel_return'] = route('admin.shop.payment-failed', [ $token, $module ] );
                $paypal->config['currency_code'] = $currency_code;

                $paypal->invoice = $token;
                $paypal->add(trans('orders::global.orders.payment-description') . $id, $amount_payable, 1, $id); //ADD  item
                return $paypal->pay(); //Proccess the payment
            } elseif ( 'payu' === $payment_gateway ) {
                
                $payu_testmode = getSetting('payu_testmode','payu', 'true');
                $payu_provider = getSetting('payu-provider', 'payu', 'payubiz');

                $env = ( 'true' === $payu_testmode ) ? 'test' : 'secure';
                $payconfig = array( 'payu.env' => $env);
                $payconfig['payu.default'] = $payu_provider;
                                
                if ( 'payubiz' === $payu_provider ) {
                    $payconfig['payu.accounts.payubiz.key'] = getSetting('payu_merchant_key','payu', 'gtKFFx');
                    $payconfig['payu.accounts.payubiz.salt'] = getSetting('payu_salt','payu', 'eCwWELxi');
                } else {
                    $payconfig['payu.accounts.payumoney.key'] = getSetting('payu_merchant_key','payu', 'JBZaLc');
                    $payconfig['payu.accounts.payumoney.salt'] = getSetting('payu_salt','payu', 'GQs7yium');
                }
                config( $payconfig ); // Write the dynamic values from DB.
                
                $parameters = [
                          'txnid'         => $token . '_' . date("ymds"),
                          'order_id'    => '',
                          'firstname'   => $user->first_name,
                          'email'       => $user->email,
                          'phone'       => ($user->phone1)? $user->phone1 : '45612345678',
                          'productinfo' => trans('orders::global.orders.payment-description') . $id,
                          'amount'      => $amount_payable,
                          'surl'        => route('admin.shop.process-payu', [ $token, $module ] ),
                          'furl'        => route('admin.shop.payment-failed', [ $token, $module ] ),
                          
                          'lastname'    => $user->last_name,
                          'address1'    => $user->address,
                          'address2'    => '',
                          'city'        => $user->city,
                          'state'       => $user->state_region,
                          'country'     => $user->country->title,
                          'zipcode'     => $user->zip_postal_code,
                          'curl'        => route('admin.shop.payment-cancelled', [ $token, $module ] ),
                          'udf1'        => $id,
                          'udf2'        => '',
                          'udf3'        => '',
                          'udf4'        => '',
                          'udf5'        => '',
                          'pg'        => 'NB',
                       ];
   
                return Payment::make($parameters, function ($then) use( $token, $module) {
                    $then->redirectRoute('admin.shop.process-payu', [$token, $module]);
                });
            } elseif ( 'stripe' === $payment_gateway ) {
                
                $currency_code = getDefaultCurrency( 'code' );
                if ( ! in_array( strtolower( $currency_code ), stripeCurrencies() ) ) {
                    flashMessage('danger', 'create', trans('orders::global.orders.stripe-currency-not-supported'));
                    return redirect()->route('admin.orders.checkout');
                }

                $stripe_key = getSetting( 'stripe_key', 'stripe' );
                $stripe_secret = getSetting( 'stripe_secret', 'stripe' );

                $stripe_config = array(
                    'services.stripe.key'    => $stripe_key,
                    'services.stripe.secret' => $stripe_secret,
                );
                config( $stripe_config ); // Write the dynamic values from DB.

                $stripe = new Stripe($stripe_secret);

                $stripe_token = $request->stripeToken;
                
                if ( empty( $stripe_token ) ) {
                    return view('orders::admin.orders.payments.payment-now-stripe', compact('slug', 'module', 'record'));
                }
                $user_email = getContactInfo('', 'email');
                $amount = $amount_payable;

                $merchant_payment_confirmed = false;
                
                $customer = $stripe->customers()->create(array(
                  "email" => $user_email,
                  "source" => $stripe_token,
                ));

            //Stripe will not accept amount above 999999.99     
            if ( $amount_payable > 999999.99 ) {
                flashMessage('danger', 'create', trans('orders::global.orders.amount-danger-alert'));
                return back();
            }
        

                if ( $customer ) {
                    $merchant_payment_confirmed = true;

                    $charge = $stripe->charges()->create([
                        'customer' => $customer['id'],
                        'currency' => $currency_code,
                        'amount'   => $amount,
                    ]);
                } else {
                    flashMessage('danger', 'create', trans('orders::global.orders.stripe-token-error'));
                    return redirect()->route('admin.orders.checkout');
                }
                
                if ( $merchant_payment_confirmed ) {                    
                    $payment_record = OrdersPayments::where('slug', '=', $token)->first();

                    if ( ! $payment_record ) {
                        flashMessage('danger', 'not_found', trans('custom.messages.not_found_payment') );
                        return back();
                    }
                    // Payment done
                    if( $this->processPaymentRecord($payment_record) ) {
                        $amount = $charge['amount'] / 100;

                        $payment_record->payment_status = PAYMENT_STATUS_SUCCESS;
                        $payment_record->transaction_data = json_encode($charge);
                        $payment_record->transaction_id = $charge['id'];
                        $payment_record->amount = $amount;
                        $payment_record->save();

                        $order_status = getSetting('payment-success-order-status', 'order-settings');
                        if ( 'Active' === $order_status ) {
                            $payment_record->order->status = 'Active';
                        } else {
                            $payment_record->order->status = 'Pending';
                        }
                        $payment_record->order->save();

                        $record = Order::find( $payment_record->order_id );

                        $this->notifyCustomer( $record );
                        
                        // Let us add this payment to incomes list as per settings.
                        $add_to_income = getSetting('add-to-income', 'order-settings');
                     
                        if ( 'yes' === $add_to_income ) {
                           
                            $this->addToIncome( $record, $payment_record ); 
                        }

                        $this->clearCartOnly();

                        flashMessage('success', 'create', trans('orders::global.orders.payments.success'));
                        return redirect()->route('admin.shop.payment-success', [$token, $module]);
                    } else {
                        flashMessage('danger', 'not_found', trans('orders::global.orders.stripe-payment-failed') );
                        return redirect()->route('admin.orders.payment-now', $token);
                    }
                } else {
                    flashMessage('danger', 'create', trans('orders::global.orders.stripe-token-error'));
                    return redirect()->route('admin.orders.checkout');
                }       
            }
        } else {
            flashMessage('danger', 'somethiswentwrong');
            return back();
        }
    }

    protected function updateProductCount( OrdersPayments $payment_record ) {
        // Update product stock.
        $products = json_decode( $payment_record->order->products );
        
        if ( ! empty( $products ) ) {
            $names = $products->product_name;

            for( $index = 0; $index < count( $names ); $index++ ) {
                $quantity = $products->product_qty[ $index ] ?? '0';
                $product_id = $products->product_ids[ $index ] ?? '0';
                $product = \App\Product::find( $product_id );
                if ( $product ) {
                    
                    digiUpdateProduct( $product->id, $quantity, 'desc');
                }

                $product = \App\Product::find( $product_id );
        
                // Alert admin user about quantity.
                if ( $product->stock_quantity < $product->alert_quantity ) {
                    
                    $adminUsers = \App\User::roleadmins()->pluck('contacts.id')->toArray();
                    $notification = array(
                        'text' => trans('orders::global.orders.stock-low') . $names[ $index ],
                        'link' => route('admin.products.edit', $product_id),
                    );
                    $internal_notification = \App\InternalNotification::create( $notification );
                    $internal_notification->users()->sync( array_filter( $adminUsers ) );
                }                        
            }
        }

    }

    public function processPayu( $slug, $module ) {
        $payment = Payment::capture();
        // Get the payment status.
        $isdone = $payment->isCaptured(); # Returns boolean - true / false
        
        $payment_record = OrdersPayments::where('slug', '=', $slug)->first();
        if ( ! $payment_record ) {
            flashMessage('danger', 'not_found', trans('custom.messages.not_found_payment') );
            return back();
        }

        if( $this->processPaymentRecord($payment_record) ) {
            $amount = $payment->total_amount;

            $payment_record->payment_status = PAYMENT_STATUS_SUCCESS;
            $payment_record->transaction_data = json_encode($payment->getData());
            $payment_record->amount = $amount;
            $payment_record->save();

            if ( $isdone ) {

                $order_status = getSetting('payment-success-order-status', 'order-settings');
                if ( 'Active' === $order_status ) {
                    $payment_record->order->status = 'Active';
                } else {
                    $payment_record->order->status = 'Pending';
                }
                $payment_record->order->save();

                $this->updateProductCount( $payment_record );

                $record = Order::find( $payment_record->order_id );
                $this->notifyCustomer( $record );
                
                // Let us add this payment to incomes list as per settings.
                $add_to_income = getSetting('add-to-income', 'order-settings');
              
                if ( 'yes' === $add_to_income ) {
                    $this->addToIncome( $record, $payment_record ); 
                }

                $this->clearCartOnly();

                flashMessage('success', 'create', trans('orders::global.orders.payments.success'));
                return redirect()->route('admin.shop.payment-success', [$slug, $module]);
            } else {
                flashMessage('danger', 'create', trans('orders::global.orders.payments.failed'));
                return redirect()->route('admin.orders.checkout');
            }
        } else {
            return redirect()->route('admin.orders.payment-now', $slug);
        }
    }

    // Payment
    public function processPaymentNow( UpdatePaynowRequest $request, $slug, $module ) {
        
       
        $cartorder = Order::where('slug', '=', $slug)->where('customer_id', '=', getContactId())->first();

        if ( ! $cartorder ) {
            flashMessage('danger', 'not_found' );
            return back();
        }

        $id = $cartorder->id;
        $amount_payable = $cartorder->price;
        $payment_gateway = $request->payment_gateway;
        $data = array(
            'paymentmethod' => $payment_gateway,
            'amount_payable' => $amount_payable,
        );
        if ( $id > 0 ) {        
            $record = Order::where('slug', '=', $slug)->first();

            $user = Auth::user()->contact;
            $payment_record = OrdersPayments::where('order_id', $record->id)->where('payment_status', 'pending')->orderBy('id', 'desc')->first();
            if ( $payment_record ) {
                $token = $payment_record->slug;
            } else {
                $token = $this->preserveBeforeSave( $id, $data, $module );
            }
            if(  'paypal' === $payment_gateway ){
                $paypal = new Paypal();
                $paypal->config['return']        = route('admin.shop.process-payment', [ $token, $module ] );
                $paypal->config['cancel_return'] = route('admin.shop.payment-failed', [ $token, $module ] );
                $paypal->invoice = $token;
                $paypal->add(trans('orders::global.orders.payment-description') . $id, $amount_payable, 1, $id); //ADD  item
                return $paypal->pay(); //Proccess the payment
            } elseif ( 'payu' === $payment_gateway ) {
                $payu_testmode = getSetting('payu_testmode','payu', 'true');
                $payu_provider = getSetting('payu-provider', 'payu', 'payubiz');

                $env = ( 'true' === $payu_testmode ) ? 'test' : 'secure';
                $payconfig = array( 'payu.env' => $env);
                $payconfig['payu.default'] = $payu_provider;
                                
                if ( 'payubiz' === $payu_provider ) {
                    $payconfig['payu.accounts.payubiz.key'] = getSetting('payu_merchant_key','payu', 'gtKFFx');
                    $payconfig['payu.accounts.payubiz.salt'] = getSetting('payu_salt','payu', 'eCwWELxi');
                } else {
                    $payconfig['payu.accounts.payumoney.key'] = getSetting('payu_merchant_key','payu', 'JBZaLc');
                    $payconfig['payu.accounts.payumoney.salt'] = getSetting('payu_salt','payu', 'GQs7yium');
                }
                config( $payconfig ); // Write the dynamic values from DB to config array.
                
                $parameters = [
                          'txnid'         => $token . '_' . date("ymds"),
                          'order_id'    => '',
                          'firstname'   => $user->first_name,
                          'email'       => $user->email,
                          'phone'       => ($user->phone1)? $user->phone1 : '45612345678',
                          'productinfo' => trans('orders::global.orders.payment-description') . $id,
                          'amount'      => $amount_payable,
                          'surl'        => route('admin.shop.process-payu', [ $token, $module ] ),
                          'furl'        => route('admin.shop.payment-failed', [ $token, $module ] ),
                          
                          'lastname'    => $user->last_name,
                          'address1'    => $user->address,
                          'address2'    => '',
                          'city'        => $user->city,
                          'state'       => $user->state_region,
                          'country'     => $user->country->title,
                          'zipcode'     => $user->zip_postal_code,
                          'curl'        => route('admin.shop.payment-cancelled', [ $token, $module ] ),
                          'udf1'        => $id,
                          'udf2'        => '',
                          'udf3'        => '',
                          'udf4'        => '',
                          'udf5'        => '',
                          'pg'        => 'NB',
                       ];
   
                return Payment::make($parameters, function ($then) use( $token, $module) {
                    $then->redirectRoute('admin.shop.process-payu', [$token, $module]);
                });
            } elseif ( 'stripe' === $payment_gateway ) {
                


                if ( ! $record ) {
                    flashMessage('danger', 'not_found' );
                    return back();
                }

                $currency_code = getDefaultCurrency( 'code' );
                if ( ! in_array( strtolower( $currency_code ), stripeCurrencies() ) ) {
                    flashMessage('danger', 'create', trans('orders::global.orders.stripe-currency-not-supported'));
                    return redirect()->route('admin.orders.payment-now', [$slug, 'order']);
                }

                $stripe_key = getSetting( 'stripe_key', 'stripe' );
                $stripe_secret = getSetting( 'stripe_secret', 'stripe' );

                $stripe_config = array(
                    'services.stripe.key'    => $stripe_key,
                    'services.stripe.secret' => $stripe_secret,
                );
                config( $stripe_config ); // Write the dynamic values from DB.

                $stripe = new Stripe($stripe_secret);

                $stripe_token = $request->stripeToken;

               
                
                if ( empty( $stripe_token ) ) {
                    return view('orders::admin.orders.payments.payment-now-stripe', compact('slug', 'module', 'record'));
                }
                $user_email = getContactInfo('', 'email');
                $amount = $record->price;

                $merchant_payment_confirmed = false;

                $token_chk = $stripe->tokens()->find( $stripe_token );
                
                if ( ! $token_chk ) {
                    flashMessage('danger', 'create', trans('orders::global.orders.stripe-token-not-found'));
                    return redirect()->route('admin.orders.payment-now', ['slug' => $slug] );
                }
                
                if ( $token_chk['used'] ) {
                    flashMessage('danger', 'create', trans('orders::global.orders.stripe-token-error'));
                    return redirect()->route('admin.orders.payment-now', ['slug' => $slug] );
                }
               //Stripe will not accept amount above 999999.99     
                if ( $amount > 999999.99 ) {
                    flashMessage('danger', 'create', trans('orders::global.orders.amount-danger-alert'));
                    return back();
                }
                
                $customer = $stripe->customers()->create(array(
                  "email" => $user_email,
                  "source" => $stripe_token,
                ));


                if ( $customer ) {
                    $merchant_payment_confirmed = true;

                    $charge = $stripe->charges()->create([
                        'customer' => $customer['id'],
                        'currency' => $currency_code,
                        'amount'   => $amount,
                    ]);
                } else {
                    flashMessage('danger', 'create', trans('orders::global.orders.stripe-token-error'));
                    return redirect()->route('admin.orders.payment-now', ['slug' => $slug] );
                }
                
                if ( $merchant_payment_confirmed ) {                    
                    $payment_record = OrdersPayments::where('slug', '=', $token)->first();

                    if ( ! $payment_record ) {
                        flashMessage('danger', 'not_found', trans('custom.messages.not_found_payment') );
                        return redirect()->route('admin.orders.payment-now', ['slug' => $slug] );
                    }
                    // Payment done
                    if( $this->processPaymentRecord($payment_record) ) {
                        $amount = $charge['amount'] / 100;

                        $payment_record->payment_status = PAYMENT_STATUS_SUCCESS;
                        $payment_record->transaction_data = json_encode($charge);
                        $payment_record->transaction_id = $charge['id'];
                        $payment_record->amount = $amount;
                        $payment_record->save();

                        $this->updateProductCount( $payment_record );

                        $order_status = getSetting('payment-success-order-status', 'order-settings');
                        if ( 'Active' === $order_status ) {
                            $payment_record->order->status = 'Active';
                        } else {
                            $payment_record->order->status = 'Pending';
                        }
                        $payment_record->order->save();

                        $record = Order::find( $payment_record->order_id );
                        $this->notifyCustomer( $record );
                        
                // Let us add this payment to incomes list as per settings.
                $add_to_income = getSetting('add-to-income', 'order-settings', 'no');


                      
                        if ( 'yes' === $add_to_income ) {
                            $this->addToIncome( $record, $payment_record ); 
                        }

                        $this->clearCartOnly();

                        flashMessage('success', 'create', trans('orders::global.orders.payments.success'));
                        return redirect()->route('admin.shop.payment-success', [$token, $module]);
                    } else {
                        flashMessage('danger', 'not_found', trans('orders::global.orders.stripe-payment-failed') );
                        return redirect()->route('admin.orders.payment-now', $token);
                    }
                } else {
                    return view('orders::admin.orders.payments.payment-now-stripe', compact('slug', 'module', 'record'));
                }                
            }
        } else {
            flashMessage('danger', 'somethiswentwrong');
            return back();
        }
    }

    private function preserveBeforeSave( $id, $predata, $module ) {
        
        $default_orders_account = getSetting('default-account', 'order-settings', '');
        if ( ! empty( $default_orders_account ) ) {
            $details = \App\Account::find( $default_orders_account );
            if ( ! $details ) {
                $default_orders_account = '';
            }
        }
        $data = array(
            'date' => digiTodayDateDB(),
            'amount' => $predata['amount_payable'],
            'transaction_id' => '',
            'account_id' => ( $default_orders_account ) ? $default_orders_account : NULL,
            'order_id' => $id,
            'paymentmethod' => $predata['paymentmethod'],
            'description' => trans('orders::global.orders.payment-description') . $id,
            'slug' => md5(microtime() . $id . rand()),
            'payment_status' => PAYMENT_STATUS_PENDING,
        );

        $paymetn_id = OrdersPayments::create( $data );

        return $data['slug'];
    }

    /**
     * This method Process the payment record by validating through 
     * the payment status and the age of the record and returns boolen value
     * @param  Payment $payment_record [description]
     * @return [type]                  [description]
     */
    protected  function processPaymentRecord(OrdersPayments $payment_record)
    {
        
        if(!$this->isValidPaymentRecord($payment_record))
        {
            flashMessage('danger','invalid_record');
            return FALSE;
        }

        if($this->isExpired($payment_record))
        {
            flashMessage('danger','time_out');
            return FALSE;
        }

        return TRUE;
    }

    protected function clearCartOnly() {
            $cartorder = CartOrder::where('customer_id', '=', getContactId())->first();
            if ( $cartorder ) {
                $cartorder->forceDelete(); // This will delete cart order and all related products in the cart.
            }
    }


    // Paypal Payment Process for Paypal
    public function processPayment( Request $request, $slug, $module ) {
        
        $response = $request->all();
        if ( 'order' === $module ) {
            $payment_record = OrdersPayments::where('slug', '=', $slug)->first();
            if ( ! $payment_record ) {
                flashMessage('danger', 'not_found', trans('custom.messages.not_found_payment') );
                return back();
            }

            if( $this->processPaymentRecord($payment_record) ) {
                $amount = $request->mc_gross;

                $payment_record->transaction_id = $request->txn_id;
                $payment_record->payment_status = PAYMENT_STATUS_SUCCESS;
                $payment_record->transaction_data = json_encode($response);
                $payment_record->amount = $amount;
                $payment_record->save();

                $record = Order::find( $payment_record->order_id );

                $order_status = getSetting('payment-success-order-status', 'order-settings');
                if ( 'Active' === $order_status ) {
                    $payment_record->order->status = 'Active';
                } else {
                    $payment_record->order->status = 'Pending';
                }
                $payment_record->order->save();

                $record = Order::find( $payment_record->order_id );
                $this->notifyCustomer( $record );

                $this->updateProductCount( $payment_record );                
                
                // Let us add this payment to incomes list as per settings.
                $add_to_income = getSetting('add-to-income', 'order-settings');
              
                if ( 'yes' === $add_to_income ) {
                    $this->addToIncome( $record, $payment_record ); 
                }

             
                $this->clearCartOnly();

                flashMessage('success', 'create', trans('orders::global.orders.payments.success'));
                return redirect()->route('admin.shop.payment-success', [$slug, $module]);
            } else {
                return back();
            }
        }
    }
    
    public function addToIncome( $record, $payment_record ) {
        $account_id = getSetting('default-account', 'order-settings', 0);

        $account_details = \App\Account::find( $account_id ); 
        
        $pay_method = getSetting('default_payment_gateway', 'site_settings', 0);
        
        $paymentmethod = null;
        if ( ! empty( $pay_method ) ) {             
            $paymentmethod = $pay_method;
        }
        
        $amount = $converted_amount = $payment_record->amount;
                    
    
        $basecurrency = \App\Currency::where('is_default', 'yes')->first();
        $currency_id = getDefaultCurrency( 'id', $record->customer_id ); // It gets the default currency, if the logged in user is 'Customer' it will get customer currency.
        $ordercurrency = \App\Currency::find( $currency_id );
        if ( $basecurrency && $ordercurrency ) {
            if ( $basecurrency->code != $ordercurrency->code ) {
                $converted_amount = ( $amount / $ordercurrency->rate ) * $basecurrency->rate;
            }
        }

        if ( $account_details && ! empty( $account_id ) ) {
            // Let us add this account to the specified account. We need to add converted amount to the account!
            \App\Account::find( $account_id )->increment('initial_balance', $converted_amount);
                               
        
            // As this is the Invoice payment, so it was Income, lets add it in income.
          
            $pay_method = $payment_record->paymentmethod;
            $pay_method_id = null;
            if ( $pay_method ) {
                $pay_method_id = \App\PaymentGateway::where('key', '=', $pay_method )->first()->id;
            }
            $income = array(
                'slug' => md5(microtime() . rand()),
                'entry_date' => date('Y-m-d', time()),
                'amount' =>  $converted_amount,
                'original_amount' =>  $amount ,
                'original_currency_id' => getDefaultCurrency('id'),
                'description' => trans('others.orders.payment-for') . $record->id,
                'ref_no' => $record->id,
                'account_id' => ( $account_details ) ? $account_id : null,
                'payer_id' => $record->customer_id,
                'pay_method_id' => $pay_method_id,
            );          
            \App\Income::create( $income ); 
        }
    }

    public function paymentSuccess( $slug, $module ) {
        $record = OrdersPayments::where('slug', '=', $slug)->first();
        
        return view('orders::admin.orders.payments.payment-success', compact('slug', 'module', 'record'));
    }

    /**
     * This method checks the age of the payment record
     * If the age is > than MAX TIME SPECIFIED (30 MINS), it will update the record to aborted state
     * @param  payment $payment_record [description]
     * @return boolean                 [description]
     */
    public static function isExpired(OrdersPayments $payment_record)
    {

        $is_expired = FALSE;
        if ( $payment_record ) {
            $to_time = strtotime(Carbon\Carbon::now());
            $from_time = strtotime($payment_record->updated_at);
            $difference_time = round(abs($to_time - $from_time) / 60,2);

            $payment_record_max_time_minutes = getSetting('payment-record-max-time-minutes', 'order-settings');
            if( empty( $payment_record_max_time_minutes ) ) {
                $payment_record_max_time_minutes = PAYMENT_RECORD_MAXTIME;
            }

            if($difference_time > $payment_record_max_time_minutes)
            {
                $payment_record->payment_status = PAYMENT_STATUS_CANCELLED;
                $payment_record->save();
                return $is_expired =  TRUE;
            }
        }
        return $is_expired;
    }

    /**
     * This method validates the payment record before update the payment status
     * @param  [type]  $payment_record [description]
     * @return boolean                 [description]
     */
    public static function isValidPaymentRecord(OrdersPayments $payment_record)
    {
        $valid = FALSE;
        
        if($payment_record)
        {
            if( empty( $payment_record->payment_status ) || $payment_record->payment_status == PAYMENT_STATUS_PENDING || $payment_record->paymentmethod==PAYMENT_METHOD_OFFLINE)
                $valid = TRUE;
        }
        return $valid;
    }

    public function paymentFailed( $slug, $module ) {
        
        $record = OrdersPayments::where('slug', '=', $slug)->first();
        return view('orders::admin.orders.payments.payment-failed', compact('slug', 'module', 'record'));
    }

    public function paymentCancelled( $slug, $module ) {
        
        $record = OrdersPayments::where('slug', '=', $slug)->first();
        return view('orders::admin.orders.payments.payment-cancelled', compact('slug', 'module', 'record'));
    }

    public function paymentNow( $slug ) {

        $record = Order::where('slug', '=', $slug)->first();

        if ( ! $record ) {
            flashMessage('danger', 'not_found' );
            return back();
        }
        return view('orders::admin.orders.payments.payment-now', compact('slug', 'module', 'record'));
    }
    
    public function reOrder( $slug ) {
        $record = Order::where('slug', '=', $slug)->first();
        
        if ( ! $record ) {
            flashMessage('danger', 'not_found' );
            return back();
        }
        
        $order = CartOrder::where('customer_id', '=', getContactId())->first();
        if ( ! $order ) {
            $order = CartOrder::create(
                [
                'customer_id' => getContactId(),
                'slug' => md5(microtime() . rand() . getContactId()),
                ]
            );
        }
        
        if ( $record->products ) {
            CartOrdersProduct::where('cart_order_id', '=', $order->id)->delete();
            
            $products = json_decode( $record->products, true );
            for( $p = 0; $p < count( $products['product_ids'] ); $p++ ) {               
                $quantity = $products['product_qty'][ $p ];
                $product_id = $products['product_ids'][ $p ];
                
                // Let us check whether the product exists now.
                $product = \App\Product::where('id', '=', $product_id )->where('stock_quantity', '>=', $quantity)->first();
                if ( $product ) {
                    $data = array(
                        'quantity' => $quantity,
                        'cart_order_id' => $order->id,
                        'product_id' => $product_id,
                        'slug' => md5(microtime() . rand()),
                    );
                    CartOrdersProduct::create( $data );
                }
            }
            return redirect()->route('admin.orders.checkout');
            
        } else {
            flashMessage('danger', 'create', trans('orders::admin.orders.no-products') );
            return back();
        }
        
        flashMessage('danger', 'create', trans('orders::admin.orders.wrong-operation') );
        return back();
    }


    public function refreshStats() {
        if (request()->ajax()) {
            $currency = request('currency');

            return view('orders::admin.orders.canvas.canvas-panel-body', ['currency_id' => $currency]);
        }
    }

    public function savePayment( Request $request ) {
        if ( request()->ajax() ) {
            
            $rules = [
                'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                'payment_status' => 'required',
                'order_status' => 'required',
                'paymethod' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);

            if ( ! $validator->passes() ) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }

            $order_id = request()->order_id;
            $amount = request()->price;
            $payment_status = request()->payment_status;
            $order_status = request()->order_status;
            $pay_method = request()->paymethod;

            $order = Order::find( $order_id );

            $account_id = getSetting('default-account', 'order-settings', 0);
            // Even the value exists in global settings, the account may be deleted so lets check if it exits!
            $account_details = \App\Account::find( $account_id );        
            
            $paymentmethod = null;
            if ( ! empty( $pay_method ) ) {             
                $paymentmethod = $pay_method;
            }
            
            $payment = array(
                'date' => date('Y-m-d', time()),
                'amount' => $amount,
                'transaction_id' => rand(),
                'account_id' => ( $account_details ) ? $account_id : null,
                'order_id' => $order->id,
                'paymentmethod' => $paymentmethod,
                'description' => trans('others.orders.payment-for') . $order->id,
                'slug' => md5(microtime() . rand() . $order->id),
                'payment_status' => $payment_status,
            );
            $record = OrdersPayments::create( $payment );

            // Let us add this payment to incomes list as per settings.
            $add_to_income = getSetting('add-to-income', 'order-settings', 'no');
            if ( $account_details && 'yes' === $add_to_income ) {
                $this->addToIncome( $order, $record ); 
            }

            if ( ! empty( $order_status ) ) {
                $order->status = $order_status;
                $order->save();
            }

            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $record]);
        }
    }
}