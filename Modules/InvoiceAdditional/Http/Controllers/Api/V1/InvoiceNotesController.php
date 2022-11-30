<?php

namespace App\Http\Controllers\Api\V1;

use App\InvoiceNote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceNotesRequest;
use App\Http\Requests\Admin\UpdateInvoiceNotesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class InvoiceNotesController extends Controller
{
    public function index()
    {
        return InvoiceNote::all();
    }

    public function show($id)
    {
        return InvoiceNote::findOrFail($id);
    }

    public function update(UpdateInvoiceNotesRequest $request, $id)
    {
        $invoice_note = InvoiceNote::findOrFail($id);
        $invoice_note->update($request->all());
        

        return $invoice_note;
    }

    public function store(StoreInvoiceNotesRequest $request)
    {
        $invoice_note = InvoiceNote::create($request->all());
        

        return $invoice_note;
    }

    public function destroy($id)
    {
        $invoice_note = InvoiceNote::findOrFail($id);
        $invoice_note->delete();
        return '';
    }
}
