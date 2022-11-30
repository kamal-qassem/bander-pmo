<?php

namespace App\Http\Controllers\Admin;

use App\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExpensesRequest;
use App\Http\Requests\Admin\UpdateExpensesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ExpensesController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Expense.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {
        if (! Gate::allows('expense_access')) {
            return prepareBlockUserMessage();
        }

        if (request()->ajax()) {
            $query = Expense::query();
            $query->with("account");
            $query->with("expense_category");
            $query->with("payee");
            $query->with("payment_method");
            $template = 'actionsTemplate';
            
            $query->select([
                'expenses.id',
                'expenses.name',
                'expenses.account_id',
                'expenses.expense_category_id',
                'expenses.entry_date',
                'expenses.amount',
                'expenses.description',
                'expenses.payee_id',
                'expenses.payment_method_id',
                'expenses.ref_no',
                'expenses.slug',
                'expenses.currency_id',
                'expenses.project_id',
            ]);

            /**
             * when we call invoices display from other pages!
            */
            if ( ! empty( $type ) && 'account' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('expenses.account_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'expense_category' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('expenses.expense_category_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'contact' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('expenses.payee_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'pay_method' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('expenses.pay_method_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'currency' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('expenses.currency_id', $type_id);
                });
            }

            if ( ! empty( $type ) && 'project' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('expenses.project_id', $type_id);
                });
            }
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'expense_';
                $routeKey = 'admin.expenses';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('account.name', function ($row) {
                return $row->account ? $row->account->name : '';
            });
            $table->editColumn('amount', function ($row) {
               return $row->amount ?  digiCurrency( $row->amount, $row->currency_id )  : '';
            });
            $table->editColumn('expense_category.name', function ($row) {
                return $row->expense_category ? $row->expense_category->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->editColumn('entry_date', function ($row) {
                return $row->entry_date ? digiDate( $row->entry_date ) : '';
            });

            $table->editColumn('description_file', function ($row) {
                $build  = '';
                foreach ($row->getMedia('description_file') as $media) {
                    $build .= '<p class="form-group"><a href="' . route('admin.home.media-download', $media->id) . '" >' . $media->name . '</a></p>';
                }
                
                return $build;
            });
            $table->editColumn('payee.name', function ($row) {
                return $row->payee ? '<a href="'.route('admin.contacts.show', ['contact_id' => $row->payee->id, 'list' => 'expense']).'">' . $row->payee->name : '';
            });
            $table->editColumn('payment_method.name', function ($row) {
                return $row->payment_method ? $row->payment_method->name : '';
            });
            $table->editColumn('ref_no', function ($row) {
                return $row->ref_no ? $row->ref_no : '';
            });

            $table->editColumn('name', function ($row) {
                if ( ! empty( $row->project_id ) ) {
                    $title = trans('global.client-projects.expense-created-from-project', [ 'title' => '<a href="'.route('admin.client_projects.show', $row->project_id).'">' . '<b>'.$row->project->title .'</b>'. '</a>'] );
                       
                    return $row->name . '<p style="background-color:#a9d4d2;color:#0a2c54;">'.$title.'</p>';
                } else {
                    return $row->name ? $row->name : '';
                }
            });

            $table->rawColumns(['actions','massDelete','description_file', 'payee.name', 'name']);

            return $table->make(true);
        }

        // Total Expense.
        $total_expense = Expense::all()->sum('amount');

        // This month Expense.
        $today = Carbon::today();
        $date_from = $from = date("Y-m-01", strtotime($today));
        $date_to = $to = date("Y-m-t", strtotime($today));
        $total_expense_current_month = Expense::where('created_at', '>=', $date_from)->where('created_at', '<=', $date_to)->sum('amount');

        // This week Expense.
        
        $date_from = Carbon::today()->startOfWeek();
        $date_to = Carbon::today()->endOfWeek();
        
        $total_expense_current_week = Expense::where('created_at', '>=', $date_from)->where('created_at', '<=', $date_to)->sum('amount');

        // This Last 30 Days.
        $today = Carbon::today();
        $date_from = Carbon::today()->subDays(30);
        $total_expense_last_30_days = Expense::whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $today)->sum('amount');

        // Expenses Graph               
        $reportTitle_expense = 'Expenses Report';
        $reportLabel_expense = 'SUM';
        $chartType_expense   = 'bar';

        $results_expense = Expense::whereBetween('entry_date', [$from, $to])->get()->sortBy('entry_date')->groupBy(function ($entry) {
            if ($entry->entry_date instanceof \Carbon\Carbon) {
                return \Carbon\Carbon::parse($entry->entry_date)->format('Y-m-d');
            }
            try {
               return \Carbon\Carbon::createFromFormat(config('app.date_format'), digiDate($entry->entry_date))->format('Y-m-d');
            } catch (\Exception $e) {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', digiDate($entry->entry_date))->format('Y-m-d');
            }        })->map(function ($entries, $group) {
            return $entries->sum('amount');
        });

        return view('admin.expenses.index', compact('total_expense', 'total_expense_current_month', 'total_expense_current_week',
            'total_expense_last_30_days',
            'reportTitle_expense', 'results_expense', 'chartType_expense', 'reportLabel_expense'
        ));
    }

    /**
     * Show the form for creating new Expense.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $project_id = '' )
    {
        if (! Gate::allows('expense_create')) {
            return prepareBlockUserMessage();
        }
        
        $accounts = \App\Account::get()->pluck('namebalance', 'id')->prepend(trans('global.app_please_select'), '');
        $expense_categories = \App\ExpenseCategory::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $payees = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $payment_methods = \App\PaymentGateway::where('status', '=', 'Active')->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $projects = \App\ClientProject::all()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $taxes = \App\Tax::all()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $project = '';
        if ( ! empty( $project_id ) ) {
            $project = \App\ClientProject::find( $project_id );
            $currency_id = getDefaultCurrency('id');
            if ( ! empty( $project->currency_id ) ) {
                $currency_id = $project->currency_id;
            } elseif ( ! empty( $project->client->currency_id ) ) {
                $currency_id = $project->client->currency_id;
            }
            $currencies = \App\Currency::where('id', $currency_id)->get()->pluck('name', 'id');

            $payees = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CONTACT_CLIENT_TYPE);
                   })->where('contacts.id', $project->client_id)->get()->pluck('name', 'id');
        } else {
            $currencies = \App\Currency::all()->pluck('name', 'id');
        }
        $recurring_periods = \Modules\RecurringPeriods\Entities\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        

        return view('admin.expenses.create', compact('accounts', 'expense_categories', 'payees', 'payment_methods', 'projects', 'taxes', 'currencies', 'recurring_periods', 'project_id', 'project'));
    }

    /**
     * Store a newly created Expense in storage.
     *
     * @param  \App\Http\Requests\StoreExpensesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExpensesRequest $request)
    {
        if (! Gate::allows('expense_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }

        $currency_id = $request->currency_id;
        $payee_id = $request->payee_id;
        if ( ! empty( $request->project_id ) ) {
            $project = \App\ClientProject::find( $request->project_id );
            if ( $project ) {
                $currency_id = $project->client->currency_id;
                $payee_id = $project->client_id;
            }
        }

        if ( empty( $currency_id ) ) {
            $currency_id = getDefaultCurrency('id');
        }

        $addtional = array(
            'slug' =>  md5(microtime() . rand()),
            'currency_id' => $currency_id,
            'payee_id' => $payee_id,
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

         $date_set = getCurrentDateFormat();

         $additional = array(           
            'entry_date' => ! empty( $request->entry_date ) ? Carbon::createFromFormat($date_set, $request->entry_date)->format('Y-m-d') : NULL,
           
        );  
        $request->request->add( $additional ); 

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $expense = Expense::create($request->all());

        $amount = $request->amount;
        $basecurrency = \App\Currency::where('is_default', 'yes')->first();                
        if ( $basecurrency && ! empty($expense->currency->rate) ) {
            $amount = ( $amount / $expense->currency->rate ) * $basecurrency->rate;
        }       
        
        digiUpdateAccount( $request->account_id, $amount, 'desc' );


        foreach ($request->input('description_file_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $expense->id;
            $file->save();
        }

        flashMessage( 'success', 'create');

       if ( ! empty( $request->project_id ) ) {
            return redirect()->route('admin.client_projects.expenses', $request->project_id );
        } else {
            return redirect()->route('admin.expenses.index');
        }
    }


 
    /**
     * [edit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function edit($id)
    {
        if (! Gate::allows('expense_edit')) {
            return prepareBlockUserMessage();
        }
        
        $expense = Expense::findOrFail($id);
        $project_id = $expense->project_id;

        $accounts = \App\Account::get()->pluck('namebalance', 'id')->prepend(trans('global.app_please_select'), '');
        $expense_categories = \App\ExpenseCategory::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $payees = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $payment_methods = \App\PaymentGateway::where('status', '=', 'Active')->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $projects = \App\ClientProject::all()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        $taxes = \App\Tax::all()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        
        $project = '';
        if ( ! empty( $project_id ) ) {
            $project = \App\ClientProject::find( $project_id );
            $currency_id = getDefaultCurrency('id');
            if ( ! empty( $project->currency_id ) ) {
                $currency_id = $project->currency_id;
            } elseif ( ! empty( $project->client->currency_id ) ) {
                $currency_id = $project->client->currency_id;
            }
            $currencies = \App\Currency::where('id', $currency_id)->get()->pluck('name', 'id');

            $payees = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CONTACT_CLIENT_TYPE);
                   })->where('contacts.id', $project->client_id)->get()->pluck('name', 'id');
        } else {
            $currencies = \App\Currency::all()->pluck('name', 'id');
        }
        $recurring_periods = \Modules\RecurringPeriods\Entities\RecurringPeriod::get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
        
        return view('admin.expenses.edit', compact('expense', 'accounts', 'expense_categories', 'payees', 'payment_methods', 'projects', 'taxes', 'currencies', 'recurring_periods', 'project_id', 'project'));
    }

    /**
     * Update Expense in storage.
     *
     * @param  \App\Http\Requests\UpdateExpensesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExpensesRequest $request, $id)
    {
        if (! Gate::allows('expense_edit')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $expense = Expense::findOrFail($id);

        
         // Let us remove the previous amount so that we can update OR add new value.

        digiUpdateAccount( $expense->account_id, $expense->amount, 'inc' );

        $currency_id = $request->currency_id;
        $payee_id = $request->payee_id;
        if ( ! empty( $request->project_id ) ) {
            $project = \App\ClientProject::find( $request->project_id );
            if ( $project ) {
                $currency_id = $project->client->currency_id;
                $payee_id = $project->client_id;
            }
        }

        if ( empty( $currency_id ) ) {
            $currency_id = getDefaultCurrency('id');
        }

         $date_set = getCurrentDateFormat();

         $additional = array(           
            'entry_date' => ! empty( $request->entry_date ) ? Carbon::createFromFormat($date_set, $request->entry_date)->format('Y-m-d') : NULL,
            'currency_id' => $currency_id,
            'payee_id' => $payee_id,
           
        );  
        $request->request->add( $additional ); 
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        $expense->update($request->all());
        
        $expense = $expense->fresh();

        $amount = $request->amount;
        $basecurrency = \App\Currency::where('is_default', 'yes')->first();                
        if ( $basecurrency && ! empty($expense->currency->rate) ) {
            $amount = ( $amount / $expense->currency->rate ) * $basecurrency->rate;
        }
         // Let us remove the previous amount so that we can update OR add new value.
        digiUpdateAccount( $expense->account_id, $amount, 'desc' );


        $media = [];
        foreach ($request->input('description_file_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $expense->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $expense->updateMedia($media, 'description_file');

        flashMessage( 'success', 'update');

        if ( ! empty( $request->project_id ) ) {
            return redirect()->route('admin.client_projects.expenses', $request->project_id );
        } else {
            return redirect()->route('admin.expenses.index');
       }
    }


    /**
     * Display Expense.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('expense_view')) {
            return prepareBlockUserMessage();
        }
        $expense = Expense::findOrFail($id);

        return view('admin.expenses.show', compact('expense'));
    }


    /**
     * Remove Expense from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('expense_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $expense = Expense::findOrFail($id);
       
        digiUpdateAccount( $expense->account_id, $expense->amount, 'inc' );

        $expense->deletePreservingMedia();

        flashMessage( 'success', 'delete');

        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.expenses.index');
        } else {
            if ( ! empty( $request->redirect_url ) ) {
               return redirect( $request->redirect_url );
            } else {
               return back();
            } // We are deleting records from different pages, so let us back to the same page.
        }
    }

    /**
     * Delete all selected Expense at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('expense_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Expense::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                
                digiUpdateAccount( $entry->account_id, $entry->amount, 'inc' );

                $entry->deletePreservingMedia();
            }

            flashMessage( 'success', 'deletes');
        }
    }

}
