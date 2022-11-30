<?php

namespace App\Http\Controllers\Api\V1;

use App\InvoicePayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoicePaymentsRequest;
use App\Http\Requests\Admin\UpdateInvoicePaymentsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class InvoicePaymentsController extends Controller
{
    public function index()
    {
        return InvoicePayment::all();
    }

    public function show($id)
    {
        return InvoicePayment::findOrFail($id);
    }

    public function update(UpdateInvoicePaymentsRequest $request, $id)
    {
        $invoice_payment = InvoicePayment::findOrFail($id);
        $invoice_payment->update($request->all());
        

        return $invoice_payment;
    }

    public function store(StoreInvoicePaymentsRequest $request)
    {
        $invoice_payment = InvoicePayment::create($request->all());
        

        return $invoice_payment;
    }

    public function destroy($id)
    {
        $invoice_payment = InvoicePayment::findOrFail($id);
        $invoice_payment->delete();
        return '';
    }
}
