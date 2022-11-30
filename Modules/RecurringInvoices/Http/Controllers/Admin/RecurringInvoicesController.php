<?php

namespace Modules\RecurringInvoices\Http\Controllers\Admin;

use Modules\RecurringInvoices\Entities\RecurringInvoice;
use App\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\RecurringInvoices\Http\Requests\Admin\StoreRecurringInvoicesRequest;
use Modules\RecurringInvoices\Http\Requests\Admin\UpdateRecurringInvoicesRequest;

use Modules\RecurringInvoices\Http\Requests\Admin\UploadRecurringInvoicesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;

use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use PDF;
use Location;

use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Notification;
use Validator;
class RecurringInvoicesController extends Controller
{
    use FileUploadTrait;
    
    public function __construct() {
        $this->middleware('plugin:recurringinvoices');
    }
    /**
     * Display a listing of RecurringInvoice.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {
        if (! Gate::allows('recurring_invoice_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = RecurringInvoice::query();
			
            if ( isCustomer() ) {
                $query->where( 'customer_id', getContactId())->where('status', 'Published');
            }
            $query->with("customer");
            $query->with("currency");
            $query->with("tax");
            $query->with("discount");
            $query->with("recurring_period");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('recurring_invoice_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'invoices.id',
                'invoices.customer_id',
                'invoices.currency_id',
                'invoices.title',
                'invoices.address',
                'invoices.invoice_prefix',
                'invoices.show_quantity_as',
                'invoices.invoice_no',
                'invoices.status',
                'invoices.reference',
                'invoices.invoice_date',
                'invoices.invoice_due_date',
                'invoices.invoice_notes',
                'invoices.tax_id',
                'invoices.discount_id',
                'invoices.recurring_period_id',
                'invoices.amount',
                'invoices.products',
                'invoices.recurring_type',
                'invoices.recurring_value',
                'invoices.paymentstatus',
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
                if ( 'unpaid' === $paymentstatus ){
                    return $q->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date >= DATE(NOW())' );
                } elseif ( 'overdue' === $paymentstatus ){
                    return $q->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date < DATE(NOW())');
                } else {
                    return $q->where('paymentstatus', '=', $paymentstatus );
                }
            });
            $query->when(request('currency_id', false), function ($q, $currency_id) { 
                return $q->where('currency_id', $currency_id);
            });
            $query->when(request('customer', false), function ($q, $customer) { 
                return $q->where('customer_id', $customer);
            });
            /// Custom Filters End.

            /**
             * when we call invoices display from other pages!
            */
            if ( ! empty( $type ) && 'contact' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('invoices.customer_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'currency' === $type ) { // If the type is "currency" then id we are getting in "customer_id" is "currency_id"
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('invoices.currency_id', $type_id);
                });
            }

            /**
             * Tax is additional tax applied to the quote, not to the products in the quote.
             */
            if ( ! empty( $type ) && 'tax' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('invoices.tax_id', $type_id);
                });
            }
            /**
             * Tax is additional discount applied to the quote, not to the products in the quote.
             */
            if ( ! empty( $type ) && 'discount' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('invoices.discount_id', $type_id);
                });
            }

            if ( ! empty( $type ) && 'recurring_period' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('invoices.recurring_period_id', $type_id);
                });
            }
			
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'recurring_invoice_';
                $routeKey = 'admin.recurring_invoices';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('customer.first_name', function ($row) {
            
                   $name = '';
                if ( ! empty( $row->customer->id ) ) {
                    $name = $row->customer->name;
                    if ( empty( $name ) ) {
                        $name = $row->customer->first_name;
                        if ( ! empty( $row->customer->last_name ) ) {
                            $name .= ' ' . $row->customer->last_name;
                        }
                    }
                }
                if ( isCustomer() ) {
                    return $name;
                } else {
                return ! empty( $row->customer ) ? '<a href="'.route('admin.contacts.show', $row->customer->id).'" title="'.$row->customer->name.'">' . $row->customer->name . '</a>' : '-';
                 }
            });
            $table->editColumn('currency.name', function ($row) {
                return $row->currency ? $row->currency->name : '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
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
                return $row->invoice_no ? '<a href="'.route('admin.recurring_invoices.show', $row->id).'" title="'.$row->invoice_no.'">' . $row->invoice_no . '</a>' : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });
            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : '';
            });
            $table->editColumn('invoice_date', function ($row) {
                return $row->invoice_date ? digiDate( $row->invoice_date ) : '';
            });
            $table->editColumn('invoice_due_date', function ($row) {
                return $row->invoice_due_date ? digiDate( $row->invoice_due_date ) : '';
            });
            $table->editColumn('invoice_notes', function ($row) {
                return $row->invoice_notes ? $row->invoice_notes : '';
            });
            $table->editColumn('tax.name', function ($row) {
                return $row->tax ? $row->tax->name : '';
            });
            $table->editColumn('discount.name', function ($row) {
                return $row->discount ? $row->discount->name : '';
            });
            $table->editColumn('recurring_period.title', function ($row) {
                return $row->recurring_period ? $row->recurring_value .' '. $row->recurring_type.'(s)' : '';
            });

            $table->editColumn('amount', function ($row) {
                $total_paid =  \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', '=', $row->id)->where('payment_status', 'Success')->sum('amount');
                $amount_due = $row->amount - $total_paid;
                $paymentstatus = $row->paymentstatus;

                if ( $paymentstatus == 'partial' ){
                    return  $row->amount ? digiCurrency( $row->amount, $row->currency_id ).'<br/>'.'Amount due :'.'<span class="badge">'.digiCurrency($amount_due,$row->currency_id).'</span>' : '';
                } else{
                    return $row->amount ? digiCurrency( $row->amount, $row->currency_id ) : '';
                }
            });

            $table->editColumn('products', function ($row) {
                return $row->products ? $row->products : '';
            });
            $table->editColumn('paymentstatus', function ($row) {
                return $row->paymentstatus ? $row->paymentstatus : '';
            });

            $table->rawColumns(['actions','massDelete', 'invoice_no', 'customer.first_name','amount']);

            return $table->make(true);
        }

        return view('recurringinvoices::admin.recurring_invoices.index');
    }

    public function childInvoices( $id ) {

        if (! Gate::allows('invoice_edit')) {
            return prepareBlockUserMessage();
        }

        $invoice = RecurringInvoice::where('id', '=', $id)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        if (request()->ajax()) {
            $query = Invoice::query();
            
            $query->where('is_recurring_from', '=', request()->route('id'));
            if ( isCustomer() ) {
                $query->where( 'customer_id', '=', getContactId())->where('status', '=', 'Published');
            }
            $query->with("customer");
            $query->with("currency");
            $query->with("tax");
            $query->with("discount");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('invoice_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'invoices.id',
                'invoices.customer_id',
                'invoices.currency_id',
                'invoices.title',
                'invoices.address',
                'invoices.invoice_prefix',
                'invoices.show_quantity_as',
                'invoices.invoice_no',
                'invoices.status',
                'invoices.reference',
                'invoices.invoice_date',
                'invoices.invoice_due_date',
                'invoices.invoice_notes',
                'invoices.tax_id',
                'invoices.discount_id',
                'invoices.amount',
                'invoices.paymentstatus',
            ]);

            // Custom Filters Start.
            $date_filter = request()->request->get('date_filter');
            $date_type = request()->request->get('date_type');
            $paymentstatus = request()->request->get('paymentstatus');
            $currency_id = request()->request->get('currency_id');
            $customer = request()->request->get('customer');
            if ( $date_filter ) {
                $parts = explode(' - ' , $date_filter);
                $date_from = Carbon::createFromFormat('Y-m-d', $parts[0])->format('Y-m-d');
                $date_to = Carbon::createFromFormat('Y-m-d', $parts[1])->format('Y-m-d');
               
                if ( ! empty( $date_type ) ) {
                    if ( in_array($date_type, array( 'created_at') ) ) {
                        $query->where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to);
                    } else {
                        $query->where($date_type, '>=', $date_from)->where($date_type, '<=', $date_to);
                    }
                }
            }
            if ( ! empty( $paymentstatus ) ) {
                $query->where('paymentstatus', $paymentstatus );
            }
            if ( ! empty( $currency_id ) ) {
                $query->where('currency_id', $currency_id );
            }
            if ( ! empty( $customer ) ) {
                $query->where('customer_id', $customer );
            }
            /// Custom Filters End.

            $query->orderBy('id', 'desc');

           
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'invoice_';
                $routeKey = 'admin.invoices';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('customer.first_name', function ($row) {
                return $row->customer ? '<a href="'.route('admin.contacts.show', ['contact_id' => $row->customer->id, 'list' => 'recurring_invoices']).'" title="'.$row->customer->name.'">' . $row->customer->name . '</a>' : '';
            });
            $table->editColumn('currency.name', function ($row) {
                return $row->currency ? $row->currency->name : '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
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
                return $row->invoice_no ? '<a href="'.route('admin.invoices.show', $row->id).'" title="'.$row->invoice_no.'">' . $row->invoice_no . '</a>' : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });
            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : '';
            });
            $table->editColumn('invoice_date', function ($row) {
                return $row->invoice_date ? digiDate( $row->invoice_date ) : '';
            });
            $table->editColumn('invoice_due_date', function ($row) {
                return $row->invoice_due_date ? digiDate( $row->invoice_due_date ) : '';
            });
            $table->editColumn('invoice_notes', function ($row) {
                return $row->invoice_notes ? $row->invoice_notes : '';
            });
            $table->editColumn('tax.name', function ($row) {
                return $row->tax ? $row->tax->name : '';
            });
            $table->editColumn('discount.name', function ($row) {
                return $row->discount ? $row->discount->name : '';
            });
            $table->editColumn('amount', function ($row) {
                return $row->amount ? digiCurrency( $row->amount, $row->currency_id ) : '';
            });
            $table->editColumn('paymentstatus', function ($row) {
                return $row->paymentstatus ? ucfirst( $row->paymentstatus ) : '';
            });

            $table->rawColumns(['actions','massDelete', 'invoice_no', 'customer.first_name']);
            return $table->make(true);
        }

        return view( 'recurringinvoices::admin.recurring_invoices.invoice.childs', compact('invoice'));
    }

    /**
     * Show the form for creating new RecurringInvoice.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('recurring_invoice_create')) {
            return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

         $sales_agent = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CONTACT_SALE_AGENT);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $recurring_periods = \Modules\RecurringPeriods\Entities\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = RecurringInvoice::$enum_status;
                    $enum_paymentstatus = RecurringInvoice::$enum_paymentstatus;
            
        return view('recurringinvoices::admin.recurring_invoices.create', compact('enum_status', 'enum_paymentstatus', 'customers', 'sales_agent','currencies', 'taxes', 'discounts', 'recurring_periods'));
    }

    /**
     * Store a newly created RecurringInvoice in storage.
     *
     * @param  \App\Http\Requests\StoreRecurringInvoicesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRecurringInvoicesRequest $request)
    {
        if (! Gate::allows('recurring_invoice_create')) {
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
            $invoice = new RecurringInvoice();
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
            $invoice = new RecurringInvoice();
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
        // If products module disabled! lets take amount from user input!!
        if ( empty( $amount_payable ) && $request->has('amount') ) {
            $amount_payable =  $request->amount;
        }

        $addtional = array(
            'products' => json_encode( $products_details ),
            'amount' => $amount_payable,
            'currency_id' => $currency_id,
        );

        $invoice_no = $request->invoice_no;
        if ( empty( $invoice_no ) ) {
            $invoice_no = getNextNumber();
        }
        $addtional['invoice_no'] = $invoice_no;

        $addtional['slug'] = md5(microtime());

        $addtional['created_by_id'] = Auth::User()->id;
		
		$addtional['is_recurring'] = 'yes';

        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

        $date_set = getCurrentDateFormat();

        $additional = array(           
            'invoice_date' => ! empty( $request->invoice_date ) ? Carbon::createFromFormat($date_set, $request->invoice_date)->format('Y-m-d') : NULL,
            'invoice_due_date' => ! empty( $request->invoice_due_date ) ? Carbon::createFromFormat($date_set, $request->invoice_due_date)->format('Y-m-d') : NULL,
        ); 

        $additional['invoice_number_format'] = getSetting( 'invoice-number-format', 'invoice-settings', 'numberbased' );
        $additional['invoice_number_separator'] = getSetting( 'invoice-number-separator', 'invoice-settings', '-' );
        $additional['invoice_number_length'] = getSetting( 'invoice-number-length', 'invoice-settings', '0' );

        $request->request->add( $additional );

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice = RecurringInvoice::create($request->all());

        $products_sync = ! empty( $products_details['products_sync'] ) ? $products_details['products_sync'] : array();
        $invoice->invoice_products()->sync( $products_sync );

        $this->insertHistory( array('id' => $invoice->id, 'comments' => 'invoice-created', 'operation_type' => 'crud' ) );
		
		$invoice->allowed_paymodes()->sync(array_filter((array)$request->input('allowed_paymodes')));

        $customer = $invoice->customer()->first();
        if ( $customer && 'Published' === $invoice->status ) {
            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'client_name' => $customer->name,
                'content' => 'Invoice has been created',
                'invoice_url' => route( 'admin.recurring_invoices.preview', [ 'slug' => $invoice->slug ] ),
                'invoice_no' => $invoice->invoicenumberdisplay,
                'invoice_amount' => digiCurrency($invoice->amount,$invoice->currency_id),
                'invoice_date' => digiDate( $invoice->invoice_date ),
                'invoice_due_date' => digiDate( $invoice->invoice_due_date ),
                'title' => $invoice->title,
                'address' => $invoice->address,
                'reference' => $invoice->reference,
                'invoice_notes' => $invoice->invoice_notes,
                'customer_id' => $invoice->customer_id,
                'currency_id' => $invoice->currency_id,
                'tax_id' => $invoice->tax_id,
                'discount_id' => $invoice->discount_id,
                'paymentstatus' => $invoice->paymentstatus,
                'created_by_id' => $invoice->created_by_id,
                'products' => productshtml( $invoice->id, 'recurringinvoice' ),

                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),                
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'date' => digiTodayDate(),
                'site_url' => env('APP_URL'),
            );

            if ( $invoice->customer->name ) {
                $templatedata['customer_id'] = $invoice->customer->name;
            }
            
            if ( $invoice->currency->name ) {
                $templatedata['currency_id'] = $invoice->currency->name;
            }
            
            if ( $invoice->tax->name ) {
                $templatedata['tax_id'] = $invoice->tax->name;
            }
            
            if ( $invoice->discount->name ) {
                $data['discount_id'] = $invoice->discount->name;
            }
            
            $createduser = \App\User::find( $invoice->created_by_id );
            if ( $createduser ) {
                $data['created_by_id'] = $createduser->name;
            }

            $data = [
                "action" => "Created",
                "crud_name" => "User",
                'template' => 'recurring-invoice-created',
                'model' => 'App\Invoices',
                'data' => $templatedata,
            ];
            $customer->notify(new QA_EmailNotification($data));

            $this->insertHistory( array('id' => $invoice->id, 'comments' => 'invoice-created', 'operation_type' => 'email' ) );
        }

        flashMessage( 'success', 'create');

        return redirect()->route('admin.recurring_invoices.index');
    }


    /**
     * Show the form for editing RecurringInvoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('recurring_invoice_edit')) {
            return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');


        $sales_agent = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CONTACT_SALE_AGENT);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $recurring_periods = \Modules\RecurringPeriods\Entities\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = RecurringInvoice::$enum_status;
                    $enum_paymentstatus = RecurringInvoice::$enum_paymentstatus;
            
        $recurring_invoice = $invoice = RecurringInvoice::findOrFail($id);

        return view('recurringinvoices::admin.recurring_invoices.edit', compact('recurring_invoice', 'enum_status', 'enum_paymentstatus', 'customers','sales_agent', 'currencies', 'taxes', 'discounts', 'recurring_periods', 'invoice'));
    }

    /**
     * Update RecurringInvoice in storage.
     *
     * @param  \App\Http\Requests\UpdateRecurringInvoicesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRecurringInvoicesRequest $request, $id)
    {
        if (! Gate::allows('recurring_invoice_edit')) {
            return prepareBlockUserMessage();
        }
        $invoice = RecurringInvoice::findOrFail($id);

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
            
			$rate = 0;
            $rate_type = 'percent';
			if ( $tax ) {
				$rate = $tax->rate;
				$rate_type = $tax->rate_type;
			}
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
			
			$rate = 0;
            $rate_type = 'percent';
			if ( $discount ) {
				$rate = $discount->discount;
				$rate_type = $discount->discount_type;
			}
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
        // If products module disabled! lets take amount from user input!!
        if ( empty( $amount_payable ) && $request->has('amount') ) {
            $amount_payable =  $request->amount;
        }
        
        $addtional = array(
            'products' => json_encode( $products_details ),
            'amount' => $amount_payable,
            'currency_id' => $currency_id,
        );

        $invoice_no = $request->invoice_no;
        if ( empty( $invoice_no ) ) {
            $invoice_no = getNextNumber();
        }        
        $addtional['invoice_no'] = $invoice_no;

        $request->request->add( $addtional ); //add additonal / Changed values to the request object.
        

        $date_set = getCurrentDateFormat();

        $additional = array(           
            'invoice_date' => ! empty( $request->invoice_date ) ? Carbon::createFromFormat($date_set, $request->invoice_date)->format('Y-m-d') : NULL,
            'invoice_due_date' => ! empty( $request->invoice_due_date ) ? Carbon::createFromFormat($date_set, $request->invoice_due_date)->format('Y-m-d') : NULL,
        ); 


        $request->request->add( $additional );

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $invoice->update($request->all());

        $products_sync = ! empty( $products_details['products_sync'] ) ? $products_details['products_sync'] : array();
        $invoice->invoice_products()->sync( $products_sync );

        $this->insertHistory( array('id' => $invoice->id, 'comments' => 'invoice-updated', 'operation_type' => 'crud' ) );
		
		$invoice->allowed_paymodes()->sync(array_filter((array)$request->input('allowed_paymodes')));

        flashMessage( 'success', 'update');

        if ( ! empty( $request->btnsavemanage ) ) {
            return redirect( 'admin/recurring_invoices/' . $id );
        } else {
            return redirect()->route('admin.recurring_invoices.index');
        }
    }


    /**
     * Display RecurringInvoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('recurring_invoice_view')) {
            return prepareBlockUserMessage();
        }
        $recurring_invoice = $invoice = RecurringInvoice::findOrFail($id);

        return view('recurringinvoices::admin.recurring_invoices.show', compact('recurring_invoice', 'invoice'));
    }


    /**
     * Remove RecurringInvoice from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('recurring_invoice_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_invoice = RecurringInvoice::findOrFail($id);
        $recurring_invoice->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.recurring_invoices.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        } // We are deleting records from different pages, so let us back to the same page.
     }
    }

    /**
     * Delete all selected RecurringInvoice at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('recurring_invoice_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = RecurringInvoice::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore RecurringInvoice from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('recurring_invoice_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_invoice = RecurringInvoice::onlyTrashed()->findOrFail($id);
        $recurring_invoice->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete RecurringInvoice from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('recurring_invoice_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_invoice = RecurringInvoice::onlyTrashed()->findOrFail($id);
        $recurring_invoice->forceDelete();

        return back();
    }

    public function mailInvoice() {
            if (request()->ajax()) {
                $action = request('action');
                $id = request('invoice_id');

                $invoice = RecurringInvoice::findOrFail($id);
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
                    PDF::loadView('recurringinvoices::admin.recurring_invoices.invoice.invoice-content', compact('invoice'))->save(  public_path() . '/uploads/recurring_invoices/' . $file_name, true );
                }              
                if ( 'sms' === $sub ) {
                    return view( 'recurringinvoices::admin.recurring_invoices.sms.sms-form', compact('invoice', 'customer', 'template', 'action', 'sub'));
                } elseif( 'ema' === $sub ) {
                    return view( 'recurringinvoices::admin.recurring_invoices.mail.mail-form', compact('invoice', 'customer', 'template', 'action', 'sub'));
                } elseif( 'pay' === $sub ) {
                    $accounts = \App\Account::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                    $payment_gateways = \App\PaymentGateway::where('status', '=', 'Active')->get()->pluck('name', 'key')->prepend(trans('global.app_please_select'), '');
                    $categories = \App\IncomeCategory::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

                    if ( isCustomer() ) {                    
                    $payment_gateways = $invoice->allowed_paymodes->pluck('name', 'key')->prepend(trans('global.app_please_select'), '');
                    
                    return view( 'recurringinvoices::admin.recurring_invoices.invoice.payment-form-customer', compact('invoice', 'customer', 'template', 'action', 'sub', 'accounts', 'payment_gateways', 'categories'));
                    } else {
					
                    return view( 'recurringinvoices::admin.recurring_invoices.invoice.payment-form', compact('invoice', 'customer', 'template', 'action', 'sub', 'accounts', 'payment_gateways', 'categories'));
					}
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
                    'tonumber' => 'required|numeric',
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

            $invoice = RecurringInvoice::findOrFail($id);
            $customer = $invoice->customer()->first();

            if ( ! $customer ) {
                return response()->json(['status' => 'danger', 'error'=>[trans('custom.messages.customer-not-found')]]);
            }

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

            $data['ccemail'] = ! empty( $post['ccemail'] ) ? $post['ccemail'] : '';
            $data['bccemail'] = ! empty( $post['bccemail'] ) ? $post['bccemail'] : '';
            $data['bcc_admin'] = ! empty( $post['bcc_admin'] ) ? $post['bcc_admin'] : '';
            $data['bccemail_admin'] = '';

            $admin_email = getSetting('contact_email', 'site_settings');
            if ( ! empty($data['bcc_admin']) && $data['bcc_admin'] == 'Yes' && ! empty( $admin_email )) {                
                $data['bccemail_admin'] = $admin_email;
            }

            $data['attachments'] = array();
            if ( ! empty( $post['attach_pdf'] ) && 'Yes' === $post['attach_pdf'] ) {
                $file = public_path() . '/uploads/invoices/' . $invoice->id . '_' . $invoice->invoice_no . '.pdf';
                if ( file_exists( $file ) ) {
                    $data['attachments'][] = $file;
                }
            }

            $total_paid = \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', '=', $invoice->id)->where('payment_status', 'Success')->sum('amount');
                    $amount_due = $invoice->amount - $total_paid;

            $data['content'] = $post['message'];

            $data['site_title'] = getSetting( 'site_title', 'site_settings');
            $logo = getSetting( 'site_logo', 'site_settings' );
            $data['logo'] = asset( 'uploads/settings/' . $logo );
            $data['date'] = digiTodayDateAdd();

            $data['invoice_url'] = route( 'admin.recurring_invoices.preview', [ 'slug' => $invoice->slug ] );
            $data['invoice_no'] = $invoice->invoicenumberdisplay;
            $data['amount_due'] = digiCurrency($amount_due,$invoice->currency_id);
            $data['invoice_amount'] = digiCurrency($invoice->amount,$invoice->currency_id);
            $data['invoice_date'] = digiDate( $invoice->invoice_date );
            $data['invoice_due_date'] = digiDate( $invoice->invoice_due_date );
            $data['products'] = productshtml( $invoice->id, 'recurringinvoice' );
            
            $data['site_address'] = getSetting( 'site_address', 'site_settings');
            $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
            $data['site_email'] = getSetting( 'contact_email', 'site_settings');   

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
                    $action .= '-sms-failed';
                } else {
                    $response['message'] = trans('custom.messages.smssent');
                }
            } elseif( 'ema' === $sub ) {
                $res = sendEmail( $action, $data );                
            }
          
            
            $this->insertHistory( array('id' => $id, 'comments' => $action, 'operation_type' => $operation_type ) );
    
            flashMessage( $response['status'], 'restore', $response['message']);
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
            'browser' => ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'Cron job',
            'invoice_id' => $id,
            'comments' => $comments,
            'operation_type' => $operation_type,
        );
        \Modules\RecurringInvoices\Entities\RecurringInvoiceHistory::create( $log );
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
                    'category' => 'required|exists:income_categories,id',
                    'paymethod' => 'required',
                ];
            
            if ( isPluginActive('account') ) {
                $rules['account'] = 'required|exists:accounts,id';
                
            }


            if ( isCustomer() || isClient() ) {
                unset( $rules['date'] );
                unset( $rules['amount'] );
                unset( $rules['category'] );
                if ( ! empty( $rules['account'] ) ) {
                    unset( $rules['account'] );
                }
            }

             $messages = [
                'account.required' => trans('custom.invoices.messages.account'),
                'account.exists' => trans('custom.invoices.messages.account-exists'),

                'category.required' => trans('custom.invoices.messages.category'),
                'category.exists' => trans('custom.invoices.messages.income-category-exists'),

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
			
			  if ( isCustomer() || isClient() ) {
             
                $response['url'] = route('admin.payment.paynow', ['module' => 'recurring_invoices', 'id' => $id, 'paymethod' => $post['paymethod']]);
                $response['status'] = 'customer';
                $response['message'] = trans('custom.invoices.messages.save-success');
            
                return json_encode( $response );
            }

            $data = array();

            $data['date'] = date( 'Y-m-d', strtotime( $post['date'] ) );
            $data['amount'] = $post['amount'];
            $data['transaction_id'] = $post['transaction_id'];
            $data['account_id'] = $post['account'];
            $data['invoice_id'] = $id;
            $data['paymentmethod'] = $post['paymethod'];
            $data['description'] = $post['description'];            

            $record = \Modules\InvoicePayments\Entities\InvoicePayment::create( $data );

              $invoice = RecurringInvoice::find($id);
            $total_paid = \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', $id)->where('payment_status', 'Success')->sum('amount');
            if( $total_paid >= $invoice->amount ) {
                $invoice->paymentstatus = 'paid';
                $invoice->save();
            }else if( $total_paid < $invoice->amount ){
                $invoice->paymentstatus = 'partial';
                $invoice->save();
            }


            $add_to_income_invoice = getSetting( 'add-to-income-invoice', 'invoice-settings', 'no' );

            $account_details = '';
            if ( ! empty( $data['account_id'] ) ) {
                $account_details = \App\Account::find( $data['account_id'] );
            }
            if ( ! $account_details ) {
                $account_id = getSetting('default-account', 'invoice-settings', 0);
                $account_details = \App\Account::find( $account_id ); 
            }

            // Let us add thhis account to the specified account.
            $amount = $data['amount'];
            if ( ! empty( $account_details ) && 'yes' === $add_to_income_invoice ) {

            $basecurrency = \App\Currency::where('is_default', 'yes')->first();
            
            if ( $invoice && $basecurrency && ! empty( $invoice->currency_id )) {
                $amount = ( $amount / $invoice->currency->rate ) * $basecurrency->rate;
            }
            \App\Account::find($data['account_id'])->increment('initial_balance',$amount);

            // As this is the Invoice payment, so it was Income, lets add it in income.
            $pay_method = \App\PaymentGateway::where('key', '=', $post['paymethod'])->first();
            $pay_method_id = null;
            if ( $pay_method ) {
                $pay_method_id = $pay_method->id;
            }
            $income = array(
                'slug' => md5(microtime() . rand()),
                'entry_date' => Carbon::createFromFormat(config('app.date_format'), $post['date'])->format('Y-m-d'),
                'amount' => $amount, // Let is save amount in base currency.
                'original_amount' => $data['amount'],
                'original_currency_id' => $invoice->currency_id,
                'description' => $data['description'],
                'ref_no' => $data['transaction_id'],
                'account_id' => $data['account_id'],
                'payer_id' => $invoice->customer_id,
                'pay_method_id' => $pay_method_id,
                'income_category_id' => $post['category'],
            );
            \App\Income::create( $income );

        }

            $response['status'] = 'success';
            $response['message'] = trans('custom.invoices.messages.save-success');

            flashMessage( 'success', 'restore', $response['message']);
            return json_encode( $response );
        }
    }

    public function changeStatus( $id, $status ) {
        if (! Gate::allows('recurring_invoice_changestatus_access')) {
            return prepareBlockUserMessage();
        }

        $invoice = RecurringInvoice::findOrFail($id);
        $invoice->paymentstatus = $status;
        $invoice->save();

        $this->insertHistory( array('id' => $id, 'comments' => 'Status change to ' . $status, 'operation_type' => 'crud' ) );

        flashMessage( 'success', 'status' );

        return redirect()->route('admin.recurring_invoices.show', $id);
    }

    public function showPreview( $slug ) {
        if (! Gate::allows('recurring_invoice_preview')) {
            return prepareBlockUserMessage();
        }

        $invoice = RecurringInvoice::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $topbar = 'no';
        $sidebar = 'no';
        return view( 'recurringinvoices::admin.recurring_invoices.preview', compact('invoice', 'sidebar', 'topbar'));
    }

    public function invoicePDF( $slug, $operation = 'download') {

        if (! Gate::allows('recurring_invoice_pdf_view') && ! Gate::allows('recurring_invoice_pdf_download')) {
            return prepareBlockUserMessage();
        }

        $invoice = RecurringInvoice::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $file_name = $invoice->id . '_' . $invoice->invoice_no . '.pdf';
        $path = public_path() . '/uploads/invoices/' . $file_name;
        PDF::loadView('recurringinvoices::admin.recurring_invoices.invoice.invoice-content', compact('invoice'))->save( $path , true );
        
        if ( 'view' === $operation ) {
            return response()->file($path);
        } elseif ( 'print' === $operation ) {
            \Debugbar::disable();
            return view('recurringinvoices::admin.recurring_invoices.invoice.invoice-print', compact('invoice'));
        } else {
            return response()->download($path);
        }
    }

    public function uploadDocuments( $slug ) {
        if (! Gate::allows('recurring_invoice_uploads')) {
            return prepareBlockUserMessage();
        }

        $invoice = RecurringInvoice::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        return view( 'recurringinvoices::admin.recurring_invoices.invoice.uploads', compact('invoice'));
    }

    public function upload( UploadRecurringInvoicesRequest $request, $slug ) {
        if (! Gate::allows('recurring_invoice_uploads')) {
            return prepareBlockUserMessage();
        }

        $invoice = RecurringInvoice::where('slug', '=', $slug)->first();
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

        $this->insertHistory( array('id' => $invoice->id, 'comments' => 'Documents uploaded', 'operation_type' => 'crud' ) );

        flashMessage( 'success', 'create', trans('custom.invoices.upload-success'));
        return redirect()->route('admin.recurring_invoices.show', [$invoice->id]);
    }

    public function duplicate( $slug ) {
        if (! Gate::allows('recurring_invoice_duplicate')) {
            return prepareBlockUserMessage();
        }

        $invoice = RecurringInvoice::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }


        $newinvoice = $invoice->replicate();

        $invoice_no = getNextNumber();
        $newinvoice->invoice_no = $invoice_no;
        $newinvoice->paymentstatus = 'unpaid';
        $newinvoice->slug = md5(microtime());
        $newinvoice->created_by_id = Auth::User()->id;

        $newinvoice->save();

        $products_sync = \App\Invoice::select(['pop.*'])
        ->join('invoice_products as pop', 'pop.invoice_id', '=', 'invoices.id')
        ->join('products', 'products.id', '=', 'pop.product_id')
        ->where('invoices.id', $invoice->id)->get()->makeHidden(['invoice_id'])->toArray();
        $newinvoice->invoice_products()->sync( $products_sync );

        $this->insertHistory( array('id' => $newinvoice->id, 'comments' => 'recurring-invoice-created', 'operation_type' => 'crud' ) );

        flashMessage( 'success', 'create', trans('custom.invoices.recurring-duplicated'));
        return redirect()->route('admin.recurring_invoices.show', [$newinvoice->id]);
    }


    public function refreshStats() {
        if (request()->ajax()) {
            $currency = request('currency');

            return view('recurringinvoices::admin.recurring_invoices.canvas.canvas-panel-body', ['currency_id' => $currency]);
        }
    }
	
	public function getDetails( $id ) {
		if (request()->ajax()) {
			return json_encode( \App\RecurringPeriod::find( $id ) );
        }
	}
}
