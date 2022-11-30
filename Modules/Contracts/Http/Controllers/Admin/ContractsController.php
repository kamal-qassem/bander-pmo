<?php

namespace Modules\Contracts\Http\Controllers\Admin;

use Modules\Contracts\Entities\Contract;
use Modules\Contracts\Entities\ContractTask;
use Modules\Contracts\Entities\ContractType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Contracts\Http\Requests\Admin\StoreContractsRequest;
use Modules\Contracts\Http\Requests\Admin\UpdateContractsRequest;

use Modules\Contracts\Http\Requests\Admin\UploadContractsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;

use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use PDF;
use Location;
use Validator;
use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Notification;
class ContractsController extends Controller
{
    use FileUploadTrait;
    
    public function __construct() {
        $this->middleware('plugin:contracts');
    }
    /**
     * Display a listing of Contract.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {

        if (! Gate::allows('contract_access')) {
           return prepareBlockUserMessage();
        }

        
        if (request()->ajax()) {
            $query = Contract::query();
            
            $query->with("customer");
            $query->with("currency");
            $query->with("contract_type");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('contract_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'contracts.id',
                'contracts.customer_id',
                'contracts.currency_id',
                'contracts.subject',
                'contracts.address',
                'contracts.contract_value',
                'contracts.contract_type_id',
                'contracts.visible_to_customer',
                'contracts.invoice_prefix',
                'contracts.show_quantity_as',
                'contracts.invoice_no',
                'contracts.status',
                'contracts.reference',
                'contracts.invoice_date',
                'contracts.invoice_due_date',
                'contracts.invoice_notes',
                'contracts.recurring_period_id',
                'contracts.amount',
                'contracts.paymentstatus',
                'contracts.invoice_id',
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
                $query->when($type_id, function ($q, $customer_id) { 
                    return $q->where('customer_id', $customer_id);
                });
            }

            if ( ! empty( $type ) && 'currency' === $type ) { // If the type is "currency" then id we are getting in "customer_id" is "currency_id"
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('currency_id', $type_id);
                });
            }
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'contract_';
                $routeKey = 'admin.contracts';

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
              } 
                          

              else {
                return $row->customer ? '<a href="'.route('admin.contacts.show', ['contact_id' => $row->customer->id, 'list' => 'contracts']).'" subject="'.$name.'">' . $name . '</a>' : '';
              }
            });
            $table->editColumn('currency.name', function ($row) {
                return $row->currency ? $row->currency->name : '';
            });
            $table->editColumn('subject', function ($row) {
                return $row->subject ? $row->subject : '';
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
                $str = $row->invoice_no ? '<a href="'.route('admin.contracts.show', $row->id).'" subject="'.$row->invoice_no.'">' . $row->invoice_no . '</a>' : '';
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
            
            $table->editColumn('amount', function ($row) {
                $str = $row->amount ? digiCurrency( $row->amount,  $row->currency_id) : '';
                if ( $row->invoice_id > 0 ) {
                    $str .= '<p class="text-success"><a href="'.route('admin.invoices.show', $row->invoice_id).'" class="text-success">'.trans('custom.invoices.invoiced').'</a></p>';
                }
                return $str;
            });
            $table->editColumn('visible_to_customer', function ($row) {
                return $row->visible_to_customer ? $row->visible_to_customer : '';
            });

            $table->editColumn('contract.name', function ($row) {
                return $row->contract_type ? $row->contract_type->name : '';
            });
            $table->editColumn('invoice_notes', function ($row) {
                return $row->invoice_notes ? $row->invoice_notes : '';
            });
           
            $table->editColumn('recurring_period.subject', function ($row) {
                return $row->recurring_period ? $row->recurring_period->subject : '';
            });
            
            $table->editColumn('paymentstatus', function ($row) {
                return $row->paymentstatus ? ucfirst( $row->paymentstatus ) : '';
            });

            $table->rawColumns(['actions','massDelete', 'invoice_no', 'customer.first_name', 'amount']);

            return $table->make(true);
        }

        return view('contracts::admin.contracts.index');
    }

    /**
     * Show the form for creating new Contract.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('contract_create')) {
           return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        

        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $contract_types = \Modules\Contracts\Entities\ContractType::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
       
        $recurring_periods = \Modules\RecurringPeriods\Entities\RecurringPeriod::get()->pluck('subject', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = Contract::$enum_status;
        $enum_paymentstatus = Contract::$enum_paymentstatus;
        $enum_visible_to_customer = Contract::$enum_visible_to_customer;    
        return view('contracts::admin.contracts.create', compact('enum_status','enum_paymentstatus','enum_visible_to_customer', 'customers', 'currencies','contract_types', 'recurring_periods'));
    }

    /**
     * Store a newly created Contract in storage.
     *
     * @param  \App\Http\Requests\StoreContractsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContractsRequest $request)
    {
        if (! Gate::allows('contract_create')) {
           return prepareBlockUserMessage();
        }

        $contract_value =  $request->contract_value;
        $contract_type =  $request->contract_type;

        $amount_payable = $contract_value;
      
        // if there are transactions for this customer. Currency selection may disable, so we need to get it from customer profile.
        $currency_id = $request->currency_id;
        if ( empty( $currency_id ) ) {
            $currency_id = getDefaultCurrency( 'id', $request->customer_id );
        }

      
        $addtional = array(
            'contract_value' => json_encode( $contract_value ),
            'amount' => $amount_payable,
            'currency_id' => $currency_id,
        );

        $invoice_no = $request->invoice_no;
        if ( empty( $invoice_no ) ) {
            $invoice_no = getNextNumber('Contract');
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
        $additional['invoice_number_format'] = getSetting( 'contract-number-format', 'contract-settings', 'numberbased' );
        $additional['invoice_number_separator'] = getSetting( 'contract-number-separator', 'contract-settings', '-' );
        $additional['invoice_number_length'] = getSetting( 'contract-number-length', 'contract-settings', '0' ); 

        $request->request->add( $additional );
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $invoice = Contract::create($request->all());

        $this->insertHistory( array('id' => $invoice->id, 'comments' => 'contract-created', 'operation_type' => 'crud' ) );

        $customer = $invoice->customer()->first();
        if ( ! empty( $request->savesend ) && $customer ) {
            
            // If it is in "Draft" Status customer wont be see the invoice link, so if admin click 'Save & Send' button the status should be "Published"
            $invoice->status = 'Published';
            $invoice->save();

            // Notification to user
            $logo = getSetting( 'site_logo', 'site_settings' );
            $templatedata = array(
                'client_name' => $customer->name,
                'content' => 'contract has been created',
                'invoice_url' => route( 'admin.contracts.preview', [ 'slug' => $invoice->slug ] ),
                'invoice_no' => $invoice->invoicenumberdisplay,
                'invoice_amount' => $invoice->amount,
                'invoice_date' => digiDate( $invoice->invoice_date ),
                'invoice_due_date' => digiDate( $invoice->invoice_due_date ),
                'subject' => $invoice->subject,
                'address' => $invoice->address,
                'reference' => $invoice->reference,
                'invoice_notes' => $invoice->invoice_notes,
                'customer_id' => $invoice->customer_id,
                'currency_id' => $invoice->currency_id,
                
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
            
            $createduser = \App\User::find( $invoice->created_by_id );
            if ( $createduser ) {
                $data['created_by_id'] = $createduser->name;
            }

            $data = [
                "action" => "Created",
                "crud_name" => "User",
                'template' => 'contract-created',
                'model' => 'App\Invoices',
                'data' => $templatedata,
            ];

            $this->insertHistory( array('id' => $invoice->id, 'comments' => 'contract-created', 'operation_type' => 'email' ) );
        }

        flashMessage( 'success', 'create');

        return redirect()->route('admin.contracts.index');
    }


    /**
     * Show the form for editing Contract.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('contract_edit')) {
           return prepareBlockUserMessage();
        }
        
        $customers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');


       
        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        
        $contract_types = \Modules\Contracts\Entities\ContractType::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        
        $recurring_periods = \Modules\RecurringPeriods\Entities\RecurringPeriod::get()->pluck('subject', 'id')->prepend(trans('global.app_please_select'), '');
        $enum_status = Contract::$enum_status;
        $enum_paymentstatus = Contract::$enum_paymentstatus;
        $enum_visible_to_customer = Contract::$enum_visible_to_customer;    
        $recurring_invoice = $invoice = Contract::findOrFail($id);

        return view('contracts::admin.contracts.edit', compact('recurring_invoice', 'enum_status','enum_visible_to_customer','contract_types', 'enum_paymentstatus', 'customers', 'currencies','recurring_periods', 'invoice'));
    }

    /**
     * Update Contract in storage.
     *
     * @param  \App\Http\Requests\UpdateContractsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContractsRequest $request, $id)
    {
        if (! Gate::allows('contract_edit')) {
           return prepareBlockUserMessage();
        }
        $invoice = Contract::findOrFail($id);

        $amount =  $request->amount;
        $contract_type =  $request->contract_type;
        
        
        // if there are transactions for this customer. Currency selection may disable, so we need to get it from customer profile.
        $currency_id = $request->currency_id;
        if ( empty( $currency_id ) ) {
            $currency_id = getDefaultCurrency( 'id', $request->customer_id );
        }

        $addtional = array(
            
            'amount' => $amount,
            'currency_id' => $currency_id,
        );

        $invoice_no = $request->invoice_no;
        if ( empty( $invoice_no ) ) {
            $invoice_no = getNextNumber('Contract');
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

       

        $this->insertHistory( array('id' => $invoice->id, 'comments' => 'contract-updated', 'operation_type' => 'crud' ) );

        flashMessage( 'success', 'update');

        if ( ! empty( $request->btnsavemanage ) ) {
            return redirect( 'admin/contracts/' . $id );
        } else {
            return redirect()->route('admin.contracts.index');
        }
    }


    /**
     * Display Contract.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('contract_view')) {
           return prepareBlockUserMessage();
        }
        $recurring_invoice = $invoice = Contract::findOrFail($id);

        return view('contracts::admin.contracts.show', compact('recurring_invoice', 'invoice'));
    }


    /**
     * Remove Contract from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('contract_delete')) {
           return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_invoice = Contract::findOrFail($id);

        $this->insertHistory( array('id' => $id, 'comments' => 'contract-deleted', 'operation_type' => 'crud' ) );

        $recurring_invoice->delete();

        return \Redirect::to( url()->previous() ); // We are deleting records from different pages, so let us back to the same page.
    }

    /**
     * Delete all selected Contract at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('contract_delete')) {
           return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Contract::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $this->insertHistory( array('id' => $entry->id, 'comments' => 'contract-deleted', 'operation_type' => 'crud' ) );
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Contract from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('contract_delete')) {
           return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_invoice = Contract::onlyTrashed()->findOrFail($id);
        $recurring_invoice->restore();

        $this->insertHistory( array('id' => $id, 'comments' => 'contract-restored', 'operation_type' => 'crud' ) );

        flashMessage( 'success', 'restore' );
        return redirect()->route('admin.contracts.index');
    }

    /**
     * Permanently delete Contract from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('contract_delete')) {
           return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $recurring_invoice = Contract::onlyTrashed()->findOrFail($id);

        $this->insertHistory( array('id' => $id, 'comments' => 'contract-deleted', 'operation_type' => 'crud' ) );

        $recurring_invoice->forceDelete();

        return redirect()->route('admin.contracts.index');
    }

    public function mailInvoice() {
            if (request()->ajax()) {
                $action = request('action');
                $id = request('invoice_id');

                $invoice = Contract::findOrFail($id);
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
                    PDF::loadView('contracts::admin.contracts.invoice.invoice-content', compact('invoice'))->save(  public_path() . '/uploads/contracts/' . $file_name, true );
                }              
                if ( 'sms' === $sub ) {
                    return view( 'contracts::admin.contracts.sms.sms-form', compact('invoice', 'customer', 'template', 'action', 'sub'));
                } elseif( 'ema' === $sub ) {
                    return view( 'contracts::admin.contracts.mail.mail-form', compact('invoice', 'customer', 'template', 'action', 'sub'));
                } elseif( 'pay' === $sub ) {
                    $accounts = \App\Account::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                    $payment_gateways = \App\Settings::where('moduletype', 'payment')->where('status', '=', 'Active')->get()->pluck('module', 'key')->prepend(trans('global.app_please_select'), '');
                    $categories = \App\IncomeCategory::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

                    return view( 'contracts::admin.contracts.invoice.payment-form', compact('invoice', 'customer', 'template', 'action', 'sub', 'accounts', 'payment_gateways', 'categories'));
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
                    // 'message' => 'required',
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

            $invoice = Contract::findOrFail($id);
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
                $file = public_path() . '/uploads/contracts/' . $invoice->id . '_' . $invoice->invoice_no . '.pdf';
                if ( file_exists( $file ) ) {
                    $data['attachments'][] = $file;
                }
            }

            $data['content'] = $post['message'];

            $data['site_title'] = getSetting( 'site_title', 'site_settings');
            $logo = getSetting( 'site_logo', 'site_settings' );
            $data['logo'] = asset( 'uploads/settings/' . $logo );
            $data['date'] = digiTodayDateAdd();
            $data['invoice_url'] = route( 'admin.contracts.preview', [ 'slug' => $invoice->slug ] );
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
            'contract_id' => $id,
            'comments' => $comments,
            'operation_type' => $operation_type,
        );
        \Modules\Contracts\Entities\ContractHistory::create( $log );
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
            'contract_id' => $id,
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
            $data['account_id'] = ! empty( $post['account'] ) ? $post['account'] : null;
            $data['contract_id'] = $id;
            $data['paymentmethod'] = $post['paymethod'];
            $data['description'] = $post['description'];

            $record = \Modules\Contracts\Entities\ContractPayment::create( $data );

            $this->insertHistory( array('id' => $id, 'comments' => trans('contracts::custom.contracts.payment-inserted'), 'operation_type' => 'payment' ) );

           $account_details = \App\Account::find( $account_id );  

            // Let us add thhis account to the specified account.
            $amount = $data['amount'];
            $basecurrency = App\Currency::where('is_default', 'yes')->first();
            $contract = Contract::find($id);
            if ( $contract && $basecurrency ) {
                $amount = ( $amount / $contract->currency->rate ) * $basecurrency->rate;
            }
            if ( $account_details && ! empty( $data['account_id'] ) ){
            \App\Account::find($data['account_id'])->increment('initial_balance',$amount);
              }

            $response['status'] = 'success';
            $response['message'] = trans('custom.invoices.messages.save-success');

            flashMessage( 'success', 'restore', $response['message']);
            return json_encode( $response );
        }
    }

    public function changeStatus( $id, $status ) {
        if (! Gate::allows('contract_changestatus_access')) {
           return prepareBlockUserMessage();
        }

        if (! Gate::allows('contract_changestatus_' . preg_replace("/[^a-zA-Z]/", "", $status )) ) {
           return prepareBlockUserMessage();
        }

        $invoice = Contract::findOrFail($id);
        $invoice->paymentstatus = $status;
        $invoice->save();

        $this->insertHistory( array('id' => $id, 'comments' => trans('contracts::custom.contracts.status-changed-' . $status) ) );

        flashMessage( 'success', 'status' );

        return redirect()->route('admin.contracts.show', $id);
    }

    public function showPreview( $slug ) {
        if (! Gate::allows('contract_preview')) {
           return prepareBlockUserMessage();
        }

        $invoice = Contract::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $topbar = 'no';
        $sidebar = 'no';
        return view( 'contracts::admin.contracts.preview', compact('invoice', 'sidebar', 'topbar'));
    }

    public function invoicePDF( $slug, $operation = 'download') {

        if (! Gate::allows('contract_pdf_access')) {
           return prepareBlockUserMessage();
        }

        $invoice = Contract::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        if ( 'view' === $operation ) {
            if (! Gate::allows('contract_pdf_view')) {
                flashMessage('danger', 'not_allowed');
                return back();
            }
        } elseif ( 'print' === $operation ) {
            \Debugbar::disable();
            return view('contracts::admin.contracts.invoice.invoice-print', compact('invoice'));
        } else {
            if (! Gate::allows('contract_pdf_download')) {
                flashMessage('danger', 'not_allowed');
                return back();
            }
        }


        $file_name = $invoice->id . '_' . $invoice->invoice_no . '.pdf';
        $path = public_path() . '/uploads/contracts/' . $file_name;
        PDF::loadView('contracts::admin.contracts.invoice.invoice-content', compact('invoice'))->save( $path , true );
        
        if ( 'view' === $operation ) {
            return response()->file($path);
        } else {
            return response()->download($path);
        }
    }

    public function uploadDocuments( $slug ) {
        if (! Gate::allows('contract_upload')) {
           return prepareBlockUserMessage();
        }

        $invoice = Contract::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        return view( 'contracts::admin.contracts.invoice.uploads', compact('invoice'));
    }

    public function upload( UploadContractsRequest $request, $slug ) {
        if (! Gate::allows('contract_upload')) {
           return prepareBlockUserMessage();
        }
        
        $invoice = Contract::where('slug', '=', $slug)->first();
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


            $this->insertHistory( array('id' => $invoice->id, 'comments' => trans('contracts::custom.contracts.documents-uploaded') ) );

        flashMessage( 'success', 'create', trans('custom.invoices.upload-success'));
        return redirect()->route('admin.contracts.show', [$invoice->id]);
    }

    public function duplicate( $slug ) {
        if (! Gate::allows('contract_duplicate')) {
           return prepareBlockUserMessage();
        }

        $invoice = Contract::where('slug', '=', $slug)->first();

        if (! $invoice) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $newinvoice = $invoice->replicate();

        $invoice_no = getNextNumber('Contract');
        $newinvoice->invoice_no = $invoice_no;
        $newinvoice->paymentstatus = 'Delivered';
        $newinvoice->slug = md5(microtime());
        $newinvoice->created_by_id = Auth::User()->id;
        $newinvoice->save();


        $this->insertHistory( array('id' => $invoice->id, 'comments' => trans('contracts::custom.contracts.contract-created-duplicate') ) );

        flashMessage( 'success', 'create', trans('custom.invoices.duplicated-contract'));
        return redirect()->route('admin.contracts.show', [$newinvoice->id]);
    }

    public function convertToInvoice( $slug, $type ) {
        if (! Gate::allows('contract_convertinvoice')) {
           return prepareBlockUserMessage();
        }

        $contract = Contract::where('slug', '=', $slug)->first();

        if (! $contract) {
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
            'subject' => $contract->subject,
            'address' => $contract->address,
            'invoice_prefix' => $default_invoice_prefix,
            'show_quantity_as' => $contract->show_quantity_as,
            'invoice_no' => $contract->invoicenumberdisplay,
            'reference' => $contract->reference,
            'invoice_date' => ! empty( $contract->invoice_date ) ? Carbon::parse($contract->invoice_date)->format('Y-m-d') : date('Y-m-d', time()),
            'invoice_due_date' => ! empty( $contract->invoice_due_date ) ? Carbon::parse($contract->invoice_due_date)->format('Y-m-d') : date('Y-m-d', time()), 
            'customer_id' => $contract->customer_id,
            'currency_id' => $contract->currency_id,
            'tax_id' => $contract->tax_id,
            'discount_id' => $contract->discount_id,
            'amount' => $contract->amount,
            'products' => $contract->products,
            'paymentstatus' => 'Delivered',
            'contract_id' => $contract->id,
            'created_by_id' => Auth::id(),
            'delivery_address' => $contract->address,
            'sales_agent' => $contract->sale_agent,

            'invoice_notes' => $contract->invoice_notes,
            'admin_notes' => $contract->admin_notes,            
            'terms_conditions' => $contract->terms_conditions,
        );
        if ( 'convertsavedraft' === $type ) {
            $data['status'] = 'Draft';
        }
        $invoice = \App\Invoice::create($data);

        // Let us update invoice numebr with current ID.
        $invoice_no = $invoice->id;

        $invoice_start = getSetting( 'contract_start', 'contract-settings' );
        if ( is_numeric( $invoice_start ) ) {
            $invoice_no = $invoice_start + $invoice_no;
        }
        $invoice->invoice_no = $invoice_no;
        $invoice->save();
        $id  = $invoice->id;

        $this->insertHistoryInvoice( array('id' => $id, 'comments' => 'invoice-created', 'operation_type' => 'convert-from-contract' ) );

        $contract->invoice_id = $id;
        $contract->save();

        flashMessage( 'success', 'create', trans('contracts::custom.contracts.convertedtoinvoice'));
        return redirect()->route('admin.invoices.show', [$id]);
    }

    public function listTasks( $slug ) {
        if (! Gate::allows('contract_tasks')) {
           return prepareBlockUserMessage();
        }

        $contract = Contract::where('slug', '=', $slug)->first();

        if (! $contract) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        if (request()->ajax()) {
            $query = ContractTask::query();
            $query->with("recurring");
            $query->with("invoice");
            $query->with("created_by");
            $query->with("mile_stone");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('contract_task_delete')) {
            return abort(401);
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'contract_tasks.id',
                'contract_tasks.name',
                'contract_tasks.description',
                'contract_tasks.priority',
                'contract_tasks.startdate',
                'contract_tasks.duedate',
                'contract_tasks.datefinished',
                'contract_tasks.status',
                'contract_tasks.recurring_id',
                'contract_tasks.recurring_type',
                'contract_tasks.recurring_value',
                'contract_tasks.cycles',
                'contract_tasks.total_cycles',
                'contract_tasks.last_recurring_date',
                'contract_tasks.is_public',
                'contract_tasks.billable',
                'contract_tasks.billed',
                'contract_tasks.invoice_id',
                'contract_tasks.hourly_rate',
                'contract_tasks.milestone',
                'contract_tasks.kanban_order',
                'contract_tasks.milestone_order',
                'contract_tasks.visible_to_client',
                'contract_tasks.deadline_notified',
                'contract_tasks.created_by_id',
                'contract_tasks.mile_stone_id',
            ]);
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'contract_task_';
                $routeKey = 'admin.contract_tasks';

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
            $table->editColumn('recurring.subject', function ($row) {
                return $row->recurring ? $row->recurring->subject : '';
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
            $table->editColumn('invoice.contract_no', function ($row) {
                return $row->invoice ? $row->invoice->contract_no : '';
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

        return view('contracts::admin.contracts.contract_tasks.index', compact('contract'));
    }
    
    public function createTask($slug) {
        
    }
}
