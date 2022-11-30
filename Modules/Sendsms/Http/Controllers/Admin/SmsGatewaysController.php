<?php

namespace Modules\Sendsms\Http\Controllers\Admin;

use Modules\Sendsms\SmsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Sendsms\Http\Requests\Admin\StoreSmsGatewaysRequest;
use Modules\Sendsms\Http\Requests\Admin\UpdateSmsGatewaysRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class SmsGatewaysController extends Controller
{
    /**
     * Display a listing of SmsGateway.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('sms_gateway_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = SmsGateway::query();
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('sms_gateway_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'sms_gateways.id',
                'sms_gateways.name',
                'sms_gateways.key',
                'sms_gateways.description',
            ]);
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'sms_gateway_';
                $routeKey = 'admin.sms_gateways';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('key', function ($row) {
                return $row->key ? $row->key : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('admin.sms_gateways.index');
    }

    /**
     * Show the form for creating new SmsGateway.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('sms_gateway_create')) {
            return prepareBlockUserMessage();
        }

        return view('admin.sms_gateways.create');
    }

    /**
     * Store a newly created SmsGateway in storage.
     *
     * @param  \App\Http\Requests\StoreSmsGatewaysRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSmsGatewaysRequest $request)
    {
        if (! Gate::allows('sms_gateway_create')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $sms_gateway = SmsGateway::create($request->all());


        flashMessage( 'success', 'create' );

        return redirect()->route('admin.sms_gateways.index');
    }


    /**
     * Show the form for editing SmsGateway.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('sms_gateway_edit')) {
            return prepareBlockUserMessage();
        }
        $sms_gateway = SmsGateway::findOrFail($id);

        return view('admin.sms_gateways.edit', compact('sms_gateway'));
    }

    /**
     * Update SmsGateway in storage.
     *
     * @param  \App\Http\Requests\UpdateSmsGatewaysRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSmsGatewaysRequest $request, $id)
    {
        if (! Gate::allows('sms_gateway_edit')) {
            return prepareBlockUserMessage();
        }
        $sms_gateway = SmsGateway::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $sms_gateway->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.sms_gateways.index');
    }


    /**
     * Display SmsGateway.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('sms_gateway_view')) {
            return prepareBlockUserMessage();
        }
        $send_sms = \App\SendSm::where('gateway_id', $id)->get();

        $sms_gateway = SmsGateway::findOrFail($id);

        return view('admin.sms_gateways.show', compact('sms_gateway', 'send_sms'));
    }


    /**
     * Remove SmsGateway from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('sms_gateway_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $sms_gateway = SmsGateway::findOrFail($id);
        $sms_gateway->delete();

        flashMessage( 'success', 'delete' );
        if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.sms_gateways.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
     }
    }

    /**
     * Delete all selected SmsGateway at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('sms_gateway_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }

        flashMessage( 'success', 'deletes' );

        if ($request->input('ids')) {
            $entries = SmsGateway::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }

            flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore SmsGateway from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('sms_gateway_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $sms_gateway = SmsGateway::onlyTrashed()->findOrFail($id);
        $sms_gateway->restore();

        flashMessage( 'success', 'restore' );
        return back();
    }

    /**
     * Permanently delete SmsGateway from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('sms_gateway_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $sms_gateway = SmsGateway::onlyTrashed()->findOrFail($id);
        $sms_gateway->forceDelete();

        return back();
    }
}
