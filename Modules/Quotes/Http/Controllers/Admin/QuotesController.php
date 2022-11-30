<?php

namespace Modules\Quotes\Http\Controllers\Admin;

use Modules\Quotes\Entities\Quote;
use Modules\Quotes\Entities\QuoteTask;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Quotes\Http\Requests\Admin\StoreQuotesRequest;
use Modules\Quotes\Http\Requests\Admin\UpdateQuotesRequest;

use Modules\Quotes\Http\Requests\Admin\UploadQuotesRequest;
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
class QuotesController extends Controller
{
    use FileUploadTrait;
    public function __construct() {
        $this->middleware('plugin:quotes');
    }
    /**
     * Display a listing of Quote.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {

        if (! Gate::allows('quote_access')) {
           return prepareBlockUserMessage();
        }
        
        if (request()->ajax()) {
            $query = Quote::query();
            
            $query->with("customer");
            $query->with("currency");
            $query->with("tax");
            $query->with("discount");
            $query->with("recurring_period");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {

                if (! Gate::allows('quote_delete')) {
                    return prepareBlockUserMessage();
                }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'quotes.id',
                'quotes.customer_id',
                'quotes.currency_id',
                'quotes.title',
                'quotes.address',
                'quotes.invoice_prefix',
                'quotes.show_quantity_as',
                'quotes.invoice_no',
                'quotes.status',
                'quotes.reference',
                'quotes.invoice_date',
                'quotes.invoice_due_date',
                'quotes.invoice_notes',
                'quotes.tax_id',
                'quotes.discount_id',
                'quotes.recurring_period_id',
                'quotes.amount',
                'quotes.products',
                'quotes.paymentstatus',
                'quotes.invoice_id',
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
                return $q->where('paymentstatus', $paymentstatus);
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
                    return $q->where('quotes.customer_id', $type_id);
                });
            }

            if ( ! empty( $type ) && 'currency' === $type ) { // If the type is "currency" then id we are getting in "customer_id" is "currency_id"
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('quotes.currency_id', $type_id);
                });
            }

            /**
             * Tax is additional tax applied to the quote, not to the products in the quote.
             */
            if ( ! empty( $type ) && 'tax' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('quotes.tax_id', $type_id);
                });
            }
            /**
             * Tax is additional discount applied to the quote, not to the products in the quote.
             */
            if ( ! empty( $type ) && 'discount' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('quotes.discount_id', $type_id);
                });
            }

            if ( ! empty( $type ) && 'invoice' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('quotes.invoice_id', $type_id);
                });
            }

            if ( ! empty( $type ) && 'recurring_period' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('quotes.recurring_period_id', $type_id);
                });
            }
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'quote_';
                $routeKey = 'admin.quotes';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });

            $table->editColumn('customer.first_name', function ($row) {
                $name = $row->customer->name ? $row->customer->name : '';
                if ( empty( $name ) ) {
                    $name = $row->customer->first_name ? $row->customer->first_name : '';
                    if ( ! empty( $row->customer->last_name ) ) {
                        $name .= ' ' . $row->customer->last_name;
                    }
                }
                if( isCustomer() ){
                    return $row->customer ? $row->customer->name : '';
              } else {
                return $row->customer ? '<a href="'.route('admin.contacts.show', ['contact_id' => $row->customer->id, 'list' => 'quotes']).'" title="'.$name.'">' . $name . '</a>' : '';
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
                if(request('show_deleted') == 1) {
                    $str = $row->invoice_no ? $row->invoice_no : '';
                } else {
                $str = $row->invoice_no ? '<a href="'.route('admin.quotes.show', $row->id).'" title="'.$row->invoice_no.'">' . $row->invoice_no . '</a>' : '';
                }
                
                return $str;
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });
            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : '';
            });
            $table->editColumn('invoice_date', function ($row) {
                return $row->invoice_date ? digiDate($row->invoice_date) : '';
            });
            $table->editColumn('invoice_due_date', function ($row) {
                return $row->invoice_due_date ? digiDate($row->invoice_due_date) : '';
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
                return $row->recurring_period ? $row->recurring_period->title : '';
            });
            $table->editColumn('amount', function ($row) {
                $str = $row->amount ? digiCurrency( $row->amount,  $row->currency_id) : '';
                if ( $row->invoice_id > 0 ) {
                    $str .= '<p class="text-success"><a href="'.route('admin.invoices.show', $row->invoice_id).'" class="text-success">'.trans('custom.invoices.invoiced').'</a></p>';
                }
                return $str;
            });
            $table->editColumn('products', function ($row) {
                return $row->products ? $row->products : '';
            });
            $table->editColumn('paymentstatus', function ($row) {
                return $row->paymentstatus ? ucfirst( $row->paymentstatus ) : '';
            });

            $table->rawColumns(['actions','massDelete', 'invoice_no', 'customer.first_name', 'amount']);

            return $table->make(true);
        }

        return view('quotes::admin.quotes.index');
    }

    /**
     * Show the form for creating new Quote.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('quote_create')) {
           return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        
        if ( isSalesPerson() ) {
        $sales_agent = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CONTACT_SALE_AGENT);
                   })->where('id', Auth::id())->get()->pluck('name', 'id');
        } else {
        $sales_agent = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CONTACT_SALE_AGENT);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
    }

        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $recurring_periods = \Modules\RecurringPeriods\Entities\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = Quote::$enum_status;
                    $enum_paymentstatus = Quote::$enum_paymentstatus;
            
        return view('quotes::admin.quotes.create', compact('enum_status', 'sales_agent','enum_paymentstatus', 'customers', 'currencies', 'taxes', 'discounts', 'recurring_periods'));
    }

    /**
     * Store a newly created Quote in storage.
     *
     * @param  \App\Http\Requests\StoreQuotesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuotesRequest $request)
    {
        if (! Gate::allows('quote_create')) {
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
            $invoice = new Quote();
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
            $invoice = new Quote();
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
            $invoice_no = getNextNumber('Quote');
        }
        $addtional['invoice_no'] = $invoice_no;

        $addtional['slug'] = md5(microtime());

        $addtional['created_by_id'] = Auth::User()->id;
		
		$addtional['paymentstatus'] = 'delivered';		  

        $request->request->add( $addtional );

        $date_set = getCurrentDateFormat();
    

         $additional = array(           
            'invoice_date' => ! empty( $request->invoice_date ) ? Carbon::createFromFormat($date_set, $request->invoice_date)->format('Y-m-d') : NULL,
            'invoice_due_date' => ! empty( $request->invoice_due_date ) ? Carbon::createFromFormat($date_set, $request->invoice_due_date)->format('Y-m-d') : NULL,
        );
        $additional['invoice_number_format'] = getSetting( 'quote-number-format', 'quote-settings', 'numberbased' );
        $additional['invoice_number_separator'] = getSetting( 'quote-number-separator', 'quote-settings', '-' );
        $additional['invoice_number_length'] = getSetting( 'quote-number-length', 'quote-settings', '0' ); 

        $request->request->add( $additional );
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $invoice = Quote::create($request->all());

        $products_sync = ! empty( $products_details['products_sync'] ) ? $products_details['products_sync'] : array();
        $invoice->quote_products()->sync( $products_sync );

        $this->insertHistory( array('id' => $invoice->id, 'comments' => 'quote-created', 'operation_type' => 'crud' ) );

        $customer = $invoice->customer()->first();
        if ( ! empty( $request->savesend ) && $customer ) {
            
            // If it is in "Draft" Status customer wont be see the invoice link, so if admin click 'Save & Send' button the status should be "Published"
            $invoice->status = 'Published';
            $invoice->save();

            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'client_name' => $customer->name,
                'content' => 'quote has been created',
                'invoice_url' => route( 'admin.quotes.preview', [ 'slug' => $invoice->slug ] ),
                'invoice_no' => $invoice->invoicenumberdisplay,
                'invoice_amount' => $invoice->amount,
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
                'template' => 'quote-created',
                'model' => 'App\Invoices',
                'data' => $templatedata,
            ];
            $customer->notify(new QA_EmailNotification($data));

            $this->insertHistory( array('id' => $invoice->id, 'comments' => 'quote-created', 'operation_type' => 'email' ) );
        }

        flashMessage( 'success', 'create');

        return redirect()->route('admin.quotes.index');
    }


    /**
     * Show the form for editing Quote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('quote_edit')) {
           return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');


         if ( isSalesPerson() ) {
        $sales_agent = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CONTACT_SALE_AGENT);
                   })->where('id', Auth::id())->get()->pluck('name', 'id');
        } else {
        $sales_agent = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CONTACT_SALE_AGENT);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
    }

        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $recurring_periods = \Modules\RecurringPeriods\Entities\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = Quote::$enum_status;
                    $enum_paymentstatus = Quote::$enum_paymentstatus;
            
        $recurring_invoice = $invoice = Quote::findOrFail($id);

        return view('quotes::admin.quotes.edit', compact('recurring_invoice', 'enum_status','sales_agent', 'enum_paymentstatus', 'customers', 'currencies', 'taxes', 'discounts', 'recurring_periods', 'invoice'));
    }

    /**
     * Update Quote in storage.
     *
     * @param  \App\Http\Requests\UpdateQuotesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuotesRequest $request, $id)
    {
        if (! Gate::allows('quote_edit')) {
           return prepareBlockUserMessage();
        }
        $invoice = Quote::findOrFail($id);



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
            $invoice_no = getNextNumber('Quote');
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
        $invoice->quote_products()->sync( $products_sync );

        $this->insertHistory( array('id' => $invoice->id, 'comments' => 'quote-updated', 'operation_type' => 'crud' ) );

        flashMessage( 'success', 'update');

        if ( ! empty( $request->btnsavemanage ) ) {
            return redirect( 'admin/quotes/' . $id );
        } else {
            return redirect()->route('admin.quotes.index');
        }
    }


    /**
     * Display Quote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('quote_view')) {
           return prepareBlockUserMessage();
        }
        $recurring_invoice = $invoice = Quote::findOrFail($id);

        return view('quotes::admin.quotes.show', compact('recurring_invoice', 'invoice'));
    }


    /**
     * Remove Quote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('quote_delete')) {
           return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_invoice = Quote::findOrFail($id);

        $this->insertHistory( array('id' => $id, 'comments' => 'quote-deleted', 'operation_type' => 'crud' ) );

        $recurring_invoice->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.quotes.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        } // We are deleting records from different pages, so let us back to the same page.
      }
    }

    /**
     * Delete all selected Quote at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('quote_delete')) {
           return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Quote::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $this->insertHistory( array('id' => $entry->id, 'comments' => 'quote-deleted', 'operation_type' => 'crud' ) );
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Quote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('quote_delete')) {
           return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_invoice = Quote::onlyTrashed()->findOrFail($id);
        $recurring_invoice->restore();

        $this->insertHistory( array('id' => $id, 'comments' => 'quote-restored', 'operation_type' => 'crud' ) );

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete Quote from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('quote_delete')) {
           return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_invoice = Quote::onlyTrashed()->findOrFail($id);

        $this->insertHistory( array('id' => $id, 'comments' => 'quote-deleted', 'operation_type' => 'crud' ) );

        $recurring_invoice->forceDelete();

        return back();
    }

    public function mailInvoice() {
            if (request()->ajax()) {
                $action = request('action');
                $id = request('invoice_id');

                $invoice = Quote::findOrFail($id);
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
                    PDF::loadView('quotes::admin.quotes.invoice.invoice-content', compact('invoice'))->save(  public_path() . '/uploads/quotes/' . $file_name, true );
                }              
                if ( 'sms' === $sub ) {
                    return view( 'quotes::admin.quotes.sms.sms-form', compact('invoice', 'customer', 'template', 'action', 'sub'));
                } elseif( 'ema' === $sub ) {
                    return view( 'quotes::admin.quotes.mail.mail-form', compact('invoice', 'customer', 'template', 'action', 'sub'));
                } elseif( 'pay' === $sub ) {
                    $accounts = \App\Account::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                    $payment_gateways = \App\Settings::where('moduletype', 'payment')->where('status', '=', 'Active')->get()->pluck('module', 'key')->prepend(trans('global.app_please_select'), '');
                    $categories = \App\IncomeCategory::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

                    return view( 'quotes::admin.quotes.invoice.payment-form', compact('invoice', 'customer', 'template', 'action', 'sub', 'accounts', 'payment_gateways', 'categories'));
                }
            }
    }
    
    public function invoiceSend(Request $request) {
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

                if ( ! $validator->passes() ) {
                    return response()->json(['status' => 'danger', 'error' => $validator->errors()->all()]);
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

            $invoice = Quote::findOrFail($id);
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
                $file = public_path() . '/uploads/quotes/' . $invoice->id . '_' . $invoice->invoice_no . '.pdf';
                if ( file_exists( $file ) ) {
                    $data['attachments'][] = $file;
                }
            }

            $data['content'] = $post['message'];

            $data['site_title'] = getSetting( 'site_title', 'site_settings');
            $logo = getSetting( 'site_logo', 'site_settings' );
            $data['logo'] = asset( 'uploads/settings/' . $logo );
            $data['date'] = digiTodayDateAdd();
            $data['invoice_url'] = route( 'admin.quotes.preview', [ 'slug' => $invoice->slug ] );
            $data['invoice_no'] = $invoice->invoicenumberdisplay;
            $data['invoice_amount'] = $invoice->amount;
            $data['invoice_date'] = digiDate( $invoice->invoice_date );
            $data['invoice_due_date'] = digiDate( $invoice->invoice_due_date );
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
                    $data['tonumber'] = $customer->phone1_code . $data['tonumber'];
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
            'quote_id' => $id,
            'comments' => $comments,
            'operation_type' => $operation_type,
        );
        \Modules\Quotes\Entities\QuoteHistory::create( $log );
    }

    private function insertHistoryInvoice( $data ) {
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
            'country' => $position->countryName,
            'city' => $city,
            'browser' => $_SERVER['HTTP_USER_AGENT'],
            'quote_id' => $id,
            'comments' => $comments,
            'operation_type' => $operation_type,
        );
        \App\InvoicesHistory::create( $log );
    }

    public function savePayment() {
        if (request()->ajax()) {
            $post = request('data');
            $sub = $post['sub'];
        
            $id = $post['invoice_id'];

            $response = array('status' => 'danger', 'message' => trans('custom.messages.somethiswentwrong') );

         

            $data = array();

            $data['date'] = $post['date'];
            $data['amount'] = $post['amount'];
            $data['transaction_id'] = $post['transaction_id'];
            $data['account_id'] = $post['account'];
            $data['quote_id'] = $id;
            $data['paymentmethod'] = $post['paymethod'];
            $data['description'] = $post['description'];

           

            $record = \Modules\Quotes\Entities\QuotePayment::create( $data );

            $this->insertHistory( array('id' => $id, 'comments' => trans('quotes::custom.quotes.payment-inserted'), 'operation_type' => 'payment' ) );

            // Let us add thhis account to the specified account.
            $amount = $data['amount'];
            $basecurrency = App\Currency::where('is_default', 'yes')->first();
            $quote = Quote::find($id);
            if ( $quote && $basecurrency ) {
                $amount = ( $amount / $quote->currency->rate ) * $basecurrency->rate;
            }
            \App\Account::find($data['account_id'])->increment('initial_balance',$amount);

            $response['status'] = 'success';
            $response['message'] = trans('custom.invoices.messages.save-success');

            flashMessage( 'success', 'restore', $response['message']);
            return json_encode( $response );
        }
    }

    public function changeStatus( $id, $status ) {
        if (! Gate::allows('quote_changestatus_access')) {
           return prepareBlockUserMessage();
        }

        if (! Gate::allows('quote_changestatus_' . preg_replace("/[^a-zA-Z]/", "", $status )) ) {
           return prepareBlockUserMessage();
        }

        $invoice = Quote::findOrFail($id);
        $invoice->paymentstatus = $status;
        $invoice->save();

        $this->insertHistory( array('id' => $id, 'comments' => trans('quotes::custom.quotes.status-changed-' . $status) ) );

        flashMessage( 'success', 'status' );

        return redirect()->route('admin.quotes.show', $id);
    }

    public function showPreview( $slug ) {
        if (! Gate::allows('quote_preview')) {
           return prepareBlockUserMessage();
        }

        $invoice = Quote::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $topbar = 'no';
        $sidebar = 'no';
        return view( 'quotes::admin.quotes.preview', compact('invoice', 'sidebar', 'topbar'));
    }

    public function invoicePDF( $slug, $operation = 'download') {

        if (! Gate::allows('quote_pdf_access')) {
           return prepareBlockUserMessage();
        }

        $invoice = Quote::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        if ( 'view' === $operation ) {
            if (! Gate::allows('quote_pdf_view')) {
                flashMessage('danger', 'not_allowed');
                return back();
            }
        } elseif ( 'print' === $operation ) {
            \Debugbar::disable();
            return view('quotes::admin.quotes.invoice.invoice-print', compact('invoice'));
        } else {
            if (! Gate::allows('quote_pdf_download')) {
                flashMessage('danger', 'not_allowed');
                return back();
            }
        }


        $file_name = $invoice->id . '_' . $invoice->invoice_no . '.pdf';
        $path = public_path() . '/uploads/quotes/' . $file_name;
        PDF::loadView('quotes::admin.quotes.invoice.invoice-content', compact('invoice'))->save( $path , true );
        
        if ( 'view' === $operation ) {
            return response()->file($path);
        } else {
            return response()->download($path);
        }
    }

    public function uploadDocuments( $slug ) {
        if (! Gate::allows('quote_upload')) {
           return prepareBlockUserMessage();
        }

        $invoice = Quote::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        return view( 'quotes::admin.quotes.invoice.uploads', compact('invoice'));
    }

    public function upload( UploadQuotesRequest $request, $slug ) {
        if (! Gate::allows('quote_upload')) {
           return prepareBlockUserMessage();
        }
		
        $invoice = Quote::where('slug', '=', $slug)->first();
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


            $this->insertHistory( array('id' => $invoice->id, 'comments' => trans('quotes::custom.quotes.documents-uploaded') ) );

        flashMessage( 'success', 'create', trans('custom.invoices.upload-success'));
        return redirect()->route('admin.quotes.show', [$invoice->id]);
    }

    public function duplicate( $slug ) {
        if (! Gate::allows('quote_duplicate')) {
           return prepareBlockUserMessage();
        }

        $invoice = Quote::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $newinvoice = $invoice->replicate();

        $invoice_no = getNextNumber('Quote');
        $newinvoice->invoice_no = $invoice_no;
        $newinvoice->paymentstatus = 'delivered';
        $newinvoice->slug = md5(microtime());
        $newinvoice->created_by_id = Auth::User()->id;
        $newinvoice->save();

        $products_sync = Quote::select(['pop.*'])
        ->join('quote_products as pop', 'pop.quote_id', '=', 'quotes.id')
        ->join('products', 'products.id', '=', 'pop.product_id')
        ->where('quotes.id', $invoice->id)->get()->makeHidden(['quote_id'])->toArray();
        $newinvoice->quote_products()->sync( $products_sync );


        $this->insertHistory( array('id' => $invoice->id, 'comments' => trans('quotes::custom.quotes.quote-created-duplicate') ) );

        flashMessage( 'success', 'create', trans('custom.invoices.duplicated-quote'));
        return redirect()->route('admin.quotes.show', [$newinvoice->id]);
    }

    public function convertToInvoice( $slug, $type ) {
        if (! Gate::allows('quote_convertinvoice')) {
           return prepareBlockUserMessage();
        }

        $quote = Quote::where('slug', '=', $slug)->first();

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $date_format = getSetting('date_format', 'site_settings');
        if ( empty( $date_format ) ) {
            $date_format = 'Y-m-d';
        }
        $date_format = 'Y-m-d';

        $invoice_no = getNextNumber();
        $default_invoice_prefix = getSetting('invoice-prefix', 'invoice-settings');


        $data = array(
            'slug' => md5(microtime()),
            'status' => 'Published',
            'title' => $quote->title,
            'address' => $quote->address,
            'invoice_prefix' => $default_invoice_prefix,
            'show_quantity_as' => $quote->show_quantity_as,
            'invoice_no' => $quote->invoicenumberdisplay,
            'reference' => $quote->reference,
            'invoice_date' => ! empty( $quote->invoice_date ) ? Carbon::parse($quote->invoice_date)->format('Y-m-d') : date('Y-m-d', time()),
            'invoice_due_date' => ! empty( $quote->invoice_due_date ) ? Carbon::parse($quote->invoice_due_date)->format('Y-m-d') : date('Y-m-d', time()), 
            'customer_id' => $quote->customer_id,
            'currency_id' => $quote->currency_id,
            'tax_id' => $quote->tax_id,
            'discount_id' => $quote->discount_id,
            'amount' => $quote->amount,
            'products' => $quote->products,
            'paymentstatus' => 'unpaid',
            'quote_id' => $quote->id,
            'created_by_id' => Auth::id(),
            'delivery_address' => $quote->address,
            'sales_agent' => $quote->sale_agent,

            'invoice_notes' => $quote->invoice_notes,
            'admin_notes' => $quote->admin_notes,            
            'terms_conditions' => $quote->terms_conditions,
        );
        if ( 'convertsavedraft' === $type ) {
            $data['status'] = 'Draft';
        }
        $invoice = \App\Invoice::create($data);

        // Let us update invoice numebr with current ID.
        $invoice_no = $invoice->id;

        $invoice_start = getSetting( 'quote_start', 'quote-settings' );
        if ( is_numeric( $invoice_start ) ) {
            $invoice_no = $invoice_start + $invoice_no;
        }
        $invoice->invoice_no = $invoice_no;
        $invoice->save();
        $id  = $invoice->id;

        $this->insertHistoryInvoice( array('id' => $id, 'comments' => 'invoice-created', 'operation_type' => 'convert-from-quote' ) );

        $quote->invoice_id = $id;
        $quote->save();

        flashMessage( 'success', 'create', trans('quotes::custom.quotes.convertedtoinvoice'));
        return redirect()->route('admin.invoices.show', [$id]);
    }

    public function listTasks( $slug ) {
        if (! Gate::allows('quote_tasks')) {
           return prepareBlockUserMessage();
        }

        $quote = Quote::where('slug', '=', $slug)->first();

        if (! $quote) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        if (request()->ajax()) {
            $query = QuoteTask::query();
            $query->with("recurring");
            $query->with("invoice");
            $query->with("created_by");
            $query->with("mile_stone");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('quote_task_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'quote_tasks.id',
                'quote_tasks.name',
                'quote_tasks.description',
                'quote_tasks.priority',
                'quote_tasks.startdate',
                'quote_tasks.duedate',
                'quote_tasks.datefinished',
                'quote_tasks.status',
                'quote_tasks.recurring_id',
                'quote_tasks.recurring_type',
                'quote_tasks.recurring_value',
                'quote_tasks.cycles',
                'quote_tasks.total_cycles',
                'quote_tasks.last_recurring_date',
                'quote_tasks.is_public',
                'quote_tasks.billable',
                'quote_tasks.billed',
                'quote_tasks.invoice_id',
                'quote_tasks.hourly_rate',
                'quote_tasks.milestone',
                'quote_tasks.kanban_order',
                'quote_tasks.milestone_order',
                'quote_tasks.visible_to_client',
                'quote_tasks.deadline_notified',
                'quote_tasks.created_by_id',
                'quote_tasks.mile_stone_id',
            ]);
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'quote_task_';
                $routeKey = 'admin.quote_tasks';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('priority', function ($row) {
                return $row->priority ? $row->priority : '';
            });
            $table->editColumn('startdate', function ($row) {
                return $row->startdate ? digiDate( $row->startdate ) : '';
            });
            $table->editColumn('duedate', function ($row) {
                return $row->duedate ? digiDate( $row->duedate ) : '';
            });
            $table->editColumn('datefinished', function ($row) {
                return $row->datefinished ? digiDate( $row->datefinished ) : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });
            $table->editColumn('recurring.title', function ($row) {
                return $row->recurring ? $row->recurring->title : '';
            });
            $table->editColumn('recurring_type', function ($row) {
                return $row->recurring_type ? $row->recurring_type : '';
            });
            $table->editColumn('recurring_value', function ($row) {
                return $row->recurring_value ? $row->recurring_value : '';
            });
            $table->editColumn('cycles', function ($row) {
                return $row->cycles ? $row->cycles : '';
            });
            $table->editColumn('total_cycles', function ($row) {
                return $row->total_cycles ? $row->total_cycles : '';
            });
            $table->editColumn('last_recurring_date', function ($row) {
                return $row->last_recurring_date ? digiDate( $row->last_recurring_date ) : '';
            });
            $table->editColumn('is_public', function ($row) {
                return $row->is_public ? $row->is_public : '';
            });
            $table->editColumn('billable', function ($row) {
                return $row->billable ? $row->billable : '';
            });
            $table->editColumn('billed', function ($row) {
                return $row->billed ? $row->billed : '';
            });
            $table->editColumn('invoice.quote_no', function ($row) {
                return $row->invoice ? $row->invoice->quote_no : '';
            });
            $table->editColumn('hourly_rate', function ($row) {
                return $row->hourly_rate ? $row->hourly_rate : '';
            });
            $table->editColumn('milestone', function ($row) {
                return $row->milestone ? $row->milestone : '';
            });
            $table->editColumn('kanban_order', function ($row) {
                return $row->kanban_order ? $row->kanban_order : '';
            });
            $table->editColumn('milestone_order', function ($row) {
                return $row->milestone_order ? $row->milestone_order : '';
            });
            $table->editColumn('visible_to_client', function ($row) {
                return $row->visible_to_client ? $row->visible_to_client : '';
            });
            $table->editColumn('deadline_notified', function ($row) {
                return $row->deadline_notified ? $row->deadline_notified : '';
            });
            $table->editColumn('created_by.name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });
            $table->editColumn('mile_stone.name', function ($row) {
                return $row->mile_stone ? $row->mile_stone->name : '';
            });
            $table->editColumn('attachments', function ($row) {
                $build  = '';
                foreach ($row->getMedia('attachments') as $media) {
                    $build .= '<p class="form-group"><a href="' . $media->getUrl() . '" target="_blank">' . $media->name . '</a></p>';
                }
                
                return $build;
            });

            $table->rawColumns(['actions','massDelete','attachments']);

            return $table->make(true);
        }

        return view('quotes::admin.quotes.quote_tasks.index', compact('quote'));
    }
	
	public function createTask($slug) {
		
	}
}
