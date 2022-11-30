<?php

namespace Modules\Sendsms\Http\Controllers\Admin;

use Modules\Sendsms\Entities\SendSm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Sendsms\Http\Requests\Admin\StoreSendSmsRequest;
use Modules\Sendsms\Http\Requests\Admin\UpdateSendSmsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
class SendSmsController extends Controller
{   
    public function __construct() {
     $this->middleware('plugin:Sendsms');
    }
    /**
     * Display a listing of SendSm.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('send_sm_access')) {
            return prepareBlockUserMessage();
        }


        
        if (request()->ajax()) {
            $query = SendSm::query();
            $query->with("gateway");
            $template = 'actionsTemplate';
            if(request('show_deleted') == 1) {
                
        if (! Gate::allows('send_sm_delete')) {
            return prepareBlockUserMessage();
        }
                $query->onlyTrashed();
                $template = 'restoreTemplate';
            }
            $query->select([
                'send_sms.id',
                'send_sms.send_to',
                'send_sms.message',
                'send_sms.gateway_id',
            ]);

            $query->orderBy('id', 'desc');
            $table = Datatables::of($query);

            $table->setRowAttr([
                'data-entry-id' => '{{$id}}',
            ]);
            $table->addColumn('massDelete', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($template) {
                $gateKey  = 'send_sm_';
                $routeKey = 'admin.send_sms';

                return view($template, compact('row', 'gateKey', 'routeKey'));
            });
            $table->editColumn('send_to', function ($row) {
                return $row->send_to ? $row->send_to : '';
            });
            $table->editColumn('message', function ($row) {
                return $row->message ? $row->message : '';
            });
            $table->editColumn('gateway.name', function ($row) {
                return $row->gateway ? $row->gateway->name : '';
            });

            $table->rawColumns(['actions','massDelete']);

            return $table->make(true);
        }

        return view('sendsms::admin.send_sms.index');
    }

    /**
     * Show the form for creating new SendSm.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('send_sm_create')) {
            return prepareBlockUserMessage();
        }
        
        $gateways = \Modules\Sendsms\Entities\SmsGateway::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $clients = \App\Contact::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');


        return view('sendsms::admin.send_sms.create', compact('gateways', 'clients'));
    }

    public function getUserById() {
        if (request()->ajax()) {
            $contact_id = request('contact_id');
            $contact = \App\Contact::find( $contact_id );
            
            $status = 'success';
            $message = '';
            $edit_message = '';
            $data = array(
                'contact_id' => $contact_id,
                'phone' => '',
            );
            if ( $contact ) {
                $contact->phone = $contact->phone1_code . $contact->phone1;
                $data['phone'] = $contact->phone1;
                if ( empty( $contact->phone ) ) {
                    $contact->phone = $contact->phone2_code . $contact->phone2;
                    $data['phone'] = $contact->phone2;
                }
                if ( empty( $contact->phone ) ) {
                    $status = 'danger';
                    $message = trans('sendsms::global.send-sms.dont-have-phone');
                    $edit_message = trans('sendsms::global.send-sms.dont-have-phone-edit', ['url' => route('admin.contacts.edit', $contact->id)]);
                } else {
                    $data['contact'] = $contact->toArray();
                }
             } else {
                $status = 'danger';
                $message = trans('custom.messages.not_found');
            }

            $response = array(
                'status' => $status,
                'message' => $message,
                'data' => $data,
                'edit_message' => $edit_message,
            );
            return response()->json( $response );
        }
    }

    /**
     * Store a newly created SendSm in storage.
     *
     * @param  \App\Http\Requests\StoreSendSmsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSendSmsRequest $request)
    {
        if (! Gate::allows('send_sm_create')) {
            flashMessage('danger', 'not-allowed');
            return back();
        }

        $default_sms_gateway = getSetting( 'default_sms_gateway', 'site_settings', '');
		
        if ( empty( $default_sms_gateway )) {
            flashMessage('danger', 'create', 'sendsms::global.send-sms.no-gateway');
            return back();
        }
        
        $data['tonumber']  = $request->send_to;
        $data['content'] = $request->message;

        $res = SendSm::sendSms( $data );
     
        if ( ! empty( $res['status'] ) && 'failed' === $res['status'] ) {
            flashMessage('danger', 'create', $res['message'] );
        } else {
            $gateway = \Modules\Sendsms\Entities\SmsGateway::where('key', '=', $default_sms_gateway )->withTrashed()->first();
            $addtional = array(
                'gateway_id' => ! empty( $gateway ) ? $gateway->id : NULL,
                'status' => $res['status'],
                'gateway_response' => json_encode( $res ),
            );
            $request->request->add( $addtional ); //add additonal / Changed values to the request object.
            if ( isDemo() ) {
             return prepareBlockUserMessage( 'info', 'crud_disabled' );
            }
            $send_sm = SendSm::create($request->all());
            flashMessage($res['status'], 'create', $res['message'] );
        }

        return redirect()->route('admin.send_sms.index');
    }


    /**
     * Show the form for editing SendSm.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('send_sm_edit')) {
            return prepareBlockUserMessage();
        }
        
        $gateways = \Modules\Sendsms\Entities\SmsGateway::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');

        $send_sm = SendSm::findOrFail($id);

        return view('sendsms::admin.send_sms.edit', compact('send_sm', 'gateways'));
    }

    /**
     * Update SendSm in storage.
     *
     * @param  \App\Http\Requests\UpdateSendSmsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSendSmsRequest $request, $id)
    {
        if (! Gate::allows('send_sm_edit')) {
            return prepareBlockUserMessage();
        }
        $send_sm = SendSm::findOrFail($id);
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $send_sm->update($request->all());
		
		$default_sms_gateway = getSetting( 'default_sms_gateway', 'site_settings', '');
		

        if ( empty( $default_sms_gateway )) {
            flashMessage('danger', 'create', 'sendsms::global.send-sms.no-gateway');
            return back();
        }
        
        $data['tonumber']  = $request->send_to;
        $data['content'] = $request->message;

        $res = SendSm::sendSms( $data );

        if ( ! empty( $res['status'] ) && 'failed' === $res['status'] ) {
            flashMessage('danger', 'create', $res['message'] );
        } else {
            $gateway = \Modules\Sendsms\Entities\SmsGateway::where('key', '=', $default_sms_gateway )->withTrashed()->first();
            $addtional = array(
                'gateway_id' => ! empty( $gateway ) ? $gateway->id : NULL,
                'status' => $res['status'],
                'gateway_response' => json_encode( $res ),
            );
            $request->request->add( $addtional ); //add additonal / Changed values to the request object.

            $send_sm = SendSm::create($request->all());
            flashMessage( 'success', 'update' );
        }
        return redirect()->route('admin.send_sms.index');
    }


    /**
     * Display SendSm.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('send_sm_view')) {
            return prepareBlockUserMessage();
        }
        $send_sm = SendSm::findOrFail($id);

        return view('sendsms::admin.send_sms.show', compact('send_sm'));
    }


    /**
     * Remove SendSm from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! Gate::allows('send_sm_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $send_sm = SendSm::findOrFail($id);
        $send_sm->delete();

        flashMessage( 'success', 'delete' );
         if ( isSame(url()->current(), url()->previous()) ) {
            return redirect()->route('admin.send_sms.index');
        } else {
        if ( ! empty( $request->redirect_url ) ) {
           return redirect( $request->redirect_url );
        } else {
           return back();
        }
      }
    }

    /**
     * Delete all selected SendSm at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('send_sm_delete')) {
            return prepareBlockUserMessage();
        }


        if ($request->input('ids')) {
            $entries = SendSm::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        flashMessage( 'success', 'deletes' );
        }
    }


    /**
     * Restore SendSm from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('send_sm_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $send_sm = SendSm::onlyTrashed()->findOrFail($id);
        $send_sm->restore();

        flashMessage( 'success', 'restore' );    
        return back();
    }

    /**
     * Permanently delete SendSm from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('send_sm_delete')) {
            return prepareBlockUserMessage();
        }
        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $send_sm = SendSm::onlyTrashed()->findOrFail($id);
        $send_sm->forceDelete();

        return back();
    }
}
