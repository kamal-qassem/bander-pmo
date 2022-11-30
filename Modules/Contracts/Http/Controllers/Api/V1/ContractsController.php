<?php

namespace Modules\Contracts\Http\Controllers\Api\V1;

use Modules\Contracts\Entities\Contract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContractsRequest;
use App\Http\Requests\Admin\UpdateContractsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ContractsController extends Controller
{
    public function index()
    {
        return Contract::all();
    }

    public function show($id)
    {
        return Contract::findOrFail($id);
    }

    public function update(UpdateContractsRequest $request, $id)
    {
        $recurring_invoice = Contract::findOrFail($id);
        $recurring_invoice->update($request->all());
        

        return $recurring_invoice;
    }

    public function store(StoreContractsRequest $request)
    {
        $recurring_invoice = Contract::create($request->all());
        

        return $recurring_invoice;
    }

    public function destroy($id)
    {
        $recurring_invoice = Contract::findOrFail($id);
        $recurring_invoice->delete();
        return '';
    }
}
