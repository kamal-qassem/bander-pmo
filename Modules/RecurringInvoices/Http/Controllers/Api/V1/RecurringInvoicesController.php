<?php

namespace Modules\RecurringInvoices\Http\Controllers\Api\V1;

use Modules\RecurringInvoices\Entities\RecurringInvoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\RecurringInvoices\Http\Requests\Admin\StoreRecurringInvoicesRequest;
use Modules\RecurringInvoices\Http\Requests\Admin\UpdateRecurringInvoicesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class RecurringInvoicesController extends Controller
{
    public function index()
    {
        return RecurringInvoice::all();
    }

    public function show($id)
    {
        return RecurringInvoice::findOrFail($id);
    }

    public function update(UpdateRecurringInvoicesRequest $request, $id)
    {
        $recurring_invoice = RecurringInvoice::findOrFail($id);
        $recurring_invoice->update($request->all());
        

        return $recurring_invoice;
    }

    public function store(StoreRecurringInvoicesRequest $request)
    {
        $recurring_invoice = RecurringInvoice::create($request->all());
        

        return $recurring_invoice;
    }

    public function destroy($id)
    {
        $recurring_invoice = RecurringInvoice::findOrFail($id);
        $recurring_invoice->delete();
        return '';
    }
}
