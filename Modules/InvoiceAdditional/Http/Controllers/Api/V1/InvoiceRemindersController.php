<?php

namespace App\Http\Controllers\Api\V1;

use App\InvoiceReminder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceRemindersRequest;
use App\Http\Requests\Admin\UpdateInvoiceRemindersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class InvoiceRemindersController extends Controller
{
    public function index()
    {
        return InvoiceReminder::all();
    }

    public function show($id)
    {
        return InvoiceReminder::findOrFail($id);
    }

    public function update(UpdateInvoiceRemindersRequest $request, $id)
    {
        $invoice_reminder = InvoiceReminder::findOrFail($id);
        $invoice_reminder->update($request->all());
        

        return $invoice_reminder;
    }

    public function store(StoreInvoiceRemindersRequest $request)
    {
        $invoice_reminder = InvoiceReminder::create($request->all());
        

        return $invoice_reminder;
    }

    public function destroy($id)
    {
        $invoice_reminder = InvoiceReminder::findOrFail($id);
        $invoice_reminder->delete();
        return '';
    }
}
