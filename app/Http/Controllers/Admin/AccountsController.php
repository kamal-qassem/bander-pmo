<?php
namespace App\Http\Controllers\Admin;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAccountsRequest;
use App\Http\Requests\Admin\UpdateAccountsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;

class AccountsController extends Controller
{
    public function __construct() {
        $this->middleware('plugin:account');
    }

    /**
     * Display a listing of Account.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('account_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = Account::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('account_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'accounts.id',
                'accounts.name',
                'accounts.description',
                'accounts.initial_balance',
                'accounts.account_number',
                'accounts.contact_person',
                'accounts.phone',
                'accounts.url',
            ]);

            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'account_';
                $routeKey = 'admin.accounts';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('initial_balance', function ($row) {

                $total_in  = \App\Income::where('account_id', $row->id)->sum('amount');
                $total_out = \App\Expense::where('account_id', $row->id)->sum('amount');

            $number_fomat_in  = number_format( $total_in , 2 , "." , "" );
            $number_fomat_out = number_format( $total_out , 2 , "." , "" );

                return $row->initial_balance ? '<b>'.trans( 'custom.accounts.balance' ).' '.digiCurrency( $row->initial_balance ).'<br/>'.trans( 'custom.accounts.total-in' ).' '.digiCurrency( $number_fomat_in ).'<br/>'.trans( 'custom.accounts.total-out' ).' '.digiCurrency( $number_fomat_out ).'</b>': '';
            });
            $table->editColumn('account_number', function ($row) {
                return $row->account_number ? $row->account_number : '';
            });
            $table->editColumn('contact_person', function ($row) {
                return $row->contact_person ? $row->contact_person : '';
            });
            $table->editColumn('phone', function ($row) {
                return $row->phone ? $row->phone : '';
            });
            $table->editColumn('url', function ($row) {
                return $row->url ? $row->url : '';
            });

            $table->rawColumns(['actions','massDelete','initial_balance']);

            return $table->make(true);
        }

        return view('admin.accounts.index');
    }

    /**
     * Show the form for creating new Account.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('account_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.accounts.create');
    }

    /**
     * Store a newly created Account in storage.
     *
     * @param  \App\Http\Requests\StoreAccountsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('account_create')) {
            return prepareBlockUserMessage();
        }

        $rules = [
            'name' => 'required|unique:accounts,name',
            'initial_balance' => 'nullable|regex:/^\d+(\.\d{1,4})?$/'
            ,
            'phone' => 'nullable|phone_number',
            
        ];
        $validator = Validator::make($request->all(), $rules);
        if ( ! $validator->passes() ) {
            if ( $request->ajax() ) {
                return response()->json(['error'=>$validator->errors()->all()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $additional = array();
        if ( empty( $request->initial_balance ) ) {
            $additional['initial_balance'] = 0;
        }
        if ( empty( $request->description ) ) {
            $additional['description'] = $request->name;
        }
        if ( count( $additional ) > 0 ) { 
            $request->request->add( $additional ); //add additonal / Changed values to the request object.
        }
        
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $account = Account::create($request->all());

        if ( $request->ajax() ) {
            $account->selectedid = $request->selectedid;
            return response()->json(['success'=>trans( 'custom.messages.record_saved' ), 'record' => $account]);
        } else {
            flashMessage( 'success', 'create' );
            return redirect()->route('admin.accounts.index');
        }
    }


    /**
     * Show the form for editing Account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('account_edit')) {
            return prepareBlockUserMessage();
        }
        $account = Account::findOrFail($id);

        return view('admin.accounts.edit', compact('account'));
    }

    /**
     * Update Account in storage.
     *
     * @param  \App\Http\Requests\UpdateAccountsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountsRequest $request, $id)
    {
        if (! Gate::allows('account_edit')) {
            return prepareBlockUserMessage();
        }
        $account = Account::findOrFail($id);
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $account->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.accounts.index');
    }


    /**
     * Display Account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $list = '')
    {
        if (! Gate::allows('account_view')) {
            return prepareBlockUserMessage();
        }
        
        $account = Account::findOrFail($id);
        $incomes = '';
        $expenses = '';
        $transfers = '';
        return view('admin.accounts.show', compact('account', 'incomes', 'expenses', 'transfers', 'list'));
    }


    /**
     * Remove Account from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('account_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $account = Account::findOrFail($id);
        $account->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.accounts.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
            return redirect( $request->redirect_url );
        } else {
            return back();
        }
      }
    }

    /**
     * Delete all selected Account at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('account_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = Account::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore Account from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('account_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $account = Account::onlyTrashed()->findOrFail($id);
        $account->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete Account from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('account_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $account = Account::onlyTrashed()->findOrFail($id);
        $account->forceDelete();

        return back();
    }
}
