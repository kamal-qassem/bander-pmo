<?php

namespace Modules\ModulesManagement\Http\Controllers\Api\V1;

use Modules\ModulesManagement\Entities\ModulesManagement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\ModulesManagement\Http\Requests\Admin\StoreModulesManagementsRequest;
use Modules\ModulesManagement\Http\Requests\Admin\UpdateModulesManagementsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ModulesManagementsController extends Controller
{
    public function index()
    {
        return ModulesManagement::all();
    }

    public function show($id)
    {
        return ModulesManagement::findOrFail($id);
    }

    public function update(UpdateSendSmsRequest $request, $id)
    {
        $send_sm = ModulesManagement::findOrFail($id);
        $send_sm->update($request->all());
        

        return $send_sm;
    }

    public function store(StoreSendSmsRequest $request)
    {
        $send_sm = ModulesManagement::create($request->all());
        

        return $send_sm;
    }

    public function destroy($id)
    {
        $send_sm = ModulesManagement::findOrFail($id);
        $send_sm->delete();
        return '';
    }
}
