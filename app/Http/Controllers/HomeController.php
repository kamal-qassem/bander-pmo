<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use \Modules\Orders\Entities;
use Yajra\DataTables\DataTables;
use Artisan;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        
        if ( isCustomer() ) {
            $customer_id = Auth::user()->id;
            $invoices = \App\Invoice::withoutGlobalScopes(['is_recurring'])->where('customer_id',  $customer_id)->where('status',  'Published')->latest()->limit(5)->get(); 
            $quotes = \Modules\Quotes\Entities\Quote::where('customer_id',  $customer_id)->where('status', 'Published')->latest()->limit(5)->get(); 
            $orders = \Modules\Orders\Entities\Order::where('customer_id',  $customer_id)->latest()->limit(5)->get();
            
            $widgets = \App\DashBoard::select('dashboard_widgets.*', 'dwr.display_columns as columns')->join('dashboard_widgets_role as dwr', 'dwr.dash_board_id', '=', 'dashboard_widgets.id')->where('dwr.role_id', CUSTOMERS_TYPE)->where('status', 'active')->orderBy('dwr.display_order')->get();
            
            return view('home-customer', compact( 'invoices', 'quotes', 'orders', 'widgets' ));
        } else {            
            $widgets = '';
            $widgets_arr = [];
            $role_id = '';
            $roles = Auth::User()->role;
            if ( ! empty( $roles ) ) {
                $priority = $priority_old = 0;
                foreach ($roles as $role ) {
                    if ( $role->priority >= $priority_old ) {
                        $role_id = $role->id;
                    } elseif ( $roles->count() == 1 ) {
                        $role_id = $role->id;
                    }
                    $priority_old = $role->priority;
                }
            }
            if ( ! empty( $role_id ) ) {
                $widgets = \App\DashBoard::select('dashboard_widgets.*', 'dwr.display_columns as columns')->join('dashboard_widgets_role as dwr', 'dwr.dash_board_id', '=', 'dashboard_widgets.id')->where('dwr.role_id', $role_id)->where('status', 'active')->orderBy('dwr.display_order')->get();
                $widgets_arr = $widgets->pluck('slug')->toArray();
            }

            if ( in_array( 'income-amount', $widgets_arr ) ) {
                $incomes = \App\Income::latest()->limit(5)->get(); 
            }
            if ( in_array( 'expenses', $widgets_arr ) || in_array( 'recent-expenses', $widgets_arr )) {
                $expenses = \App\Expense::latest()->limit(5)->get(); 
            }
            if ( in_array( 'recent-invoices', $widgets_arr ) ) {
                $invoices = \App\Invoice::latest()->limit(5)->get(); 
            }
            if ( in_array( 'recent-quotes', $widgets_arr ) ) {
                $quotes = \Modules\Quotes\Entities\Quote::latest()->limit(5)->get();
            }
            if ( in_array( 'recent-orders', $widgets_arr ) ) {
                $orders = \Modules\Quotes\Entities\Quote::latest()->limit(5)->get();
            }
            
            $yearly_data = [
                'areachart' => [], // Orders
                'linechart' => [], // Orders
                'areachart_invoices' => [],
                'linechart_invoices' => [],
            ];
            $quarters = [];
            $start = new Carbon('first day of April');
            for($i=0; $i<4; $i++)
            {
                $title = 'Q' . ($i+1);
                $quarter_months = $title . '(' . $start->format('M') . ' - ' . $start->addMonth(3)->format('M') . ')';
                $quarters[] = [ 
                    'title' => $title,
                    'quarter_months' => $quarter_months,
                     ];
            }
            $last_sixth_year = date('Y')-6;
            for( $year = $last_sixth_year; $year <= date('Y'); $year++ ) {
                $labels = $labels_invoices = [];

                $start = new Carbon('first day of April ' . $year);
                for($i=0; $i<4; $i++)
                {
                    $quarter = 'Q' . ($i+1);
                    $labels[ $quarter ]['period'] = $start->format('Y') . ' ' . $quarter;
                    $labels[ $quarter ]['year'] = $start->format('Y');
                    $quarter_start_date = $start->format('Y-m-d');
                    $labels[ $quarter ]['quarter_months'] = $start->format('M');
                    $labels[ $quarter ]['end_date'] = $start->addMonth(3)->format('M Y');
                    $quarter_end_date = $start->format('Y-m-d');
                    $labels[ $quarter ]['quarter_months'] = $labels[ $quarter ]['quarter_months'] . ' - ' . $start->format('M');

                    $labels[ $quarter ]['lable'] = $quarter . ' '.$labels[ $quarter ]['quarter_months'].' ' . $labels[ $quarter ]['year'];
                    $order_payments_for_quarter = \Modules\Orders\Entities\Order::select('op.amount', 'products.name', 'products.id', 'orders.id as main_order_id', 'op.order_id', 'order_products.quantity')
                        ->join('orders_payments as op', 'op.order_id', '=', 'orders.id')
                        ->join('order_products', 'order_products.order_id', '=', 'orders.id')
                        ->join('products', 'products.id', '=', 'order_products.product_id')
                        ->where(DB::raw('date('.config('app.db_prefix').'op.created_at)'), '>=', $quarter_start_date)
                        ->where(DB::raw('date('.config('app.db_prefix').'op.created_at)'), '<=', $quarter_end_date)
                        ->whereIn('op.payment_status', ['success', 'Success'])
                        ->whereNotNull('op.amount');

                    $labels[ $quarter ]['amount'] = $order_payments_for_quarter->sum('op.amount');
                    

                    // Invoices amount report for charts.
                    $labels_invoices[ $quarter ]['period'] = $labels[ $quarter ]['period'];
                    $labels_invoices[ $quarter ]['year'] = $labels[ $quarter ]['year'];
                    $labels_invoices[ $quarter ]['quarter_months'] = $labels[ $quarter ]['quarter_months'];
                    $labels_invoices[ $quarter ]['end_date'] = $labels[ $quarter ]['end_date'];
                    $labels_invoices[ $quarter ]['quarter_months'] = $labels[ $quarter ]['quarter_months'];
                    $labels_invoices[ $quarter ]['lable'] = $labels[ $quarter ]['lable'];
                    $invoice_payments_for_quarter = \App\Invoice::withoutGlobalScopes(['is_recurring'])->select('invoice_payments.amount')
                        ->join('invoice_payments', 'invoice_payments.invoice_id', '=', 'invoices.id')
                        ->where(DB::raw('date('.config('app.db_prefix').'invoice_payments.created_at)'), '>=', $quarter_start_date)
                        ->where(DB::raw('date('.config('app.db_prefix').'invoice_payments.created_at)'), '<=', $quarter_end_date)
                        ->whereIn('invoice_payments.payment_status', ['success', 'Success'])->whereNotNull('invoice_payments.amount');                    
                   
                    $labels_invoices[ $quarter ]['amount'] = number_format( $invoice_payments_for_quarter->sum('invoice_payments.amount'), 2, '.', '' );
                }

                $yearly_data['areachart'][ $year ] = $labels;
                $yearly_data['areachart_invoices'][ $year ] = $labels_invoices;
            }

            $income = \App\Income::join('income_categories', 'income_categories.id', '=', 'incomes.income_category_id')->get()->sortBy('entry_date')->groupBy(function ($entry) {
                    return $entry->income_category->name;
                        })->map(function ($entries, $group) {
                return number_format( $entries->sum('amount'), 2, '.', '' );
            });
            $yearly_data['income'] = $income;

            $expenses_chart = \App\Expense::join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')->get()->sortBy('entry_date')->groupBy(function ($entry) {
                    return $entry->expense_category->name;
                        })->map(function ($entries, $group) {
                return number_format( $entries->sum('amount'), 2, '.', '' );
            });

            $yearly_data['expenses'] = $expenses_chart;

            $contacts_chart = \App\ContactType::where('status', 'active')->get()->groupBy(function ($entry) {
                return $entry->title;
            })->map(function ($entries, $group) {
                return $entries->sum(function ($entry) {
                    return \App\Contact::join('contact_contact_type', 'contact_contact_type.contact_id', 'contacts.id')->join('roles', 'roles.id', 'contact_contact_type.contact_type_id')->where('contact_type_id', $entry->id)->count('contacts.id');
                });
            });
            $yearly_data['contacts_chart'] = $contacts_chart;

            // Monthly Income Graph               
            $today = Carbon::today();
            $date_from = $from = date("Y-m-t", strtotime($today->subMonth(12)));

            $today = Carbon::today();
            $date_to = $to = date("Y-m-t", strtotime($today));

            $income_monthwise = \App\Income::whereBetween('entry_date', [$from, $to])->get()->sortBy('entry_date')->groupBy(function ($entry) {
                if ($entry->entry_date instanceof \Carbon\Carbon) {
                    return \Carbon\Carbon::parse($entry->entry_date)->format('M');
                }
                try {
                   return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->entry_date)->format('M');
                } catch (\Exception $e) {
                     return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->entry_date)->format('M');
                }        })->map(function ($entries, $group) {
                return number_format( $entries->sum('amount'), 2, '.', '' );
            });
            $yearly_data['income_monthwise'] = $income_monthwise;

         $data = [];
            if ( ! empty( $incomes  ) ) {
                $data['incomes']  = $incomes;
            }
            if ( ! empty( $expenses  ) ) {   
                $data['expenses'] = $expenses;
            }
            if ( ! empty( $invoices  ) ) {    
                $data['invoices'] = $invoices;
            }
            if ( ! empty( $quotes  ) ) {
                $data['quotes']   = $quotes;
            }   
            if ( ! empty( $orders_quanter_chart  ) ) {    
                $data['orders_quanter_chart']   = $orders_quanter_chart;
            }   
            if ( ! empty( $yearly_data  ) ) {    
                $data['yearly_data']   = $yearly_data;
            }
            if ( ! empty( $quarters  ) ) {   
                $data['quarters']   = $quarters;
            }   
            if ( ! empty( $widgets  ) ) {  
                $data['widgets']   = $widgets;
            }
            if ( ! empty( $orders  ) ) {   
                $data['orders']   = $orders;
            }
            return view('dashboard', $data);
            
        }
    }

    public function loadModal() {
        $action = request()->action;
        $id = request()->id;
        $type = request()->type;
        $selectedid = request()->selectedid;

        switch ( $action ) {
            case 'createsupplier':
            case 'createcustomer':
            case 'sale_agent':
            case 'createclient':
                $companies = \App\ContactCompany::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                $groups = \App\ContactGroup::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                $contact_types = \App\ContactType::get()->pluck('title', 'id');

                $languages = \App\Language::get()->pluck('language', 'id');

                $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');

                $countries_code = \App\Country::get()->pluck('title', 'dialcode')->prepend(trans('global.app_please_select'), '');

                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $fetchaddress = 'yes';
                $contact = '';
                $default_contact_type = CUSTOMERS_TYPE;
                if ( 'createsupplier' === $action ) {                    
                    $default_contact_type = SUPPLIERS_TYPE;
                } elseif ( 'sale_agent' === $action ) {
                    $default_contact_type = CONTACT_SALE_AGENT;                    
                } elseif ( 'createclient' === $action ) {
                    $default_contact_type = CONTACT_CLIENT_TYPE;                    
                } elseif ( 'createcustomer' === $action ) {
                    $default_contact_type = CUSTOMERS_TYPE;
                    $fetchaddress = 'yes';
                }
                $type = $default_contact_type;
                
                return view('admin.contacts.create-form', compact('companies', 'groups', 'contact_types', 'languages', 'countries', 'countries_code', 'topbar', 'sidebar', 'is_ajax', 'default_contact_type', 'fetchaddress', 'selectedid', 'type', 'contact'));
                break;
            case 'createproduct':
                $categories = \App\ProductCategory::get()->pluck('name', 'id');
                $taxes = \App\Tax::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                $discounts = \App\Discount::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                $ware_houses = \App\Warehouse::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                $brands = \App\Brand::where('status', 'Active')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
                $enum_product_status = \App\Product::$enum_product_status;

                $suppliers = \App\Contact::whereNull('deleted_at')->whereHas("contact_type",
                    function ($query) {
                        $query->where('id', SUPPLIERS_TYPE);
                    })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

                $supplier_id = 1;
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $row_id = $id;
                $master_products = 'no';
                if ( 'masterproduct' === $type ) {
                    $master_products = 'yes';
                }
                return view('admin.products.create-form', compact('categories', 'ware_houses', 'brands', 'taxes', 'discounts', 'enum_product_status', 'suppliers', 'supplier_id', 'topbar', 'sidebar', 'is_ajax', 'row_id', 'master_products', 'selectedid'));

            break;
            case 'createwarehouse':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $selectedid = 'ware_house_id';
                $row_id = '';
                return view('admin.warehouses.create-form', compact('topbar', 'sidebar', 'is_ajax', 'row_id', 'selectedid'));
            break;
            case 'createbrand':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $selectedid = 'ware_house_id';
                $enum_status = \App\Brand::$enum_status;
                return view('admin.brands.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid', 'enum_status'));
            break;
            case 'createtax':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $selectedid = 'tax_id';
                $enum_rate_type = \App\Tax::$enum_rate_type;
                return view('admin.taxes.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid', 'enum_rate_type'));
            break;
            case 'creatediscount':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $selectedid = 'tax_id';
                $enum_discount_type = \App\Discount::$enum_discount_type;
                return view('admin.discounts.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid', 'enum_discount_type'));
            break;
            case 'createaccount':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $selectedid = 'account_id';
                return view('admin.accounts.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid'));
            break;
            case 'createincomecategory':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $selectedid = 'income_category_id';
                return view('admin.income_categories.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid'));
            break;
            case 'createexpensecategory':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                return view('admin.expense_categories.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid'));
            break;
            case 'createrecurringperiod':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $fetchaddress = 'yes';
                return view('recurringperiods::admin.recurring_periods.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid', 'fetchaddress'));
            break;
            case 'createcompany':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $fetchaddress = 'yes';
                $countries = \App\Country::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
                return view('admin.contact_companies.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid', 'countries'));
            break;
            case 'creategroup':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                return view('admin.contact_groups.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid'));
            break;
            case 'createcountry':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                return view('admin.countries.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid'));
            break;
            case 'createdepartment':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                return view('admin.departments.create-form', compact('topbar', 'sidebar', 'is_ajax', 'selectedid'));
            break;
            case 'makeorderpayment':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $order = \Modules\Orders\Entities\Order::find( $id );
                return view('orders::admin.orders.make-payment', compact('topbar', 'sidebar', 'is_ajax', 'selectedid', 'order'));
            break;
            case 'sendcontactemail':
                $topbar = 'no';
                $sidebar = 'no';
                $is_ajax = 'yes';
                $contact = \App\Contact::find( $id );
                $template = \Modules\Templates\Entities\Template::where('key', 'contact-email')->first();
                return view('admin.contacts.mail.mail-form', compact('topbar', 'sidebar', 'is_ajax', 'action', 'selectedid', 'contact', 'template'));
            break;          
    
            default:
                # code...
                break;
        }
    }

    public function mediaDownload( $id )
    {
        $model          = config('medialibrary.media_model');
        $file           = $model::find($id);

        if ( ! $file ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        if ( env('UPLOAD_PATH') ) {
            $path = public_path(env('UPLOAD_PATH')) . '/' . $file->id . '/' . $file->file_name;
        } else {
            $path = storage_path('app/public/' . $file->id . '/' . $file->file_name );
        }

        if ( ! file_exists( $path ) ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        return response()->download($path);
    }

    public function mediaFileDownload( $model, $field, $id, $namespace = '' )
    {
        if ( ! empty( $namespace ) ) {
            $model = $namespace . '\\' . $model;
        } else {
            $model = '\App\\' . $model;
        }

        $record = $model::select( $field )->find($id);

        if ( ! $record ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $path = public_path(env('UPLOAD_PATH')) . '/' . $record->$field;

        if ( ! file_exists( $path ) ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        return response()->download($path);
    }

    public function dashboardWidgets( $role_id = '' )
    {
        
        if (request()->ajax()) {
            $query = \App\DashBoard::with('role_widgets')
            ->join('dashboard_widgets_role', 'dashboard_widgets_role.dash_board_id', '=', 'dashboard_widgets.id')
            ->join('roles', 'roles.id', '=', 'dashboard_widgets_role.role_id');
            if ( ! empty( $role_id ) ) {
                $query->where('dashboard_widgets.role_id', $role_id );
            }
            $template = 'actionsTemplate';
            
            $query->select([
                'dashboard_widgets.id',
                'dashboard_widgets.title',
                'dashboard_widgets.status',                
                'dashboard_widgets.type',
                'dashboard_widgets.slug',
                'roles.title as roletitle',
            ]);
            $query->orderBy( 'dashboard_widgets.title' );
            $query->groupBy( 'dashboard_widgets.id' );
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) {                
                $str = '<a href="'.route('admin.home.dashboard-widgets-add', $row->id).'" class="btn btn-xs btn-info">'.trans('global.app_edit').'</a>';
                if( config('app.debug') ) {
                    $str .= '<form method="POST" action="'.route('admin.home.dashboard-widgets-delete', $row->id).'" accept-charset="UTF-8" style="display: inline-block; padding: 5px;" onsubmit="return confirm(\''.trans('global.app_are_you_sure').'\');"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="'.csrf_token().'"><input class="btn btn-xs btn-danger" type="submit" value="'.trans('global.app_delete').'"></form>';
                }

                return $str;
            });

            $table->editColumn('title', function ($row) {
                return $row->title . ' ('.$row->slug.')';
            });
            
            $table->editColumn('role.title', function ($row) {
                if(count($row->role_widgets) == 0) {
                    return '';
                }

                return '<span class="label label-info label-many">' . implode('</span><span class="label label-info label-many"> ',
                        $row->role_widgets->pluck('title')->toArray()) . '</span>';
            });

            $table->rawColumns(['actions', 'role.title']);

            return $table->make(true);
        }
        return view('dashboard-parts.widgets', compact('role_id'));
    }

    public function dashboardWidgetsAll()
    {
        
        if (request()->ajax()) {
            $query = \App\DashBoard::query();
            $template = 'actionsTemplate';
            
            $query->select([
                'dashboard_widgets.id',
                'dashboard_widgets.title',
                'dashboard_widgets.status',
                'dashboard_widgets.type',
                'dashboard_widgets.slug',
            ]);
            $query->orderBy( 'title' );

            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) {                
                $str = '<a href="'.route('admin.home.dashboard-widgets-add', $row->id).'" class="btn btn-xs btn-info">'.trans('global.app_edit').'</a>';
                if( config('app.debug') ) {
                    $str .= '<form method="POST" action="'.route('admin.home.dashboard-widgets-delete', $row->id).'" accept-charset="UTF-8" style="display: inline-block; padding: 5px;" onsubmit="return confirm(\''.trans('global.app_are_you_sure').'\');"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="'.csrf_token().'"><input class="btn btn-xs btn-danger" type="submit" value="'.trans('global.app_delete').'"></form>';
                }

                return $str;
            });

            $table->editColumn('title', function ($row) {
                return $row->title . ' ('.$row->slug.')';
            });
            

            $table->rawColumns(['actions']);

            return $table->make(true);
        }
        return view('dashboard-parts.widgets-all');
    }

    /**
     * Show the form for creating new Account.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboardWidgetsAdd( \Illuminate\Http\Request $request, $id = '' )
    {
        $widget = \App\DashBoard::find($id);

        if ( $request->isMethod('post') )
        {
            if ( ! empty( $widget ) ) {
                $widget->update($request->all());
                flashMessage( 'success', 'update');
            } else {
                \App\DashBoard::create($request->all());
                flashMessage( 'success', 'create');
            }
            return redirect()->route('admin.home.dashboard-widgets-all');
        }

        return view('dashboard-parts.widgets-add', compact('widget'));
    }


    /**
     * Remove Income from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteWidget($id)
    {

        $widget = \App\DashBoard::findOrFail($id);

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $widget->delete();

        flashMessage( 'success', 'delete');

        return redirect()->route('admin.home.dashboard-widgets');
    }
    
    /**
     * Show the form for creating new Account.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboardWidgetsChangeorder( \Illuminate\Http\Request $request, $role_id = '' )
    {
        $widgets = \App\DashBoard::join('dashboard_widgets_role', 'dash_board_id', '=', 'dashboard_widgets.id')->where('dashboard_widgets_role.role_id', $role_id)->orderBy('dashboard_widgets_role.display_order')->get();

        if ( $widgets->count() == 0 ) {
            flashMessage( 'danger', 'create', trans('global.dashboard-widgets.no-widgets'));
            return redirect()->route('admin.home.dashboard-widgets');
        }

        if ( $request->isMethod('post') )
        {
            $order = $request->order;
            if( ! empty( $order )) {
                $start = 1;
                foreach ($order as $key => $value) {
                    $widget = DB::table('dashboard_widgets_role')->where('dash_board_id', $key)->where('role_id', $role_id)->first();
                    if ( $widget ) {
                        DB::table('dashboard_widgets_role')->where('dash_board_id', $key)->where('role_id', $role_id)->update(['display_order' => $start]);
                        $start++;
                    }
                }
            }
            flashMessage( 'success', 'update');
            return redirect()->route('admin.home.dashboard-widgets');
        }

        $role = \App\Role::find( $role_id );

        return view('dashboard-parts.widgets-changeorder', compact('widgets', 'role_id', 'role'));
    }

    public function dashboardWidgetsStatus()
    {
        $widget_id = request('widget_id');
        $widget = \App\DashBoard::find( $widget_id );
        if ( $widget ) {
            $widget->status = 'inactive';
            $widget->save();
        }
        return json_encode(['status' => 'success']);
    }

    public function dashboardWidgetsAssign( \Illuminate\Http\Request $request, $role_id = ''  )
    {
        $widgets = \App\DashBoard::join('dashboard_widgets_role', 'dash_board_id', '=', 'dashboard_widgets.id')->where('dashboard_widgets_role.role_id', $role_id)->orderBy('dashboard_widgets_role.display_order')->get();


        $role = \App\Role::find( $role_id );

        if ( $request->isMethod('post') )
        {
            
            $selected_widgets = $request->widgets;

            if ( empty( $selected_widgets ) ) {
                flashMessage( 'danger', 'create', trans('global.dashboard-widgets.no-widgets'));
                return redirect()->route('admin.home.dashboard-widgets-assign', $role_id);
            }

            $selected_widgets_new = [];

            $orders = $request->order;
            $columns = $request->columns;
            $key = 0;
            foreach ($selected_widgets as $widget_id => $on) {
                $selected_widgets_new[] = [
                    'dash_board_id' => $widget_id,
                    'display_order' => ! empty( $orders[ $widget_id ] ) ? $orders[ $widget_id ] : ( $key + 1 ),
                    'display_columns' => ! empty( $columns[ $widget_id ] ) ? $columns[ $widget_id ] : 2,
                    'role_id' => $role_id,
                ];
                $key++;
            }

            DB::table('dashboard_widgets_role')->where('role_id', $role_id)->delete();
            if ( ! empty( $selected_widgets_new ) ) {
                DB::table('dashboard_widgets_role')->insert( $selected_widgets_new );
            }

            flashMessage( 'success', 'update');
            return redirect()->route('admin.home.dashboard-widgets');
        }

        return view('dashboard-parts.widgets-assign', compact('widgets', 'role_id', 'role'));
    }

    public function systemReset( Request $request )
    {
        if ( $request->isMethod('post') ) {
            
            $prefix = config('app.db_prefix');
            $preserved_tables = [
                'master_settings',
                'smstemplates',
                'templates',
                'site_themes',
                'payment_gateways',
                'sms_gateways',
                'ticketit_settings',
                'permissions',
                'permission_role',
                'roles',
                'role_user',
                'contacts',
                'modules_managements',
                
                'dashboard_widgets',
                'dashboard_widgets_role',

                'migrations',
                'options',
                'project_billing_types',
                'ticketit_statuses',
                'contract_types',
                'task_statuses',
                'project_statuses',
                'currencies',
                'countries',
                'dynamic_options',
            ];

            DB::statement("SET foreign_key_checks=0");
            $databaseName = DB::getDatabaseName();
            $tables = DB::select("SELECT * FROM information_schema.tables WHERE table_schema = '$databaseName'");
            
            foreach ($tables as $table) {
                $name = str_replace($prefix, '', $table->TABLE_NAME);
                //if you don't want to truncate few tables
                if ( ! in_array( $name, $preserved_tables ) ) {
                    DB::table( $name )->truncate();
                }                
            }
            DB::statement("SET foreign_key_checks=1");

            $loggedin_user_id = Auth::id();
            $loggedin_user_email = Auth::user()->email;
            $default_accounts = [
                'admin@admin.com'     => ADMIN_TYPE,
                'sam@gmail.com'       => EXECUTIVE_TYPE,
                'domenic@gmail.com'   => CUSTOMERS_TYPE,
                'cieo@gmail.com'      => CONTACT_SALE_AGENT,
                'brent@gmail.com'     => SUPPLIERS_TYPE,
                'lavinia@gmail.com'   => SALES_MANAGER_TYPE,
                'himla@gmail.com'     => EMPLOYEES_TYPE,
                'merle@gmail.com'     => CONTACT_CLIENT_TYPE,
                'joanie@gmail.com'    => PROJECT_MANAGER,
                'robert@gmail.com'    => BUSINESS_MANAGER_TYPE,
                'donald@example.com'  => STOCK_MANAGER,
            ];
            $default_ids = [];
            foreach ($default_accounts as $email => $role ) {
                // $contact = DB::table('contacts')->where('email', $email)->first();
                $contact = \App\User::where('email', $email)->first();
                if ( $contact ) {
                    $default_ids[] = $contact->id;
                }
            }
            DB::table('contacts')->whereNotIn('id', $default_ids)->delete();
            DB::table('role_user')->whereNotIn('user_id', $default_ids)->delete();
            
            foreach ($default_accounts as $email => $role ) {
                // $contact = DB::table('contacts')->where('email', $email)->first();
                $contact = \App\User::where('email', $email)->first();
                if ( $contact ) {
                    $contact->contact_type()->sync([$role]);
                }
            }
            
            session()->forget('languages');
            session()->forget('plugins');
            session()->forget('settings');
            
            flashMessage( 'success', 'reset');
            return redirect()->route('admin.home.dashboard');
        }

        return view('system-reset');
    }

    public function fakeDataFunctions()
    {
        $contacts = \App\Contact::where('id', '!=', Auth::id())->get();
        foreach( $contacts as $contact ) {
            $contact_type = \App\ContactType::inRandomOrder()->take(1)->pluck('id');
            $contact->contact_type()->sync($contact_type);

            $language = \App\Language::inRandomOrder()->take(1)->pluck('id');
            $contact->language()->sync($language);
        }

        $users = \App\User::where('id', '!=', Auth::id())->get();
        foreach( $users as $user ) {
            $roles = \App\Contact::where('id', $user->id)->first()->contact_type->pluck('id')->toArray();
            $user->role()->sync($roles);
        }

        flashMessage( 'success', 'reset');
        return redirect()->route('admin.home.dashboard');
    }
}
