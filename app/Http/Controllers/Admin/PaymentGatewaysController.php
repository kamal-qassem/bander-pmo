<?php

namespace App\Http\Controllers\Admin;

use App\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentGatewaysRequest;
use App\Http\Requests\Admin\UpdatePaymentGatewaysRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class PaymentGatewaysController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of PaymentGateway.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('payment_gateway_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = PaymentGateway::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('payment_gateway_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'payment_gateways.id',
                'payment_gateways.name',
                'payment_gateways.description',
                'payment_gateways.logo',
            ]);
            
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'payment_gateway_';
                $routeKey = 'admin.payment_gateways';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('logo', function ($row) {
                if($row->logo) { return '<a href="'. asset(env('UPLOAD_PATH').'/' . $row->logo) .'" target="_blank"><img src="'. asset(env('UPLOAD_PATH').'/thumb/' . $row->logo) .'"/>'; };
            });

            $table->rawColumns(['actions','massDelete','logo']);

            return $table->make(true);
        }

        return view('admin.payment_gateways.index');
    }

    /**
     * Show the form for creating new PaymentGateway.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('payment_gateway_create')) {
            return prepareBlockUserMessage();
        }
        return view('admin.payment_gateways.create');
    }

    /**
     * Store a newly created PaymentGateway in storage.
     *
     * @param  \App\Http\Requests\StorePaymentGatewaysRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentGatewaysRequest $request)
    {
        if (! Gate::allows('payment_gateway_create')) {
            return prepareBlockUserMessage();
        }
        $request = $this->saveFiles($request);
        $payment_gateway = PaymentGateway::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.payment_gateways.index');
    }


    /**
     * Show the form for editing PaymentGateway.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('payment_gateway_edit')) {
            return prepareBlockUserMessage();
        }
        $payment_gateway = PaymentGateway::findOrFail($id);

        return view('admin.payment_gateways.edit', compact('payment_gateway'));
    }

    /**
     * Update PaymentGateway in storage.
     *
     * @param  \App\Http\Requests\UpdatePaymentGatewaysRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentGatewaysRequest $request, $id)
    {
        if (! Gate::allows('payment_gateway_edit')) {
            return prepareBlockUserMessage();
        }
        $request = $this->saveFiles($request);
        $payment_gateway = PaymentGateway::findOrFail($id);
        $payment_gateway->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.payment_gateways.index');
    }


    /**
     * Display PaymentGateway.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('payment_gateway_view')) {
            return prepareBlockUserMessage();
        }
        $transfers = \App\Transfer::where('payment_method_id', $id)->get();$incomes = \App\Income::where('pay_method_id', $id)->get();$expenses = \App\Expense::where('payment_method_id', $id)->get();

        $payment_gateway = PaymentGateway::findOrFail($id);

        return view('admin.payment_gateways.show', compact('payment_gateway', 'transfers', 'incomes', 'expenses'));
    }


    /**
     * Remove PaymentGateway from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('payment_gateway_delete')) {
            return prepareBlockUserMessage();
        }
        $payment_gateway = PaymentGateway::findOrFail($id);
        $payment_gateway->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.payment_gateways.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected PaymentGateway at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('payment_gateway_delete')) {
            return prepareBlockUserMessage();
        }
        if ($request->input('ids')) {
            $entries = PaymentGateway::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore PaymentGateway from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('payment_gateway_delete')) {
            return prepareBlockUserMessage();
        }
        $payment_gateway = PaymentGateway::onlyTrashed()->findOrFail($id);
        $payment_gateway->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete PaymentGateway from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('payment_gateway_delete')) {
            return prepareBlockUserMessage();
        }
        $payment_gateway = PaymentGateway::onlyTrashed()->findOrFail($id);
        $payment_gateway->forceDelete();

        return back();
    }
}
