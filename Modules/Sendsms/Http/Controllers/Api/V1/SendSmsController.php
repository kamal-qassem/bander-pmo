<?php

namespace Modules\Sendsms\Http\Controllers\Api\V1;

use App\SendSm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Sendsms\Http\Requests\Admin\StoreSendSmsRequest;
use Modules\Sendsms\Http\Requests\Admin\UpdateSendSmsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class SendSmsController extends Controller
{
    public function index()
    {
        return SendSm::all();
    }

    public function show($id)
    {
        return SendSm::findOrFail($id);
    }

    public function update(UpdateSendSmsRequest $request, $id)
    {
        $send_sm = SendSm::findOrFail($id);
        $send_sm->update($request->all());
        

        return $send_sm;
    }

    public function store(StoreSendSmsRequest $request)
    {
        $send_sm = SendSm::create($request->all());
        

        return $send_sm;
    }

    public function destroy($id)
    {
        $send_sm = SendSm::findOrFail($id);
        $send_sm->delete();
        return '';
    }
}
