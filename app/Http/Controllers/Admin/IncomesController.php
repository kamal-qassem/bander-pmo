<?php

namespace App\Http\Controllers\Admin;

use App\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIncomesRequest;
use App\Http\Requests\Admin\UpdateIncomesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use PDF;
use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Notification;
class IncomesController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Income.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $type = '', $type_id = '' )
    {
        if (! Gate::allows('income_access')) {
            return prepareBlockUserMessage();
        }
        
        if (request()->ajax()) {
            $query = Income::query();
            $query->with("account");
            $query->with("income_category");
            $query->with("payer");
            $query->with("pay_method");
            $template = 'actionsTemplate';
            
            $query->select([
                'incomes.id',
                'incomes.account_id',
                'incomes.income_category_id',
                'incomes.entry_date',
                'incomes.amount',
                'incomes.description',
                'incomes.payer_id',
                'incomes.pay_method_id',
                'incomes.ref_no',
                'incomes.slug',
            ]);

            /**
             * when we call invoices display from other pages!
            */
            if ( ! empty( $type ) && 'account' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('incomes.account_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'income_category' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('incomes.income_category_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'contact' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('incomes.payer_id', $type_id);
                });
            }
            if ( ! empty( $type ) && 'pay_method' === $type ) {
                $query->when($type_id, function ($q, $type_id) { 
                    return $q->where('incomes.pay_method_id', $type_id);
                });
            }
            
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'income_';
                $routeKey = 'admin.incomes';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('amount', function ($row) {
                return $row->amount ? digiCurrency( $row->amount ) : '';
            });
            $table->editColumn('account.name', function ($row) {
                return $row->account ? $row->account->name : '';
            });
            $table->editColumn('income_category.name', function ($row) {
                return $row->income_category ? $row->income_category->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('description_file', function ($row) {
                $build  = '';
                foreach ($row->getMedia('description_file') as $media) {
                    $build .= '<p class="form-group"><a href="' . route('admin.home.media-download', $media->id) . '" >' . $media->name . '</a></p>';
                }
                
                return $build;
            });
            $table->editColumn('payer.first_name', function ($row) {
                return $row->payer ? '<a href="'.route('admin.contacts.show', ['contact_id' => $row->payer->id, 'list' => 'income']).'">' . $row->payer->name : '';
            });
            $table->editColumn('pay_method.name', function ($row) {
                return $row->pay_method ? $row->pay_method->name : '';
            });
            $table->editColumn('entry_date', function ($row) {
                return $row->entry_date ? $row->entry_date : '';
            });
            $table->editColumn('ref_no', function ($row) {
                return $row->ref_no ? $row->ref_no : '';
            });

            $table->rawColumns(['actions','massDelete','description_file', 'payer.first_name']);

            return $table->make(true);
        }

        // Total Income.
        $total_income = Income::all()->sum('amount');

        // This month Income.
        $today = Carbon::today();
        $date_from = $from = date("Y-m-01", strtotime($today));
        $date_to = $to = date("Y-m-t", strtotime($today));
        $total_income_current_month = Income::where('created_at', '>=', $date_from)->where('created_at', '<=', $date_to)->sum('amount');

        // This week Income.
        // $today = Carbon::today();
        $date_from = Carbon::today()->startOfWeek();
        $date_to = Carbon::today()->endOfWeek();
        
        $total_income_current_week = Income::where('created_at', '>=', $date_from)->where('created_at', '<=', $date_to)->sum('amount');

        // This Last 30 Days.
        $today = Carbon::today();
        $date_from = Carbon::today()->subDays(30);
        $total_income_last_30_days = Income::whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $today)->sum('amount');

        // Income Graph               
        $reportTitle = 'Income Report';
        $reportLabel = 'SUM';
        $chartType   = 'bar';

        $results = Income::whereBetween('entry_date', [$from, $to])->get()->sortBy('entry_date')->groupBy(function ($entry) {
            if ($entry->entry_date instanceof \Carbon\Carbon) {
                return \Carbon\Carbon::parse($entry->entry_date)->format('Y-m-d');
            }
            try {
               return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->entry_date)->format('Y-m-d');
            } catch (\Exception $e) {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->entry_date)->format('Y-m-d');
            }        })->map(function ($entries, $group) {
            return $entries->sum('amount');
        });
        
        return view('admin.incomes.index', compact('total_income', 'total_income_current_month', 'total_income_current_week',
            'total_income_last_30_days',
            'reportTitle', 'results', 'chartType', 'reportLabel'
        ));
    }

    /**
     * Show the form for creating new Income.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('income_create')) {
            return prepareBlockUserMessage();
        }
        
        $accounts = \App\Account::get()->pluck('namebalance', 'id')->prepend(trans('global.app_please_select'), '');
        $income_categories = \App\IncomeCategory::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $payers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $pay_methods = \App\PaymentGateway::where('status', '=', 'Active')->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        return view('admin.incomes.create', compact('accounts', 'income_categories', 'payers', 'pay_methods'));
    }

    /**
     * Store a newly created Income in storage.
     *
     * @param  \App\Http\Requests\StoreIncomesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIncomesRequest $request)
    {
        if (! Gate::allows('income_create')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $addtional = array(
            'slug' =>  md5(microtime() . rand()),
            'original_amount' => $request->amount,
            'original_currency_id' => getDefaultCurrency('id'),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

        $date_set = getCurrentDateFormat();

         $additional = array(           
            'entry_date' => ! empty( $request->entry_date ) ? Carbon::createFromFormat($date_set, $request->entry_date)->format('Y-m-d') : NULL,
         
        );   
       $request->merge($additional);
       if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $income = Income::create($request->all());
        if( ! empty( $request->account_id ) ){
        \App\Account::find($request->account_id)->increment('initial_balance', $request->amount);
      }
        
        foreach ($request->input('description_file_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $income->id;
            $file->save();
        }

        flashMessage( 'success', 'create');

        return redirect()->route('admin.incomes.index');
    }


    /**
     * Show the form for editing Income.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('income_edit')) {
            return prepareBlockUserMessage();
        }
        
        $accounts = \App\Account::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $income_categories = \App\IncomeCategory::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $payers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $pay_methods = \App\PaymentGateway::where('status', '=', 'Active')->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $income = Income::findOrFail($id);

        return view('admin.incomes.edit', compact('income', 'accounts', 'income_categories', 'payers', 'pay_methods'));
    }

    /**
     * Update Income in storage.
     *
     * @param  \App\Http\Requests\UpdateIncomesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateIncomesRequest $request, $id)
    {
        if (! Gate::allows('income_edit')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
        }
        $income = Income::findOrFail($id);

        
        digiUpdateAccount( $income->account_id, $income->amount, 'desc');
        
         // Let us remove the previous amount so that we can update OR add new value.
        
        $addtional = array(
            'original_amount' => $request->amount,
            'original_currency_id' => getDefaultCurrency('id'),
        );
        $request->request->add( $addtional ); //add additonal / Changed values to the request object.

      
        $date_set = getCurrentDateFormat();

         $additional = array(           
            'entry_date' => ! empty( $request->entry_date ) ? Carbon::createFromFormat($date_set, $request->entry_date)->format('Y-m-d') : NULL,
         
        );    
       $request->merge($additional);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $income->update($request->all());

        $income = $income->fresh();

        
        digiUpdateAccount( $income->account_id, $income->amount, 'inc');

        $media = [];
        foreach ($request->input('description_file_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $income->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $income->updateMedia($media, 'description_file');

        flashMessage( 'success', 'update' );

        return redirect()->route('admin.incomes.index');
    }

    /**
     * Update Income in storage.
     *
     * @param  \App\Http\Requests\UpdateIncomesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function receipt( $slug )
    {
        
        if (! Gate::allows('income_receipt')) {
            flashMessage( 'danger', 'create', trans('custom.common.not-allowed'));
            return redirect()->back();
        }
            
        $income = Income::where(['slug' => $slug])->first();
        if ( ! $income ) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }
        return view('admin.incomes.receipt', compact('income'));
    }


    /**
     * Display Income.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('income_view')) {
            return prepareBlockUserMessage();
        }


        $income = Income::findOrFail($id);

        return view('admin.incomes.show', compact('income'));
    }


    /**
     * Remove Income from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('income_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $income = Income::findOrFail($id);

        
        

        if(! empty( $income->account_id ) ){
        digiUpdateAccount( $income->account_id, $income->amount, 'desc');         
         }

        $income->deletePreservingMedia();

        flashMessage( 'success', 'delete');
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.incomes.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        } // We are deleting records from different pages, so let us back to the same page.
     }
    }

    /**
     * Delete all selected Income at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('income_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Income::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {               
                
                digiUpdateAccount( $entry->account_id, $entry->amount, 'desc');
                $entry->deletePreservingMedia();            
            }
            flashMessage( 'success', 'deletes');
        }
    }

    public function mailReceipt() {
        if (request()->ajax()) {
            $action = request('action');
            $id = request('invoice_id');

            $income = Income::findOrFail($id);
            $customer = $income->payer()->first();

            $action = substr($action, 0, -4);
            
            $file_name = $id . '.pdf';                
            PDF::loadView('admin.incomes.receipt-content-pdf', compact('income'))->save(  public_path() . '/uploads/receipts/' . $file_name, true );

            $to_email = ! empty( $customer->email ) ? $customer->email : '';
            if ( ! empty( $to_email ) ) {
                $templatedata = array(
                    'to_email' => $to_email,
                    'client_name' => $customer->name,
                    
                    'site_address' => getSetting( 'site_address', 'site_settings'),
                    'site_phone' => getSetting( 'site_phone', 'site_settings'),
                    'site_email' => getSetting( 'contact_email', 'site_settings'),                
                    'site_title' => getSetting( 'site_title', 'site_settings'),
                    'logo' => asset( 'uploads/settings/' . getSetting( 'site_logo', 'site_settings' ) ),
                    'date' => digiTodayDate(),
                    'site_url' => env('APP_URL'),
                );

                $data = array(
                    'attachment' => public_path() . '/uploads/receipts/' . $file_name,
                    'template' => 'payment-acknowledgment',
                    'data' => $templatedata,
                );
                $customer->notify(new QA_EmailNotification($data));

                return json_encode( array( 'status' => 'success' ) );
            }
        }
    }

    public function receiptPDF( $slug, $operation = 'download') {

        if (! Gate::allows('invoice_pdf_view') && ! Gate::allows('invoice_pdf_download')) {
            return prepareBlockUserMessage();
        }

        $income = Income::where('slug', '=', $slug)->first();

        if (! $income) {
            flashMessage( 'danger', 'create', trans('custom.settings.no_records_found'));
            return redirect()->back();
        }

        $file_name = $income->id . '.pdf'; 
        $path = public_path() . '/uploads/receipts/' . $file_name;               
        PDF::loadView('admin.incomes.receipt-content-pdf', compact('income'))->save(  $path, true );
        
        if ( 'view' === $operation ) {
            return response()->file($path);
        } elseif ( 'print' === $operation ) {
            \Debugbar::disable();
            return view('admin.incomes.receipt-content-print', compact('income'));
        } else {
            return response()->download($path);
        }
    }

}
