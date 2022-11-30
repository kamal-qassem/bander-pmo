<?php

namespace App\Http\Controllers\Api\V1;

use App\InvoiceTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceTasksRequest;
use App\Http\Requests\Admin\UpdateInvoiceTasksRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class InvoiceTasksController extends Controller
{
    use FileUploadTrait;

    public function index()
    {
        return InvoiceTask::all();
    }

    public function show($id)
    {
        return InvoiceTask::findOrFail($id);
    }

    public function update(UpdateInvoiceTasksRequest $request, $id)
    {
        $request = $this->saveFiles($request);
        $invoice_task = InvoiceTask::findOrFail($id);
        $invoice_task->update($request->all());
        

        return $invoice_task;
    }

    public function store(StoreInvoiceTasksRequest $request)
    {
        $request = $this->saveFiles($request);
        $invoice_task = InvoiceTask::create($request->all());
        

        return $invoice_task;
    }

    public function destroy($id)
    {
        $invoice_task = InvoiceTask::findOrFail($id);
        $invoice_task->delete();
        return '';
    }
}
