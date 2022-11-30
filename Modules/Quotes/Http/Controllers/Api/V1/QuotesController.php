<?php

namespace Modules\Quotes\Http\Controllers\Api\V1;

use Modules\Quotes\Entities\Quote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuotesRequest;
use App\Http\Requests\Admin\UpdateQuotesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class QuotesController extends Controller
{
    public function index()
    {
        return Quote::all();
    }

    public function show($id)
    {
        return Quote::findOrFail($id);
    }

    public function update(UpdateQuotesRequest $request, $id)
    {
        $recurring_invoice = Quote::findOrFail($id);
        $recurring_invoice->update($request->all());
        

        return $recurring_invoice;
    }

    public function store(StoreQuotesRequest $request)
    {
        $recurring_invoice = Quote::create($request->all());
        

        return $recurring_invoice;
    }

    public function destroy($id)
    {
        $recurring_invoice = Quote::findOrFail($id);
        $recurring_invoice->delete();
        return '';
    }
}
